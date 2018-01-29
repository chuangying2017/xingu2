<?php
namespace app\index\controller;
use Org\pay\Pay_lagou;
use think\Controller;
use think\Loader;
use think\Request;
use think\Session;
use think\Validate;
use think\captcha\Captcha;
use think\Db;
class Suey extends \app\common\controller\Common
{
    public function index()
    {//添加银行卡
        $bank = Db::name('bank')->where('status',1)->field('id as value,bankcrad as text')->select();
//        dump($bank);
        $list = json_encode($bank);
//        dump($list);
        $this -> assign('list',$list);
        return $this->fetch();
    }

    public function Unbundling(){//解绑银行卡

        if (Request::instance()->isAjax()) {
            $input = input('post.');
            $uid = Session::get('uid');
            $re = Db::name('member')->where('id',$uid)->find();
            if(md5_pass(1,$input['password_two']) != $re['password_two']){
                return ['status'=>2,'msg'=>'安全密码错误！请重试'];
            }
            $list = Db::table('web_mbank')->where('id',$input['id'])->delete();
            if($list>0){
                return ['status'=>1,'msg'=>'成功'];
            }else{
                return ['status'=>2,'msg'=>'网络超时！请重试'];
            }

        }
    }
    //支付验证码页面
    public function zhifu_yanzheng(){
        return view('zhifu');
    }
	//拉钩快捷支付
    public function shortcut_pay(){
       $route_parameter=\request()->route();
       if($route_parameter['order_data']){
        $this->assign('route_parameter',json_decode(base64_decode($route_parameter['order_data']),true));
       }else{
           $this->error('页面不存在','index/index/index');
       }
        return view();
    }
	    public  function qrcode_s(){
        /*图片大小
         * 边距
         *
         * */
        header('Content-Type: image/png');
        Loader::import('Org.phpqrcode.qrlib');
        ob_clean();
        \QRcode::png(Session::get('zhifu_code'),false,QR_ECLEVEL_H,4,1);
    }
	

    public function withdrawals(){//提现记录
        if(Request::instance()->isAjax()) {
            $uid = Session::get('uid');
            $input = input('post.');
            $total = count(Db::name('deposit')->where('uid',$uid)->order('create_date desc')->select());
            $page = $input['pageIndex'];
            $page = $page-1;
            $start = $page*$input['PageSize'];
            $result = Db::name('deposit')->where('uid',$uid)->order('create_date desc')->limit($start,$input['PageSize'])->select();
			if(count($result)>0){
                for($i = 0;$i < count($result);$i++){
                    $result[$i]['create_date'] = date('Y-m-d H:i:s',  $result[$i]['create_date']);
                }
            }
            return ['status'=>1,'total'=>$total,'result'=>$result];
        }
        return $this->fetch();
    }

    public function team(){//我的团队
        if(\request()->isAjax()){
            $input = input('post.');
            $input['uid'] = Session::get('uid');
            return \app\index\logic\User::iteration($input);
        }
        return view();
    }
    //推荐人异步数据
    public function recommend_data_ajax(){
        if(\request()->isAjax()){
            $member = Db::name('member')->where('recommend',input('post.dd'))->select();
            if(!empty($member)){
                return ['status'=>1,'data'=>$member,'count'=>count($member)];
            }else{
                return ['status'=>2,'msg'=>'暂无数据'];
            }
        }
    }
    public function share(){//推广管理
        $list = Db::name('member')->where('id',Session::get('uid'))->field('invite_code')->find();
        $this->assign('tui',request()->domain().url('index/register/index',['mid'=>base64_encode($list['invite_code'])]));
        return view();
    }

