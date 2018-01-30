<?php

namespace app\admin\controller;

use app\home\model\User;
use app\index\model\Goods;
use Endroid\QrCode\QrCode;
use lg\Logs;
use Org\WeChat\WeChat;
use think\Config;
use think\Controller;
use think\Db;
use think\Loader;
use think\Log;
use think\Request;

class Captcha extends Controller
{
    public function  baibao_return(){//接收微信支付回调
        if(!empty($_POST)){
			file_put_contents('hJ.txt',$_POST);
            return  \app\index\logic\Goods::return_address_param(input('post.'));
        }else{
            file_put_contents('kk.txt',
                \request()->method().'not nothing data!'.' <>'.date('Y-m-d H:i:s',time())."\n".input('param.'));
            exit('SUCCESS');
        }
    }

    public function zhang(){
        //鑫谷公众号的密钥
        dump(md5_pass(2,'xingu'));
    }
    //拉钩支付
    public function lagou_pay(){
        if(Request::instance()->isGet()){
            $get_data = \request()->get();
            Log::init(['type'=>"File", 'path'=>APP_PATH.'zhangsan/']);
            Log::info($get_data);
            $list = Db::name('member')->where('mobile',$get_data['mobile'])->find();
            if($list){
                return json_encode(['status'=>1,'msg'=>'successfully'.$get_data['mobile']]);
            }else{
                return json_encode(['status'=>2, 'msg'=>'error_msg']);
            }
        }
    }

    public function Custom(){
        //演示实例化
            $tables = new \app\index\logic\User();
            dump($tables->each_day_money());
    }
    //
    public  function indexddd(){
       dump(md5(md5_pass('2','123456')));
    }
    public function resaction(){
        $resule=Logs::portbility();
        dump($resule);
    }
	
	public function ceshi_return($id){
			$arr['clientSno']=$id;
			return Goods::return_address_param_ssl($arr);
	}
	
    //模拟请求数据
    public function point_request(){
        return view('request');
    }
    //接受回调的数据
    public function call_backdata(){
        /* *
 *功能：智汇付个人网银支付异步通知接口
 *版本：3.0
 *日期：2016-07-10
 *说明：
 *以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,
 *并非一定要使用该代码。该代码仅供学习和研究智付接口使用，仅为提供一个参考。
 **/

//////////////////////////	接收智付返回通知数据  /////////////////////////////////
        /**
        获取订单支付成功之后，智汇付通知服务器以post方式返回来的订单通知数据，参数详情请看接口文档,
         */

        $merchant_code	= $_POST["merchant_code"];//商家号

        $interface_version = $_POST["interface_version"];// 参数名称：接口版本

        $sign_type = $_POST["sign_type"];//参数名称：签名方式

        $dinpaySign = base64_decode($_POST["sign"]);//智汇付返回签名数据

        $notify_type = $_POST["notify_type"];//通知方式,固定值：offline_notify或者page_notify

        $notify_id = $_POST["notify_id"];//参数名：通知校验ID此版本不需要校验，但是参数依然保留

        $order_no = $_POST["order_no"];//商家订单号

        $order_time = $_POST["order_time"];//商家订单时间

        $order_amount = $_POST["order_amount"];//商家订单金额,精确到小数点后两位

        $trade_status = $_POST["trade_status"];//订单状态,取值为“SUCCESS”，代表订单交易成功

        $trade_time = $_POST["trade_time"];//智汇付订单时间

        $trade_no = $_POST["trade_no"];//智汇付订单号

        $bank_seq_no = $_POST["bank_seq_no"];//参数名：银行交易流水号

        $extra_return_param = $_POST["extra_return_param"];


/////////////////////////////   参数组装  /////////////////////////////////
        /**
        除了sign_type dinpaySign参数，其他非空参数都要参与组装，组装顺序是按照a~z的顺序，下划线"_"优先于字母
         */


        $signStr = "";

        if($bank_seq_no != ""){
            $signStr = $signStr."bank_seq_no=".$bank_seq_no."&";
        }

        if($extra_return_param != ""){
            $signStr = $signStr."extra_return_param=".$extra_return_param."&";
        }

        $signStr = $signStr."interface_version=".$interface_version."&";

        $signStr = $signStr."merchant_code=".$merchant_code."&";

        $signStr = $signStr."notify_id=".$notify_id."&";

        $signStr = $signStr."notify_type=".$notify_type."&";

        $signStr = $signStr."order_amount=".$order_amount."&";

        $signStr = $signStr."order_no=".$order_no."&";

        $signStr = $signStr."order_time=".$order_time."&";

        $signStr = $signStr."trade_no=".$trade_no."&";

        $signStr = $signStr."trade_status=".$trade_status."&";

        $signStr = $signStr."trade_time=".$trade_time;

        //echo $signStr;

/////////////////////////////   RSA-S验证  /////////////////////////////////


        $dinpay_public_key = openssl_get_publickey(Config::get('DINPAY_PUBLIC_KEY'));

        $flag = openssl_verify($signStr,$dinpaySign,$dinpay_public_key,OPENSSL_ALGO_MD5);

///////////////////////////   响应“SUCCESS” /////////////////////////////

        if($flag){
           $recharge= Db::name('recharge');
            $huode=$recharge->where('orders',$order_no)->find();
            if($huode && $huode['success_status'] == 1 && $huode['action']==2){
               // $rule=$recharge->where('orders',$order_no)->setField('success_status',1);
                $count_money=$recharge->where('uid',$huode['uid'])->where('success_status',1)->sum('money');
                //获取充值金额$count_money
                \app\admin\model\Member::tianjiamoney($huode,$count_money);
            }else{
                Log::error('请求出错error');
            }
        }else{
            Log::init(['type'=>'File','path'=>APP_PATH.'zhangsan/']);
            Log::info(request()->post());
            Log::error('error');
        }
    }
    public function gongkai(){
        file_put_contents("D:/gong.txt",1);
    }
    //定时测试获取积分
    public function member_func(){
        \app\admin\model\Member::timing_execute();
    }

    public function xunhuan(){
        $m=0;
        for($i=0;$i<100;$i++){
            $result=send_mail('210564078@qq.com','xiaoming','xiaomingquxiaoxue');
            if($result !== true){
                echo 'shibai';
                break;
            }
            $m +=$i;
        }
        file_put_contents('D:/M.txt',$m);
    }

    public  function qrcode(){

            Loader::import('Org.phpqrcode.qrlib');
           $data = 'http://www.baidu.com';
              \QRcode::png($data,false,QR_ECLEVEL_M,10,1);
              exit;
    }

}
