<?php
namespace app\Admin\validate;
use think\Validate;
class Role extends Validate{
    //验证规则
    protected $rule=[
        'name'=>'require',
        'control_action'=>'control_action'
    ];
}