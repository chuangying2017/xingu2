<?php

namespace app\admin\model;

use think\Model\Merge;

class Log extends Merge
{
    // 设置主表名
    protected $table = 'web_loginlog';
    // 定义关联模型列表
}
