<?php

return [
    '__file__'=>['common.php'],

    //自定义一个官网的模块
    'index'=>[
        '__dir__'=>['behavior','validate','controller','model','view'],
        'controller'=>['Index','Login'],
        'validate'=>['Products','User','Orderno','Recharge','Deposit','Login'],
        'model'=>['User','Products','Orderno','Recharge','Deposit','Login'],
        'view'=>['Login/index','Index/index']
    ]
];