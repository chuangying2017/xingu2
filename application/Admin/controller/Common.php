<?php

namespace app\Admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Common extends Controller
{
    //thinkphp5.0自定义的构造方法
    public function _initialize(){
     session::set('user_id',1);
          $this->check_login();
          $this->verify();
          $this->errorlogin();
          $database=database(1);
          $request = Request::instance();
          $ip = $request->ip();
          if(!empty($database['ip'])){
                if(!check_ip($database['ip'],$ip)){
                    $this->error();
                }
          }
        if(session::get('user_id')>0){
            if($database['chaoshi'] == 1){
                $this->checkAdminSession();
            }
            $userinfo=$this->userinfo(session::get('user_id'));
            if($userinfo['id'] == 1){
                $role['rolename']='无敌管理员';
            }else{
                $role = Db::name('role')->where('id',session::get('groupid'))->find();
            }
            $this->assign('userinfo',$userinfo);
            $this->assign('role',$role);
             $this->assign('config',$database);
        }
    }
            //检查有没有登录
    public final function check_login(){
                    $request = Request::instance();
                    if(empty(session::get('user_id'))){
                    $this->redirect('admin/login/login');
                     }
                    if($request->controller() == 'Index'){
                        return true;
                    }
        }

    public function  verify(){
            $request = Request::instance();
            $control_action = $request->controller().'/'.$request->action();
            $role = db('role')->where('id',session::get('groupid'))->find();
            $role = explode(',',$role['power_control_action']);
            $allower = array(
                'Index/index',
                'Index/welcome',
                'Login/login',
                'Index/logout'
            );
            $contro = array_merge($allower,$role);
            if(in_array($control_action,$contro) || session::get('user_id') == 1){
                    return true;
            }else{
                exit('<div style="position: absolute;top:40%;text-align:center;width:95%;color:red;font-size: 24px;">权限不足，无法操作//</div>');
            }
    }

    //重置一个登录错误次数
    public function errorlogin(){
        $name = Db::name('admin')->where('errorlognum','neq','0')->field('id,errorlognum,errorlogtime')->select();
        $count = empty($name)?null:count($name);
        for ($i=0;$i<$count;$i++){
            Db::name('admin')->where('id',$name[$i]['id'])->update(array('errorlognum'=>0,'errorlogtime'=>0));
        }
    }

            //获取后台账号的一条数据
    public  function userinfo($uid) {
        $userinfo = \think\Db::name('admin')->find($uid);
        return $userinfo;
    }

    //登录超时验证
    function checkAdminSession() {
        //设置超时为20分
        $nowtime = time();
        $s_time = Session::get('timeout');
        if (($nowtime - $s_time) > 60 * 60 * 2) {
            unset($_SESSION['timeout']);
            unset($_SESSION['user_id']);
            $this->error('登录超时，请重新登录', \think\Url::build('Admin/Login/login'));
            exit;
        } else {
            Session::set('timeout',$nowtime);
        }
    }

}
