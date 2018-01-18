<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Validate;
use think\Db;
class record extends \app\common\controller\Common
{
    public function index(){//奖金记录
        if(Request::instance()->isAjax()) {
            $uid = Session::get('uid');
            $input = input('post.');
            $total = count(Db::name('bonus')->where('uid',$uid)->where('type','>','0')->where('type','<','5')->order('create_date desc')->select());
            $page = $input['pageIndex'];
            $page = $page-1;
            $start = $page*$input['PageSize'];
            $result = Db::name('bonus')->where('uid',$uid)->where('type','>','0')->where('type','<','5')->order('create_date desc')->limit($start,$input['PageSize'])->select();
            return ['status'=>1,'total'=>$total,'result'=>$result];
        }
        return $this->fetch();
    }

    public function inscl(){//投资记录

        if(Request::instance()->isAjax()) {
            $uid = Session::get('uid');
            $input = input('post.');
            $page = $input['pageIndex'];
            $page = $page-1;
            $start = $page*$input['PageSize'];
            $total = count(Db::name('orders')->where('uid',$uid)->order('create_date desc')->select());
            $result[0] = Db::table('web_orders')->alias('a')->join('web_product w','a.pid=w.id')->field('a.*,w.title')->where('uid',$uid)->order('a.create_date desc')->limit($start,$input['PageSize'])->select();
            foreach($result[0] as &$value){
                $value['lixi'] = $value['price'] * (database(2)['deduct_s'] / 100);
            }
			if(count($result[0])>0){
                for($i = 0;$i < count($result[0]);$i++){
                    $result[0][$i]['create_date'] = date('Y-m-d H:i:s',  $result[0][$i]['create_date']);
                }
            }

            $total += count(Db::name('torder')->where('uid',$uid)->order('create_time desc')->select());
            $result[1] = Db::table('web_torder')->alias('a')->join('web_product w','a.pid=w.id')->field('a.*,w.title')->where('uid',$uid)->order('a.create_time desc')->limit($start,$input['PageSize'])->select();

            if(count($result[1])>0){
                for($i = 0;$i < count($result[1]);$i++){
                    $result[1][$i]['create_time'] = date('Y-m-d H:i:s',  $result[1][$i]['create_time']);
                }
            }


            return ['status'=>1,'total'=>$total,'result'=>$result];
        }
        return $this->fetch();
    }
	
	public function insla(){//返现记录
		    if(Request::instance()->isAjax()) {
            $uid = Session::get('uid');
            $input = input('post.');
            $total = count(Db::name('bonus')->where('uid',$uid)->where('type','=','0')->order('create_date desc')->select());
            $page = $input['pageIndex'];
            $page = $page-1;
            $start = $page*$input['PageSize'];
            $result = Db::name('bonus')->where('uid',$uid)->where('type',0)->where('status',2)->order('create_date desc')->limit($start,$input['PageSize'])->select();
						if(count($result)>0){
                for($i = 0;$i < count($result);$i++){
                    $result[$i]['create_date'] = date('Y-m-d H:i:s',  $result[$i]['create_date']);
                }
            }
            return ['status'=>1,'total'=>$total,'result'=>$result];
        }
        return $this->fetch();
	}
	
	    //删除撤销众筹的订单
    public function revocation(){
        if(Request::instance()->isAjax()){
            $Input = input('post.id');
            $result = Db::name('orders')->where(['uid'=>Session::get('uid'),'id'=>$Input,'type'=>1])->delete();
            if($result){
                return ['status'=>1,'msg'=>'撤销成功','url'=>url('index/index/index')];
            }else{
                return ['status'=>2,'msg'=>'撤销失败'];
            }
        }
    }

    //删除撤销分红的订单
    public function my_cexiaos(){
        if(Request::instance()->isAjax()){
            $Input = input('post.id');
            $result = Db::name('torder')->where(['uid'=>Session::get('uid'),'id'=>$Input,'status'=>0])->delete();
            if($result){
                return ['status'=>1,'msg'=>'撤销成功','url'=>url('index/record/inscl')];
            }else{
                return ['status'=>2,'msg'=>'撤销失败'];
            }
        }
    }


    public function a_bonus(){//分红返利记录
        if(Request::instance()->isAjax()) {
            $uid = Session::get('uid');
            $input = input('post.');
            $total = count(Db::name('bonus')->alias('a')->join('__TORDER__ b','a.order_id=b.id')->where('a.uid',$uid)->where('a.type',5)->field('a.*,b.order_no')->order('a.create_date desc')->select());
            $page = $input['pageIndex'];
            $page = $page-1;
            $start = $page*$input['PageSize'];
            $result = Db::name('bonus')->alias('a')->join('__TORDER__ b','a.order_id=b.id')->where('a.uid',$uid)->where('a.type',5)->field('a.*,b.order_no')->order('a.create_date desc')->limit($start,$input['PageSize'])->select();
            return ['status'=>1,'total'=>$total,'result'=>$result];
        }
        return $this->fetch();
    }

	
}