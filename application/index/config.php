<?php

return [
    // 接入ID
    'aid' => "617033015310",

    // 商户号(通过登陆 或 登陆2接口获得)
    'merchant_id' => "617071705453761",

    // 商户密钥
    'key' => "G7nwBoWlSrAelySqPvGFcN0VRIzwTLNvBxeGFKkFXbO6Ed0QHBRMtkvBQH16wQIi",

    // 一级代理商ID
    'agent_id' => "495687",

    //回调地址
    'notify_url' => 'http://www.bvrka.com/admin/Captcha/baibao_return',

    //// 交易类型，0:T0, 1:T1
    'trade_type'=>'1',

    //扫码支付地址
    'scan_Qr_code_url'=>'http://dd.o2obaibao.com/bsystem/index.php?g=Api&m=Pays&a=qrpay'
];