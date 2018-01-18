<?php
namespace app\Admin\controller;
use app\Admin\controller\Common;
use app\admin\model\Logs;
use think\Db;
use think\Request;
use think\Session;
class Index extends Common
{
    public function index()
    {
        //左侧菜单
        $data['id'] = session::get('groupid');
        $role_row = Db::name('role')->where($data)->find();
            if(session::get('user_id')==1){
                $list1 = Db::name('power')->where('level',0)->select();
                $list2 = Db::name('power')->where('level',1)->select();
            }elseif (!empty($role_row['power_id'])){
                $map['level'] = 0;
                $map['id'] = array('in', $role_row['power_id']);
                $list1 = Db::name('power')->order('sort ASC')->where($map)->select();
                       // file_put_contents('G:/sql.txt',$list1);
                $info['level'] = 1;
                $info['id'] = array('in', $role_row['power_id']);
                $list2 =Db::name('power')->where($info)->select();
            }
            $this->assign('list1',$list1);
            $this->assign('list2',$list2);
            return $this->fetch();
    }

    public  function welcome(){
        $systeminfo = getSystemInfo();
        $this->assign('systeminfo', $systeminfo);
        return view();
    }

    //退出
    public function logout(){
        $request = Request::instance();
        $data['logip'] = $request->ip();
        $data['lasttime'] = time();
        $data['id'] = session::get('groupid');
        if($data['id']){
            Db::name('admin')->update($data);
        }
        session::clear();
        $this->redirect('admin/login/login');
    }

}
