<?php

namespace app\admin\controller;

use app\admin\model\Products;
use think\Db;
use think\Exception;
use think\Image;
use think\Log;
use think\Request;
use think\Session;
use think\Validate;
class Product extends Common{
    public $imgs;
    //展示产品列表
    public  function  index(){
//        dump($_GET);
        $where = [];
//        $call = Products::index_call(input('post.'));
        $keyword = input('get.');
        if($keyword){
            if($keyword['search_type']){
                $where['m.type'] = ['like','%'.$keyword['search_type'].'%'];
                $this -> assign('type',$keyword['search_type']);
            }else{
                $where['m.type'] = ['like','%0%'];
            }
        }else{
            $where['m.type'] = ['like','%0%'];
        }
        $name = Db::name('product')
            ->alias('a')
            ->join('web_infinite_class m','a.type_id=m.id')
            ->where($where)
            ->field('a.*,m.title as title_s,m.type')
            ->select();
        for($i = 0;$i < count($name);$i++){
            if($name[$i]['type'] == 1){
                $name[$i]['beishu'] = $name[$i]['price']*$name[$i]['name_bei'];
            }
        }
//        dump($name);
        $this ->assign('list',$name);
        return $this->fetch();
    }


    public function class_type_sele(){
        $keyword = input('post.');
        $list = Db::name('infinite_class')->where('id',$keyword['id'])->find();
        if($list['type'] == 1){
            return ['status'=>1];
        }else{
            return ['status'=>2];
        }
    }



