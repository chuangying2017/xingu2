<?php
namespace app\index\controller;
use think\Request;
use think\Loader;
use think\Session;
use think\Url;
use think\Validate;
use think\Db;
class User extends \app\common\controller\Common
{
    public function index()
    {//会员中心
        $uid = Session::get('uid');
        $list = Db::name('mbank')->where('uid',$uid)->select();
        $count = count($list);
        $value = Db::name('orders')->where('uid',$uid)->where('type',2)->sum('price');
        $this -> assign('total_pricce',$value);
        $this -> assign('count',$count);
        return $this->fetch();
    }

	
	    //转换到余额
    public function transition(){
        if(\request()->isAjax()){
            $uid=Session::get('uid');
            $member = Db::name('member');
            $find_data=$member->where(['status'=>1,'id'=>$uid])->find();
            if($find_data['bonus'] < 1){
                    return ['status'=>2,'msg'=>'无奖金可转'];
            }
            $find_data['money'] += $find_data['bonus'];
            $return_result=$member->where(['id'=>$uid])->update(['money'=>$find_data['money'],'bonus'=>'0']);
            if($return_result){
                return ['status'=>1,'msg'=>'操作成功'];
            }else{
                return ['status'=>2,'msg'=>'操作失败'];
            }
        }
    }
	
	
    public function takeThe(){//提现
        $mbank = Db::name('mbank')->where('uid',Session::get('uid'))->field('id as value,bank as text')->select();
        $json = json_encode($mbank);
        $this->assign('bank',$json);
        return view();
    }
    //检查一下有无添加银行卡
    public  function load_event(){
        if(Request::instance()->isAjax()){
            $mbank_table = Db::name('mbank')->where('uid',Session::get('uid'))->find();
            if(empty($mbank_table)){
                    return ['status'=>1,'msg'=>'请先去添加银行卡','url'=>Url::build('index/suey/index','','')];
            }
        }
    }
    //提现
    public function deposit(){
        if(Request::instance()->isAjax()){
            return \app\index\model\User::deposit_money(input('post.'));
        }
    }
    public function up_pass(){//修改密码
//        var_dump(Session::get('uid'));
        return view();
    }

    public function loginpass(){
        if (Request::instance()->isAjax()) {
            $input = input('post.');
            $uid = Session::get('uid');
            $rule = [
                'xpassword'=>'require|max:12|min:6',
                'rexpasswo'=>'confirm:xpassword',
            ];
            $message = [
                'xpassword'=>'密码为6-12位任意组成！',
                'rexpasswo'=>'兩次密碼不一致'
            ];
            if($input['password'] == $input['xpassword']){
                return ['status'=>2,'msg'=>'原密码跟新密码不能一致'];
            }
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $list = Db::name('member')->where('id',$uid)->find();
            if(md5_pass(2,$input['password']) !== $list['password']){
                return ['status'=>2,'msg'=>'原登陆密码错误！'];
            }else{
                $resu = Db::name('member')->where('id',$uid)->update(['password' => md5_pass(2,$input['xpassword'])]);
                if($resu){

                    return ['status'=>1,'msg'=>'修改成功！请重新登陆...','url'=>'/index/login'];
                }else{
                    return ['status'=>2,'msg'=>'网络超时！请重试！'];
                }
            }
        }
    }

    public function anquanLs(){
        if (Request::instance()->isAjax()) {
            $input = input('post.');
            $uid = Session::get('uid');
            $rule = [
                'xpassword'=>'require|max:12|min:6',
                'rexpasswo'=>'confirm:xpassword',
            ];
            $message = [
                'xpassword'=>'密码为6-12位任意组成！',
                'rexpasswo'=>'兩次密碼不一致'
            ];
            if($input['password'] == $input['xpassword']){
                return ['status'=>2,'msg'=>'原密码跟新密码不能一致'];
            }
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $list = Db::name('member')->where('id',$uid)->find();
            if(md5_pass(1,$input['password_two']) !== $list['password_two']){
                return ['status'=>2,'msg'=>'原安全密码错误！'];
            }else{
                $resu = Db::name('member')->where('id',$uid)->update(['password_two' => md5_pass(1,$input['xpassword'])]);
                if($resu){
                    return ['status'=>1,'msg'=>'安全密码修改成功！','url'=>'/index/user'];
                }else{
                    return ['status'=>2,'msg'=>'网络超时！请重试！'];
                }
            }
        }
    }

    public  function qrcode_s(){
        /*图片大小
         * 边距
         *
         * */
        header('Content-Type: image/png');
        $uid = Session::get('uid');
        $list = Db::table('web_member')->where('id',$uid)->field('invite_code')->find();
        Loader::import('Org.phpqrcode.qrlib');
        ob_clean();
        \QRcode::png(request()->domain().url('index/register/index',['mid'=>base64_encode($list['invite_code'])]),false,QR_ECLEVEL_H,4,1);
    }

    public function leaving(){//留言

        if(Request::instance()->isAjax()) {
            $uid = Session::get('uid');
            $input = input('post.');
            $total = count(Db::name('liu')->where('uid',$uid)->order('time desc')->select());
            $page = $input['pageIndex'];
            $page = $page-1;
            $start = $page*$input['PageSize'];
            $result = Db::name('liu')->where('uid',$uid)->order('time desc')->limit($start,$input['PageSize'])->select();
            return ['status'=>1,'total'=>$total,'result'=>$result];
        }
        return $this->fetch();
    }

    public function leavingadd(){
        if (Request::instance()->isAjax()) {
            $input = input('post.');
            $uid = Session::get('uid');
            $rule = [
                'title'=>'require|max:10',
                'content'=>'require|max:50',
            ];
            $message = [
                'title'=>'留言标题不能为空并且仅限10字内！',
                'content'=>'留言内容不能为空并且仅限50字内！'
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $input['uid'] = $uid;
            $input['time'] = time();
            $input['res_time'] = strtotime(date('Y-m-d'));
            $res_time = Db::name('liu')->where('uid',$uid)->where('res_time',$input['res_time'])->select();
            if(count($res_time) == 3){
                return ['status'=>3,'msg'=>'每日只能提交三次留言！'];
            }else{
                $list = Db::name('liu')->insert($input);
                if($list){
                    return ['status'=>1,'msg'=>'您的留言已提交！我们将会第一时间回复！'];
                }else{
                    return ['status'=>2,'msg'=>'网络超时'];
                }
            }
        }
    }

}
