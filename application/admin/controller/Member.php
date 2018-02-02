<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Exception;
use think\Log;
use think\model\Merge;
use think\Request;
use app\admin\model\Member as m;
use think\Session;

class Member extends Common
{
    protected $data_base_data;//设置迭代层数
        //展示页面的数据
    public  function index(){
            $two_last = m::call_select(request()->get());
            $member_table = m::where($two_last[0])->order('id desc')->paginate(20);
            $member_obj = Db::name('member');
            $order_tables = Db::name('orders');
            $data_base = database(2);//获取后台设置的参数
            $this->data_base_data = $data_base['relationship'];

            foreach ($member_table as &$value){
                    $fin=m::get(['id'=>$value['recommend']]);
                    $value['recommend'] = $fin->mobile ?: '无';
                    $value['total_price'] = $order_tables
                        ->where('uid',$value['id'])
                        ->where('type','in','2,3')
                        ->sum('price*num');//获取客户购买产品和数量的总和;
					$value['total_earnings'] = $order_tables->where(['uid'=>$value['id']])->where('type','in','2,3')->sum('interest');
                    $value['group_size'] = $this -> team($value['id'],$member_obj,$data_base['relationship']);
                    $arr[]=$value->toArray();
            }
            $page = $member_table->render();
            return view('index',['search'=>$two_last[1],'count'=>m::count(),'page'=>$page,'list'=>$arr]);
        }

    public function team($id,$obj,$search_level){
        $member = $obj->where('recommend',$id)->where('status',1)->select();
        $num = count($member);
        if($num < 1 || $search_level < 1){
            return $num;
        }else{
            for($i=0;$i<$num;$i++){
                $num+=self::team($member[$i]['id'],$obj,$search_level - 1);
            }
            return $num;
        }
    }

        //添加会员
    public function useradd(){
                if(Request::instance()->isAjax()){
                       return \app\admin\model\Member::add(input('post.'),self::rand_ji());
                }else{
                    return view();
                }
        }
    public function rand_ji(){
            $rand = rand(111111,999999);
            $success=\app\admin\model\Member::get(['invite_code'=>$rand]);
            if($success){
                self::rand_ji();
            }else{
                return $rand;
            }
        }
        //删除会员
    public function userdel(){
        if(Request::instance()->isAjax()){
            return \app\admin\model\Member::del(input('post.id'));
        }
    }
    //重置密码

