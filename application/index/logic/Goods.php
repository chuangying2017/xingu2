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
            $member_table = Db::name('member');//会员table
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
            if(!$member_data['recommend']){
                return 'SUCCESS';
            }
            $obj_user_server=new \app\index\service\User();
            $arr_is=$obj_user_server->iterator_money($member_data['recommend'],$each_num_money);
            if(is_array($arr_is)){
                return 'SUCCESS';
            }
            self::mean_total_money($return_data['uid'],$each_num_money);
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
        $count_team = self::team_people($one_level['id']);
        //判断最大直和团队人数
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
            //判断直推较小的人数
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
        $count_num = count($select_find);//设置一个迭代人数
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
            'buy_num'=>'require|number|max:5|>:0',//产品数量
        ];
        $uid = Session::get('uid');
        $message = [
            'pay_type.require'=>'支付类型不能为空','pay_type.number'=>'支付类型不正确','buy_num.gt'=>'请输入1以上',
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
        $result_num = $orders_table_object->where(['pid'=>$data['pid'],'uid'=>$uid])->where($day)->select();//查出会员单个产品,下单情况
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
            if($member_data['money'] < ($product_buy_total + $total_charge) && $product_buy_total > 0){
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
            if($member_data['recommend']){
                $obj_user_server=new \app\index\service\User();
                $arr_is=$obj_user_server->iterator_money($member_data['recommend'],$each_num_money);//计算迭代10代奖金
               if(is_array($arr_is)){
                   return $arr_is;
               }
                self::mean_total_money($order_num['uid'],$each_num_money);
            }
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
    //提现
    public static function deposit_money($data){
        $role = [
            'money'=>'require|float|>:0',
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