<?php
namespace app\Admin\validate;
use think\Validate;
class Products extends Validate{

    protected $rule = [
        'title'=>'require',
        'feature'=>'require',
        'class_id'=>'require',
        'price'=>'require|float|min:2',
        'volume'=>'require|number',
        'create_date'=>'require|date',
        'content'=>'require|min:3',

    ];
    protected $message=[
        'title.require'=>'标题不能为空',
        'feature.require'=>'功效不能为空',
        'class_id'=>'类别不能为空',
        'price'=>'套价不能为空',
        'volume'=>'购买量不能为空',
        'create_date'=>'发布日期不能为空',
        'content.require'=>'发布内容不能为空',

    ];
    protected $scene=[
        'add'=>['title','feature','class_id','price','volume','create_date','content']
        ,'edit'=>['title','feature','class_id','price','create_date']
    ];

}