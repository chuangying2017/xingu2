<?php
namespace app\admin\validate;

use think\Validate;
class Member extends Validate{

    protected $rule = [
        'id'=>'require',
        'newpassword'=>'require|min:6',
        'repassword'=>'require|min:6',
        'repassword'=>'require|confirm:newpassword',
        'mobile'=>['regex'=>'^1(3|4|5|7|8)\d{9}$'],
        'name'=>'require|min:1',
        'level'=>'require',
        'identity_card'=>'require|min:12'
        ,'type'=>'require','income'=>'require|float','username'=>'require|min:6','message'=>'require','username'=>'require'
    ];

    protected  $message=[
        'type.require'=>'类型不能为空','income.require'=>'金额不为空','username.require'=>'账号必须',
        'message'=>'信息不能为空',
        'id.require'=>'id不能空',
        'newpassword.min'=>'密码长度不够',
        'repassword.min'=>'第二次密码不够长',
        'repassword.confirm'=>'两次密码不一致',
        'level.require'=>'等级不能为空',
        'username.require'=>'账号不能为空'
    ];

}