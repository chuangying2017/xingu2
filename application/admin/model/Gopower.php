<?php

namespace app\admin\model;

use think\Exception;
use think\Model;
use think\Validate;

class Gopower extends Model
{
    protected static $rule = [
        'name'=>'require',
        'pid'=>'require|number'
    ];
    protected static $message = ['name.require'=>'名称不能为空','pid.require'=>'栏目不能为空'];
    //添加前台节点
    public static function addnode($data){
            $self = new self;
            $validate = new Validate(self::$rule,self::$message);
            if(!$validate::token('','',['__token__'=>$data['token']])){
                return ['status'=>2,'msg'=>'请不要重复提交表单'];
            }
            if(!$validate->check($data)){
                return ['status'=>2,'msg'=>$validate->getError()];
            }
            $count=$self->where('name',$data['name'])->count();
            if($count){
                return ['status'=>2,'msg'=>'节点名称已存在'];
            }
            if($data['pid']>0){
                $data_one=$self->find($data['pid']);
                switch ($data_one->level){
                    case 0:
                        $data['path'] = $data_one->path.'-'.$data_one->id;
                        $data['level']=1;
                        $call = $self->allowField(true)->isUpdate(false)->save($data);
                        break;
                    case 1:
                        $data['path'] = $data_one->path.'-'.$data_one->id;
                        $data['level']=2;
                        $call = $self->allowField(true)->isUpdate(false)->save($data);
                        break;
                    case 2:
                        return ['status'=>2,'msg'=>'不给添加了喔！'];
                    default:
                        return ['status'=>2,'msg'=>'等级不存在'];
                }
            }else{
                $data['level'] = 0;
                $data['path'] = 0;
                $call=$self->allowField(true)->save($data);
            }
            if($call){
                return ['status'=>1,'msg'=>'添加成功'];
            }else{
                return ['status'=>2,'msg'=>'添加失败'];
            }
    }
    //展示前台节点
    public  static function nodedata($data){
        $new = new self;
        if(!empty($data)){
            $map['name|control_action'] = $data;
        }
        return $new->where($map)->paginate(20);
    }
    //展示节点
    public static function power_node_rule(){
            $self = new self;
            $all = $self->all();
            foreach ($all as &$value){
                switch ($value['level']){
                    case 0:
                        $value['tree']=$value['name'];
                        break;
                    case 1:
                        $value['tree']='| - '.$value['name'];
                        break;
                    case 2:
                        $value['tree'] = '| - - '.$value['name'];
                        break;
                    default:
                        return ['status'=>2,'msg'=>'等级有误002'];
                }
            }
            foreach ($all as $row){
                $array[]=$row->toArray();
                $arr[] = $row['path'].'-'.$row['id'];
            }
            array_multisort($arr,$array);
        return $array;
    }
    //删除节点
    public static function delete_power($id){
            try{
                $new = new self;
               $call = $new::destroy($id);
                return $call?['status'=>1,'msg'=>'删除成功']:['status'=>2,'msg'=>'删除失败'];
            }catch(Exception $e){
                \think\Log::error($e->getMessage());
            }
    }
}
