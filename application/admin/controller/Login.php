<?php

namespace app\admin\controller;

use Org\Net\IpLocation;
use think\Controller;
use think\Db;
use think\Request;
use Think\Session;
use think\Validate;

class Login extends Controller
{
            //后台登录判断
    public  function login(Request $request){

            if(Request::instance()->isAjax()){
                $input = input('post.');
                $rules = [
                    'username'=>'require|min:6',
                    'password'=>'require|min:5',
                    'code'=>'alphaNum|length:4'
                ];
                $message = [
                    'username'=>'账号格式不对',
                    'password'=>'密码格式不对',
                    'code'=>'验证码纯数字'
                ];
                $validate = new Validate($rules,$message);
                $token=$validate::token('__token__','',['__token__'=>$input['token']]);
                if(!$token){
                    return ['status'=>2,'msg'=>$validate->getError()];
                }
                if(!$validate->check($input)){
                    return ['status'=>2,'msg'=>$validate->getError()];
                }
                $red = Db::name('admin')->where(array('username'=>$input['username']))->find();
                $webconfig = db('webconfig')->where('id',1)->find();
                $web_value = json_decode($webconfig['value'],true);
                if(captcha_check($input['code'])){
                    $Ip = new IpLocation('UTFWry.dat');
                    $location = $Ip->getlocation(\request()->ip()); // 获取某个IP地址所在的位置
                    $usepdw = db('admin')->field('id,lognum,status,groupid')->where(array('username'=>$input['username'],'password'=>md5_pass(1,$input['password'])))->find();
                    if(!empty($usepdw)){
                                if($red['errorlognum']<$web_value['num']){
                                    if($usepdw['status']==1){
                                        $da['id']=$usepdw['id'];
                                        $da['lognum'] = $usepdw['lognum'] + 1;
                                        $da['errorlognum'] = 0;
                                        $da['errorlogtime'] = 0;
                                        $call=Db::name('admin')->update($da);
                                        if($call){
                                            Session::set('user_id',$usepdw['id']);
                                            Session::set('groupid',$usepdw['groupid']);
                                            Session::set('timeout',time());
                                            $location['status'] = 1;
                                            $location['uid'] = $usepdw['id'];
                                            $location['create_date'] = date('Y-m-d H:i:s');
                                            Db::name('loginlog')->insert($location);
                                             return ['status'=>1,'url'=>url('admin/index/index')];
                                        }
                                    }else{
                                       return ['type'=>3,'msg'=>'账号被冻结'];
                                    }
                                }else{
                                    return ['type'=>3,'msg'=>'登录错误次数超过了'.$web_value['num'].'次等一个小时再试'];
                                }
                    }else{
                        if($red){
                            if($red['errorlognum']<$web_value['num']){
                               $location['uid']=$red['id'];
                               $location['status']=2;
                               $location['create_date'] = date('Y-m-d H:i:s');
                               Db::name('loginlog')->insert($location);
                               $dd=Db::name('admin')->update(['id'=>$red['id'],'errorlognum'=>$red['errorlognum']+1,'errorlogtime'=>time()+60*20]);
                               $f=empty($dd)?'失败'.','.time():'成功'.','.time();
                               $myfile=fopen('D:/success.txt','w');
                               fwrite($myfile,$f);
                               fclose($myfile);
                            }else{
                                return ['type'=>3,'msg'=>'登录错误数次过多'];
                            }
                        }else{
                            return ['type'=>1,'msg'=>'账号不对'];
                        }
                        return ['type'=>1,'msg'=>'密码错误!'];
                    }
                }else{
                    return ['type'=>2,'msg'=>$validate->getError()];
                }
            }else{
                return $this->fetch();
            }
    }

}
