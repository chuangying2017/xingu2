<?php

namespace app\index\model;

use Org\pay\Pay_lagou;
use Org\service\Service;
use Org\WeChat\WeChat;
use think\Db;
use think\Exception;
use think\Log;
use think\Model;
use think\Request;
use think\Session;
use think\Url;
use think\Validate;
class Goods extends Model
{

    use \traits\controller\Jump;


	protected function httpPost($url,$post_data){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
	
    //购买
    public static  function buy_data_verfiy($data){

        try{
            /*
      * 亲支付接口对接在这里
      * 您参考一下
      * */
            $role = [
                'pid'=>'require|number',
                'pay_type'=>'require|number'
            ];
            $message = [
                'pay_type.require'=>'支付类型不能为空','pay_type.number'=>'支付类型不正确',
                'pid.require'=>'产品id不能为空','pid.number'=>'必须是数字'];
            $validate = new Validate($role,$message);

            if(!$validate->check($data)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $uid = Session::get('uid');
            $product_table = Db::name('product');//产品表
            $order_table = Db::name('orders');//订单表
            $product_data=$product_table->where('id',$data['pid'])->find();
            if(!$product_data){
                return ['status'=>2,'msg'=>'产品不存在'];
            }
            $day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
            $sql = "SELECT DISTINCT price From web_product WHERE type_id=".$product_data['type_id']." ORDER  BY price";
            $query = db()->query($sql);
            $max = $order_table->where(['uid'=>$uid,'type'=>2])->max('price');
            if($product_data['price'] < $max){
                return ['status'=>2,'msg'=>'不能购买低于上次的价格'];
            }
            $oneday = strtotime(date('Y-m-d')." 00:00:00");
            $array = array('create_date'=>['lt',$oneday]);
            $find_select=$order_table->where(['uid'=>$uid,'type'=>2])->where($array)->order('price desc')->find();//查询出已支付，订单
            if(empty($find_select) && $product_data['price'] > $query[0]['price']){
                return ['status'=>2,'msg'=>'当前购买只能买'.$query[0]['price']];
            }
            $hh500=$order_table->where(['uid'=>$uid,'type'=>2,'price'=>$query[1]['price']])->where($array)->order('price desc')->find();//查询出已支付，订单
            if(!$hh500 && $product_data['price'] > $query[1]['price']){
                return ['status'=>2,'msg'=>'当前只能购买'.$query[1]['price']];
            }
            $hh500=$order_table->where(['uid'=>$uid,'type'=>2,'price'=>$query[2]['price']])->where($array)->order('price desc')->find();//查询出已支付，订单
            if(!$hh500 && $product_data['price'] > $query[2]['price']){
                return ['status'=>2,'msg'=>'当前只能购买'.$query[2]['price']];
            }
            $return_data_boolean=$order_table->where(['uid'=>$uid,'type'=>2])->where($day)->find();
            if($return_data_boolean){
                return ['status'=>2,'msg'=>'当天只能购买一单'];
            }
            $return_data_boolean_=$order_table->where(['uid'=>$uid,'type'=>1])->where($day)->count();
            if($return_data_boolean_ >= 5){
                return ['status'=>2,'msg'=>'您今天的待支付超出请撤销'];
            }

            if(!$validate::token('','',['__token__'=>$data['token']])){
                return ['status'=>2,'msg'=>'请不要重复提交表单'];
            }
            /*
           * 首先去如果金额足够的情况下查一下上一次有无购买
           * 验证提交过来的数据的规则
           * 如果有购买的话就必须大于等于上一次的购买的数量才可以解锁上一次返还的积分
           * */
            $database = database(2);
            $build_order_no = self::self_return();
            $arr_add = array(
                'pid'=>$data['pid'],
                'uid'=>$uid,
                'create_date'=>request()->time(),
                'order_no'=>$build_order_no,
                'num'=>1,
                'price'=>$product_data['price'],
                'interest'=>($database['deduct_s'] / 100) * $product_data['price']
            );
            $resturn_id=$order_table->insertGetId($arr_add);
            $order_num = $order_table->where('id',$resturn_id)->find();
            if($data['pay_type'] == 6){
                $arr_money = [0.1,0.2,0.3,0.4,0.5];
                $WeChat = new WeChat(['attach_id'=>$resturn_id.'&cashier2','total_fee'=>($product_data['price']- $arr_money[array_rand($arr_money,1)]) * 100]);//购买众酬
                $code_url = $WeChat->unifiedOrder();
                Log::init(['type'=>'File','path'=>APP_PATH.'WeChat_logs/']);
                Log::log($code_url);
                if(isset($code_url['mweb_url']) || isset($code_url['code_url'])){
                    $qrUrl = $code_url['mweb_url'] ?: "http://paysdk.weixin.qq.com/example/qrcode.php?data={$code_url['code_url']}";
                    if($order_num['codeUrl'] == ''){
                        $order_table->where('id',$resturn_id)->update(['codeUrl'=>$qrUrl]);
                    }
                }else{
                    return ['status'=>2,'msg'=>'暂无法支付'];
                }
            }else if($data['pay_type'] == 7){
                $arr_money = [0.1,0.2,0.3,0.4,0.5];
                $WeChat = new WeChat([
                    'attach_id'=>$resturn_id.'&cashier2',
                    'total_fee'=>($product_data['price']- $arr_money[array_rand($arr_money,1)]) * 100,
                     'APPID'=>'wx1cfd2a62a95d3535','MCHID'=>'1494678642',
                    ]);//购买众酬
                $code_url = $WeChat->unifiedOrder();
                Log::init(['type'=>'File','path'=>APP_PATH.'WeChat_logs/']);
                Log::log($code_url);
                if(isset($code_url['mweb_url']) || isset($code_url['code_url'])){
                    $qrUrl = $code_url['mweb_url'] ?: "http://paysdk.weixin.qq.com/example/qrcode.php?data={$code_url['code_url']}";
                    if($order_num['codeUrl'] == ''){
                        $order_table->where('id',$resturn_id)->update(['codeUrl'=>$qrUrl]);
                    }
                }else{
                    return ['status'=>2,'msg'=>'暂无法支付'];
                }
            }elseif($data['pay_type'] == 8){//添加快捷
                    $pay_lagou = new Pay_lagou(2,$resturn_id,'cashier2');
                   $order_table->where('id',$resturn_id)->update(['lagou_sign'=>$pay_lagou->index()['pay_md5sign']]);
                    return ['status'=>1,'msg'=>\url('index/suey/shortcut_pay',['order_data'=>base64_encode(json_encode($pay_lagou->index()))])];
            }
            else{
                $arra['mach_order_id'] = $resturn_id;//商户订单编号,这里设置订单id
                $arra['price']=$product_data['price'];//产品价格
                $arra['pay_type'] = $data['pay_type'];// 付款类型，0:微信, 1:支付宝
                $arra['cashier'] = 2;//众筹
                $res = baibao_pay_interface($arra);
                if(empty($res['errcode']) && $res['qrcode']){
                    $qrUrl = "http://pan.baidu.com/share/qrcode?w=300&h=300&url={$res['qrcode']}";
                    if($order_num['codeUrl'] == ''){
                        $order_table->where('id',$resturn_id)->update(['codeUrl'=>$qrUrl]);
                    }
                    if($order_num['order_id_baibao'] == ''){
                        $order_table->where(['id'=>$resturn_id])->update(['order_id_baibao'=>$res['order_id']]);
                    }
                    Log::init(['type'=>'File','path'=>APP_PATH.'log_s/']);
                    Log::log($res);
                }else{
                    return ['status'=>3,'msg'=>'订单支付超时请撤销后再生成','url'=>\url('index/record/inscl')];
                }
            }
            if(!isset($code_url['mweb_url'])){
                Session::set('zhifu_code',!empty($order_num['codeUrl'])?$order_num['codeUrl']:$qrUrl);
                Session::set('zhifu_jinqian',$product_data['price']);
            }
            return ['status'=>1,'msg'=>isset($code_url['mweb_url'])?$qrUrl:Url::build('index/Suey/zhifu_yanzheng')];
        }catch (Exception $exception){
            Log::init(['type'=>'File','path'=>APP_PATH.'WeChat_error/']);
            Log::error($exception->getMessage());
        }

    }

    private static function self_return(){
            while (true){
                $order =  build_order_no();
                $select_find_result = Db::name('orders')->where('order_no',$order)->find();
                if(!$select_find_result){
                    return $order;
                }
            }
		}
	
	
    public static function return_address_param_ssl($post){//百宝回调处理
            Log::init(['type'=>'File','path'=> APP_PATH.'return_logs/']);
            Log::info($post);
            $order_table = Db::name('orders');
            $return_data=$order_table->where('order_no',$post['clientSno'])->where('type',1)->find();
            if(!$return_data){
                self::error('非法请求',url('index/Index/index'));
            }
            $oneday = strtotime(date('Y-m-d')." 23:59:59");
            $yesterday = $oneday - (3600 * 24);
            $arr = array('uid'=>$return_data['uid'],'status'=>1,'type'=>2,'create_date'=>['elt',$yesterday]);
            $find_last=$order_table->where($arr)->sum('price');//去查询上一次有无购买,统计出来的那个金额
            $add_insert_result=$order_table->where(['id'=>$return_data['id']])->update(['type'=>2,'update_time'=>time()]);
            if(!$add_insert_result){
                Log::init(['type'=>'File','path'=> APP_PATH.'error_logs/']);
                Log::error('订单更改失败');
                exit();
            }
            $day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
            Db::name('product')->where('id',$return_data['pid'])->setInc('buy_num');//购买成功，把购买的数量加入到那个产品的购买量
            $cash=$order_table->where($day)->where(['status'=>1,'type'=>2])->sum('price');//查询今天购买的总产品的价格
            $database = database(2);//拿到后台设置的参数
            $member_table = Db::name('member');
			$member_table->where(['id'=>$return_data['uid']])->update(['buy_result'=>1]);
            $bonus = Db::name('bonus');//奖金表
            $result_table = $member_table->where('id',$return_data['uid'])->find();
            //统计直推人数奖励
                /*
                 * 这里计算团队
                 * 奖金
                 * */
                self::mean_total_money($return_data['uid'],$return_data['price']);
            $recommend_one=$member_table->where('id',$result_table['recommend'])->find();//拿到一级的推荐人
		if(!empty($recommend_one)){
                $one_money = $return_data['price'] * ($database['recommend_one'] / 100);
                $find_result = $order_table->where(['uid'=>$recommend_one['id'],'status'=>1,'type'=>2])->find();
                if(empty($find_result) || $find_result['price'] < $return_data['price'] ){
                    $one_money = $one_money / 2;
                }
                $one_recommend=$member_table->where('id',$result_table['recommend'])->setInc('bonus',$one_money);//一级推荐人先拿奖金
                if($one_recommend){
                    $bonus->insert(['uid'=>$recommend_one['id'],'type'=>1,'create_date'=>request()->time(),'money'=>$one_money]);
                }
                $recommend_two_level = $member_table->where('id',$recommend_one['recommend'])->find();//查一下二级有无存在
                $two_money = $return_data['price'] * ($database['recommend_tow'] / 100);//计算二级推荐奖金
                $recommend_two = $member_table->where('id',$recommend_one['recommend'])->setInc('bonus',$two_money);//拿二级推荐奖
                if($recommend_two){//检验一下是否返回true
                    $bonus->insert(['uid'=>$recommend_two_level['id'],'type'=>2,'create_date'=>request()->time(),'money'=>$two_money]);
                }
                if($recommend_two_level['recommend']){
                    $three_money = $return_data['price'] * ($database['recommend_three'] / 100);//计算三级奖金
                    $member_table->where('id',$recommend_two_level['recommend'])->setInc('bonus',$three_money);//拿三级推荐奖
                    $bonus->insert(['uid'=>$recommend_two_level['recommend'],'type'=>3,'create_date'=>request()->time(),'money'=>$three_money]);
                }
            }
            if($find_last > 0 && $cash >= $find_last){//将上一次购买的产品和今天购买的所有产品做对比
                $ben_bonus=($find_last * ($database['deduct_s'] / 100)) + $find_last;  //得到一个本金加奖励
                $bonus->insert(['uid'=>$return_data['uid'],'status'=>2,'create_date'=>request()->time(),'money'=>$ben_bonus]);//记录解锁的本金加奖励
                $member_table->where('id',$return_data['uid'])->setInc('money',$ben_bonus);
                $find_affect_line=$order_table->where($arr)->update(['status'=>2]);//将所有上一次购买的产品状态为1的全部改为2
            }else{
                $find_affect_line = true;
            }
            if($find_affect_line){
                exit('success');
               // self::success('购买成功','index/Index/index');
            }else{
                exit('error');
               // self::error('购买失败','index/Index/index');
            }
    }
	
	
    public static function return_address_param($post){//百宝回调处理
            Log::init(['type'=>'File','path'=> APP_PATH.'return_logs/']);
            Log::info($post);
            $order_table = Db::name('orders');
            $return_data=$order_table->where('id',$post['merchant_id'])->where('type',1)->find();
            if(!$return_data){
                self::error('非法请求',url('index/Index/index'));
            }
            $oneday = strtotime(date('Y-m-d')." 23:59:59");
            $yesterday = $oneday - (3600 * 24);
            $arr = array('uid'=>$return_data['uid'],'status'=>1,'type'=>2,'create_date'=>['elt',$yesterday]);
            $find_last=$order_table->where($arr)->sum('price');//去查询上一次有无购买,统计出来的那个金额
            $add_insert_result=$order_table->where(['id'=>$return_data['id']])->update(['type'=>2,'update_time'=>time()]);
            if(!$add_insert_result){
                Log::init(['type'=>'File','path'=> APP_PATH.'error_logs/']);
                Log::error('订单更改失败');
                exit();
            }
            $day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
            Db::name('product')->where('id',$return_data['pid'])->setInc('buy_num');//购买成功，把购买的数量加入到那个产品的购买量
            $cash=$order_table->where($day)->where(['status'=>1,'type'=>2])->sum('price');//查询今天购买的总产品的价格
            $database = database(2);//拿到后台设置的参数
            $member_table = Db::name('member');
			$member_table->where(['id'=>$return_data['uid']])->update(['buy_result'=>1]);
            $bonus = Db::name('bonus');//奖金表
            $result_table = $member_table->where('id',$return_data['uid'])->find();
            //统计直推人数奖励
                /*
                 * 这里计算团队
                 * 奖金
                 * */
                self::mean_total_money($return_data['uid'],$return_data['price']);
            $recommend_one=$member_table->where('id',$result_table['recommend'])->find();//拿到一级的推荐人
           if(!empty($recommend_one)){
               $one_money = $return_data['price'] * ($database['recommend_one'] / 100);
               $find_result = $order_table->where(['uid'=>$recommend_one['id'],'status'=>1,'type'=>2])->find();
               $find_tresult = Db::name('torder')->where(['uid'=>$recommend_one['id'],'status'=>1])->find();
               if(!empty($find_result) || !empty($find_tresult)){
                   $one_recommend=$member_table->where('id',$result_table['recommend'])->setInc('bonus',$one_money);//一级推荐人先拿奖金
                   if($one_recommend){
                       $bonus->insert(['uid'=>$recommend_one['id'],'type'=>1,'create_date'=>request()->time(),'money'=>$one_money]);
                   }
               }
               if($recommend_one['recommend']){
                   $recommend_two_level = $member_table->where('id',$recommend_one['recommend'])->find();//查一下二级有无存在
                   $two_money = $return_data['price'] * ($database['recommend_tow'] / 100);//计算二级推荐奖金
                   $find_result_money = $order_table->where(['uid'=>$recommend_two_level['id'],'status'=>1,'type'=>2])->find();
                   $find_result_tmoney = Db::name('torder')->where(['uid'=>$recommend_two_level['id'],'status'=>1])->find();
                   if($find_result_money || $find_result_tmoney){
                       $recommend_two = $member_table->where('id',$recommend_one['recommend'])->setInc('bonus',$two_money);//拿二级推荐奖
                       if($recommend_two){//检验一下是否返回true
                           $bonus->insert(['uid'=>$recommend_two_level['id'],'type'=>2,'create_date'=>request()->time(),'money'=>$two_money]);
                       }
                   }
               }
               if($recommend_two_level['recommend']){
                   $three_money = $return_data['price'] * ($database['recommend_three'] / 100);//计算三级奖金
                   $find_result_three = $order_table->where(['uid'=>$recommend_two_level['recommend'],'status'=>1,'type'=>2])->find();
                   $find_result_tthree = Db::name('torder')->where(['uid'=>$recommend_two_level['recommend'],'status'=>1])->find();
                   if($find_result_three || $find_result_tthree){
                       $member_table->where('id',$recommend_two_level['recommend'])->setInc('bonus',$three_money);//拿三级推荐奖
                       $bonus->insert(['uid'=>$recommend_two_level['recommend'],'type'=>3,'create_date'=>request()->time(),'money'=>$three_money]);
                   }
               }
           }
            if($find_last > 0 && $cash >= $find_last){//将上一次购买的产品和今天购买的所有产品做对比
                $ben_bonus=($find_last * ($database['deduct_s'] / 100)) + $find_last;  //得到一个本金加奖励
                $bonus->insert(['uid'=>$return_data['uid'],'status'=>2,'create_date'=>request()->time(),'money'=>$ben_bonus]);//记录解锁的本金加奖励
                $member_table->where('id',$return_data['uid'])->setInc('money',$ben_bonus);
                $find_affect_line=$order_table->where($arr)->update(['status'=>2]);//将所有上一次购买的产品状态为1的全部改为2
            }else{
                $find_affect_line = true;
            }
            if($find_affect_line){
                exit('success');
               // self::success('购买成功','index/Index/index');
            }else{
                exit('error');
               // self::error('购买失败','index/Index/index');
            }
    }

    //计算奖金
    private static function mean_total_money($uid,$price){
        //查询出我的上级
        $member = Db::name('member');
        $one_level=$member->where('id',$uid)->find();
        $database = database(2);
        $count_team = self::team_people($one_level['id']);
        if($one_level['invite_person'] >= $database['team_people_num_zhis'] && $count_team >= $database['team_people_num1'] && !empty($one_level)){//判断直推人数是否达到要求
                $team_bonus_money=$price * ($database['bonus_money_team1'] / 100);//计算团队奖励
                /*
                 * 首先计算推荐人数最多
                 * */
                $member->where('id',$one_level['id'])->setInc('bonus',$team_bonus_money);
                Db::name('bonus')->insert([
                    'uid'=>$one_level['id'],'create_date'=>time(),'type'=>4,'money'=>$team_bonus_money
                ]);
            $team = self::mean_total_money($one_level['recommend'],$price);
        }else if($one_level['invite_person'] >= $database['team_people_num_zhi'] && $count_team >= $database['team_people_num'] && !empty($one_level)){
                $team_bonus_money=$price * ($database['bonus_money_team'] / 100);//计算团队奖励
                $member->where('id',$one_level['id'])->setInc('bonus',$team_bonus_money);//将金额加入会员列表
                Db::name('bonus')->insert([
                    'uid'=>$one_level['id'],'create_date'=>time(),'type'=>4,'money'=>$team_bonus_money
                ]);
            $team = self::mean_total_money($one_level['recommend'],$price);
        }else if(!empty($one_level)){
            $team = self::mean_total_money($one_level['recommend'],$price);
        }else{
            return 'No calculation';
        }
        return $team;
    }

//            //计算奖金
//    private static function mean_total_money($uid,$price){
//                //查询出我的上级
//            $member = Db::name('member');
//            $one_level=$member->where('id',$uid)->find();
//            $database = database(2);
//            if($one_level['invite_person'] >= $database['team_people_num_zhis'] && !empty($one_level)){//判断直推人数是否达到要求
//                $count_team = self::team_people($one_level['id']);//查询团队人数
//                if($count_team >= $database['team_people_num1']){//判断团队已购买人数是否达到要求
//                    $team_bonus_money=$price * ($database['bonus_money_team1'] / 100);//计算团队奖励
//                    /*
//                     * 首先计算推荐人数最多
//                     * */
//                    $member->where('id',$one_level['id'])->setInc('bonus',$team_bonus_money);
//                    Db::name('bonus')->insert([
//                        'uid'=>$one_level['id'],'create_date'=>time(),'type'=>4,'money'=>$team_bonus_money
//                    ]);
//                }
//               $team = self::mean_total_money($one_level['recommend'],$price);
//            }else if($one_level['invite_person'] >= $database['team_people_num_zhi'] && !empty($one_level)){
//                $count_team = self::team_people($one_level['id']);
//                if($count_team >= $database['team_people_num']){
//                    $team_bonus_money=$price * ($database['bonus_money_team'] / 100);//计算团队奖励
//                    $member->where('id',$one_level['id'])->setInc('bonus',$team_bonus_money);//将金额加入会员列表
//                    Db::name('bonus')->insert([
//                        'uid'=>$one_level['id'],'create_date'=>time(),'type'=>4,'money'=>$team_bonus_money
//                    ]);
//                }
//                $team = self::mean_total_money($one_level['recommend'],$price);
//            }else if(!empty($one_level)){
//                $team = self::mean_total_money($one_level['recommend'],$price);
//            }else{
//                return 'No calculation';
//            }
//            return $team;
//        }
         //计算团队
    public static function team_people($id){
                //传本身的id过来然后这边记性计算有无
                /*
                 * 如果团队里面没有人购买产品是不算奖励在里面
                 * */
                $member = Db::name('member');
               $select_find = $member->where('recommend',$id)->where('status',1)->select();//获取直推人数
               $count_team = count($select_find);//获取总数
               $count = 0;
               if(empty($select_find)){//如果直推人数为0则没有推荐会员
                   return $count_team;
               }else{
                   for($i=0;$i<$count_team;$i++){
                       $count += self::trans_func($select_find[$i]['id']);
                   }
                   return $count;
               }
        }
    //传递给另一个方法做处理
    public function trans_func($id){
        $member = Db::name('member');
        $select_find = $member->where('recommend',$id)->where('status',1)->select();//获取直推人数
        $count_num = count($select_find);//获取直推人数总数
        $res_ult = Db::name('orders')->where(['status'=>1,'type'=>2,'uid'=>$id])->find();//查询是否购买过众筹产品
        $re_lt = Db::name('torder')->where(['status'=>1,'uid'=>$id])->find();
        $count = 0;
        if($res_ult || $re_lt){
            $count+=1;
        }else{
           return $count;
        }
        for ($k=0;$k<$count_num;$k++){
            $count += self::trans_func($select_find[$k]['id']);
        }
        return $count;
    }

    public static function Goodsnvestment($data){//投资购买
        $role = [
            'pid'=>'require|number',
            'pay_type'=>'require|number'
        ];
        $message = [
            'pay_type.require'=>'支付类型不能为空','pay_type.number'=>'支付类型不正确',
            'pid.require'=>'产品id不能为空','pid.number'=>'必须是数字'];
        $validate = new Validate($role,$message);
        if(!$validate::token('','',['__token__'=>$data['token']])){
            return ['status'=>2,'msg'=>'请不要重复提交表单'];
        }
        if(!$validate->check($data)){
            return ['status'=>2,'msg'=>$validate->getError()];
        }

        $list = Db::name('product')->where('id',$data['pid'])->find();//查询产品是否存在
        if(!$list){
            return ['status'=>2,'msg'=>'产品不存在！'];
        }
        $num = 0;
        $arr['uid'] = Session::get('uid');
        $torder_table = Db::name('torder');
        $prinum = $torder_table->where('uid', $arr['uid'])->where('status',1)->select();
        for($i = 0;$i < count($prinum);$i++){
            $num += $prinum[$i]['price'];//获取该会员已完成投资订单的总额，5000封顶

        }
        $arr['pid'] =  $list['id'];//商品ID
        $arr['price'] = $list['price'];//订单产品单价
//        $arr['real'] = $arr['should']/ $list['name_tian'];//订单的每天返还额度
        $arr['create_time'] = time();//订单创建事件
        $arr['order_no'] = self::self_return();//订单号
        $arr['should'] = $list['price']*$list['name_bei'];//获取订单的总赠送额
        $arr['status'] = 0;//0为未支付，1是已支付
        $result = $torder_table->insertGetId($arr);//获取刚完成订单的ID
        if(!$result){
            return ['status'=>2,'msg'=>'网络超时！'];
        }
        $order_num = $torder_table->where('id',$result)->find();
        if($data['pay_type'] == 6){
            $arr_money = [0.1,0.2,0.3,0.4,0.5];
            $WeChat = new WeChat(['attach_id'=>$result.'&cashier1','total_fee'=>($list['price']- $arr_money[array_rand($arr_money,1)]) * 100]);//购买投资
            $code_url = $WeChat->unifiedOrder();
            if(isset($code_url['mweb_url'])){
               // $qrUrl = "http://paysdk.weixin.qq.com/example/qrcode.php?data={$code_url['code_url']}";
                $qrUrl = $code_url['mweb_url'];
                if($order_num['codeUrl'] == ''){
                    $torder_table->where('id',$result)->update(['codeUrl'=>$qrUrl]);
                }
                Log::init(['type'=>'File','path'=>APP_PATH.'WeChat_logs/']);
                Log::log($code_url);
            }else{
                return ['status'=>2,'msg'=>'暂无法支付'];
            }
        }elseif($data['pay_type'] > 6){
            $arr_money = [0.1,0.2,0.3,0.4,0.5];
            $WeChat = new WeChat([
                'attach_id'=>$result.'&cashier2',
                'total_fee'=>($result['price']- $arr_money[array_rand($arr_money,1)]) * 100,
                'APPID'=>'wx1cfd2a62a95d3535','MCHID'=>'1494678642',
            ]);//购买众酬
            $code_url = $WeChat->unifiedOrder();
            Log::init(['type'=>'File','path'=>APP_PATH.'WeChat_logs/']);
            Log::log($code_url);
            if(isset($code_url['mweb_url'])){
                $qrUrl = $code_url['mweb_url'] ?: "http://paysdk.weixin.qq.com/example/qrcode.php?data={$code_url['code_url']}";
                if($order_num['codeUrl'] == ''){
                    $torder_table->where('id',$result)->update(['codeUrl'=>$qrUrl]);
                }
            }else{
                return ['status'=>2,'msg'=>'暂无法支付'];
            }
        }
        else{
            $arra['mach_order_id'] = $result;//商户订单编号,这里设置订单id
            $arra['price']=$list['price'];//产品价格
            $arra['pay_type'] = $data['pay_type'];// 付款类型，0:微信, 1:支付宝
            $arra['cashier'] = 1;//投资
            $res = baibao_pay_interface($arra);
            if(empty($res['errcode']) && $res['qrcode']){
                $qrUrl = "http://pan.baidu.com/share/qrcode?w=300&h=300&url={$res['qrcode']}";
                if($order_num['codeUrl'] == ''){
                    $torder_table->where('id',$result)->update(['codeUrl'=>$qrUrl]);
                }
                if($order_num['order_id_baibao'] == ''){
                    $torder_table->where(['id'=>$result])->update(['order_id_baibao'=>$res['order_id']]);
                }
                Log::init(['type'=>'File','path'=>APP_PATH.'log_s/']);
                Log::log($res);
            }else{
                return ['status'=>3,'msg'=>'订单支付超时请撤销后再生成','url'=>\url('index/record/inscl')];
            }
        }
        if(!isset($code_url['mweb_url'])){
            Session::set('zhifu_code',!empty($order_num['codeUrl'])?$order_num['codeUrl']:$qrUrl);
            Session::set('zhifu_jinqian',$list['price']);
        }
        return ['status'=>1,'msg'=>isset($code_url['mweb_url'])?$qrUrl:Url::build('index/Suey/zhifu_yanzheng')];
    }
    //百宝回调数据,
    public static function return_pay($result){
        $data = Db::name('torder')->where('id',$result['merchant_id'])->find();//获取当完成订单的信息
        Db::table('web_member')->where('id', $data['uid'])->setInc('frozenmoney',  $data['should']);//把完成订单的赠送数额加入到会员表的冻结钱袋
        self::personal__bonus($data);//发放三代奖励
        self::mean_total_money($data['uid'],$data['price']);//发放团队奖励
    }

    private static function personal__bonus($data){//直推奖金
        $member = Db::name('member');//会员表
        $bonus = Db::name('bonus');//奖金表
        $database = database(2);//获取数据库参数

        $list = $member->where('id',$data['uid'])->find();
        $recommend_one=$member->where('id',$list['recommend'])->find();//查询直推人是否存在
        if(!empty($recommend_one)){//存在执行下列代码
            $yi_zhen = Db::name('orders')->where('uid',$recommend_one['id'])->where('type',2)->select();//查询是否购买过众筹产品
            $two_zhen = Db::name('torder')->where('uid',$recommend_one['id'])->where('status',1)->select();//查询是否购买过分红产品
            if(count($yi_zhen)>0 || count($two_zhen)>0){//如果购买过众筹或者分红产品，则分发直推奖
                $one_money = $data['price'] * ($database['zhong_one'] / 100);
                $one_recommend=$member->where('id',$recommend_one['id'])->setInc('bonus',$one_money);//一级推荐人先拿奖金
                if($one_recommend){
                    $bonus->insert(['uid'=>$recommend_one['id'],'type'=>1,'create_date'=>request()->time(),'money'=>$one_money]);
                }
                return self::er_jiang($recommend_one,$data);
            }else{
                return self::er_jiang($recommend_one,$data);
            }
        }
    }

    private static function er_jiang($recommend_one,$data){//二代奖金
        $member = Db::name('member');//会员表
        $bonus = Db::name('bonus');//奖金表
        $database = database(2);//获取数据库参数
//        return $database;
        $recommend_two_level = $member->where('id',$recommend_one['recommend'])->find();//查一下二级有无存在
        if(!empty($recommend_two_level)){

            $twoes_zhen = Db::name('orders')->where('uid',$recommend_two_level['id'])->where('type',2)->select();
            $twos_zhen = Db::name('torder')->where('uid',$recommend_two_level['id'])->where('status',1)->select();
            if(count($twoes_zhen)>0 || count($twos_zhen)>0){
                $two_money = $data['price'] * ($database['zhong_tow'] / 100);//计算二级推荐奖金
//                return ['price'=>$data['price'],'z'=>$database['zhong_tow'] / 100];
                $recommend_two = $member->where('id',$recommend_two_level['id'])->setInc('bonus',$two_money);//拿二级推荐奖
                if($recommend_two){//检验一下是否返回true
                    $bonus->insert(['uid'=>$recommend_two_level['id'],'type'=>2,'create_date'=>request()->time(),'money'=>$two_money]);
                }
                return self::san_jiang($recommend_two_level,$data);
            }else{
                return self::san_jiang($recommend_two_level,$data);
            }
        }
    }

    private static function san_jiang($recommend_two_level,$data){//三代奖金
        try{}catch (Exception $exception){
            Log::init();

        }
        $member = Db::name('member');//会员表
        $bonus = Db::name('bonus');//奖金表
        $database = database(2);//获取数据库参数
        $recommend_two_levelwe = $member->where('id',$recommend_two_level['recommend'])->find();//查一下三级有无存在
        if(!empty($recommend_two_levelwe)){//如果存在
            $san_zhen = Db::name('orders')->where('uid',$recommend_two_levelwe['id'])->where('type',2)->select();//查询是否由购买过众筹
            $sans_zhen = Db::name('torder')->where('uid',$recommend_two_levelwe['id'])->where('status',1)->select();//查询是否由购买过分红
            if(count($san_zhen)>0 || count($sans_zhen)>0){//如果购买过众筹或者分红则分发三代奖
                $three_money = $data['price'] * ($database['zhong_three'] / 100);//计算三级奖金
                $member->where('id',$recommend_two_levelwe['id'])->setInc('bonus',$three_money);//拿三级推荐奖
                $bonus->insert(['uid'=>$recommend_two_levelwe['id'],'type'=>3,'create_date'=>request()->time(),'money'=>$three_money]);
            }
        }
    }
}
