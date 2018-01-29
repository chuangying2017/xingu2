<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29 0029
 * Time: 下午 3:58
 */

namespace app\index\logic;


use think\Db;
use think\Model;

class User extends Model
{
    //计算迭代推荐人数
    public static function iteration($data){
        /**
         * @items 接收传过来的值 int
         * @uid 接收会员id int
         * **/
        $member = Db::name('member');//实例化一个会员表member
        $member_one = $member->where('id',$data['uid'])->find();
        $result_people = $member->where('recommend',$member_one['recommend'])->field('mobile,reg_time')->select();
        for($i=1;$i<11;$i++){
            if(!$result_people[$i-1]['mobile']){
                return ['status'=>2,'msg'=>'暂无数据'];
            }
            if($i == $data['num']){
                return $result_people;
            }else{
                $result_people = $member->where('recommend',$result_people[$i-1]['recommend'])->field('mobile,reg_time')->select();
            }
        }

    }
}