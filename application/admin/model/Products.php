<?php

namespace app\admin\model;

use think\Db;
use think\Exception;
use think\Model;
use think\Validate;

class Products extends Model
{
    protected $table='web_product';
    protected $createTime='create_date';
    protected $updateTime='';
    //添加产品
    public static  function product_data($data){
                $product = new Products;
                //这个验证是公共的
                /*      $role = [
                    'title'=>'require|min:3','class_id'=>'require|number',
                    'price'=>'require|float'
                ];
                $message = [
                    'title.require'=>'名字不能为空','title.min'=>'标题长度不够','price.require'=>'价格不能为空','price.float'=>'价格是小数点'
                ];
                $validate = new Validate($role,$message);
                if(!$validate->check($data)){
                        return ['status'=>2,'msg'=>$validate->getError()];
                            }
                if($data['name_bei']){
                    $product->name_bei=$data['name_bei'];
                }
                if($data['name_tian']){
                    $product->name_tian=$data['name_tian'];
                }
                if($data['gou_num']){
                    $product->gou_num = $data['gou_num'];
                }*/
                $product->allowField(true)->data($data)->save();
                return isset($product->id)?['status'=>1,'msg'=>'添加成功']:['status'=>2,'msg'=>'添加失败'];
    }
    //上架
    public static function shangjia($id){
            $new = new self;
            $result=$new->update(['id'=>$id,'status'=>1]);
              if($result){
                  return ['status'=>1,'msg'=>'上架成功'];
              }else{
                  return ['status'=>2,'msg'=>'上架失败'];
              }
    }
    //产品下架
    public static function xiajia($id){
        $new = new self;
        $result = $new->update(['id'=>$id,'status'=>'2']);
        if($result){
                return ['status'=>1,'msg'=>'下架成功'];
        }else{
            return ['status'=>2,'msg'=>'下架失败'];
        }
    }
    //编辑产品
    public static function edit_product($id,$data){
                $new = new self;
     /*   $role = [
            'title'=>'require|min:3','type_id'=>'require|number',
            'price'=>'require|float'
        ];
        $message = [
            'title.require'=>'名字不能为空','title.min'=>'标题长度不够','price.require'=>'价格不能为空','price.float'=>'价格是小数点'
        ];
                $validate = new Validate($role,$message);
                if(!$validate->check($data)){
                    return ['status'=>2,'msg'=>$validate->getError()];
                }*/
                $result=$new->update($data,['id'=>$id]);
                if($result){
                    return ['status'=>1,'msg'=>'编辑成功'];
                }else{
                    return ['status'=>2,'msg'=>'编辑失败'];
                }
    }
    //删除图片
    public static function del_img($id){
            $new = Db::name('img');
            $where = $new->find($id);
            $unlink1 = unlink($where['path_img']);
            $unlink2 = unlink($where['path_thumb']);
            $unlink3 = unlink($where['path_xiao']);
            if($unlink1 && $unlink2 && $unlink3){
                $call = $new->delete($id);
                return $call?['status'=>1,'msg'=>'删除成功']:['status'=>2,'msg'=>'删除失败0010'];
            }else{
                return ['status'=>2,'msg'=>'图片删除出错'];
            }
    }
    //更新主图
    public static function zhutu_fou($id){
        $table = Db::name('img')->where('id',$id)->update(['status'=>2]);
        return $table?['status'=>1,'msg'=>'更新成功']:['status'=>2,'msg'=>'更新失败'];
    }
    //更新主图
    public static function zhutu_shi($id){
        $table = Db::name('img');
        $shang=$table->field('pid')->find($id);
        $select=$table->where('pid',$shang['pid'])->select();
        for($i=0;$i<count($select);$i++){
            $table->where('pid',$select[$i]['pid'])->update(['status'=>2]);
        }
        $clas = $table->where('id',$id)->update(['status'=>1]);
        return $clas?['status'=>1,'msg'=>'操作成功']:['status'=>2,'msg'=>'操作失败'];
    }
    //时间日期查询产品
    public static  function index_call($data){
            if(!empty($data['search_starttime'] && !empty($data['search_endtime']))){
                $str = strtotime($data['search_starttime'].'00:00:00'.','.$data['search_endtime'].'23:59:59');
                $map['create_time'] = array('between',$str);
                $call_data['search_starttime']=$data['search_starttime'];
                $call_data['search_endtime'] = $data['search_endtime'];
            }elseif (!empty($data['search_starttime'])){
                $str = strtotime($data['search_starttimeh']);
                $map['create_time'] = array('elt',$str);
                $call_data['search_starttime'] = $data['search_starttime'];
            }elseif (!empty($data['search_endtime'])){
                 $str = strtotime($data['search_endtime']);
                 $map['create_time'] = array('egt',$str);
                 $call_data['search_endtime'] = $data['search_endtime'];
            }else{
                $map['status'] = 1;
            }
            if(!empty($data['search_title'])){
                $map['title|price']=$data['search_title'];
                $call_data['search_title'] = $data['search_title'];
            }
            return array($map,$call_data);
    }
    //删除单一个商品
    public static function product_delete($id){
        $new = new self;
        $table_imgae = db('img');
        $select=$table_imgae->where('pid',$id)->select();
        try{
            for($i=0;$i<count($select);$i++){
                unlink($select[$i]['path_img']);
                unlink($select[$i]['path_thumb']);
                unlink($select[$i]['path_xiao']);
                $table_imgae->delete($select[$i]['id']);
            }
                $result = $new::destroy($id);
                if($result){
                    return ['status'=>1,'msg'=>'删除成功'];
                }else{
                    return ['status'=>2,'msg'=>'删除失败'];
                }
        }catch(Exception $e){
            \think\Log::init(['type'=>'File','path'=>APP_PATH.'log_zhi/']);
            \think\Log::error($e->getMessage());
        }
    }
    //添加图片信息
    public static function tianjiamsg($data,$id){
            $rule = ['images_name'=>'require','images_sponsor'=>'require'];
            $message = ['images_name'=>'名称不能为空','images_sponsor.require'=>'内容不能为空'];
            $validate = new Validate($rule,$message);
            if(!$validate->check($data)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $table_img = Db::name('image')->where('id',$id)->update(['images_name'=>$data['images_name'],'images_sponsor'=>$data['images_sponsor']]);
            return $table_img?['status'=>1,'msg'=>'添加成功']:['status'=>2,'msg'=>'添加失败'];
    }
}