    public function bank(){//展示银行卡
        $uid = Session::get('uid');
        $list = Db::name('mbank')->where('uid',$uid)->select();
        for($i=0;$i<count($list);$i++){
			$numls = substr($list[$i]['bank_user'],0,4);
            $list[$i]['bank_user'] = $numls.' **** **** '.substr($list[$i]['bank_user'],-4);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function bankyl(){//添加银行卡
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $rule = [
                'name'=>'require',
                'bank_user'=>['regex'=>'/^\d{15,21}$/'],
                'bank'=>'require',
                'zhihang'=>'require',
                'bankShen'=> ['regex'=>'/^([\d]{17}[xX\d]|[\d]{15})$/'],
                'bankcode'=>'require|min:6|max:12'
            ];
            $message = [
                'name'=>'开户姓名不能为空',
                'bank'=>'开户银行必填信息',
                'zhihang'=>'开户支行不能为空',
                'bank_user'=>'银行卡号不合法，请核对',
                'bankShen'=> '请输入正确身份证',
                'bankcode.require'=>'不能为空','bankcode.min'=>'最小6位数','bankcode.max'=>'最大12位数'
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $uid = Session::get('uid');
            $DB = Db::name('member')->where('id',$uid)->find();
            if($DB['password_two'] != md5_pass(1,$input['bankcode'])){
                return ['status'=>2,'msg'=>'安全密码错误!'];
            }
            $phone = $DB['mobile'];
				$liks = Db::name('mbank')->where('crad',$input['bankShen'])->where('uid','<>',$uid)->select();
                if(count($liks) > 0){
                    return ['status'=>2,'msg'=>'此身份证已绑定其他会员卡号！'];
                }
                $data['uid'] = $uid;
                $data['name'] = $input['name'];
                $data['bank'] = $input['bank'];
                $data['zhihang'] = $input['zhihang'];
                $data['bank_user'] = $input['bank_user'];
                $data['create_time'] = time();
                $data['bank_id'] = $input['bankId'];
				$data['crad'] = $input['bankShen'];
                $call_back=Db::table('web_mbank')->strict(true)->insertGetId($data);
                if($call_back){
                    unlink('./banktext/'.$phone.'.txt');
                    return ['status'=>1,'msg'=>'添加银行卡成功'];
                }else{
                    return ['status'=>2,'msg'=>'网络超时！请重试'];
                }
        }
    }

    public function xin(){//新闻公告
        if(Request::instance()->isAjax()) {
            $input = input('post.');
            $total = count(Db::name('article')->order('art_time desc')->select());
            $page = $input['pageIndex'];
            $page = $page-1;
            $start = $page*$input['PageSize'];
            $result = Db::name('article')->order('art_time desc')->limit($start,$input['PageSize'])->select();
            for($i=0;$i<count($result);$i++){
                $result[$i]['art_time'] = date('Y-m-d',  $result[$i]['art_time'] );
            }
            return ['status'=>1,'total'=>$total,'result'=>$result];
        }
        return $this->fetch();
    }

    public  function news(){//新闻内容
        if (Request::instance()->isGet()){
            $input = input('get.');
            $list = Db::name('article')->where('id',$input['id'])->find();
            $this -> assign('list',$list);
            return $this->fetch();
        }
    }


    public function htr(){
        $uid = Session::get('uid');
        $DB = Db::name('member')->where('id',$uid)->find();
        $phone = $DB['mobile'];
        $randStr = str_shuffle('1234567890');
        $code = substr($randStr,0,6);
        $msg="您的短信验证码为：".$code;
        $result = $this -> NewSms($phone,$msg);
        $resul = explode('/',$result);
        if($resul[0] == 000){
            file_put_contents('./banktext/'.$phone.'.txt',$code);
            return json_encode(['status'=>1,'msg'=>'发送成功，请注意查收!']);
        }else{
            return json_encode(['status'=>2,'msg'=>'网络超时']);
        }
    }


    private function NewSms($phone,$msg)
    {
        $url="http://service.winic.org:8009/sys_port/gateway/index.asp?";
        $data = "id=%s&pwd=%s&to=%s&content=%s&time=";
        $id = 'zhl168';
        $pwd = '2058505';
        $to = $phone;
        $content = iconv("UTF-8","GB2312",$msg);
        $rdata = sprintf($data, $id, $pwd, $to, $content);



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = substr($result,0,3);
        return $result;
    }

}
