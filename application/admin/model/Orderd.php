<?php

namespace app\admin\model;

use think\Db;
use think\Model;
use think\Session;

class Orderd extends Model
{
    protected $table='web_order';
    //修改订单状态
    public static function order_edit($data){
            $uid = Session::get('user_id');
            $self = new self();
            if($data['status'] != 2 && $data['status'] != 3 && $data['status'] != 5 && $data['status']!=6){
                return ['status'=>2,'msg'=>'数据不存在'];
            }

            $result = $self->where('id',$data['id'])->update(['status'=>$data['status'],'admin_id'=>$uid,'update_time'=>time()]);
            $self_s=$self->get($data['id']);
            if($data['status'] == 6){
                $database = database(2);
                $yuan=(float)$self_s->total - ((float)$self_s->total * ($database['tuihuo'] / 100));
                $result_s= Db::name('member')->where('id',$self_s->uid)->setInc('money',$yuan);
            }else{
                $result_s = true;
            }
           if($result && $result_s){
               return ['status'=>1,'msg'=>'操作成功'];
           }else{
               return ['status'=>2,'msg'=>'操作失败'];
           }
    }
    //搜索
    public static  function search($data){
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
            $map['a.create_date'] = array('between',$strtime);
        }elseif(!empty($data['search_starttime'])){
            $strtime = strtotime($data['search_starttime'].'00:00:00');
            $map['a.create_date']=array('egt',$strtime);
            $search['search_starttime'] = $data['search_starttime'];
        }elseif (!empty($data['search_endtime'])){
            $strtime = strtotime($data['search_endtime'].'00:00:00');
            $map['a.create_date']=array('elt',$strtime);
            $search['search_endtime'] = $data['search_endtime'];
        }
         if(!empty($data['order_number'])){
              if(!preg_match("/^1[3|4|5|7|8]\\d{9}$/",$data['order_number'])){
                $map['a.order_no'] = $data['order_number'];
            }else{
                $map['m.mobile']=$data['order_number'];
            }
             $search['order_number'] = $data['order_number'];
         }
        if (!empty($data['search_type']) && $data['search_type'] == 1){
            $map = array('a.status'=>1,'a.type'=>1);
            $search['search_type'] = $data['search_type'];//待支付
        }else if(!empty($data['search_type']) && $data['search_type'] == 2){
            $map = array('a.status'=>1,'a.type'=>2);
            $search['search_type'] = $data['search_type'];//已支付,投资
        }else if(!empty($data['search_type']) && $data['search_type'] == 3){
            $map = array('a.status'=>2,'a.type'=>2);
            $search['search_type'] = $data['search_type'];//已返利
        }else{
            $search['search_type'] = 2;
        }
        return array($map,$search);
    }
}
