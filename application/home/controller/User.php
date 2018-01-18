<?php

namespace app\home\controller;

use think\Controller;

class User extends Controller
{
    //个人中心
    public function index(){
        return view();
    }
    //展示新闻
    public function journalism(){
        return view();
    }
    //提款页面
    public  function draw(){
        return view();
    }
    //银行卡展示页面
    public function bank_show(){
        return view();
    }
}
