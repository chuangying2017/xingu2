<?php

namespace app\admin\model;

use think\Exception;
use think\Model;

class Report extends Model
{
    protected $table='web_recharge';

    public static function select_s($param){
            if(!empty($param['search_starttime']) && !empty($param['search_endtime'])){
                $time = strtotime($param['search_starttime'].'00:00:00'.','.$param['search_endtime'].'23:59:59');
                $arr['search_starttime'] = $param['search_starttime'];
                $arr['search_endtime'] = $param['search_endtime'];
                $map['recharge_date'] = array('between',$time);
            }elseif (!empty($param['search_starttime'])){
                 $time = strtotime($param['search_starttime'].'00:00:00');
                 $map['recharge_date'] = array('egt',$time);
                 $arr['search_starttime'] = $param['search_starttime'];
            }elseif (!empty($param['search_endtime'])){
                $time = strtotime($param['search_endtime'].'23:59:59');
                $map['recharge_date'] = array('elt',$time);
                $arr['search_endtime'] = $param['search_endtime'];
            }
            if(!empty($param['search_money'])){
                $map['bin_type'] = $param['search_money'];
                $arr['search_level']=$param['search_money'];
            }
            if(!empty($param['search_type'])){
                 $map['type'] = $param['search_type'];
                 $arr['search_type'] = $param['search_type'];
            }elseif(!empty($param['success_status'])){
                $map['success_status'] = $param['success_status'];
                $arr['success_status'] = $param['success_status'];
            }else{
                $map['success_status'] = 1;
                $arr['success_status'] = 1;
            }
            try{
                $report = new Report();
                $return=$report::where($map)->order('id desc')->paginate(20);
                return array($return,$arr,$return->render(),$report::count());
            }catch (Exception $e){
                   Log::init([
                       'type'=>'file',
                       'path'=>APP_APTH.'Admin/log_s/'
                   ]);
                   Log::error($e->getMessage());
            }
    }
}
