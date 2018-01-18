<?php
namespace app\home\model;

use think\Db;
use think\Model;
use think\Session;
use think\Validate;

class Login extends Model
{
        protected $table='web_member';//会员表
        public static $role = [
            'username'=>['regex'=>'/^1[3|4|5|7|8][0-9]\d{8}$/'],
            'password'=>'require|min:6'
        ];
        public static $message = [
            'username'=>'帐号格式不正确',
            'password.require'=>'密码不能为空','password.min'=>'密码长度不够'
        ];//登录验证
        public static function login_verify($data){
                $validate = new Validate(self::$role,self::$message);
                if(!$validate::token('','',['__token__'=>$data['token']])){
                        return ['status'=>2,'msg'=>'请不要重复提交单表'];
                }
                if(!$validate->check($data)){
                    return ['status'=>2,'msg'=>$validate->getError()];
                }
                $table_member = new self;
                $web_username=$table_member::get(['username'=>$data['username']]);
                if(isset($web_username->username)){
                            if($web_username['error_num'] >= 5 && $web_username['error_time'] != '' && $web_username['error_time'] > time()){
                                    return ['status'=>2,'msg'=>'您登录错误次数过多请稍后再试'];
                            }
                                $web_username->log_ip = request()->ip();
                                $web_username->log_time = time();
                            if($web_username['password'] != md5_pass(2,$data['password'])){
                                if($web_username['error_num'] >= 4){
                                    $web_username->error_time = time() + 3600;
                                }else{
                                    $web_username->error_time = '';
                                }
                                $web_username->error_num += 1;
                                $web_username->isUpdate(true)->save();
                                return ['status'=>2,'msg'=>'密码错误'];
                            }
                            switch ($web_username->status){
                                case 2:
                                    return ['status'=>2,'msg'=>'帐号冻结中'];
                                    break;
                                case 3:
                                    return ['status'=>2,'msg'=>'帐号已删除'];
                                    break;
                            }
                            $web_username->error_num = 0;
                            $web_username->error_time = '';
                            $web_username->log_num += 1;
                            $web_username->save();
                            Session::set('web_uid',$web_username->id);
                            return ['status'=>2,'msg'=>'登录成功'];
                }else{
                    return ['status'=>2,'msg'=>'帐号不存在'];
                }
        }
        //注册验证
        public static function register($data){
                self::$role['password_two'] = 'require|min:6';
                self::$role['invite_code'] = 'require|min:6';
                self::$message['password_two.require']='提现密码不能为空';
                self::$message['password_two.min']='提现密码长度不够';
                self::$message['invite_code.require'] = '邀请码不能为空';
                self::$message['invite_code.min'] = '邀请码长度不够';
                $validate = new Validate(self::$role,self::$message);
                if(!$validate::token('','',['__token__'=>$data['token']])){
                    return ['status'=>2,'msg'=>'请不要重复提交单表'];
                }
                if(!$validate->check($data)){
                    return ['status'=>2,'msg'=>$validate->getError()];
                }
                $table_code = Db::name('code')->where('code',$data['code'])->where('status',1)->find();
                if(!$table_code){
                    return ['status'=>2,'msg'=>'手机验证码错误'];
                }elseif((time() - $table_code['create_date']) > 300){
                    Db::name('code')->where('code',$data['code'])->update(['status'=>2]);
                    return ['status'=>2,'msg'=>'验证码已过期'];
                    }
                $table_member = new self;
                $table_result=$table_member::get(['invite_code'=>$data['invite_code']]);
                if(!isset($table_result->id)){
                    return ['status'=>2,'msg'=>'推荐人不存在'];
                }
                $table_result->invite_person+=1;
                $table_result->update_time=time();
                $table_result->isUpdate(true)->save();
                $table_member->mobile = $data['username'];
                $table_member->password = md5_pass(2,$data['password']);
                $table_member->password_two = md5_pass(1,$data['password_two']);
                $table_member->reg_ip = request()->ip();
                $table_member->reg_time = request()->time();
                $table_member->invite_code = self::verfiy_code();
                $table_member->recommend = $table_result->id;
                $table_member->isUpdate(false)->allowField(true)->save();
                Session::set('web_uid',$table_member->id);
                return $table_member->id?['status'=>1,'msg'=>'注册成功','url'=>url('home/Index/index')]:['status'=>2,'msg'=>'注册失败'];
        }
        //
        public function verfiy_code(){
            $table_member = new self;
            $random_str = random_str(6);
            $result=$table_member::get(['invite_code'=>$random_str]);
            if(isset($result->id)){
                self::verfiy_code();
            }
            return $random_str;
        }
}