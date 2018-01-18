<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Webconfig extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $webconfig = Db::name('webconfig')->where('id',1)->update(['value'=>json_encode($input)]);
            if($webconfig){
                echo json_encode(['status'=>1,'msg'=>'修改成功']);
                exit;
            }else{
                echo json_encode(['status'=>2,'msg'=>'修改失败']);
                exit;
            }
        }
        //展示基本设置
        $getparma = setparma(1);
        return $this->fetch('index',['config'=>$getparma]);
    }
    //展示参数设置
    public function setbonus(){
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $webconfig = Db::name('webconfig')->where('id',2)->update(['value'=>json_encode($input)]);
            if($webconfig){
                return ['status'=>1,'msg'=>'操作成功'];
            }else{
                return ['status'=>2,'msg'=>'操作失败'];
            }
        }
        $call=setparma(2);
        return view('setbonus',['setparameter'=>$call]);
    }

    public function banklist(){
        $name = Db::name('bank')->where('status',1)->order('sort desc')->paginate(20);
        return view('',['list'=>$name,'page'=>$name->render(),'count'=>count($name)]);
    }
    //添加银行卡
    public function bankadd(Request $request){
        if($request->isAjax()){
                $input = input('post.');
                $call = Db::name('bank')->insertGetId($input);
                if($call){
                    return ['status'=>1,'msg'=>'添加成功'];
                }else{
                    return ['status'=>2,'msg'=>'添加失败'];
                }
        }else{
            return view();
        }
    }
    //展示编辑银行
    public function bank_edit(Request $request){
        if($request->isAjax()){
            $input = input('post.');
            $name = Db::name('bank')->update($input);
            if($name){
                return ['status'=>1,'msg'=>'操作成功'];
            }else{
                return ['status'=>2,'msg'=>'操作失败'];
            }
        }else{
            $name = Db::name('bank')->find(request()->get('id'));
            return view('bankedit',['bank'=>$name]);
        }
    }
    //删除银行
    public function bankdel(){
        if(Request::instance()->isAjax()){
            $call = Db::name('bank')->delete(input('post.id'));
            if($call){
                return ['status'=>1,'msg'=>'删除成功'];
            }else{
                return ['status'=>2,'msg'=>'删除失败'];
            }
        }
    }
    //停用
    public function bank_stop(){
        if(Request::instance()->isAjax()){
         $name = Db::name('bank')->where('id',input('id'))->update(['status'=>2]);
         if($name){
             return ['status'=>1,'msg'=>'操作成功'];
         }else{
             return ['status'=>2,'msg'=>'操作失败'];
         }
        }
    }
    //起用
    public function bank_start(){
        if(Request::instance()->isAjax()){
            $name = Db::name('bank')->where('id',input('id'))->update(['status'=>1]);
            if($name){
                return ['status'=>1,'msg'=>'操作成功'];
            }else{
                return ['status'=>2,'msg'=>'操作失败'];
            }
        }
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
