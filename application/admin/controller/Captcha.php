<?php

namespace app\admin\controller;

use lg\Logs;
use think\Controller;
use think\Db;
use think\Log;
use think\Request;

class Captcha extends Controller
{
    public function  baibao_return(){//接收微信支付回调

        if(\request()->isPost()){
            $file_in = file_get_contents("php://input"); //接收post数据
            libxml_disable_entity_loader(true);
            $value_array = json_decode(json_encode(simplexml_load_string($file_in, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            return  \app\index\logic\Goods::return_address_param($value_array);
        }else{
            $file_in = file_get_contents("php://input"); //接收post数据
            file_put_contents('hJ2.txt',$file_in);
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

	
    //模拟请求数据
    public function point_request(){
        return view('request');
    }

}
