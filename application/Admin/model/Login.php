<?php

namespace app\Admin\model;

use think\Model;

class login extends Model
{
    protected $table = 'web_admin';
    // birthday读取器
    protected function getBirthdayAttr($birthday)
    {
        return date('Y-m-d', $birthday);
    }
}
