<?php
/* 生成订单号 */
function build_order_no() {
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 9);
}

function encrypt($string, $operation, $key = '') {
    $key = md5($key);
    $key_length = strlen($key);
    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result.=chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'D') {
        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } else {
            return'';
        }
    } else {
        return str_replace('=', '', base64_encode($result));
    }
}

//百宝支付
function baibao_pay_interface($array){
    $rand_array = [0.01,0.02,0.03];
    $arra['aid'] = config('aid');// 接入ID
    $arra['merchant_id'] = config('merchant_id'); // // 商户编号
    $arra['trade_type'] = config('trade_type');// 交易类型，0:T0, 1:T1
    $arra['pay_money'] = $array['price']-$rand_array[array_rand($rand_array,1)];//支付金额
    $arra['notify_url'] = config('notify_url');//回调地址
    $arra['mach_order_id']=$array['mach_order_id'];//订单id
    $arra['pay_type'] = $array['pay_type'];//支付类型
    $arra['cashier'] = $array['cashier'];
    $service = new Org\service\Service();
    $arra['sign'] = $service->getSign($arra,config('key'));
    $res = $service->httpPost(config('scan_Qr_code_url'),$arra);
    return json_decode($res,true);
}