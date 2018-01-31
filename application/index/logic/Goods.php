<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/23 0023
 * Time: 下午 4:45
 */

namespace app\index\logic;

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
    protected static $attr_value;//设置后台迭代人数的值
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
    //获取订单号
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
    //微信支付处理回调数据
    public static function return_address_param($post){
        /*
             'appid' => 'wxb044063c2f33112a',公众账号ID
             'attach' => '294',
             'bank_type' => 'CFT',付款银行
             'cash_fee' => '980',现金支付金额，等于9.8元
             'fee_type' => 'CNY',货币种类
             'is_subscribe' => 'Y',是否关注公众账号
             'mch_id' => '1497314162',商户号
             'nonce_str' => 'h77r8cyx63phs6u5wdxcmluiqlctjzvy',随机字符串
             'openid' => 'oMXs-1pf1cWiFTBr1-KPyNmsD3pI',用户标识
             'out_trade_no' => '149731416220180130143342',商户订单号
             'result_code' => 'SUCCESS',结果状态码
             'return_code' => 'SUCCESS',返回状态码
             'sign' => '124BA515CC50F32D6C606D54E6A7D198',设备号
             'time_end' => '20180130143355',支付完成时间
             'total_fee' => '980',分为单位等于9.8元
             'trade_type' => 'MWEB',H5支付模式
             'transaction_id' => '4200000058201801303693366653',微信支付订单号
         */
        Log::init(['type'=>'File','path'=> APP_PATH.'return_logs/']);
        Log::info($post);
        $order_table = Db::name('orders');
        $return_data=$order_table->where('id',$post['attach'])->where('type',1)->find();
        if(!$return_data){
            self::error('非法请求',url('index/Index/index'));
        }
        $add_insert_result=$order_table
            ->where(['id'=>$return_data['id']])
            ->update(['type'=>2,'update_time'=>time(),'transaction_id'=>$post['transaction_id']]);//type=2表示在线支付transaction_id记录微信支付订单号
        if(!$add_insert_result){
            Log::init(['type'=>'File','path'=> APP_PATH.'error_logs/']);
            Log::error('订单更改失败');
            exit();
        }
        $day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
        Db::name('product')->where('id',$return_data['pid'])->setInc('buy_num');//购买成功，把购买的数量加入到那个产品的购买量
        try{
            $data_base = database(2);//拿到后台设置的参数
            $member_table = Db::name('member');//会员table
            $bonus_table = Db::name('bonus');//奖金表
            $product_tables = Db::name('product');//产品表
            $member_data = $member_table->where('id',$return_data['uid'])->find();//会员
            $product_buy_total = $return_data['price'] * $return_data['num'];//购买数量 * 产品价格 = 总金额
            $data_product=$product_tables->find($return_data['pid']);//获取产品记录
            $each_num_money = $data_product['name_bei'] / 10 * $product_buy_total;//每次分配的金额20%
            $member_table->where('id',$member_data['id'])->setInc('money',$each_num_money);//现在投资马上有20%分红
            Db::name('mp')->insert([//会员分红表
                'uid'=>$member_data['id'],'create_date'=>\request()->time(),
                'total_money'=>$product_buy_total,//每次购买分配的总金额
                'type'=>'1',//分配类型1分配中
                'day_each'=>$data_product['name_tian'] - 1,//获取产品的发放天数 -1
                'raito'=>$data_product['name_bei'],
                'each_money'=>$each_num_money,//每次分配的金额
                'order_id'=>$return_data['id'],//添加一个订单id
                'residue_money'=>($data_product['name_tian'] - 1) * $each_num_money//得到一个剩余分配金额
            ]);
            Db::name('profit')->insert([//每日分红表
                'uid'=>$member_data['id'],
                'money'=>$each_num_money,
                'type'=>'1',//type=1表示每日分红
                'create_time'=>\request()->time(),
                'order_id'=>$return_data['id']
            ]);
            if($member_data['recommend']){//计算直推荐关系奖金
                $recommend_id = 0;$recommend_one = $member_data['recommend'];//$recommend_one推荐人id
                for ($j=1;$j<$data_base['iteration_of']+1;$j++){//计算迭代10层级关系
                    if($recommend_id >= $j){//$recommend_id这个等于2的时候计算第二次
                        switch ($recommend_id){
                            case 2://计算二代奖金
                                $two_bonus = $product_buy_total * ($data_base['recommend_tow'] / 100);//计算二代的奖励
                                $two_level_member=$member_table->where('id',$one_member_data['recommend'])->find();//查看上一级推荐人
                                if($two_level_member){//为true就进来查询有无购买记录
                                    $order_boolean = $order_table->where('price','egt','100')->where(['uid'=>$one_member_data['recommend']])->where('type','in','2,3')->count();
                                    if($order_boolean){
                                        $member_table->where('id',$two_level_member['id'])->setInc('bonus',$two_bonus);//添加二级推荐奖励
                                        $bonus_table->insert([
                                            'uid'=>$two_level_member['id'],'type'=>'2','create_date'=>time(),'money'=>$two_bonus,'order_id'=>$order_id
                                        ]);//生成一条奖金记录
                                    }
                                    $recommend_id++;
                                }else{//否者不为真就是停止
                                    return 'SUCCESS';
                                }
                                break;
                            case 3://计算三代奖金
                                $three_bonus = $product_buy_total * ($data_base['recommend_three'] / 100);
                                $three_level_member=$member_table->where('id',$two_level_member['recommend'])->find();
                                if($three_level_member){
                                    $order_boolean = $order_table->where('price','egt','100')->where(['uid'=>$two_level_member['recommend']])->where('type','in','2,3')->count();
                                    if($order_boolean){
                                        $member_table->where('id',$three_level_member['id'])->setInc('bonus',$three_bonus);//添加三级级推荐奖励
                                        $bonus_table->insert([
                                            'uid'=>$three_level_member['id'],'type'=>'3','create_date'=>time(),'money'=>$three_bonus,'order_id'=>$order_id
                                        ]);//生成一条奖金记录
                                    }
                                    $recommend_id++;
                                    $recommend_one = $three_level_member['recommend'];//拿到上一级的id
                                }else{
                                    return 'SUCCESS';
                                }
                                break;
                            default://剩余全部计算奖金
                                $total_bonus = $product_buy_total * ($data_base['iteration_percentage'] / 100);
                                $all_member_data = $member_table->where('id',$recommend_one)->find();//查询一下这个id有没有
                                if($all_member_data){//查询上一级有无推荐人
                                    $order_boolean = $order_table->where('price','egt','100')->where(['uid'=>$recommend_one])->where('type','in','2,3')->count();
                                    if($order_boolean){//查询一下上一级有无购买超过100元的金额
                                        $member_table->where('id',$all_member_data['id'])->setInc('bonus',$total_bonus);//添加三级级推荐奖励
                                        $bonus_table->insert([
                                            'uid'=>$all_member_data['id'],'type'=>$recommend_id,'create_date'=>time(),'money'=>$total_bonus,'order_id'=>$order_id
                                        ]);//生成一条奖金记录
                                    }
                                    $recommend_one = $all_member_data['recommend'];
                                    $recommend_id++;
                                }else{
                                    return 'SUCCESS';
                                }
                                break;
                        }
                    }else{
                        if($recommend_id > 0){
                            break;
                        }else{//大于0，第二次循环计算推荐奖励,第一次是0，计算第一次的推荐奖励
                            $one_member_data=$member_table->where('id',$recommend_one)->find();//查询上一级
                            $order_boolean = $order_table->where('price','egt','100')->where(['uid'=>$recommend_one,'type'=>array('in','2,3')])->count();
                            //计算购买产品的一代奖金
                            if($one_member_data && $order_boolean){
                                $one_bonus = $product_buy_total * ($data_base['recommend_one'] / 100);
                                $member_table->where('id',$one_member_data['id'])->setInc('bonus',$one_bonus);//加到会员奖金
                                $bonus_table->insert([
                                    'uid'=>$one_member_data['id'],'type'=>'1','create_date'=>time(),'money'=>$one_bonus,'order_id'=>$order_id
                                ]);//生成一条奖金记录
                                $recommend_id = 2;//循环第二次
                            }elseif ($one_member_data){
                                $recommend_id = 2;
                            }else{
                                return 'SUCCESS';
                            }
                        }
                    }
                }
            }
            self::mean_total_money($return_data['uid'],$product_buy_total);
            return 'SUCCESS';
            //统计直推人数奖励
            /*
             * 这里计算团队
             * 奖金
             * */
        }catch (Exception $exception){
            Log::init(['type'=>'File','path'=>APP_PATH.'pay_log/']);
            Log::error($exception->getMessage());
            exit;
        }
    }
    //计算奖金
    private static function mean_total_money($uid,$price){
        //查询出我的上级
        $member = Db::name('member');
        $one_level=$member->where('id',$uid)->find();
        $database = database(2);//获取后台设置的参数
        self::$attr_value = $database['relationship'];
        $count_team = self::team_people($one_level['id']);
        if($one_level['invite_person'] >= $database['team_people_num_zhis'] && $count_team >= $database['team_people_num1'] && !empty($one_level)){//判断直推人数是否达到要求
            $team_bonus_money=$price * ($database['bonus_money_team1'] / 100);//计算团队奖励
            /*
             * 首先计算推荐人数最多
             * */
            $member->where('id',$one_level['id'])->setInc('bonus',$team_bonus_money);
            Db::name('bonus')->insert([
                'uid'=>$one_level['id'],'create_date'=>time(),'type'=>12,'money'=>$team_bonus_money
            ]);
            $team = self::mean_total_money($one_level['recommend'],$price);
        }else if($one_level['invite_person'] >= $database['team_people_num_zhi'] && $count_team >= $database['team_people_num'] && !empty($one_level)){
            $team_bonus_money=$price * ($database['bonus_money_team'] / 100);//计算团队奖励
            $member->where('id',$one_level['id'])->setInc('bonus',$team_bonus_money);//将金额加入会员列表
            Db::name('bonus')->insert([
                'uid'=>$one_level['id'],'create_date'=>time(),'type'=>12,'money'=>$team_bonus_money
            ]);
            $team = self::mean_total_money($one_level['recommend'],$price);
        }else if(!empty($one_level)){
            $team = self::mean_total_money($one_level['recommend'],$price);
        }else{
            return 'No calculation';
        }
        return $team;
    }
    //查询已有购买的会员
    public static function team_people($id){
        //传本身的id过来然后这边记性计算有无
        /*
         * 如果团队里面没有人购买产品是不算奖励在里面
         * 按照后台设置的迭代人数去计算
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
        $iteration_people = count($select_find); //获取直推人数总数
        $count_num = $iteration_people < self::$attr_value ? $iteration_people : self::$attr_value;//设置一个迭代人数
        $res_ult = Db::name('orders')->where(['uid'=>$id])->where('type','in','2,3')->find();//查询有无购买产品
        //2表示在线支付3表示复投
        $count = 0;
        if($res_ult){
            $count+=1;
        }else{
            return $count;
        }
        for ($k=0;$k<$count_num;$k++){
            $count += self::trans_func($select_find[$k]['id']);
        }
        return $count;
    }
    //购买产品数据
    public static function Goodsnvestment($data){//投资购买
        $role = [
            'pid'=>'require|number',
            'pay_type'=>'require|number',//支付接口类型
            'buy_num'=>'require|number|max:5',//产品数量
        ];
        $uid = Session::get('uid');
        $message = [
            'pay_type.require'=>'支付类型不能为空','pay_type.number'=>'支付类型不正确',
            'pid.require'=>'产品id不能为空','pid.number'=>'必须是数字','buy_num.require'=>'产品数量必须的','buy_num.number'=>'必须是数字'];
        $validate = new Validate($role,$message);
        if(!$validate::token('','',['__token__'=>$data['token']])){
            return ['status'=>2,'msg'=>'请不要重复提交表单'];
        }
        if(!$validate->check($data)){
            return ['status'=>2,'msg'=>$validate->getError()];
        }

        $product_table = Db::name('product');
        //生成订单
        $orders_table_object = new \app\index\model\Goods();//订单表
        $order_table = Db::name('orders');//获取一个订单表的实例
        $day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
        $result_num = $orders_table_object->where(['pid'=>$data['pid'],'uid'=>$uid])->where($day)->select();//查出会员单个产品下单情况
        $data_product=$product_table->where('id',$data['pid'])->where('status',1)->find();//查出产品表数据
        if(!$data_product){
            return ['status'=>2,'msg'=>'产品不存在'];
        }
        $num=0;$num1=0;$num2=0;//统计循环未支付和待支付$num未支付$num1在线支付$num2复投
        for ($i=0;$i<count($result_num);$i++){
            if($result_num[$i]['type'] == 1){//未支付
                $num += 1;
            }elseif ($result_num[$i]['type'] == 2){//在线已支付,
                $num1 += 1;
            }elseif ($result_num[$i]['type'] == 3){//复投支付
                $num2 +=1;
            }
        }
        if($num >= 15){ //如果未支付的订单超越15单 false
            return ['status'=>2,'msg'=>'未支付订单过多'];
        }elseif ($num1 > 0 || $num2 > 0){//当日购买相同的产品false
            return ['status'=>2,'msg'=>'请选择更高'];
        }elseif($data['buy_num'] > $data_product['gou_num']){//当前
            return ['status'=>2,'msg'=>'购买数量超出'];
        }
        $member_table = Db::name('member');//会员表
        $bonus_table = Db::name('bonus');//奖金表

        $member_data=$member_table->where('id',$uid)->find();
        if(!$member_data){
            return ['status'=>2,'msg'=>'会员不存在'];
        }
        $data_base = database(2);//获取参数设置
        $product_buy_total = $data_product['price'] * $data['buy_num'];//购买数量 * 产品价格 = 总金额
        $order_id = $orders_table_object->insertGetId([//产生一个订单id
            'order_no'=>self::self_return(),
            'create_date'=>time(),'price'=>$data_product['price'],
            'uid'=>$uid,'pid'=>$data['pid'],'num'=>$data['buy_num'],//type=1待支付
            'type'=>1,'interest'=>$data_product['price'] * ($data_product['name_bei'] / 10) * $data_product['name_tian']//得到价格的20%金额
        ]);
        $order_num = $order_table->find($order_id);
        if($data['pay_type'] == 7){//H5支付购买
            $arr_money = [0.1,0.2,0.3,0.4,0.5];//attach_id这个设置为订单号的id
            $WeChat = new WeChat(['attach_id'=>$order_id,'total_fee'=>($data_product['price'] * $data['buy_num'] - $arr_money[array_rand($arr_money,1)]) * 100]);//购买投资
            $code_url = $WeChat->unifiedOrder();
            if(isset($code_url['mweb_url'])){//H5支付地址
                // $qrUrl = "http://paysdk.weixin.qq.com/example/qrcode.php?data={$code_url['code_url']}";
                $qrUrl = $code_url['mweb_url'];
                if($order_num['codeUrl'] == ''){
                    $order_table->where('id',$order_id)->update(['codeUrl'=>$qrUrl]);
                }
                Log::init(['type'=>'File','path'=>APP_PATH.'WeChat_logs/']);
                Log::log($code_url);
            }else{
                return ['status'=>2,'msg'=>'暂无法支付'];
            }
        }elseif($data['pay_type'] == 8){//H5支付购买
            return ['status'=>2,'msg'=>'暂无开放'];
           //$arr_money = [0.1,0.2,0.3,0.4,0.5];
            /*            $WeChat = new WeChat([
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
                    $order_table->where('id',$result)->update(['codeUrl'=>$qrUrl]);
                }
            }else{
                return ['status'=>2,'msg'=>'暂无法支付'];
            }*/
        }
        elseif($data['pay_type'] == 9){//余额复投
            $total_charge = $product_buy_total * 0.03;//复投产生百分之3的手续费
            if($member_data['money'] < ($product_buy_total + $total_charge)){
                return ['status'=>5,'msg'=>'余额不足'];
            }
            if($data_product['price'] < $data_base['deposit_number1a']){
                return ['status'=>2,'msg'=>'最低投资'.$data_base['deposit_number1a'].'￥'];
            }
            try{
            $order_table->where('id',$order_id)->update(['type'=>3]);
            //结算每次分配的金额
            $each_num_money = $product_buy_total * ($data_product['name_bei'] / 10);
            $member_table->where(['id'=>$uid])->update(['money'=>$member_data['money'] - $product_buy_total - $total_charge,'update_time'=>time(),
                'bonus'=>$member_data['bonus'] + $each_num_money]);//把每日分配的金额加入到会员表
            Db::name('mp')->insert([//会员分红表
                'uid'=>$uid,'create_date'=>\request()->time(),
                'total_money'=>$product_buy_total,//每次购买分配的总金额
                'type'=>'1',//分配类型1分配中
                'day_each'=>$data_product['name_tian'] - 1,//获取产品的发放天数 -1
                'raito'=>$data_product['name_bei'],
                'each_money'=>$each_num_money,//每次分配的金额
                'order_id'=>$order_id,//添加一个订单id
                'residue_money'=>($data_product['name_tian'] - 1) * $each_num_money//得到一个剩余分配金额
            ]);
            Db::name('profit')->insert([//每日分红表
                'uid'=>$uid,
                'money'=>$each_num_money,
                'type'=>'1',//type=1表示每日分红
                'create_time'=>\request()->time(),
                'order_id'=>$order_id
            ]);
            if($member_data['recommend']){//计算直推荐关系奖金
                $recommend_id = 0;$recommend_one = $member_data['recommend'];//$recommend_one推荐人id
                for ($j=1;$j<$data_base['iteration_of']+1;$j++){//计算迭代10层级关系
                    if($recommend_id >= $j){//$recommend_id这个等于2的时候计算第二次
                        switch ($recommend_id){
                            case 2://计算二代奖金
                                $two_bonus = $product_buy_total * ($data_base['recommend_tow'] / 100);//计算二代的奖励
                                $two_level_member=$member_table->where('id',$one_member_data['recommend'])->find();//查看上一级推荐人
                                if($two_level_member){//为true就进来查询有无购买记录
                                    $order_boolean = $order_table->where('price','egt','100')->where(['uid'=>$one_member_data['recommend']])->where('type','in','2,3')->count();
                                    if($order_boolean){
                                        $member_table->where('id',$two_level_member['id'])->setInc('bonus',$two_bonus);//添加二级推荐奖励
                                        $bonus_table->insert([
                                            'uid'=>$two_level_member['id'],'type'=>'2','create_date'=>time(),'money'=>$two_bonus,'order_id'=>$order_id
                                        ]);//生成一条奖金记录
                                    }
                                    $recommend_id++;
                                }else{//否者不为真就是停止
                                    return ['status'=>4,'msg'=>'复投成功','url'=>\url('index/index/index')];
                                }
                                break;
                            case 3://计算三代奖金
                                $three_bonus = $product_buy_total * ($data_base['recommend_three'] / 100);
                                $three_level_member=$member_table->where('id',$two_level_member['recommend'])->find();
                                if($three_level_member){
                                    $order_boolean = $order_table->where('price','egt','100')->where(['uid'=>$two_level_member['recommend']])->where('type','in','2,3')->count();
                                    if($order_boolean){
                                        $member_table->where('id',$three_level_member['id'])->setInc('bonus',$three_bonus);//添加三级级推荐奖励
                                        $bonus_table->insert([
                                            'uid'=>$three_level_member['id'],'type'=>'3','create_date'=>time(),'money'=>$three_bonus,'order_id'=>$order_id
                                        ]);//生成一条奖金记录
                                    }
                                    $recommend_id++;
                                    $recommend_one = $three_level_member['recommend'];//拿到上一级的id
                                }else{
                                    return ['status'=>4,'msg'=>'复投成功','url'=>\url('index/index/index')];
                                }
                                break;
                            default://剩余全部计算奖金
                                $total_bonus = $product_buy_total * ($data_base['iteration_percentage'] / 100);
                                $all_member_data = $member_table->where('id',$recommend_one)->find();//查询一下这个id有没有
                                if($all_member_data){//查询上一级有无推荐人
                                    $order_boolean = $order_table->where('price','egt','100')->where(['uid'=>$recommend_one])->where('type','in','2,3')->count();
                                    if($order_boolean){//查询一下上一级有无购买超过100元的金额
                                        $member_table->where('id',$all_member_data['id'])->setInc('bonus',$total_bonus);//添加三级级推荐奖励
                                        $bonus_table->insert([
                                            'uid'=>$all_member_data['id'],'type'=>$recommend_id,'create_date'=>time(),'money'=>$total_bonus,'order_id'=>$order_id
                                        ]);//生成一条奖金记录
                                    }
                                    $recommend_one = $all_member_data['recommend'];
                                    $recommend_id++;
                                }else{
                                    return ['status'=>4,'msg'=>'复投成功','url'=>\url('index/index/index')];
                                }
                                break;
                        }
                    }else{
                        if($recommend_id > 0){
                            break;
                        }else{//大于0，第二次循环计算推荐奖励,第一次是0，计算第一次的推荐奖励
                            $one_member_data=$member_table->where('id',$recommend_one)->find();//查询上一级
                            $order_boolean = $order_table->where('price','egt','100')->where(['uid'=>$recommend_one,'type'=>array('in','2,3')])->count();
                            //计算购买产品的一代奖金
                            if($one_member_data && $order_boolean){
                                $one_bonus = $product_buy_total * ($data_base['recommend_one'] / 100);
                                $member_table->where('id',$one_member_data['id'])->setInc('bonus',$one_bonus);//加到会员奖金
                                $bonus_table->insert([
                                    'uid'=>$one_member_data['id'],'type'=>'1','create_date'=>time(),'money'=>$one_bonus,'order_id'=>$order_id
                                ]);//生成一条奖金记录
                                $recommend_id = 2;//循环第二次
                            }elseif ($one_member_data){
                                $recommend_id = 2;
                            }else{
                                return ['status'=>4,'msg'=>'复投成功','url'=>\url('index/index/index')];
                            }
                        }
                    }
                }
            }
            self::mean_total_money($order_num['uid'],$product_buy_total);
            if($order_id){
                return ['status'=>4,'msg'=>'复投成功','url'=>\url('index/index/index')];
            }else{
                return ['status'=>5,'msg'=>'复投失败'];
            }
            }catch (Exception $exception){
                Log::init(['type'=>'File','path'=>APP_PATH.'pay_log/']);
                Log::error($exception->getMessage());
                exit;
            }
        }
        if(!isset($code_url['mweb_url'])){
            Session::set('zhifu_code',!empty($order_num['codeUrl'])?$order_num['codeUrl']:$qrUrl);
            Session::set('zhifu_jinqian',$product_buy_total);
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
    //购买产品
    public static function buy_product_data($data){
        /*
         * type == 'a1'余额复投
         * type == 'a2'现金支付
         * 'pid' => '4',
         * 'buy_num' => '10',
         * 'member_id' => '1',
         * 'totol' => '30000',
         * */
        $orders_table_object = new \app\index\model\Goods();//订单表
        $product_table = Db::name('product');
        $day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
            $data_product=$product_table->where('id',$data['pid'])->where('status',1)->find();//查出产品表数据
            if(!$data_product){
                return json_encode(['status'=>2,'msg'=>'产品不存在']);
            }
           $result_num = $orders_table_object->where(['pid'=>$data['pid'],'uid'=>$data['uid']])->where($day)->select();//查出会员单个产品下单情况
            $num=0;$num1=0;//统计循环未支付和待支付
            for ($i=0;$i<count($result_num);$i++){
                if($result_num[$i]['type'] == 1){//未支付
                    $num += 1;
                }elseif ($result_num[$i]['type'] == 2){//已支付
                    $num1 += 1;
                }
            }
            if($num >= 15){ //如果未支付的订单超越15单 false
                return json_encode(['status'=>2,'msg'=>'未支付订单过多']);
            }elseif ($num1 > 0){//当日购买相同的产品false
                return json_encode(['status'=>2,'msg'=>'请选择更高']);
            }elseif($data['buy_num'] > $data_product['gou_num']){//当前
                return json_encode(['status'=>2,'msg'=>'购买数量超出']);
            }
            $member_table = Db::name('member');//会员表
            $bonus_table = Db::name('bonus');//奖金表
            $uid = redis_obj()->read('uid');
            $member_data=$member_table->where('id',$uid)->find();
            if(!$member_data){
                return json_encode(['status'=>2,'msg'=>'会员不存在']);
            }
            $data_base = database(2);//获取参数设置
            $product_buy_total = $data_product['price'] * $data['buy_num'];//购买数量 * 产品价格 = 总金额
            $product_buy_total += $product_buy_total * 0.03;
            if($member_data['money'] < $product_buy_total){
                return json_encode(['status'=>2,'msg'=>'余额不足']);
            }
            $member_table->where(['id'=>$uid])->update(['money'=>$member_data['money'] - $product_buy_total,'update_time'=>time()]);
            $order_id = $orders_table_object->insertGetId([//产生一个订单id
                'order_no'=>self::self_return(),
                'create_date'=>time(),'price'=>$data_product['price'],
                'uid'=>$data['member_id'],'pid'=>$data['pid'],'num'=>$data['buy_num'],
                'type'=>1,'interest'=>$data_product['price'] * ($data_product['name_bei'] / 10) * $data_product['name_tian']//得到价格的20%金额
            ]);
            $product_table->where(['id'=>$data['pid']])->update([//产品表更新购买数量，剩余数量，
                'buy_num'=>$data_product['buy_num'] + $data['buy_num'],
                'residue_num'=>$data_product['residue_num'] - $data['buy_num'],'update_time'=>time()
            ]);
            return $order_id;//直接返回一个会员id
    }
    //提现
    public static function deposit_money($data){
        $role = [
            'money'=>'require|float',
            'password_two'=>'require|min:6',
            'code'=>"require|min:4|captcha",
            'bank_id'=>'require|number'
        ];
        $message = [
            'money.require'=>'金额不能为空',
            'money.float'=>'必须是小数',
            'password_two.require'=>'安全密码不能为空',
            'password_two.min'=>'安全密码长度不够',
            'code.require'=>'验证码不能为空',
            'code.min'=>'验证码不能小于四位数',
            'bank_id.require'=>'银行不能为空',
            'bank_id.number'=>'银行必须是数字',
            'code.captcha'=>'验证码不正确'
        ];
        $validate = new Validate($role,$message);
        if(!$validate::token('','',['__token__'=>$data['token']])){
            return ['status'=>2,'msg'=>'请不要重复提交表单'];
        }
        if(!$validate->check($data)){
            return ['status'=>2,'msg'=>$validate->getError()];
        }
        $bank_ = Db::name('mbank')->where('id',$data['bank_id'])->find();
        if(!$bank_){
            return ['status'=>2,'msg'=>'您的银行卡不正确'];
        }
        $new = new \app\index\model\User();
        $uid = Session::get('uid');
        $member_data=$new::get(['id'=>$uid]);
        if($member_data->password_two !== md5_pass(1,$data['password_two'])){
            return ['status'=>2,'msg'=>'安全密码不正确'];
        }
        $databases = database(2);

        if($databases['tixian_status'] < 1){
            return ['status'=>2,'msg'=>'提现已关闭'];
        }
        if($databases['deposit_time_start'] > 0 ){
            $deposit_time_start = strtotime(date('Y-m-d').$databases['deposit_time_hour'].$databases['deposit_time_minute']);//start time
            $deposit_time_end = strtotime(date('Y-m-d').$databases['deposit_time_hour_stop'].$databases['deposit_time_minute_stop']);//time out.
            $time = request()->time();
            if($time < $deposit_time_start || $time > $deposit_time_end){
                return ['status'=>2,'msg'=>'请在当天'.date('H:i:s',$deposit_time_start).' 至 '.date('H:i:s',$deposit_time_end).'提现'];
            }
        }
        $day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
        $count_deposit = Db::name('deposit')->where($day)->where('uid',$uid)->count();
        if($count_deposit >= $databases['deposit_number']){
            return ['status'=>2,'msg'=>'每日可提现'.$databases['deposit_number'].'次'];
        }
        if($databases['tixianjinemin'] > $data['money']){
            return ['status'=>2,'msg'=>'提现金额不能小于'.$databases['tixianjinemin'].'￥'];
        }
        if($databases['tixianjinemax'] <= $data['money']){
            return ['status'=>2,'msg'=>'提现金额不能大于'.$databases['tixianjinemax'].'￥'];
        }
        if($member_data->money < $data['money']){
            return ['status'=>2,'msg'=>'余额不足'];
        }
        $member_data->money -= $data['money'];
        $shouxufei=$data['money'] * ($databases['shouxufei'] / 100);
        $benjin=$data['money'] - $shouxufei;
        $web_deposit = Db::name('deposit')->insert([
            'money'=>$benjin,
            'service_charge'=>$shouxufei,
            'create_date'=>request()->time(),
            'uid'=>$uid,'name'=>$bank_['name'],
            'bank'=>$bank_['bank'],'province'=>$bank_['sheng'],
            'city'=>$bank_['shi'],'kaihubank'=>$bank_['zhihang'],'bank_crad'=>$bank_['bank_user'],'mobile'=>$bank_['mobile'],
            'crad'=>$bank_['crad'],
            'type'=>1
        ]);
        $member_data->save();
        if($web_deposit){
            return ['status'=>1,'msg'=>'提现成功','url'=>Url::build('index/Suey/withdrawals','','')];
        }else{
            return ['status'=>2,'msg'=>'提现失败'];
        }
    }
}