    //产品下架
    public function xiajia(){
            if(Request::instance()->isAjax()){
                    return Products::xiajia(input('get.id'));
            }
    }
    //产品上架
    public function shangjia(){
            if(Request::instance()->isAjax()){
                return Products::shangjia(input('get.id'));
            }
    }
    //展示添加产品
    public  function productadd(){
            $class = Db::name('infinite_class')->where('status',1)->select();
            $list=$this->rescursion($class);
            $this->assign('list1',$list);
            return view();
    }
    //产品添加数据
    public function product_data(Request $request){
                    if($request->isAjax()){
                     return Products::product_data(input('post.'));
                    }
    }
    //编辑产品
    public function product_edit($id){
            if(Request::instance()->isAjax()){
                return Products::edit_product($id,input('post.'));
            }
         $table_class = Db::name('infinite_class');
         $call_back= Products::get($id);

         $call_value=$table_class->where('status',1)->select();
         $type = $table_class->where('id',$call_back['type_id'])->find();
         $call_=$this->rescursion($call_value);
        return view('',['full_page'=>$call_back->toArray(),'list1'=>$call_,'type'=>$type['type']]);
    }
    //添加产品图片页面
    public function productimg($id=null){
       $name = Db::name('img')->where('pid',$id)->select();
       return view('',['id'=>$id,'list'=>$name,'count'=>count($name)]);
    }
    //添加图片
    public function productaddimg($id=null){
        if(Request::instance()->isPost()){
            $file = request()->file();
            $arr=$this->proadd($file);
            $table_image = Db::name('img')->insert(['pid'=>input('post.product_id'),'path_img'=>$arr[0],'path_thumb'=>$arr[1],'path_xiao'=>$arr[2]]);
            if($table_image){
                return json_encode($arr[3]);
            }else{
                exit('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "上传失败!文件打开失败"}, "id" : "id"}');
            }
        }
        return view('',['id'=>$id]);
    }
    //进行php上传文件处理
    public function proadd($files){
                if($files){
                    $arr = array();
                    foreach ($files as $v){
                                $rand = 'mb_'.rand(1111,9999).time();
                                 $image=Image::open($v);
                                $width = $image->width();
                                $height = $image->height();
                                $type = $image->type();
                                $typeimg = '.'.$type;
                                $a = 'upload'.DS.'images'.DS.$rand;
                                $pa=str_replace("\\",'/',$a);
                                $b=$pa.'thumb'.$typeimg;
                                $c=$pa.'xiao'.$typeimg;
                                $d = (string)$pa.$typeimg;
                                $image->crop($width,$height)->save((string)$d);
                                $image->crop($width,$height)->save((string)$d);
                                $image->crop('220','220')->save((string)$b);
                                $result=$image->crop('120','120')->save((string)$c);
                                $arr[]=(string)$d;
                                $arr[]=(string)$b;
                                $arr[]=(string)$c;
                                $arr[]=$result;
                    }
                    return $arr;
                }else{
                    return null;
                }
    }
    //删除图片
    public function delete_img(){
        if(Request::instance()->isAjax()){
            return Products::del_img(input('id'));
        }
    }
    //否主图
    public function zhutu_fou(){
            if(Request::instance()->isAjax()){
                return Products::zhutu_fou(input('id'));
            }
    }
    //主图为是
    public function zhutu_shi(){
            if(Request::instance()->isAjax()){
                return Products::zhutu_shi(input('id'));
            }
    }
    //删除产品
    public function product_delete(){
        if(Request::instance()->isAjax()){
                return Products::product_delete(input('post.id'));
        }
    }
    //展示分类
    public  function productclass(){
        $name = Db::name('class')->select();
        $this->assign('count',count($name));
        $this->assign('list',$name);
        return view();
    }
    //展示添加分类页面
    public  function product_add_class(){
        return view();
    }
    //编辑分类
    public  function productclassedit(){
        if(Request::instance()->isGet()){
                $name = Db::name('class')->find(input('param.id'));
                $this->assign('row',$name);
            }
        if(Request::instance()->isAjax()){
                  $name = Db::name('class')->update(request()->post());
                  if($name){
                      return ['status'=>1,'msg'=>'编辑成功'];
                  }else{
                      return ['status'=>2,'msg'=>'编辑失败'];
                  }
        }
            return view();
    }
    //展示文章分类
    public function product_class_del(){
        if(Request::instance()->isAjax()){
                $name = Db::name('class')->delete(input('post.id'));
                if($name){
                    return ['status'=>1,'msg'=>'操作成功'];
                }else{
                    return ['status'=>2,'msg'=>'操作失败'];
                }
        }
    }
    //展示列表
    public  function classxian(){
        $this->wuxian();
        return view('classshow');
    }
    //展示无限分类添加页面
    public  function wuxian(){
            $name = Db::name('infinite_class')->select();
             if($name){
                 $list=$this->rescursion($name);
             }else{
                 $list=null;
             }
            $this->assign('list',$list);
            return view();
    }
    //用实现无限分类表
    public  function rescursion($list){
            foreach($list as &$vs){
             $count =substr_count($vs['path'],'-');
             if($vs['pid'] > 0){
                    $intega = '| '.str_repeat('★ ',$count);
             }else{
                    $intega = '';
                }
                $vs['tree']  = $intega.$vs['title'];
            }
           foreach ($list as $v){
                $arr[]=$v['path'].'-'.$v['id'];
           }
           array_multisort($arr,$list);
           return $list;
    }
    //添加无限分类
    public function  product_class_adds(){
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $rule = [
                'pid'=>'require|number',
                'title'=>'require|min:2'
                    ];
            $message = [
                'pid.require'=>'分类不能为空',
                'title.min'=>'类名称三个字以上'
            ];
            $validate = new Validate($rule,$message);
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            if($input['pid']>0){
                $name = Db::name('infinite_class')->where('id',$input['pid'])->find();
                $str = $name['path'].'-'.$name['id'];
                $success=Db::name('infinite_class')->insert(['title'=>$input['title'],'pid'=>$input['pid'],'path'=>$str,'content'=>$input['content'],'type'=>$input['type']]);
            }else{
                $input['path'] = 0;
                $success= Db::name('infinite_class')->insertGetId($input);
            }
            if($success){
                return ['status'=>1,'msg'=>'添加分类成功'];
            }else{
                return ['status'=>2,'msg'=>'添加分类失败'];
            }
        }
    }
    //禁止
    public function user_stop(){
        if(Request::instance()->isAjax()){
            $name = Db::name('infinite_class')->update(['id'=>input('get.id'),'status'=>2]);
            if($name){
                   return ['status'=>1,'msg'=>'已禁用'];
            }else{
                return ['status'=>2,'msg'=>'禁用失败'];
            }
        }
    }
    //启动
    public function user_start(){
        if(Request::instance()->isAjax()){
            $name = Db::name('infinite_class')->update(['id'=>input('get.id'),'status'=>1]);
             if($name){
                 return ['status'=>1,'msg'=>'已启用'];
             }else{
                 return ['status'=>2,'msg'=>'启用失败'];
             }
        }
    }
    //编辑无限分类
    public function product_wuxian_class(){
                if(Request::instance()->isAjax()){
                            $input = input('post.');
                            $r = [
                                'pid'=>'require|number',
                                'title'=>'require',
                                'id'=>'require',
                                'pid_id'=>'require|number'
                            ];
                            $message = [
                                'pid.require'=>'选项不能为空',
                                'title.require'=>'名字不能为空',
                                'id.require'=>'id不能为空',
                                'pid_id.require'=>'不能为空'
                            ];
                            $validate = new Validate($r,$message);
                    $call=$validate::token('','',['__token__'=>$input['token']]);
                      if(!$call){
                          return ['status'=>2,'msg'=>'请不要重复提交表单'];
                      }
                      if(!$validate->check($input)){
                          return ['status'=>2,'msg'=>$validate->getError()];
                      }
                      $infinite_class = Db::name('infinite_class');
                      try{
                            if($input['pid_id']>$input['pid'] && $input['pid_id']!=$input['pid'] ){
                                return ['status'=>2,'msg'=>'不能挂比自己低级'];
                            }else{
                                if($input['id'] == $input['pid']){
                                    $rollback=$infinite_class->update(['id'=>$input['id'],'title'=>$input['title'],'content'=>$input['content']]);
                                }elseif ($input['id']!=$input['pid'] && $input['pid'] > 0){
                                    $branches=$infinite_class->find($input['pid']);
                                    $variadio=$branches['path'].'-'.$branches['id'];
                                    $rollback=$infinite_class->update(['id'=>$input['id'],'title'=>$input['title'],'content'=>$input['content'],'pid'=>$branches['id'],'path'=>$variadio]);
                                }else{
                                    $rollback=$infinite_class->update(['id'=>$input['id'],'title'=>$input['title'],'content'=>$input['content'],'pid'=>$input['pid'],'path'=>'0']);
                                }
                            }
                          if($rollback){
                              return ['status'=>1,'msg'=>'修改成功'];
                          }else{
                              return ['status'=>2,'msg'=>'修改失败'];
                          }
                      }catch(Exception $e){
                          Log::init(['type'=>'File','path'=>APP_PATH.'log_zhi/']);
                          Log::error($e->getMessage());
                      }
                }
                $data = Db::name('infinite_class');
                $date=$data->where('status',1)->select();
                $result=self::rescursion($date);
                $id = request()->param('id');
                $call_back=$data->where('id',$id)->find();
//                dump($call_back);
                return view('',['id'=>$call_back,'list'=>$result]);
    }


    public function class_type_up(){
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $r = [
                'pid'=>'require|number',
                'title'=>'require',
                'id'=>'require',
                'pid_id'=>'require|number'
            ];
            $message = [
                'pid.require'=>'选项不能为空',
                'title.require'=>'名字不能为空',
                'id.require'=>'id不能为空',
                'pid_id.require'=>'不能为空'
            ];
            $validate = new Validate($r,$message);
            $call=$validate::token('','',['__token__'=>$input['token']]);
            if(!$call){
                return ['status'=>2,'msg'=>'请不要重复提交表单'];
            }
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $infinite_class = Db::name('infinite_class');
            try{
                if($input['pid_id']>$input['pid'] && $input['pid_id']!=$input['pid'] ){
                    return ['status'=>2,'msg'=>'不能挂比自己低级'];
                }else{
                    if($input['id'] == $input['pid']){
                        $rollback=$infinite_class->update(['id'=>$input['id'],'title'=>$input['title'],'content'=>$input['content'],'name_bei'=>$input['name_bei'],'name_tian'=>$input['name_tian']]);
                    }elseif ($input['id']!=$input['pid'] && $input['pid'] > 0){
                        $branches=$infinite_class->find($input['pid']);
                        $variadio=$branches['path'].'-'.$branches['id'];
                        $rollback=$infinite_class->update(['id'=>$input['id'],'title'=>$input['title'],'content'=>$input['content'],'pid'=>$branches['id'],'path'=>$variadio,'name_bei'=>$input['name_bei'],'name_tian'=>$input['name_tian']]);
                    }else{
                        $rollback=$infinite_class->update(['id'=>$input['id'],'title'=>$input['title'],'content'=>$input['content'],'pid'=>$input['pid'],'path'=>'0','name_bei'=>$input['name_bei'],'name_tian'=>$input['name_tian']]);
                    }
                }
                if($rollback){
                    return ['status'=>1,'msg'=>'修改成功'];
                }else{
                    return ['status'=>2,'msg'=>'修改失败'];
                }
            }catch(Exception $e){
                Log::init(['type'=>'File','path'=>APP_PATH.'log_zhi/']);
                Log::error($e->getMessage());
            }
        }
    }


    //状态禁用为2
    public function  product_list_status(){
        if(Request::instance()->isAjax()){
            $id = input('get.id');
            $name = Db::name('product')->where('id',$id)->update(['status'=>'2']);

            if($name){
                return ['status'=>1,'msg'=>'操作成功'];
            }else{
                return ['status'=>2,'msg'=>'操作失败'];
            }
        }
    }
    //状态启动1
    public function  product_list_start(){
        if(Request::instance()->isAjax()){
            $id = input('get.id');
            $name = Db::name('product')->where('id',$id)->update(['status'=>'1']);
            if($name){
                return ['status'=>1,'msg'=>'操作成功'];
            }else{
                return ['status'=>2,'msg'=>'操作失败'];
            }
        }
    }
    //展示修改页面
    public function product_edit_show($id){
            $select = Db::name('product')->find($id);
             $arr=explode(',',$select['image']);
             Session::set('edit_id',$id);
             $select['image1'] = $arr[0];
             $select['image2'] = $arr[1];
             $this->assign('edits',$select);
             $name = Db::name('class')->where('type',3)->select();
             $this->assign('list1',$name);
              return view();
    }
    //上传文件
    public function upload(){
            if(Request::instance()->isAjax()) {
                $file = request()->file();
               $table_product= Db::name('product');
                foreach ($file as $v) {
                    $rand = rand(1111, 9999) . time();
                    $img = Image::open($v);
                    $w=$img->width();
                    $h=$img->height();
                    $type = '.'.$img->type();
                    $result = $img->crop($w,$h)->save('upload/admin/'.$rand.$type);
                    if(empty(Session::get('img_id'))){
                        $imgid=$table_product->insertGetId(['image'=>'upload/admin/'.$rand.$type]);
                        Session::set('img_id',$imgid);
                    }else{
                        $add=$table_product->find(Session::get('img_id'));
                        $table_product->update(['id'=>$add['id'],'image'=>$add['image'].','.'upload/admin/'.$rand.$type]);
                    }

                    if ($result) {
                        return json_encode(['status' => 1, 'msg' => '上传成功']);
                    } else {
                        return json_encode(['status' => 2, 'msg' => '上传失败']);
                    }
                }

            }
    }
    //删除分类
    public function product_delete_class(){
        if(Request::instance()->isAjax()){
            $class = Db::name('infinite_class');
            $id = input('post.id');
            $result=$class->where('pid',$id)->find();
            if($result){
                return ['status'=>2,'msg'=>'请先删除下一级'];
            }
            try{
                $r = $class->delete($id);
                if($r){
                    return ['status'=>1,'msg'=>'删除成功'];
                }else{
                    return ['status'=>2,'msg'=>'删除失败'];
                }
            }catch (Exception $e){
                Log::init(['type'=>'File','path'=>APP_PATH.'log_zhi/']);
                Log::error($e->getMessage());
            }

        }
    }
    //添加文章分类
    public function product_add_article(){
        if(Request::instance()->isAjax()){
            $input = input('post.');
            $rule = ['type_name'=>'require'];
            $g = ['type_name.require'=>'必填'];
            $validate = new Validate($rule,$g);
            if(!$validate->check($input)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            try{
                $input['type'] = 1;
                $name = Db::name('class')->insertGetId($input);
                if($name){
                    return ['status'=>1,'msg'=>'添加成功'];
                }else{
                    return ['status'=>2,'msg'=>'添加失败'];
                }
            }catch(Exception $e){
                Log::init([
                    'type'=>'file','path'=>APP_PATH.'Admin/logs/'
                ]);
                Log::error($e->getMessage());
            }

        }
    }
    //展示添加圖片介紹
    public function product_content($id){
            $table_img = Db::name('image');
            $find = $table_img->where('id',$id)->find();
            return view('',['message_row'=>$find]);
    }
    public function product_conten($id){
        if(Request::instance()->isAjax()){
            return Products::tianjiamsg(input('post.'),$id);
        }
    }


    public function  chan_config(){//投资产品返利设置

        return view();

    }


}
