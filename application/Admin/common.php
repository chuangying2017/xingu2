<?php


function getSystemInfo() {
    $systemInfo = array();

    // 系统
    $systemInfo['os'] = PHP_OS;

    // PHP版本
    $systemInfo['phpversion'] = PHP_VERSION;

    // Apache版本
    // $systemInfo['apacheversion'] = apache_get_version();
    // ZEND版本
    $systemInfo['zendversion'] = zend_version();

    // GD相关
    if (function_exists('gd_info')) {
        $gdInfo = gd_info();
        $systemInfo['gdsupport'] = true;
        $systemInfo['gdversion'] = $gdInfo['GD Version'];
    } else {
        $systemInfo['gdsupport'] = false;
        $systemInfo['gdversion'] = '';
    }
    //现在的时间
    $systemInfo['nowtime'] = date('Y-m-d H:i:s', time());
    //客户端ip
    $systemInfo['remote_addr'] = getenv('REMOTE_ADDR');
    //服务器端
    $systemInfo['server_name'] = gethostbyname($_SERVER["SERVER_NAME"]);
    // 安全模式
    $systemInfo['safemode'] = ini_get('safe_mode');

    // 注册全局变量
    $systemInfo['registerglobals'] = ini_get('register_globals');

    // 开启魔术引用
    $systemInfo['magicquotes'] = (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc());

    // 最大上传文件大小
    $systemInfo['maxuploadfile'] = ini_get('upload_max_filesize');

    // 脚本运行占用最大内存
    $systemInfo['memorylimit'] = get_cfg_var("memory_limit") ? get_cfg_var("memory_limit") : '-';

    return $systemInfo;
    }
function webconfig(){
    $webconfig =\think\Db::name('webconfig')->find(1);
    return json_decode($webconfig['value'],true);
}

function tokencheck($type,$token){
        if(session('__token__')!=$token){
            return false;
        }else{
            session('__token__',null);
            return true;
        }
}

//充值本金
function benjin(){
    return $arr=array('1'=>'现金','2'=>'奖金');
}
/* 生成订单号 */
function build_order_no() {
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}
\think\Loader::addNamespaceAlias('lg','app\admin\model');