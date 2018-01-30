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
        $file_in = file_get_contents("php://input"); //接收post数据
        file_put_contents('xml.txt',$file_in);
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        //先把xml转换为simplexml对象，再把simplexml对象转换成 json，再将 json 转换成数组。
      //  $value_array = json_decode(json_encode(simplexml_load_string($file_in, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
       // Log::init(['type'=>'File','path'=>APP_PATH.'WebChat_log_msg/']);
        //Log::info($value_array);
        if(\request()->isPost()){
			file_put_contents('hJ.txt',$file_in);
            return  \app\index\logic\Goods::return_address_param(input('post.'));
        }else{
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
