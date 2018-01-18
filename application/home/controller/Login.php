<?php
namespace app\home\controller;

use think\Request;
use think\Validate;

class Login
{
        public  function  index(){
            return view();
        }
        //验证登录
        public function login_ajax(){
            if(Request::instance()->isAjax()){
            return \app\home\model\Login::login_verify(input('post.'));
            }
        }
        //注册
        public function register(){
            if(Request::instance()->isAjax()){
                return \app\home\model\Login::register(input('post.'));
            }else{
                return view();
            }
        }
        public function retrieval(){
            return view();
        }
}