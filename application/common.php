<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//獲取一個更好的隨機數
function random_str($length)
{
    //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

    $str = '';
    $arr_len = count($arr);
    for ($i = 0; $i < $length; $i++)
    {
        $rand = mt_rand(0, $arr_len-1);
        $str.=$arr[$rand];
    }
    return $str;
}

function md5_pass($type,$data){
    //1表示后台模式
    //2表示前台模式
    //1是安全密码或者提现密码
    //2是登录密码
        if($type == 1){
            $password = md5($data.md5(config('ADMINKEY')));
        }elseif ($type == 2){
            $password = $data.md5(config('USERKEY'));
        }
        return $password;
}

        //检查ip
function check_ip($ip_data, $ip) {
    $ALLOWED_IP = explode(',', $ip_data);

    $check_ip_arr = explode('.', $ip); //要检测的ip拆分成数组
    #限制IP
    if (!in_array($ip, $ALLOWED_IP)) {
        foreach ($ALLOWED_IP as $val) {
            if (strpos($val, '*') !== false) {//发现有*号替代符
                // $arr = array();
                $arr = explode('.', $val);
                $bl = true; //用于记录循环检测中是否有匹配成功的
                for ($i = 0; $i < 4; $i++) {
                    if ($arr[$i] != '*') {//不等于*  就要进来检测，如果为*符号替代符就不检查
                        if ($arr[$i] != $check_ip_arr[$i]) {
                            $bl = false;
                            break; //终止检查本个ip 继续检查下一个ip
                        }
                    }
                }//end for
                if ($bl) {//如果是true则找到有一个匹配成功的就返回
                    return true;
                    die;
                }
            }
        }//end foreach
        return false;
        die;
    }
}

//获取配置数据库
function database($type){
           $data = \think\Db::name('webconfig')->find($type);
           $data = json_decode($data['value'],true);
            return $data;
}
//获取参数设置
function setparma($num){
    $array= \think\Db::name('webconfig')->find($num);
    return json_decode($array['value'],true);
}
//设置币种类型
function bin_type_s(){
    return array(1=>'美元',2=>'积分',3=>'奖金');
}
//充值类型
function chongzhitype(){
    return array(1=>'手动充值',2=>'第三方充值',3=>'手动扣除');
}
function getChongzhiBonusStatus() {
    $data = array('1' => '充值成功', '2' => '等待支付', '0' => '充值失败', '3' => '取消支付',4=>'扣除成功');
    return $data;
}

function search_status(){
       return array('1'=>'待支付','2'=>'已支付','3'=>'已返利');
}

function searchs_status(){
    return array('1'=>'已支付','2'=>'待支付');
}


//Execl方法调用
function exportExcel($expTitle, $expCellName, $expTableData) {
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); //文件名称
    $fileName = date('EXcel_YmdHis'); //or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
   // \think\Loader::import('Org.ex.PHPExcel',EXTEND_PATH,'.class.php');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Sam.c")
        ->setLastModifiedBy("Sam.c Test")
        ->setTitle("Microsoft Office Excel Document")
        ->setSubject("Test")
        ->setDescription("Test")
        ->setKeywords("Test")
        ->setCategory("Test result file");
    $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

//    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 2] . '1'); //合并单元格
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '*批次号');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '*付款日期');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '*批总金额');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '*批总笔数');
    $objPHPExcel->getActiveSheet()->setCellValue('A2',"");
    $objPHPExcel->getActiveSheet()->setCellValue('B2', ''.date('Ymd').'');
    $objPHPExcel->getActiveSheet()->setCellValue('C2', '');
    $objPHPExcel->getActiveSheet()->setCellValue('D2', '');






    for ($i = 0; $i < $cellNum; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '3', $expCellName[$i][1]);
    }
    // Miscellaneous glyphs, UTF-8
    for ($i = 0; $i < $dataNum; $i++) {
        for ($j = 0; $j < $cellNum; $j++) {

            //  $objPHPExcel->getActiveSheet(0)->setCellValueExplicit($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]], PHPExcel_Cell_DataType::TYPE_STRING);
            //  $objPHPExcel->getActiveSheet(0)->getStyle($cellName[$j] . ($i + 3))->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 4), $expTableData[$i][$expCellName[$j][0]])->setCellValueExplicit($cellName[$j] . ($i + 4), $expTableData[$i][$expCellName[$j][0]], PHPExcel_Cell_DataType::TYPE_STRING);
        }
    }
    ob_clean();
    header('pragma:public');
    header('Content-type:applicationnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls"); //attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output'); //文件通过浏览器下载
    exit;
}


    /**
     * 发送邮件方法
     * @param string $to：接收者邮箱地址
     * @param string $title：邮件的标题
     * @param string $content：邮件内容
     * @return boolean  true:发送成功 false:发送失败
     */
    function sendMail($to,$title,$content){

         date("H:i:s");
        //实例化PHPMailer核心类
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = 1;
        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth=true;
        //链接qq域名邮箱的服务器地址
        $mail->Host = 'smtp.163.com';
        //设置使用ssl加密方式登录鉴权
//        $mail->SMTPSecure = 'ssl';
        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->Port = 25;
        //设置smtp的helo消息头 这个可有可无 内容任意
        $mail->Helo = 'Hello smtp.qiye.163.com server';
        //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        $mail->Hostname = 'localhost';
        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->CharSet = 'UTF-8';
        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = 'srovern';
        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username ='13059551109@163.com';
        //smtp登录的密码 使用生成的授权码 你的最新的授权码
        $mail->Password = 'cy205805';
        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = '13059551109@163.com';
        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);
        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        $mail->addAddress($to,'测试通知');
        //添加多个收件人 则多次调用方法即可
        // $mail->addAddress('xxx@qq.com','lsgo在线通知');
        //添加该邮件的主题
        $mail->Subject = $title;
        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        $mail->Body = $content;

        //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
        // $mail->addAttachment('./d.jpg','mm.jpg');
        //同样该方法可以多次调用 上传多个附件
        // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');

        $status = $mail->send();

        //简单的判断与提示信息
        if($status) {
            return true;
        }else{
            return false;
        }
    }

/**
 * 系统邮件发送函数
 * @param string $tomail 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 * @author static7 <static7@qq.com>
 */

function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null) {
    $database=database(1);
    if(!$database['smsservice']){
        die('请填写服务地址');
    }
    $mail = new PHPMailer\PHPMailer\PHPMailer();        //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                    // 设定使用SMTP服务
    $mail->SMTPDebug = 1;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';          // 使用安全协议
    $mail->Host = $database['smsservice']; // SMTP 服务器
    $mail->Port = 465;                  // SMTP服务器的端口号
    $mail->Username = $database['smsusername'];    // SMTP服务器用户名
    $mail->Password = $database['smspassword'];     // SMTP服务器密码
    $mail->SetFrom($database['smsusername'], 'static7');
    $replyEmail = '';                   //留空则为发件人EMAIL
    $replyName = '';                    //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($tomail, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

