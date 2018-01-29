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
error_reporting(E_ERROR | E_PARSE );
return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              =>true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------
    //'deny_ext' => '*',
    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html|xml',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '<{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}>',
        // 标签库标签开始标记
        'taglib_begin' => '<',
        // 标签库标签结束标记
        'taglib_end'   => '>',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__PUBLIC__'=>'/',
        '__UPLOADS__'=>'/upload/images'
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => APP_PATH . 'tpl' . DS . 'tpl.html',
    'dispatch_error_tmpl'    => APP_PATH . 'tpl' . DS . 'tpl.html',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    'http_exception_template'    =>  [
        // 定义404错误的重定向页面地址
    404 =>  APP_PATH.'admin'.DS.'view'.DS.'common'.DS.'error.html',
    // 还可以定义其它的HTTP status
    401 =>  APP_PATH.'401.html',
],


    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    // 开启应用Trace调试
    'app_trace' =>  true,
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],
    'captcha'  => [
        // 字体大小
        'fontSize' => 15,
        'useNoise'=>false,
        // 验证码长度（位数）
        'length'   => 4,
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
	
	    'callbackUrl'=>'http://www.mall700.com/admin/Captcha/baibao_return',//支付回调地址
		'success_callback_url'=>'http://www.mall700.com/index/record/inscl',
		'secretKey'=>'06b797bc3c0864faba33a4cb26d0baa1',
		'request_pay_url'=>'http://jypay-center.join51.com/v1.0/payOrder/prepay/',
	
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    // +----------------------------------------------------------------------
    // | 表单令牌配置
    // +----------------------------------------------------------------------
    'token'     => [
        'token_on'    => true,
        'token_name'  => '__hash__',
        'token_type'  => 'md5',
        'token_reset' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    'USERKEY'=>'ddd30+3ewxdsL:"L432424$$%6523$#d11',
    'ADMINKEY'=>'DD@@1!DDDDsf3$@#45DDDDDD',
    'return_url'=>'http://www.13173428298.com/chenggonghoutongzhi',//成功后地址
    'notify_url_address'=>'http://www.mall700.com/admin/Captcha/baibao_return',//回调地址
    'aid' => "617033015310",// 接入ID
    'merchant_id' => "617101302503963",// 商户号(通过登陆 或 登陆2接口获得)
    'merchant_key' => "G7nwBoWlSrAelySqPvGFcN0VRIzwTLNvBxeGFKkFXbO6Ed0QHBRMtkvBQH16wQIi",// 商户密钥
    'agent_id' => "495687",// 一级代理商ID
    /**
    1）merchant_private_key，商户私钥;merchant_public_key,商户公钥；商户需要按照《密钥对获取工具说明》操作并获取商户私钥，商户公钥。
    2）demo提供的merchant_private_key、merchant_public_key是测试商户号1118004517的商户私钥和商户公钥，请商家自行获取并且替换；
    3）使用商户私钥加密时需要调用到openssl_sign函数,需要在php_ini文件里打开php_openssl插件
    4）php的商户私钥在格式上要求换行，如下所示
     */
    'MERCHANT_PRIVATE_KEY'=>'-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAKcY6llbgPjOtxNR
R8PtihtR6B5RgG10J28HmP9YMfxe9N7Oytwk8BhWOwlItdob4YCqfhATbCHRwjrl
tCZMRkfDqKntHvejOsTKRox2S0WRGrl7q7eMYfKiq4wLHWd/Zdur7WyG/28UGUTF
Wr78EzMNk4Ov04m4RuhoLxa04haFAgMBAAECgYA7F0yInRtW4YNMiYnfd7lHQAfd
8OSB0HnBgeZRJldG8C2YPXjvsQBXGud1uQjWQNXWMnSyKqwqqYsOWP0ELiUL2ZSk
HtnZnELuC1JGhszCWy2hTOXP81ezgux22URsibDBwEuKY7Plj2gVPt7YVwNFkvim
UTMyMvGTb4Ztb5g4YQJBAM+4+qdhyfxZSgnyDv/9g2klYlB7ZF1ShoGOwlIhmVOP
8NW+bC+/F4bWfVp3jNo9/kK79K8tAItBdiH93oS8lk8CQQDN7tONg//mA2MWkq4j
/10nKcf8XSl2MyKqyffgGBts0MEG/gQmI1f1B7LASx/dIa5+VejaBG7GS/s7NtH4
DyTrAkEAsaTBf8oKgmwd/HltSJGXA6H7/VY5U/ISo9Ph7XlcdfEghrsuLHsg9KiU
VXzuEWp4+rthGzrCP1WBYLqKxTu+0wJALuxNTgCWzX1WFa2kelVUj3jotaswqFss
egf9MBWuIhRK92Hn5hzFjPKNG13Cy+tBzE2c+hhqeUqU20A5hbFFswJALW7H0RHV
y4CHF8wfpkPHO3aCW+9xRgKXDjOY42NhVj+QWalciu4xcrjvXGA+agFZlIl/99ea
wCKThZiw/u1gLw==
-----END PRIVATE KEY-----',
//merchant_public_key,商户公钥，按照说明文档上传此密钥到智付商家后台，位置为"支付设置"->"公钥管理"->"设置商户公钥"，代码中不使用到此变量
    'MERCHANT_PUBLIC_KEY'=>'-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnGOpZW4D4zrcTUUfD7YobUege
UYBtdCdvB5j/WDH8XvTezsrcJPAYVjsJSLXaG+GAqn4QE2wh0cI65bQmTEZHw6ip
7R73ozrEykaMdktFkRq5e6u3jGHyoquMCx1nf2Xbq+1shv9vFBlExVq+/BMzDZOD
r9OJuEboaC8WtOIWhQIDAQAB
-----END PUBLIC KEY-----
',
    /**
    1)dinpay_public_key，智汇付公钥，每个商家对应一个固定的智汇付公钥（不是使用工具生成的密钥merchant_public_key，不要混淆），
    即为智付商家后台"公钥管理"->"智汇付公钥"里的绿色字符串内容,复制出来之后调成4行（换行位置任意，前面三行对齐），
    并加上注释"-----BEGIN PUBLIC KEY-----"和"-----END PUBLIC KEY-----"
    2)demo提供的dinpay_public_key是测试商户号1118004517的智付公钥，请自行复制对应商户号的智付公钥进行调整和替换。
    3）使用智付公钥验证时需要调用openssl_verify函数进行验证,需要在php_ini文件里打开php_openssl插件
     */
    'DINPAY_PUBLIC_KEY'=>'-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GN
ADCBiQKBgQCkfxhJcPhUeDCrgFb8
u3cDbzweUVr9CNJxTXYeCwifv7Cn
dCwy8IWoLPTrk7bL7jLATBJrgxTw
DTo7oPolsq17pV45qCQjxIQimoD0CrNTQ6xh3yf7Y4lMIOBTu4ExbTtl4xJzHb5tHOcd1hlMshIKZ0t9JCWP3fDxmJtId7PXvwIDAQAB 
-----END PUBLIC KEY-----'
];
