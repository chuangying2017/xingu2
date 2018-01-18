<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Validate;
use think\Db;
class Login extends controller
{
    public function index()
    {
        if(Session::get('uid')){
            $file = file_get_contents('num.txt');
            file_put_contents('num.txt',$file - 1);
        }
        Session::clear();
//        var_dump(Session::get('uid'));
        return  view();
    }

    public function Verification()
    {
        if (Request::instance()->isGet()) {
            $input = input('get.');
            $rule = [
                'mobile'=>['regex'=>'/^1[34578]\d{9}$/'],//手机
            ];
            $message = [
                'mobile'=>'手机错误或者不存在！',
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return json_encode(['msg'=>$validate->getError()]);
            }
            $list = Db::name('member')->where('mobile',$input['mobile'])->find();
            if(count($list) == 0){
                return json_encode(['status'=>2,'msg'=>'用户不存在']);
            }
			if($list['status'] > 1 ){
				 return json_encode(['status'=>2,'msg'=>'帐户异常...']);
			}
            $time = strtotime(date('Y-m-d'));
            if($time > $list['re_time']){
                Db::name('member')->where('mobile', $input['mobile'])->update(['re_num' => '0','re_time'=>$time]);
            }
            if($time == $list['re_time'] && $list['re_num'] == 5){
                return json_encode(['status'=>2,'msg'=>'错误次数超过限制！请明天在试...']);
            }
            if(md5_pass(2,$input['password']) === $list['password']){
                    $file = file_get_contents('num.txt');
                    if($file < database(2)['zbjfen']){
                        $total_num = (int)database(2)['zbjfen'] + 1;
                        file_put_contents('num.txt',$total_num);
                    }else{
                        file_put_contents('num.txt',$file + 1);
                    }
                Session::set('uid',$list['id']);
                Session::set('log_time',\request()->time());
                return json_encode(['status'=>1,'msg'=>'登陆成功！正在跳转....','urls'=>'/index/index']);
            }else{
                Db::name('member')->where('mobile', $input['mobile'])->setInc('re_num');
                return json_encode(['status'=>2,'msg'=>'密码错误！']);
            }

        }
    }

    public function back_login($m) {
        if(!empty(Session::get('user_id','admin'))){
            $m = base64_decode($m);
            Session::set('uid', $m);
            Session::set('log_time', time());
            $this->redirect('index/Index/index');
        } else {
            $this->redirect('index/Login/index');
            exit;
        }
    }
	
	    public function lks(){
        return view();
    }
}


