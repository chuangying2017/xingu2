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

use think\Route;
Route::rule('shuang','admin/Login/shuang');
Route::rule('xiaoming','admin/index/hello');
Route::any('username','index/user/index');
Route::any('admin/login','admin/login/login');
Route::any('admin/index','admin/index/index');
Route::rule('admin/logout','admin/index/logout');
Route::get('coluds','index/Index/index');
Route::rule('shuhu','admin/product/shuhu');
Route::post('product/product_class_adds','admin/product/product_class_adds');
Route::rule([
    'user/red'=>'user/read'
]);

Route::group('settlement',function (){
    Route::rule(':pid/:num','home/settlement/index',[],['pid'=>'\d+','num'=>'\d+']);
    Route::miss('home/settlement/index');
},['method'=>'get|post','ext'=>'html']);
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    'welcome'=>[
        'Admin/Index/welcome', ['method' => 'get']
    ],
    'registers'=>['home/register/index',['method'=>'get|post']],
    'hellod/culture'=>'hellod/index/culture',
    'hellod/xieyi'=>'hellod/index/xieyi',
    'hellod/activity'=>'hellod/index/activity',
    'hellod/membersystem'=>'hellod/index/membersystem',
    'hellod/relation'=>'hellod/index/relation',
    'product/product_add_article'=>['admin/product/product_add_article',['method'=>'post']],
    'product_edit/:id'=>['admin/product/product_edit',['method'=>'get|post'],['id'=>'\d+']],
    'productimg/[:id]'=>['admin/product/productimg?status=2&name=zhangsan',['method'=>'get'],['id'=>'\d+']],
    'productaddimg/:id'=>['admin/product/productaddimg?img=jpeg&name=kamen','GET|POST',['id'=>'\d+']],
    'qiantai'=>'Home/index/index',
    'gopowers/:id'=>['admin/Gopower/power_show',['method'=>'get|post'],['id'=>'\d+']],
    'login_log'=>['index/login/index',['method'=>'get|post']],
    'img_content/:id'=>['admin/product/product_content','POST',['id'=>'\d+']],
    'img_imgcontent/:id'=>['admin/product/product_conten','POST',['id'=>'\d+']],
    'fanhui'=>['home/commodity/fanhui','POST'],
    'commoditypurcheast/:id/:cover_num/:price'=>['home/commodity/purchase',['method'=>'get']],
    'security_ss'=>['home/user/security','GET|POST'],
    'commodity_product'=>['home/commodity/product_buy','POST'],
    'shangjhiafneon'=>['home/user/shenqing','POST'],
    'zhan/:id/:uid$'=>['home/back/kuaidu',['method'=>'post']],
    'appy_shangvvv'=>['admin/member/appy_shang','POST'],
    'dingshizhixing'=>['admin/member/member_func'],
    'qingqiuaddress/:status'=>['home/register/request_address','GET|POST'],
    'emailceshi'=>'home/register/return_ceshi',
    'hernoneronhro'=>['home/finance/tixian_s','POST'],
    'Transformationrngreg'=>['home/finance/simpleness','POST'],
    'shenqingtuihuo/:id/:status'=>['home/orders/tuohuo','POST',['id'=>'\d+','status'=>'\d+']],
    'chenggonghuidiao'=>'home/finance/call_backdata',
    'zhifuchanpin/:order/:rmb'=>['home/finance/zhifu','POST'],
    'zhifuyemianhelish'=>['home/finance/verifys','POST'],
    'chenggonghoutongzhi'=>'home/finance/tongzhi',
    'vfnronoreng'=>['home/classification/index','GET|POST'],
    'zhunbeilaiduan/[:mid]'=>['index/register/index'],
    'zidongdenglu/:m'=>['index/Login/back_login'],
    'tianbfgiebfge/[:bank]'=>['admin/report/xiazai'],
    'Verification'=>['/index/Login/Verification']
];
