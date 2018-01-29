<?php

namespace app\admin\model;

use app\index\model\Goods;
use think\Db;
use think\Exception;
use think\Model;
use think\Session;
use think\Validate;

class Member extends Model
{           //后台添加会员
        static public  function add($data,$invite){
                    $user = new Member;
                    $role = [
                        'mobile'=>['regex'=>'/^1(3|4|5|7|8)\d{9}$/'],
                        'password'=>'require|min:6',
                        'password_two'=>'require|min:6',
                        'invite_code'=>'require|min:6'
                    ];
                    $message = [
                        'mobile.regex'=>'手机格式不正确','password.require'=>'密码不能为空','password.min'=>'密码长度不够',
                        'password_two.require'=>'提现密码不能为空','password_two.min'=>'提现密码的长度不够',
                        'invite_code.require'=>'邀请码不能为空','invite_code.min'=>'邀请码的长度不够'
                    ];
                    $validate = new Validate($role,$message);
                    if(!$validate::token('','',['__token__'=>$data['__token__']])){
                        return ['status'=>2,'msg'=>'请不要重复提交表单'];
                    }
					$find_data_exites=$user->where('mobile',$data['mobile'])->find();
					if($find_data_exites){
						return ['status'=>2,'msg'=>'推荐人已经存在'];
					}
                    if(!empty($data['invite_code'])){
                        if(!$validate->check($data)){
                            return ['status'=>2,'msg'=>$validate->getError()];
                        }
                        $member_res=$user::get(['invite_code'=>$data['invite_code']]);
                        if(!isset($member_res->id)){
                            return ['status'=>2,'msg'=>'邀请人不存在'];
                        }
                        $data['recommend']=$member_res->id;
                        $member_res->invite_person += 1;
                        $member_res->isUpdate(true)->save();
                     }else{
                        $data['recommend'] = '';
                    }
                        $user->data([
                                'mobile'=>$data['mobile'],
                                'recommend'=>$data['recommend'],
                                'password'=>md5_pass(2,$data['password']),//前台会员登录密码
                                'password_two'=>md5_pass(1,$data['password_two']),//前台会员提现密码
                                'invite_code'=>$invite,//生成一个推荐人id
                                'reg_ip'=>request()->ip(),'reg_time'=>time()
                        ]);
                        $user->isUpdate(false)->allowField(true)->save();
                    return isset($user->id)?['status'=>1,'msg'=>'添加成功']:['status'=>2,'msg'=>'添加失败'];
        }
        public static function create($data = [], $field = null)
        {
            return parent::create($data, $field); // TODO: Change the autogenerated stub
        }
        //删除会员，只是为了更改删除状态不是真正的删除
        public static function del($id){
               $new = new Member();
               $result=$new->update(['status'=>3],['id'=>$id]);
               if($result){
                   return ['status'=>1,'msg'=>'删除成功'];
               }else{
                   return ['status'=>2,'msg'=>'删除失败'];
               }
        }
        //修改状态
        public static function edit_status($data,$num){
                    //把状态改成4未激活状态
                    $new = Member::where('id',$data)->update(['status'=>$num]);
                    if($new){
                        return ['status'=>1,'msg'=>'操作成功'];
                    }else{
                        return ['status'=>2,'msg'=>'操作失败'];
                    }
        }
        //修改密码
        public static function editpassword($id,$password){
                     $new = new Member;
                      //为什么手册不说有update_time这个字段坑货
                        $result= $new->update($password,['id'=>$id]);
                     if($result){
                         return ['status'=>1,'msg'=>'修改成功'];
                     }else{
                         return ['status'=>2,'msg'=>'修改失败'];
                     }
    }
            //重置密码
   public static function resetfunction($data){
                           $new = new self();
                           $find=$new::get(['mobile'=>$data['username']]);
                           if(empty($find->id)){
                               return ['status'=>2,'msg'=>'账号不存在'];
                           }
                           if($data['type'] == 1){
                               $find->password = md5_pass(2,$data['newpassword']);//设置登录密码
                               $find->save();
                           }elseif($data['type'] == 2){
                                $find->password_two = md5_pass(1,$data['newpassword']);//设置提现密码
                               file_put_contents("D:/HH.txt",$find->password_two);
                                $find->save();
                           }else{
                               return ['status'=>2,'msg'=>'选择不存在'];
                           }
                           return $find->id?['status'=>1,'msg'=>'修改成功']:['status'=>2,'msg'=>'修改失败'];
    }
            //修改会员资料
   public static  function  user_edit_data($data){
                    $new = new Member();
                    $call=$new->get($data['id']);
                    if($call->level < $data['level']){
                        return ['status'=>2,'msg'=>"不能降级"];
                    }elseif ($call->level != $data['level']){
                        if($data['level'] == 3){
                            $data['d_sign'] = $call->invite_code;
                            $data['s_sign'] = '';
                        }elseif ($data['level'] == 2){
                            $data['s_sign'] = $call->invite_code;
                        }else{
                            $data['d_sign'] = '';
                            $data['s_sign'] = '';
                        }
                    }
                    $result = $new->where('id',$data['id'])->update($data);
                    if($result){
                        return ['status'=>1,'msg'=>'修改资料成功'];
                    }else{
                        return ['status'=>2,'msg'=>'修改失败'];
                    }
    }
        //会员充值和扣款
   public static function recharge($input){
                $member = new Member();
                Db::startTrans();
                try{
                    $aa=$member::get($input['user_id']);
                    if(empty($id=$aa->id)){
                        return ['status'=>2,'msg'=>'用户不存在'];
                    }else{
                     $recharge = Db::name('recharge');//充值表
                        if($input['type_select'] == 1){
                            switch ($input['type']){
                                case 1://1现金
                                    $aa->money += (float)$input['income'];//传过来的金额
                                    $type=1;
                                    break;
                                case 2://2奖金
                                    $aa->bonus += (float)$input['income'];
                                    $type=2;
                                    break;
                                default:
                                    return ['status'=>2,'msg'=>'币种不存在'];
                            }
                        }elseif($input['type_select'] == 2 ){
                            switch ($input['type']){
                                case 1://1现金
                                    if($aa->money < $input['income']){
                                    return ['status'=>2,'msg'=>'当前账户金额不足'];
                                    }
                                    $aa->money -= (float)$input['income'];//传过来的金额
                                    $type=1;
                                    break;
                                case 2://2奖金
                                    if($aa->bonus < $input['income']){
                                        return ['status'=>2,'msg'=>'当前账户奖金不足'];
                                    }
                                    $aa->bonus -= (float)$input['income'];
                                    $type=2;
                                    break;
                                default:
                                    return ['status'=>2,'msg'=>'币种不存在'];
                            }
                        }

                       $aa->isUpdate(true)->save();
                        $result_recharge=$recharge->insert([
                            'recharge_date'=>request()->time(),
                            'uid'=>$id,
                            'orders'=>build_order_no(),
                            'type'=>$input['type_select'],
                            'admin_id'=>Session::get('user_id'),
                            'money'=>$input['income'],
                            'message'=>$input['message'],
                            'bin_type'=>$type
                            ]);
                        if($result_recharge && $aa->id){
                            Db::commit();
                            return ['status'=>1,'msg'=>'操作成功'];
                        }else{
                            Db::rollback();
                            return ['status'=>2,'msg'=>'操作失败'];
                        }
                    }
                }catch (Exception $e){
                    //事物回滚
                    Db::rollback();
                    Log::init([
                        'type'=>'file',
                        'path'=>APP_PATH.'logs/'
                    ]);
                    \think\Log::error($e->getMessage());
                }
        }


