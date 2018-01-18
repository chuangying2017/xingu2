<?php

namespace app\admin\controller;

use think\Controller;
use think\Exception;
use think\Log;
use think\Request;

class Gopower extends Common{

    public  function  index(){
             $data=\app\admin\model\Gopower::nodedata(input('post.serch_name'));
              return view('',['select'=>$data,'page'=>$data->render(),'count'=>count($data),'search'=>input('post.serch_name')]);
    }
    //展示添加前台节点
    public function addnode(){
        if(Request::instance()->isAjax()){
            return \app\admin\model\Gopower::addnode(input('post.'));
        }else{
            $data = \app\admin\model\Gopower::power_node_rule();
            return view('',['list'=>$data]);
        }
    }
    //展示编辑页面
    public function power_show($id){
            if(Request::instance()->isAjax()){
                try{
                    return \app\admin\model\Gopower::update(input('post.'),['id'=>$id])?['status'=>1,'msg'=>'修改成功']:['status'=>2,'msg'=>'修改失败'];
                }catch(Exception $e){
                    Log::error($e->getMessage());
                }
            }else{
                $data = \app\admin\model\Gopower::get($id);
                return view('poweredit',['power'=>$data]);
            }
    }
    //单个删除
    public function  delete_power(){
        if(Request::instance()->isAjax()){
            return \app\admin\model\Gopower::delete_power(input('id'));
        }
    }


}
