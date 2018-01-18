<?php

namespace app\index\model;

use think\Db;
use think\Model;
use think\Session;
use think\Url;
use think\Validate;

class User extends Model
{
    //
    protected $table = 'web_member';

    //提现
    public static function deposit_money($data){
        $role = [
            'money'=>'require|float',
            'password_two'=>'require|min:6',
            'code'=>"require|min:4|captcha",
            'bank_id'=>'require|number'
                ];
        $message = [
            'money.require'=>'金额不能为空',
            'money.float'=>'必须是小数',
            'password_two.require'=>'安全密码不能为空',
            'password_two.min'=>'安全密码长度不够',
            'code.require'=>'验证码不能为空',
            'code.min'=>'验证码不能小于四位数',
            'bank_id.require'=>'银行不能为空',
            'bank_id.number'=>'银行必须是数字',
            'code.captcha'=>'验证码不正确'
        ];
        $validate = new Validate($role,$message);
        if(!$validate::token('','',['__token__'=>$data['token']])){
                return ['status'=>2,'msg'=>'请不要重复提交表单'];
        }
        if(!$validate->check($data)){
           return ['status'=>2,'msg'=>$validate->getError()];
        }
            $bank_ = Db::name('mbank')->where('id',$data['bank_id'])->find();
        if(!$bank_){
           return ['status'=>2,'msg'=>'您的银行卡不正确'];
        }
        $new = new self;
        $uid = Session::get('uid');
        $member_data=$new::get(['id'=>$uid]);
        if($member_data->password_two !== md5_pass(1,$data['password_two'])){
                return ['status'=>2,'msg'=>'安全密码不正确'];
        }
        $databases = database(2);
		
		    if($databases['tixian_status'] < 1){
            return ['status'=>2,'msg'=>'提现已关闭'];
        }
			if($databases['deposit_time_start'] > 0 ){
					$deposit_time_start = strtotime(date('Y-m-d').$databases['deposit_time_hour'].$databases['deposit_time_minute']);//start time
        $deposit_time_end = strtotime(date('Y-m-d').$databases['deposit_time_hour_stop'].$databases['deposit_time_minute_stop']);//time out.
        $time = request()->time();
        if($time < $deposit_time_start || $time > $deposit_time_end){
            return ['status'=>2,'msg'=>'请在当天'.date('H:i:s',$deposit_time_start).' 至 '.date('H:i:s',$deposit_time_end).'提现'];
        }
			}
       $day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
		$count_deposit = Db::name('deposit')->where($day)->where('uid',$uid)->count();
		if($count_deposit >= $databases['deposit_number']){
			return ['status'=>2,'msg'=>'每日可提现'.$databases['deposit_number'].'次'];
		}
        if($databases['tixianjinemin'] > $data['money']){
            return ['status'=>2,'msg'=>'提现金额不能小于'.$databases['tixianjinemin'].'￥'];
        }
        if($databases['tixianjinemax'] <= $data['money']){
            return ['status'=>2,'msg'=>'提现金额不能大于'.$databases['tixianjinemax'].'￥'];
        }
        if($member_data->money < $data['money']){
                    return ['status'=>2,'msg'=>'余额不足'];
         }
        $member_data->money -= $data['money'];
        $shouxufei=$data['money'] * ($databases['shouxufei'] / 100);
        $benjin=$data['money'] - $shouxufei;
        $web_deposit = Db::name('deposit')->insert([
            'money'=>$benjin,
            'service_charge'=>$shouxufei,
            'create_date'=>request()->time(),
            'uid'=>$uid,'name'=>$bank_['name'],
            'bank'=>$bank_['bank'],'province'=>$bank_['sheng'],
            'city'=>$bank_['shi'],'kaihubank'=>$bank_['zhihang'],'bank_crad'=>$bank_['bank_user'],'mobile'=>$bank_['mobile'],
            'crad'=>$bank_['crad'],
            'type'=>1
        ]);
        $member_data->save();
        if($web_deposit){
            return ['status'=>1,'msg'=>'提现成功','url'=>Url::build('index/Suey/withdrawals','','')];
        }else{
            return ['status'=>2,'msg'=>'提现失败'];
        }
    }
}
