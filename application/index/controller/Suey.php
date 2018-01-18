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
        $uid  = Session::get('uid');
        $memberr = Db::name('member');
        $result=$memberr->where('recommend',$uid)->where('status',1)->select();
        for ($i=0;$i<count($result);$i++){
                $select_one=$memberr->where('recommend',$result[$i]['id'])->where('status',1)->select();
                for($j=0;$j<count($select_one);$j++){
                        $select_two=$memberr->where('recommend',$select_one[$j]['id'])->where('status',1)->select();
                }
        }
        $countls = Db::name('member')->where('recommend',$uid)->select();//一代人数
        $countlsnum = count($countls);//一代人数
        $ernum = 0;
        $twonum = 0;
        $li = array();
        $lis = array();
        for($i = 0;$i < $countlsnum;$i++){
            $list =Db::name('member')->where('recommend',$countls[$i]['id'])->select();
            $ernum += count($list);//二代人数
            for($ii = 0;$ii < $ernum;$ii++){
                if(empty($list[$ii])){

                }else{
                    $li[] = $list[$ii];
                }
            }
            for($k = 0;$k < $ernum;$k++){
                $yu = Db::name('member')->where('recommend',$list[$k]['id'])->select();
                $twonum += count($yu);//三代人数
                for($kk = 0;$kk < $twonum;$kk++){
                    if(empty($yu[$kk])){

                    }else{
                        $lis[] = $yu[$kk];
                    }
                }
            }
        }
        $this -> assign('twonum',$li);
        $this -> assign('threess',$lis);
        $this -> assign('one',$countlsnum);
        $this -> assign('two',$ernum);
        $this -> assign('three',$twonum);
        $this->assign('select_one',$select_one);
        $this->assign('select_two',$select_two);
        return view('',['list'=>$result]);
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
//        dump($list);
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
            ];
            $message = [
                'name'=>'开户姓名不能为空',
                'bank'=>'开户银行必填信息',
                'zhihang'=>'开户支行不能为空',
                'bank_user'=>'银行卡号不合法，请核对',
                'bankShen'=> '请输入正确身份证',
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $uid = Session::get('uid');
            $DB = Db::name('member')->where('id',$uid)->find();
            $phone = $DB['mobile'];
            if(file_exists('./banktext/'.$phone.'.txt')){
                $text = file_get_contents('./banktext/'.$phone.'.txt');
                if($input['bankcode'] != $text){
                    return ['status'=>2,'msg'=>'验证码错误！请重新输入'];
                }
				$liks = Db::name('mbank')->where('crad',$input['bankShen'])->where('uid','<>',$uid)->select();
                if(count($liks) > 0){
                    return ['status'=>2,'msg'=>'此身份证已绑定其他会员卡号！'];
                }
                $data['uid'] = Session::get('uid');
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
            }else{
                return ['status'=>2,'msg'=>'验证码不存在'];
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
