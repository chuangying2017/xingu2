<?php

namespace app\Admin\controller;

use app\Admin\controller\Common;
use app\admin\model\Log;
use think\Db;
use think\Exception;
use think\Request;
use think\Validate;
use app\Admin\model\Rbac as rrb;
class Rbac extends Common
{
        //角色管理展示
    public function adminrole(){
            $select=Db::name('role')->select();
            $arrs = array();
            $string = '';
            $counts = count($select);
            for ($i=0;$i<$counts;$i++){
                    $arr_two=Db::name('admin')->where('groupid',$select[$i]['id'])->where('status',1)->field('groupid','username')->select();
                        $count = empty($arr_two)?null:count($arr_two);
                        if($count>1){
                            for ($j=0;$j<$count;$j++){
                                $arrs[]=$arr_two[$j]['username'];
                                for ($k=0;$k<count($arrs);$k++){
                                        $string.='★★★'.$arrs[$k];
                                }
                                unset($arrs);
                            }
                            $string = trim($string,'★★★');
                            $select[$i]['username']=$string;
                            $string = '';
                        }else{
                            if($count>0){
                                $select[$i]['username']=$arr_two[0]['username'];
                            }else{
                                $select[$i]['username']=null;
                            }
                        }
            }
            $this->assign('count',$counts);
            $this->assign('select',$select);
            return view();
    }
        //编辑权限节点修改
    public final function power_edit_save(){
            if(Request::instance()->isAjax()){
                    $input=input('post.');
                    $data['name']=$input['name'];
                    $data['pid']=$input['pid'];
                    $data['sort']=$input['sort'];
                    $data['level']=$input['level'];
                    $data['style']=$input['address'];
                    $data['control_action'] = $input['control'];
                    $datas['id']=$input['powerid'];
                   $call = \app\admin\model\Rbac::where($datas)->update($data);
                   if($call){
                       return ['status'=>1,'msg'=>'数据更新成功'];
                   }else{
                       return ['status'=>2,'msg'=>'数据更新失败'];
                   }
            }
    }
        //添加角色
    public function admin_role_add(){

        if(Request::instance()->isAjax()){
                $input=input('post.');
                if(empty($input['rolename'])){
                    return ['status'=>2,'msg'=>'角色名不能为空'];
                }
              /*
                if(!tokencheck($input['__token__'])){
                    return ['status'=>2,'msg'=>'请不要重复提交表单'];
                }*/
              // $arr=array_merge($input['one'],$input['two'],$input['three']);
                $string_s= trim($input['powerid'],',');
                $id['id']=array('in',$string_s);
                $select = Db::name('power')->where($id)->field('control_action')->select();
                if(empty($select)){
                    return json_encode(['status'=>2,'msg'=>'角色名不能为空']);
                }
                $null = '';
                for ($i=0;$i<count($select);$i++){
                      $null.=','.$select[$i]['control_action'];
                }
                $data['power_id'] = $string_s;
                $data['power_control_action'] = $null;
                $data['remarks']=$input['remarks'];
                $data['rolename']=$input['rolename'];
                  $call = Db::name('role')->insert($data);
                if($call){
                   return ['status'=>1,'msg'=>'新增成功'];
                }else{
                    return ['status'=>2,'msg'=>'新增失败'];
                }
        }else{
            $list1= Db::name('power')->where('level',0)->select();
            $list2 = Db::name('power')->where('level',1)->select();
            $list3 = Db::name('power')->where('level',2)->select();
            $this->assign('list1',$list1);
            $this->assign('list2',$list2);
            $this->assign('list3',$list3);
        }
        return view();
    }
        //编辑规则
    public  function admin_role_edit_show(){
        //首先香克斯去接受值，因为这个值是特定给香克斯
        //香克斯拿到了这个值之后就放进去session里面的memberid先生
                if(Request::instance()->isAjax()){
                    $id=input('get.id');
                   session('memberid',$id);
                }
                //memberid先生去根据自己的值去role表查看一下没有这么一条数据
        $memberid=session('memberid');
        $one_array = Db::name('role')->where('id',$memberid)->find();
        $list1= Db::name('power')->where('level',0)->select();
        $list2 = Db::name('power')->where('level',1)->select();
        $list3 = Db::name('power')->where('level',2)->select();
        $one_array['power_id'] = explode(',',$one_array['power_id']);
        $this->assign('one',$one_array);
        $this->assign('list1',$list1);
        $this->assign('list2',$list2);
        $this->assign('list3',$list3);
         return  $this->fetch('Rbac/admin_role_edit');
    }
        //修改规则
    public  function admin_role_edit(){
            if(Request::instance()->isAjax()){
                $input=input('post.');
                if(empty($input['rolename'])){
                    return ['status'=>2,'msg'=>'角色名不能为空'];
                }
                /*
                  if(!tokencheck($input['__token__'])){
                      return ['status'=>2,'msg'=>'请不要重复提交表单'];
                  }*/
                // $arr=array_merge($input['one'],$input['two'],$input['three']);
                $string_s= trim($input['powerid'],',');
                $id['id']=array('in',$string_s);
                $select = Db::name('power')->where($id)->field('control_action')->select();
                if(empty($select)){
                    return ['status'=>2,'msg'=>'请选好规则'];
                }
                $null = '';
                for ($i=0;$i<count($select);$i++){
                    $null.=','.$select[$i]['control_action'];
                }
                $data['power_id'] = $string_s;
                $data['power_control_action'] = $null;
                $data['remarks']=$input['remarks'];
                $data['rolename']=$input['rolename'];
                $call = Db::name('role')->where('id',$input['id'])->update($data);
                if($call){
                    return ['status'=>1,'msg'=>'修改成功'];
                }else{
                    return ['status'=>2,'msg'=>'修改失败'];
                }
            }
    }
        //删除规则
    public  function admin_role_del(){
        if(Request::instance()->isAjax()){
            $id['id'] = input('post.id');
            $call = Db::name('role')->where($id)->delete();
                if($call){
                    return ['status'=>1,'msg'=>'删除成功'];
                }else{
                    return ['status'=>1,'msg'=>'删除失败'];
                }
        }
    }
        //展示添加权限节点
    public  function  poweradd(){
            $name_zero=Db::name('power')->where('pid',0)->where('level',0)->select();
                $i = 0;
                foreach($name_zero as $value){
                            $two_array[]=Db::name('power')->where('pid',$value['id'])->where('level','egt','1')->select();
                            foreach ($two_array[$i] as &$vl){
                                        $vl['name'] = '☆☆'.$vl['name'];
                            }
                        $i++;
                }
                $one_arry = $this->arrone($two_array);
                $this->assign('name_zero',$name_zero);
                $this->assign('much_array',$one_arry);
                return view();
    }
        //用来转换数组
    public function arrone($array){
        $arr=array();
        foreach ($array as $val){
             if(is_array($val)){
                 $arr = array_merge($arr,$val);
             }else{
                 $arr[]=$val;
             }
        }
        return $arr;
    }
        //展示节点列表
    public function adminpermission(){
        if(Request::instance()->isPost()){
           $input = input('post.serch_name');
           $select = db('power')->where('name|control_action','like','%'.$input.'%')->paginate(20);
          }else{
            $select=Db::name('power')->order('id desc')->paginate(20);
             }
        $this->assign('select',$select);
        return view('admin_permission');
    }
        //添加节点
    public  function  power_add(){
                if(Request::instance()->isAjax()){
                    $input=input('post.');
                    $rule=['name'=>'require','pid'=>'number'];
                    $vilidate = new Validate($rule);
                    if(!$vilidate->check($input)){
                        return ['status'=>2,'msg'=>'信息不全'];
                    }
                    $data['name']=$input['name'];
                    $data['pid']=$input['pid'];
                    $data['control_action']=$input['control'];
                    $rabc = new rrb($data);
                    $rabc->allowField(true)->save();
                    $id=$rabc->id;
                    $uidd['id']=$id;
                    if($input['pid'] == 0){
                        $uid['sort'] = $id;
                    }else{
                        $db = db('power')->where('id',$input['pid'])->field('level,sort')->find();
                        $uid['sort']=$id.'-'.$db['sort'];
                        $uid['level']=$db['level'] + 1;
                    }
                    $call=$rabc->allowField(true)->save($uid,$uidd);
                    if($call){
                        return ['status'=>1,'msg'=>'操作成功'];
                    }else{
                        return ['status'=>2,'msg'=>'操作失败'];
                    }
                        }
    }
        //管理员列表展示
    public function adminlist(){
            $name = Db::table('web_admin')->select();
            foreach($name as &$vl){
                $bb = Db::name('role')->where(array('id'=>$vl['groupid']))->find();
                if(empty($bb)){
                    $vl['namerole']='★○℃无敌';
                }else{
                    $vl['namerole']=$bb['rolename'];
                }
            }
            $this->assign('select',$name);
            return view('admin_list');
    }
        /*管理员-权限-删除*/
    public function admin_permission_del(){
            if(Request::instance()->isAjax()){
                $id = input('post.id');
                try{
                    $delete = Db::name('power')->where('id',$id)->delete();
                    if($delete){
                        return ['status'=>1,'msg'=>'删除成功'];
                    }else{
                        return ['status'=>2,'msg'=>'删除失败'];
                    }
                }catch(Exception $e){
                    \think\Log::init([
                        'type'=>'file',
                        'path'=>APP_PATH.'admin/logmsg/'
                    ]);
                    \think\Log::error($e->getMessage());
                }

            }
    }
        /* 管理员-权限-展示 */
    public  function power_show(){
                if(Request::instance()->isAjax()){
                        $id=input('get.id');
                        session('powershowid',$id);
                }
                $d = session('powershowid');
                $one_array=Db::name('power')->where('id',$d)->find();

               // $one_array = ['id'=>session('powershowid'),'name'=>'张三','control_action'=>'ff/ff','pid'=>'15','sort'=>'111','level'=>'16','style'=>'&nbsp;'];
                $this->assign('power',$one_array);
                return $this->fetch('Rbac/powerid');
    }
        //登录的历史记录
    public  function loginlog()
    {
        $result = array();
        $bool = Db::name('loginlog')->chunk(50, function ($query) use (&$result) {
            foreach ($query as $ll) {
                $result[] = $ll;
            }
        });
        $resl1 = Db::table('web_loginlog')->alias('a')->join('web_admin d', 'd.id=a.uid')->order('a.create_date desc')->paginate(20);
        $this->assign('listd', $resl1);
        $count = count($result);
        $this->assign('count', $count);
        // $this->assign('list',$result);
        return view();
    }
        //批量删除
    public function betch_del(){
                if(Request::instance()->isAjax()){
                    $input=input('get.str');
                    if(empty($input)){
                        return ['status'=>2,'msg'=>'请选择要删除的数据'];
                    }
                    $str=trim($input,',');
                    $str_call=rrb::destroy($str);
                    if($str_call){
                        return ['status'=>1,'msg'=>'批量删除成功'];
                    }else{
                        return ['status'=>2,'msg'=>'批量删除失败'];
                    }
                }
    }
        //规则表批量删除
    public function role_betch(){
        if(Request::instance()->isAjax()){
            $input=input('get.str');
            if(empty($input)){
                return ['status'=>2,'msg'=>'请选择要删除的数据'];
            }
            $str=trim($input,',');
            $str_call=Db::table('web_role')->delete($str);
            if($str_call){
                return ['status'=>1,'msg'=>'批量删除成功'];
            }else{
                return ['status'=>2,'msg'=>'批量删除失败'];
            }
        }
    }
        //批量删除员工
    public  function del_admin(){
        if(Request::instance()->isAjax()){
            $input = input('get.str');
            if(empty($input)){
                return ['status'=>2,'msg'=>'请选择批量数据'];
            }
            $str = trim($input,',');
           $call_back= Db::table('web_admin')->delete($str);
            if($call_back){
                return ['status'=>1,'msg'=>'批量删除成功'];
            }else{
                return ['status'=>2,'msg'=>'批量删除失败'];
            }
        }
    }
        //单个删除
    public  function  admin_list_del(){
        if(Request::instance()->isAjax()){
                $name = Db::name('admin')->delete(input('post.id'));
                if($name){
                    return ['status'=>1,'msg'=>'删除成功'];
                }else{
                    return ['status'=>2,'msg'=>'删除失败'];
                }
        }
    }
        //状态禁用为2
    public function  admin_list_status(){
          if(Request::instance()->isAjax()){
              $id['id'] = input('get.id');
                $select = Db::name('admin')->field('groupid')->find($id['id']);
                $find=Db::name('role')->where('id',$select['groupid'])->find();
                    if(!$find){
                            return ['status'=>2,'msg'=>'无敌会员不能冻结'];
                    }
              $name = Db::name('admin')->where($id)->update(array('status'=>2));
              if($name){
                  return ['status'=>1,'msg'=>'操作成功'];
              }else{
                  return ['status'=>2,'msg'=>'操作失败'];
              }
          }
    }
        //状态启动1
    public function  admin_list_start(){
        if(Request::instance()->isAjax()){
            $id['id'] = input('get.id');
            $name = Db::name('admin')->where($id)->update(array('status'=>1));
            if($name){
                return ['status'=>1,'msg'=>'操作成功'];
            }else{
                return ['status'=>2,'msg'=>'操作失败'];
            }
        }
    }
        //后台修改密码
    public function admin_password(){
        if(Request::instance()->isAjax()){
                $id=input('post.id');
                session('userd',$id);
        }
            $this->assign('id',session('userd'));
            return view();
    }
        //后台修改密码
    public function  houtaixiu(){
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $relu = [
                'password'=>'require|min:6|confirm'
                ];
            $message = ['password.min'=>'密码长度不够','password.confirm'=>'两次密码必须一致','password'=>'密码必须的'];
            $validate = new Validate($relu,$message);
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $data['password']=md5_pass(1,$input['password']);
            $call= Db::name('admin')->where('id',$input['id'])->update($data);
            if($call){
                return ['status'=>1,'msg'=>'操作成功'];
            }else{
                return ['status'=>2,'msg'=>'操作失败'];
                }
            }
    }
        //添加管理员
    public function admin_add_member(){
        if(Request::instance()->isAjax()){
            $input = input('post.');
                    $rules = [
                        'adminName'=>'require|alphaNum|min:6',
                        'password'=>'min:6|confirm',
                        'phone'=>['regex'=>'/^1[3|4|5|8]\d{9}$/'],
                        'email'=>'email',
                        'adminRole'=>'/\d+/'
                            ];
                    $message  =   [
                     'name.require' => '名称必须',
                   'password'     => '两次密码不正确',
                      'phone'  => '手机格式不正确',
                      'email'        => '邮箱格式错误',
                     'adminRole' =>'报错了'
                         ];
                    $validate = new Validate($rules,$message);
                    if(!$validate->check($input)){
                       return ['status'=>2,'msg'=>$validate->getError()];
                    }
                    $data['username'] = $input['adminName'];
                    $data['password'] = md5_pass(1,$input['password']);
                    $data['sex'] = $input['sex'];
                    $data['groupid']=$input['adminRole'];
                    $data['email'] = $input['email'];
                    $data['mobile'] = $input['phone'];
                    $data['regtime'] = time();
                    $call=Db::name('admin')->insert($data);
                    if($call){
                        return ['status'=>1,'msg'=>'管理员添加成功'];
                    }else{
                        return ['status'=>2,'msg'=>'添加失败'];
                    }
        }else{
            $name = Db::name('role')->field('id,rolename')->select();
            $this->assign('select',$name);
            return view('admin_add');
        }
    }
        //编辑管理员
    public function admin_edit_member(){
        if(Request::instance()->isAjax()){
            $input = input('get.data');
            session('ajaxid',$input);
        }
            $name = Db::name('admin')->where('id',session('ajaxid'))->find();
            $select = Db::name('role')->field('id,rolename')->select();
           // file_put_contents('D:/uu.txt',$name);
            $this->assign('select',$select);
            $this->assign('name',$name);
            return view('admin_edit');

    }
        //编辑管理员数据
    public  function admin_edit_data(){
        if(Request::instance()->isAjax()){
               $input=input('post.');
                $rules= ['adminName'=>'require|min:5','adminRole'=>'require'];
                $validate = new Validate($rules);
                if(!$validate->check($input)){
                    return ['status'=>2,'msg'=>'数据提交出错'];
                }
                $dd['id'] = $input['zid'];
                $data['username']=$input['adminName'];
                $data['sex'] = $input['sex'];
                $data['mobile']=$input['phone'];
                $data['email'] = $input['email'];
                $data['groupid']=$input['adminRole'];
                $call=Db::table('web_admin')->where($dd)->update($data);
                if($call){
                    return ['status'=>1,'msg'=>'操作成功'];
                   // $this->success('修改成功',url('admin/rbac/adminlist'));
                }else{
                    return ['status'=>2,'msg'=>'操作失败'];
                 //  $this->error('修改失败',url('admin/rbac/adminlist'));
                }
        }
    }
}

