<?php
namespace app\index\controller;
use Org\service\Service;
use think\Controller;
use think\Request;
use think\Session;
use think\Validate;
use think\Db;
class Register extends Controller
{
    public function index()
    {//注册
        $rid = base64_decode(request()->route('mid'));
        if(!empty($rid)){
            $member_invite = Db::name('member')->where('invite_code',$rid)->find();
            if($member_invite){
                $this->assign('reg_code',$member_invite['invite_code']);
            }
        }
        return  view();
    }

    public function seek(){//找回密码
        return view();
    }

    public function rezhao(){//找回密码数据认证处理
        if (Request::instance()->isGet()) {
            $input = input('get.');
            $list = Db::name('member')->where('mobile',$input['mobile'])->select();
            $count = count($list);
            if($count == 0) {
                return json_encode(['status' => 2, 'msg' => '该手机号码未注册']);
            }else{
                $trui = $this -> htr($input['mobile']);
                if($trui){
                    return  json_encode(['status'=>1,'msg'=>'发送成功，请注意接收']);
                }else{
                    return json_encode(['status'=>2,'msg'=>'网络超时！请刷新重试']);
                }
            }
        }
    }

    public function bankyl(){
        if (Request::instance()->isGet()) {
            $input = input('get.');
            $rule = [
                'mobile'=>['regex'=>'/^1[34578]\d{9}$/'],//手机
                'password'=>['regex'=>'/^[a-z\d]{6,12}$/i'],
                'repassword'=>'confirm:password',
            ];
            $message = [
                'mobile'=>'非法手机格式',
                'password'=>'密码为6-12位任意组成！',
                'repassword'=>'兩次密碼不一致'
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return json_encode(['status'=>2,'msg'=>$validate->getError()]);
            }
            if(file_exists('./text/'.$input['mobile'].'.txt')){
                $list  = file_get_contents('./text/'.$input['mobile'].'.txt');
                if($list !== $input['code']){
                    return json_encode(['status'=>2,'msg'=>'验证码错误']);
                }
                $li = Db::name('member')->where('mobile',$input['mobile'])->update(['password'=>md5_pass(2,$input['password']),'password_two'=>md5_pass(1,$input['password'])]);
                if($li>0){
                    unlink('./text/'.$input['mobile'].'.txt');
                    return json_encode(['status'=>1,'msg'=>'登录密码与提现密码重置成功！']);
                }else{
                    return json_encode(['status'=>2,'msg'=>'网络超时！请刷新重试']);
                }
            }else{
                return json_encode(['status'=>2,'msg'=>'手机号码与验证码不匹配']);
            }
        }
    }


    public function lsd(){
        if (Request::instance()->isGet()) {
            $input = input('get.');
            $rule = [
                'mobile'=>['regex'=>'/^1[34578]\d{9}$/'],//手机
                'password'=> 'require|max:12|min:6',
                'repassword'=>'confirm:password',
            ];
            $message = [
                'mobile'=>'非法手机格式',
                'password'=>'密码为6-12位任意组成！',
                'repassword'=>'兩次密碼不一致',
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return json_encode(['status'=>2,'msg'=>$validate->getError()]);
            }
            $los = Db::name('member')->where('mobile',$input['mobile'])->select();
            if(count($los) > 0){
                return json_encode(['status'=>2,'msg'=>'手机号码已注册!']);
            }
                $re = Db::name('member')->where('invite_code',$input['rey'])->find();
                if(count($re) == 0){
                    return json_encode(['status'=>2,'msg'=>'邀请码错误或者不存在！']);
                }
                $data['mobile'] = $input['mobile'];
                $data['password'] = md5_pass(2,$input['password']);
                $data['recommend'] = $re['id'];
                $data['invite_code'] = substr( $data['mobile'],-6);
                file_put_contents('bc.txt', $data['invite_code']);
                $invite_code = Db::name('member')->where('invite_code',$data['invite_code'])->find();
                if(count($invite_code) > 0){
                    $data['invite_code'] = substr( $data['mobile'],-3).rand(000,999);
                }
                $data['reg_time'] = time();
                $data['card'] = $input['card'];
                $call_back=Db::table('web_member')->strict(true)->insertGetId($data);
                $fanhui=Db::table('web_member')->where('id',$re['id'])->setInc('invite_person');
                if($call_back && $fanhui){
                    Session::set('uid',$call_back);
                    session('mobilesCode',null);
                    unlink('./text/'.$input['mobile'].'.txt');
                    Session::set('name',$call_back);
                    return json_encode(['status'=>1]);
                }else{
                    return json_encode(['status'=>2,'msg'=>'网络超时！请刷新重试']);
                }
        }
    }

    public function pas(){//提现密码
        if (Request::instance()->isGet()) {
            $input = input('get.');
            $rule = [
                'password'=>'require|max:12|min:6',
                'repassword'=>'confirm:password',
            ];
            $message = [
                'password'=>'密码为6-12位任意组成！',
                'repassword'=>'兩次密碼不一致'
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return json_encode(['status'=>2,'msg'=>$validate->getError()]);
            }
            $uid =  Session::get('uid');
            $password_two = md5_pass(1,$input['password']);
            $list = Db::table('web_member')->where('id', $uid)->update(['password_two' => $password_two]);
            if($list){
                return json_encode(['status'=>1]);
            }else{
                return json_encode(['status'=>2,'msg'=>'网络超时！请重试！']);
            }
        }
    }



    public function duanxin()
    {
        if (Request::instance()->isGet()) {
            $input = input('get.');
            $rule = [
                'mobile'=>['regex'=>'/^1[34578]\d{9}$/']//手机
            ];
            $message = [
                'mobile'=>'非法手机格式',
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return json_encode(['status'=>2,'msg'=>$validate->getError()]);
            }
            $resulo = Db::name('member')->where('mobile',$input['mobile'])->select();
            if(count($resulo)>0){
                return  json_encode(['status'=>2,'msg'=>'手机号码已注册']);
            }
            $phone = $input['mobile'];
            $data['mobile'] = $phone;
            $data['time'] = strtotime(date('Y-m-d'));
            $trui = $this -> htr($data['mobile']);
            if($trui){
                return  json_encode(['status'=>1,'msg'=>'发送成功，请注意接收']);
            }else{
                return json_encode(['status'=>2,'msg'=>'网络超时！请重试']);
            }
            }else{
            }
        }

    private function htr($phone){
        $randStr = str_shuffle('1234567890');
        $code = substr($randStr,0,6);
        $msg="您的短信验证码为：".$code;
        $result = $this -> NewSms($phone,$msg);
        $resul = explode('/',$result);
        if($resul[0] == 000){
            file_put_contents('./text/'.$phone.'.txt',$code);
            return true;
        }else{
            return false;
        }
    }

    private function NewSms($phone,$msg)
    {
        $url="http://service.winic.org:8009/sys_port/gateway/index.asp?";
        $data = "id=%s&pwd=%s&to=%s&content=%s&time=";
        $id = 'zhl168';
        $pwd = '2058505';
        $to = $phone;
        $content = iconv("UTF-8","GB2312",$msg);
        $rdata = sprintf($data, $id, $pwd, $to, $content);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = substr($result,0,3);
        return $result;
    }
}