        //后台进行扣款
   public static function changes($data){
            $member = new Member;
            Db::startTrans();

            try{
                $one_object=$member::get(['mobile'=>$data['username']]);
                $recharge = Db::name('recharge');
                if(empty($one_object->id)){
                    return ['status'=>2,'msg'=>'扣款账号不存在'];
                }else{
                    /*如果存在就进行扣款*/
                    switch ($data['type']){
                        case 1://现金
                            if((float)$one_object->money < (float)$data['money']){
                                /*比较值*/
                                return ['status'=>2,'msg'=>'扣款金额不足'];
                            }else{
                                $one_object->money -= $data['money'];
                                $type = 1;
                            }
                            break;
                        case 2://奖金
                            if($one_object->bonus < (float)$data['money']){
                                    return ['status'=>2,'msg'=>'奖金不足'];
                            }else{
                                $one_object->bonus -= $data['money'];
                                $type = 2;
                            }
                            break;
                        default:
                            return ['status'=>2,'msg'=>'币种不存在'];
                    }
                        $one_object->isUpdate(true)->save();
                        $result_id=$recharge->insert([
                            'recharge_date'=>request()->time(),
                            'uid'=>$one_object->id,
                            'type'=>2,'orders'=>build_order_no(),'admin_id'=>Session::get('user_id'),
                            'money'=>$data['money'],'message'=>$data['message'],'success_status'=>3,'bin_type'=>$type
                        ]);
                        if($result_id && $one_object->id){
                                Db::commit();
                                return ['status'=>1,'msg'=>'扣款成功'];
                        }else{
                            Db::rollback();
                            return ['status'=>2,'msg'=>'扣款失败'];
                        }
                }
            }catch(Exception $e){
                Db::rollback();
                Log::init(['type'=>'file','path'=>APP_PATH.'Admin/logs/']);Log::error($e->getMessage());
            }
        }
        /*
         * 这里用来查询，启用，冻结，删除，时间截
         * 首先把要查询的数据传过来
         * 进行进行筛选
         * 返回给controller
         *
         * */
   public static function call_select($data){
                if(!empty($data['search_starttime']) && !empty($data['search_endtime'])){
                    $starttime = strtotime($data['search_starttime']);
                    $endtime = strtotime($data['search_endtime']);
                    if($starttime < $endtime){
                        $strtime = strtotime($data['search_starttime'].'00:00:00').','.strtotime($data['search_endtime'].'23:59:59');
                        $search['search_starttime']=$data['search_starttime'];
                        $search['search_endtime']=$data['search_endtime'];
                    }else{
                        $strtime = strtotime($data['search_endtime'].'00:00:00').','.strtotime($data['search_starttime'].'23:59:59');
                        $search['search_starttime'] = $data['search_starttime'];
                        $search['search_endtime']=$data['search_endtime'];
                        }
                        $map['reg_time'] = array('between',$strtime);
                }elseif(!empty($data['search_starttime'])){
                          $strtime = strtotime($data['search_starttime'].'00:00:00');
                          $map['reg_time']=array('egt',$strtime);
                          $search['search_starttime'] = $data['search_starttime'];
                }elseif (!empty($data['search_endtime'])){
                          $strtime = strtotime($data['search_endtime'].'00:00:00');
                          $map['reg_time']=array('elt',$strtime);
                          $search['search_endtime'] = $data['search_endtime'];
                }

                    if(!empty($data['search_username'])){
                            $map['mobile'] = array('like','%'.$data['search_username'],'%');
                            $search['search_username'] = $data['search_username'];
                    }
                if (!empty($data['search_status'])){
                            $map['status'] = $data['search_status'];
                            $search['search_status'] = $data['search_status'];
                }else{
                            $map['status'] = 1;
                            $search['search_status'] = 1;
                }
                   return array($map,$search);
        }
        //定时执行
    public static function timing_execute(){

                 Db::startTrans();
                $self = new self;
                $timing = Db::name('bouns');//这是奖金表
                //结算每日分红
                $profit_table = Db::name('profit');//每日分红发放记录表
                $orders_tables = new Goods();
                $call=$timing->where('status',1)->where('nitegral_balance','not null')->select();
                $count = empty($call)?null:count($call);
                for($i=0;$i<$count;$i++){
                        $save_value = $call[$i]['nitegral_balance'] - $call[$i]['price_one_money'];
                        if($save_value > 0){
                            try{
                                $call_back=$self->where('id',$call[$i]['uid'])->setInc('integral',$call[$i]['price_one_money']);
                                Db::name('integral')->insert(['uid'=>$call[$i]['uid'],'integral'=>$call[$i]['price_one_money'],'create_time'=>time()]);
                                $call_backs = $timing->where('id',$call[$i]['id'])->update(['nitegral_balance'=>$save_value,'update_time'=>request()->time()]);
                                if($call_back && $call_backs){
                                    Db::commit();
                                    \think\Log::init(['type'=>'File','path'=>APP_PATH.'log_zhi/']);
                                    \think\Log::info(['status'=>'成功','updatetime'=>time(),'uid'=>$call[$i]['id'],'money'=>$call[$i]['price_one_money'],'jifen'=>'积分奖励']);
                                }else{
                                    \think\Log::error('更新失败');
                                }
                            }catch (Exception $e){
                                Db::rollback();
                                \think\Log::init(['type'=>'File','path'=>APP_PATH.'log_zhi/']);
                                \think\Log::error($e->getMessage());
                            }
                        }else{
                            continue;
                        }
                }
                exit;
        }
        //申请商家
    public static function shenqing($data){
            $rule = ['type'=>"require|number",'id'=>"require|number"];
            $message = ['type.require'=>'不能为空','id.require'=>'不能为空'];
            $validate = new Validate($rule,$message);
            if(!$validate->check($data)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            Db::startTrans();
            try{
                $self = new self;
                $table_put = Db::name('put');
                $call_data=$table_put->where('id',$data['id'])->find();
                $shouhu= $self->where('id',$call_data['uid'])->find();
                if($shouhu['status'] == 2){
                    return ['status'=>2,'msg'=>'通过后不能修改'];
                }
                if($data['type'] == 3){
                    //等于3的时候未通过
                    $result=$table_put->where('id',$data['id'])->update(['status'=>3]);
                }elseif ($data['type'] == 2){
                    $result=$table_put->where('id',$data['id'])->update(['status'=>2]);

                    switch ($call_data['level']){
                        case 2:
                            //2代表商家
                            $prople_num=$self->where('id',$call_data['uid'])->update(['level'=>$call_data['level'],'s_sign'=>$shouhu['invite_code']]);
                            $result_s= self::resurion($shouhu['invite_code'],$call_data['uid']);
                            if(!$result_s){
                                Db::rollback();
                                return ['status'=>1,'msg'=>'失败'];
                            }else{
                                Db::commit();
                                return ['status'=>1,'msg'=>'成功'];
                            }
                            break;
                        case 3:
                            $prople_num=$self->where('id',$call_data['uid'])->update(['level'=>$call_data['level'],'s_sign'=>'','d_sign'=>$shouhu['invite_code']]);
                            $result_s= self::agenry($shouhu['invite_code'],$call_data['uid']);
                            if(!$result_s){
                                Db::rollback();
                                return ['status'=>1,'msg'=>'失败'];
                            }else{
                                Db::commit();
                                return ['status'=>1,'msg'=>'成功'];
                            }
                            break;
                    }
                }else{
                    return ['status'=>2,'msg'=>'提交类型不存在'];
                }
            }catch (Exception $e){
                Db::rollback();
                \think\Log::error($e->getMessage());
            }
    }
        //计算递推关系统一成功商家
    public static function resurion($s_sign,$id){
        //首先统计一下我的下一级的人数
            $new = new self();
           $call_back= $new->where('recommend_id',$id)->select();
           for ($i=0;$i<count($call_back);$i++){
                    //这里是根据上一级去统计所以的人数
               $call_res=$new->where('id',$call_back[$i]['id'])->update(['s_sign'=>$s_sign]);
                  self::resurion($s_sign,$call_back[$i]['id']);
           }
           return $call_res?true:false;
    }
    //将会员下面的所有递推关系成功下一级//成为代理商
    public static function agenry($d_sign,$uid){
        //首先根据uid去查下一级
         $self = new self;
           $select= $self->where('recommend_id',$uid)->select();
           for($i=0;$i<count($select);$i++){
               $call_result = $self->where('id',$select[$i]['id'])->update(['d_sign'=>$d_sign,'s_sign'=>'']);
              self::agenry($d_sign,$select[$i]['id']);
           }
           return $call_result?true:false;
    }
    //智汇付回调添加金额
    public static function tianjiamoney($data,$select){
        //接收订单一条订单记录,//获取金额select
            Db::startTrans();
            try{
                \think\Log::init(['type'=>'File','path'=>APP_PATH.'zhangsan/']);
                $new = new self;
                $one_object=$new->get($data['uid']);
                $database = database(2);
                if($one_object->level == 1){
                    $random_numbe = action('home/register/random_str',7);
                    if((float)$select >= (float)$database['cwshangjia']){
                        if($one_object->invite_code == ''){
                            $one_object->invite_code = $random_numbe;
                        }
                        $one_object->s_sign = $one_object->invite_code;
                        $one_object->money +=$data['money'];
                        $one_object->isUpdate(true)->save();
                        $call_back_value=self::resurion($one_object->invite_code,$data['uid']);
                        \think\Log::info(['member'=>$new->id,'shangjia'=>$call_back_value,'time'=>time()]);
                        }elseif($select>=500 && $one_object->invite_code == ''){
                            $one_object->invite_code = $random_numbe;
                            $one_object->money += $data['money'];
                            $one_object->isUpdate(true)->save();
                            \think\Log::info(['member'=>$one_object->id,'time'=>time()]);
                        }else{
                        $one_object->money += $data['money'];
                        $one_object->isUpdate(true)->save();
                        \think\Log::info(['member'=>$one_object->id,'time'=>time()]);
                        }
                }elseif($one_object->level == 2 || $one_object->level == 3){
                    $one_object->money += $data['money'];
                    $one_object->isUpdate(true)->save();
                    \think\Log::info(['member'=>$one_object->id,'time'=>time()]);
                }else{
                    return false;
                }
                Db::commit();
            }catch (Exception $exception){
                Db::rollback();

                \think\Log::error($exception->getMessage());
            }
    }
}
