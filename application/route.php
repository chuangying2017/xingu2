<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//这里主要定义前台路由，后台路由请自定义个admin.php
use think\Route;
Route::get('Product_buy/[:type]','index/goods/buy_product',[],['type'=>'\w+']);//购买产品
Route::group('Index',function(){//主要针对单个控制器分组
    //因为控制器方法不多就针对整个模块吧
    Route::get('/show/','index/Index/index');//显示首页的
    Route::get('purchase/','index/goods/purchase');//展示购买页面
});
return [
    'login_show'=>['index/Login/index',['method'=>'get']],//登录页面
];