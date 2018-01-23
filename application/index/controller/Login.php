<?php
namespace app\index\controller;
use think\cache\driver\Redis;
use think\Controller;
use think\Log;
use think\Request;
use think\Session;
use think\Validate;
use think\Db;
class Login extends controller
{
    public function index()
    {

        return  view();
    }
    public function test_ll(){
        return view('test');
    }

    //找回密码
    public function Retrieve(){
        if(Request::instance()->isGet()){
            $parameter = \request()->param();
            $rule = [
                'mobile'=>'require|max:11|number',
                'verify_code'=>'require|max:6'
            ];
            $msg = [
                'mobile.require'=>"手机号码必填",
                'mobile..number'=>'必须是数字',
                'mobile.max'=>'最大长度11位数',
                'verify_code.require'=>'验证码必填',
                'verify_code.max'=>'验证码最大长度6位数'
            ];
            $validate = new Validate($rule,$msg);
            if(!$validate->check($parameter)){
                return json_encode(['status'=>'2', 'msg'=>$validate->getError()]);
            }
            if(Session::get('mobile_verify') != $parameter['verify_code']){
                return json_encode(['status'=>'2', 'msg'=>'验证码出错']);
            }
            $member = Db::name('member')->where('mobile',$parameter['mobile'])->find();
            if(!$member){
                return json_encode(['status'=>'2', 'msg'=>'手机号码不存在']);
            }
            \db('member')->where('id',$member['id'])->update([
                'password'=>md5_pass(2,123456),'password_two'=>md5_pass(1,123456),'update_time'=>time()
            ]);
            return json_encode(['status'=>1,'msg'=>'找回成功']);
        }
    }

    //发送短信验证码,找回密码
    public function message_verify(){
        $validate = new Validate(['mobile'=>'require|max:11|min:9|number'],['mobile.require'=>'必填','mobile.max'=>"最大11位数"]);
        $get_data = \request()->get();
        if(Request::instance()->isGet()){
            if(!$validate->check($get_data)){
                return json_encode(['status'=>'2', 'msg'=>$validate->getError()]);
            }
            $member = Db::name('member')->where('mobile',$get_data['mobile'])->find();
            if(!$member){
                return json_encode(['status'=>2,'msg'=>'手机号码不存在']);
            }
            if(NewSms($get_data['mobile'])){
                return json_encode(['status'=>'1','msg'=>'发送成功']);
            }else{
                return json_encode(['status'=>'2', 'msg'=>"发送失败"]);
            }
        }
    }

    //登录验证
    public function Verification()
    {
        if (Request::instance()->isGet()) {
            $input = \request()->param();
            Log::init(['type'=>'File','path'=>APP_PATH.'Data_log/']);
            Log::info($input);
            $rule = [
                'mobile'=>'require|number|max:11|min:9',//手机
            ];
            $message = [
                'mobile'=>'手机错误或者不存在！',
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return json_encode(['status'=>2,'msg'=>$validate->getError()]);
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
                $redis = new \think\session\driver\Redis();
                $redis->open();
                $redis->write('uid',$list['id']);
                $redis->write('log_time',\request()->time());
                return json_encode(['status'=>1,'msg'=>'登陆成功', 'member_id'=>$list['id']]);
            }else{
                Db::name('member')->where('mobile', $input['mobile'])->setInc('re_num');
                return json_encode(['status'=>2,'msg'=>'密码错误！']);
            }
        }
    }

    //注册会员
    public function register_member(){
        $rule = [
            'mobile'=>['regex'=>'/^1[34578]\d{9}$/'],
            'invite_code'=>'require|max:6|number',
            'password'=>'require|min:6|max:16|confirm:password1',
            'password_two'=>"require|min:6|max:16|confirm:password_two1"
        ];
        $message = [
            'mobile.regex'=>'手机号码格式错误',
            'invite_code.require'=>'推荐人必填',
            'invite_code.number'=>'必须是数字',
            'invite_code.min'=>"最低长度6位数"
        ];
        $validate = new Validate($rule,$message);
        $input = input('param.');
        if(!$validate->check($input)){
            return json_encode(['status'=>'2','msg'=>$validate->getError()]);
        }
        $member_table = Db::name('member');
        $select_result=$member_table->where('mobile',$input['mobile'])->find();
        if($select_result){
            return json_encode(['status'=>'2','msg'=>'会员已存在']);
        }
        $member_invite_people = $member_table->where('invite_code',$input['invite_code'])->find();
        if(!$member_invite_people){
            return json_encode(['status'=>'2','msg'=>'推荐人不存在']);
        }
        $arr = [
            'mobile'=>$input['mobile'],'invite_code'=>member_inviteCode(),'recommend'=>$member_invite_people['id'],
            'password'=>md5_pass(2,$input['password']),'password_two'=>md5_pass(1,$input['password_two']),
            'reg_time'=>time(),'reg_ip'=>\request()->ip()
        ];
        $result_return=$member_table->insertGetId($arr);
        if($result_return){
            return json_encode(['status'=>'1','msg'=>'注册成功','member_id'=>$result_return]);
        }else{
            return json_encode(['status'=>'2', 'msg'=>'注册失败']);
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


