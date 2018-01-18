<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Image;
use think\Request;
use think\Validate;

class Article extends Controller
{
    private $names;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $name = Db::name('article')->select();
        foreach($name as &$value){
                $one_array = Db::name('class')->where('id',$value['art_type'])->field('id,type_name')->find();
                $value['type'] = $one_array['type_name'];
        }
        $this->assign('list',$name);
        return  $this->fetch();
    }
            //展示文章内容
    public  function articlezhang($id){
            if(Request::instance()->isGet()){
                $name = Db::name('article')->find($id);

            }
            $this->assign('article_row',$name);
            return $this->fetch('article');
    }
            //添加文章，首先展示文章内容
    public  function  articleadd(){
        if(Request::instance()->isAjax()){
                    $input = input('post.');
                    $rules = [
                        'art_type'=>'require',
                        'art_title'=>'require|min:3',
                        'art_content'=>'require|min:5'
                    ];
                    $message = [
                        'art_type.require'=>'类型不能空',
                        'art_title.require'=>'标题不能为空',
                        'art_content.min'=>'长度不够'
                    ];
                    $validate = new Validate($rules,$message);
                    if(!$validate->check($input)){
                     return ['status'=>2,'msg'=>$validate->getError()];
                    }
                    $input['art_time'] = time();
                    $name = Db::name('article')->insert($input);
                    if($name){
                        return ['status'=>1,'msg'=>"添加成功"];
                    }else{
                        return ['status'=>2,'msg'=>'添加失败'];
                    }
        }else{
            $name = Db::name('class')->where('type',1)->select();
            $this->assign('list1',$name);
            return view();
        }

    }
            //批量删除文章
    public  function datadel_article(){
                if(Request::instance()->isAjax()){
                        $str = input('get.str');
                        if(empty($str)){
                            return ['status'=>2,'msg'=>'请选择要删除的数据'];
                        }
                        $str = trim($str,',');
                        $name = Db::name('article')->delete($str);
                        if($name){
                            return ['status'=>1,'msg'=>'批量删除成功'];
                        }else{
                            return ['status'=>2,'msg'=>'批量删除失败'];
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
    public function articleedit($id)
    {
            if(Request::instance()->isAjax()){
                    $input = input('post.');
                    $input['art_content'] = $input['editorvalue'];
                    $input['art_time'] = time();
                    unset($input['editorvalue']);
           /*         $pp=htmlspecialchars($input['art_content']);
                    $oner=explode('&quot;',$pp);
                        $ff = trim($oner[3],' ');
                        $image=Image::open('.'.$ff);
                        $image->thumb('300','300')->save('./hh.jpg');*/
                    $edit = Db::name('article')->field('art_title,art_source,art_author,art_type,art_content,art_time')->update($input);
                    if($edit){
                        return ['status'=>1,'msg'=>'修改成功'];
                    }else{
                        return ['status'=>2,'msg'=>'修改失败'];
                    }
            }
             if(Request::instance()->isGet()){
                    $name = Db::name('article')->find($id);
             }
             $names = Db::name('class')->where('type',1)->select();
             $this->assign('list1',$names);
             $this->assign('article_row',$name);
             return view();
    }


        //点击的时候停用
        public function article_stop($id){
                    if(Request::instance()->isAjax()){
                        $name = Db::name('article')->update(['art_status'=>'2','id'=>$id]);
                        if($name){
                            return ['status'=>1,'msg'=>'停用成功'];
                        }else{
                            return ['status'=>2,'msg'=>'停用失败'];
                        }
                    }
        }

        //点击的时候启用
        public function article_start($id){
            if(Request::instance()->isAjax()){
                $name = Db::name('article')->update(['art_status'=>'1','id'=>$id]);
                if($name){
                    return ['status'=>1,'msg'=>'启用成功'];
                }else{
                    return ['status'=>2,'msg'=>'启用失败'];
                }
            }
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
    //删除文章单挑记录
    public function delete($id)
    {
            if(Request::instance()->isAjax()){
                $name = Db::name('article')->delete($id);
                if($name){
                    return ['status'=>1,'msg'=>'删除成功'];
                }else{
                    return ['status'=>2,'msg'=>'删除失败'];
                }
            }
    }
}
