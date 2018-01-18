<?php
namespace Org\pay;
class Pay_lagou{

    protected $config = [];
    protected $url = "http://lagoupay.cn/Pay_Index.html";
    protected $md5Key = 'lkoptu9mejnr8g3666ce9oo8qftd3w2b';//商户密钥
   final public function __construct($price = null,$id,$cashier= null)
   {
       $native = array(
           "pay_memberid" => '10011',// 商户ID
           "pay_orderid" => 'E'.date("YmdHis").rand(100000,999999),// 订单号
           "pay_amount" => is_null($price)?1:$price,// 交易金额
           "pay_applydate" => date('Y-m-d H:i:s'),// 交易时间
           "pay_bankcode" => '911',//银行编码
           "pay_notifyurl" => 'http://www.bvrka.com/admin/captcha/lagou_pay',//回调地址
           "pay_callbackurl" => 'http://www.bvrka.com/index/record/inscl.html',//页面跳转规则
       );
       ksort($native);
       $md5str = "";
       foreach ($native as $key => $val) {
           $md5str = $md5str . $key . "=" . $val . "&";
       }
       $sign = strtoupper(md5($md5str . "key=" . $this->md5Key));
       $native["pay_md5sign"] = $sign;
       $native['pay_attach'] = $id?:"1234|456";
       //$native['pay_productname'] = $cashier;
       $this->config = $native;
   }
    //返回所有的属性
   public function index(){
       return $this->config;
   }
}