    /**
     * @return array|\think\response\View
     */
    public function twopassword(){
        if(Request::instance()->isAjax()){
                 $input = input('post.');
                 $validate = new \app\admin\validate\Member;
             /*    $call = $validate::token('__token__','',['__token__'=>$input['__token__']]);
                 if(!$call){
                     return ['status'=>2,'msg'=>$validate->getError()];
                 }*/
                 $validate->scene('edit',['type','username','newpassword','repassword']);
                 if(!$validate->scene('edit')->check($input)){
                     return ['status'=>2,'msg'=>$validate->getError()];
                 }
                return \app\admin\model\Member::resetfunction($input);
        }
        $this->assign('password_level',array(1=>'登录密码',2=>'提现密码'));
        return view();
    }
        //展示会员信息
    public function usershow($id){
                $list = Db::name('member')->find($id);
               return $this->fetch('usershow',$list);
    }
      //冻结会员
    public function user_stop(){
                if(Request::instance()->isAjax()){
                    return m::edit_status(input('get.id'),2);
                }
    }
    //启动会员
    public function user_start(){
            if(Request::instance()->isAjax()){
                return m::edit_status(input('get.id'),1);
            }
    }
    //展示修改密码
    public function userpasswordedit($id){
        if(Request::instance()->isAjax()){
                    $validate=validate('member');
                    $input = input('post.');
                    $validate->scene('edit',['repassword','newpassword']);
                if(!$validate->scene('edit')->check($input)){
                    return ['status'=>2,'msg'=>$validate->getError()];
                }
                    if($input['type_select'] == 1){
                        $arr['password'] = md5_pass(2,$input['repassword']);//登录密码
                    }elseif($input['type_select'] == 2){
                        $arr['password_two'] = md5_pass(1,$input['repassword']);//提现密码
                    }else{
                        return ['status'=>2,'msg'=>'请选择正确的'];
                    }
                    return m::editpassword($input['id'],$arr);
        }
        return view('userpassword',['id'=>$id]);
    }
    //修改会员资料
    public function useredit($id){
        if(Request::instance()->isAjax()){
                    $validate = new \app\admin\validate\Member();
                    $input = input('post.');
                    $validate->scene('edit',['mobile','level','id','name']);
                    if(!$validate->scene('edit')->check($input)){
                        return ['status'=>2,'msg'=>$validate->getError()];
                    }
                    try{
                         return m::user_edit_data($input);
                    }catch (Exception $e){
                        Log::init([
                            'type'=>'file',
                            'path'=>APP_PATH.'Admin/logs/'
                        ]);
                        Log::error($e->getMessage());
                    }
        }else{
            $name = Db::name('member')->find($id);
            $this->assign('leveldd',member_level());
            return $this->fetch('member_edit',$name);
        }
    }
    //展示充值页面
    public function recharge(){
        if(Request::instance()->isAjax()){
                $input = input('post.');
                $validate = validate('member');
                $validate->scene('edit',['type','income','message']);
                if(!$validate->scene('edit')->check($input)){
                    return ['status'=>2,'msg'=>$validate->getError()];
                }
                try{
                    return \app\admin\model\Member::recharge($input);
                }catch(Exception $e){
                    Log::init([
                        'type'=>'file',
                        'path'=>APP_PATH.'Admin/logs/'
                    ]);
                    Log::error($e->getMessage());
                }
        }else{
            $this->assign('type',benjin());
            $this->assign('id',\request()->route('id'));
            return view();
        }
    }
    //扣款
    public function deduct(){
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $validate = validate('member');
            $validate->scene('edit',['type','income','username','message']);
            if(!$validate->scene('edit')->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $data['type'] = $input['type'];
            $data['money']=$input['income'];
            $data['username']=$input['username'];
            $data['message'] = $input['message'];
            try{
                return \app\admin\model\Member::changes($data);
            }catch(Exception $e){
                Log::init([
                    'type'=>'file',
                    'path'=>APP_PATH.'Admin/logs/'
                ]);
                Log::error($e->getMessage());
            }
        }else{
            $this->assign('type',benjin());
            return view('change');
        }
    }
    //在关系表展示会员信息
    public  function  usertreeinfo(){
        if($id = request()->get('id')){
             $name = m::where('recommend',$id)->select();
             $this->assign('list',$name);
        }
        return view('userinfo');
    }
    //展示关系
    public function usertree(){
        return view();
    }
    //异步加载节点
    public function mytree(){
        if(Request::instance()->isAjax()){
            $pId = "0";
            if (array_key_exists('id', request()->request())) {
                $pId = request()->request('id');
            }
            if ($pId == null || $pId == "")
                $pId = "0";
            $member_table = db('member');
            $list = $member_table->field('id,recommend,mobile')->where(array('recommend' => $pId))->select();
            $count = count($list);
            echo '[';
            for ($i = 1; $i <= $count; $i++) {
                $nId = $list[$i - 1]['id'];
                $nName = $list[$i - 1]['mobile'];
                $info = $member_table->field('id')->where(array('recommend' => $nId))->select();
                $flag = 'false';
                if ($info) {
                    $flag = 'true';
                }
                $url = 'usertreeinfo?id=' . $nId;
                echo "{ id:'" . $nId . "',	name:'" . $nName . "',	file:'" . $url . "',	isParent:'" . $flag . "'}";
                if ($i < $count) {
                    echo ",";
                }
            }
            echo ']';
        }
    }
    //定时测试获取积分
    public function member_func(){
           \app\admin\model\Member::timing_execute();
    }
    //展示商家申请列表
    public function applyfor(){
        if(Request::instance()->isPost()){
            $input = input('post.');
            $result = \app\admin\model\Member::call_select($input);
        }
        $table_put = Db::name('put')->where($result[0])->select();
        $table_member = Db::name('member');
        foreach ($table_put as &$value){
           $username = $table_member->where('id',$value['shang_level'])->field('username,mobile')->find();
           $u = $table_member->where('id',$value['uid'])->field('username,mobile')->find();
            $value['username'] = $u['username']?:$u['mobile'];
            $value['username_s'] = $username['username']?:$username['mobile'];
        }
        return view('',['member_level'=>member_level(),'list'=>$table_put,'count'=>count($table_put),'search'=>$result[1]]);
    }
    public function shenqing($id){
        return view('',['id'=>$id]);
    }
    public function appy_shang(){
        if(Request::instance()->isAjax()){
            $input = input('post.');
            return \app\admin\model\Member::shenqing($input);
        }
    }


    public function leaving(){//留言列表
        $where = [];
        $keyword = input('post.');
        if($keyword){
            $where['mobile'] = ['like','%'.$keyword['mobile'].'%'];
            $this -> assign('name',$keyword['mobile']);
        }
        $content = Db::name('liu')
            ->alias('a')
            ->join('member b','a.uid = b.id')
            ->where($where)
            ->field('a.*,b.mobile')
            ->order('a.time desc')
            ->paginate(10,false,[
                'type'     => 'bootstrap3','var_page' => 'page',]);
        $count = count(Db::name('liu')->alias('a')->join('member b','a.uid = b.id') ->where($where) ->field('a.*,b.mobile')->order('a.time desc')->select());
        $page = $content->render();
        $this->assign('list', $content);
        $this->assign('page', $page);
        $this->assign('count', $count);
        return $this->fetch();
    }

    public function leaving_detail($id){//查看详情
        if(Request::instance()->isGet()){
            $name = Db::name('liu')->find($id);
            $username = Db::name('member')->where('id',$name['uid']) ->field('mobile')->find();
        }
//        dump($username);
        $this -> assign('username',$username);
        $this->assign('list',$name);
        return $this->fetch();
    }


    public function updeta(){//回复内容
        if(Request::instance()->isAjax()){
            $input = input('get.');
            $input['reply_time'] = time();
            $input['status'] = 1;
            $edit = Db::name('liu')->where('id',$input['id'])->update($input);
            if($edit){
                return ['status'=>1,'msg'=>'回复成功'];
            }else{
                return ['status'=>2,'msg'=>'回复失败'];
            }
        }
    }

    public function updetas(){//修改内容
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $input['reply_time'] = time();
            $edit = Db::name('liu')->where('id',$input['id'])->update($input);
            if($edit){
                return ['status'=>1,'msg'=>'修改回复成功'];
            }else{
                return ['status'=>2,'msg'=>'修改回复失败'];
            }
        }
    }

    public function leaDel(){//删除回复
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $list = Db::table('web_liu')->where('id',$input['id'])->delete();
            if($list){
                return ['status'=>1,'msg'=>'删除成功'];
            }else{
                return ['status'=>2,'msg'=>'删除失败'];
            }
        }
    }
}