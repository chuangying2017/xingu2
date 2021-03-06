<?php

namespace app\admin\controller;
use think\Db;
use think\Request;

class Orderd extends Common
{
    public function index(){
//        dump($_GET);
        $where = [];
        $posui = [];
        $keyword = input('get.');
        if($keyword){
            if($keyword['search_type']){
                if($keyword['search_type'] == 3){
                    $where['a.type'] = ['like','%2%'];
                    $where['a.status'] = ['like','%2%'];
                }else if($keyword['search_type'] == 2){
                    $where['a.type'] = ['like','%2%'];
                    $where['a.status'] = ['like','%1%'];
                } else{
                    $where['a.type'] = ['like','%'.$keyword['search_type'].'%'];
                }
                $this -> assign('search_type',$keyword['search_type']);
                $posui['search_type'] = $keyword['search_type'];
            }
            if($keyword['order_number']){
                $where['a.order_no|m.mobile'] = ['like','%'.$keyword['order_number'].'%'];
                $this -> assign('name',$keyword['order_number']);
                $posui['order_number'] = $keyword['order_number'];
            }
        }
        $table = Db::table('web_orders')
            ->alias('a')
            ->join('__PRODUCT__ p','a.pid=p.id','left')
            ->join('__MEMBER__ m','a.uid=m.id','left')
            ->field('a.*,m.mobile,p.title')
            ->where($where)
            ->order('create_date desc')
            ->paginate('20',false,array('query'=>$posui));

		$day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
        $total_day_data = Db::name('orders')->where(['type'=>2])->where($day)->sum('price');//当日在线支付
		$total_success_money = Db::name('orders')->where(['type'=>2])->sum('price');//统计所有在线支付
        $total_rebate_money = Db::name('orders')->where('type','in','2,3')->sum('interest');//统计所有利息
        $total_unpaid_money = Db::name('orders')->where(['type'=>1,'status'=>1])->sum('price');//统计待支付
        return view('',
		['list'=>$table,
		'page'=>$table->render(),
		'count'=>$table->total(),
		'status'=>search_status(),
		'list_money'=>['success_money'=>$total_success_money,'rebate_money'=>$total_rebate_money,'unpaid_money'=>$total_unpaid_money,'day_data_money'=>$total_day_data]
		]);
    }
    //展示
    public function order_edit($id){
        if(Request::instance()->isPost()){
            return \app\admin\model\Orderd::order_edit(input('post.'));
        }
        return view('',['id'=>$id]);
    }

    public function tindex(){
        $where = [];
        $posui = [];
        $keyword = input('get.');
        if($keyword){
            if($keyword['search_type']){
                if($keyword['search_type'] == 0){
                    $where['a.type'] = ['like','%%'];
                }else if($keyword['search_type'] == 1){
                    $where['a.status'] = ['like','%1%'];
                } else{
                    $where['a.status'] = ['like','%0%'];

                }
                $this -> assign('search_type',$keyword['search_type']);
                $posui['search_type'] = $keyword['search_type'];
            }
            if($keyword['order_number']){
                $where['a.order_no|m.mobile'] = ['like','%'.$keyword['order_number'].'%'];
                $this -> assign('name',$keyword['order_number']);
                $posui['order_number'] = $keyword['order_number'];
            }
        }
        $table = Db::table('web_torder')
            ->alias('a')
            ->join('__PRODUCT__ p','a.pid=p.id','left')
            ->join('__MEMBER__ m','a.uid=m.id','left')
            ->field('a.*,m.mobile,p.title')
            ->where($where)
            ->order('create_time desc')
            ->paginate('20',false,array('query'=>$posui));

        $day['create_time'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
        $total_success_money = Db::name('torder')->where('status',1)->sum('price');//统计支付成功金额
        $total_rebate_money = Db::name('torder')->where('status',1)->field('real')->select();//统计实发赠送总金额
        $proNum = 0;
        for($i = 0;$i <count($total_rebate_money);$i++){
            $proNum += $total_rebate_money[$i]['real'];
        }
        $total_unpaid_money = Db::name('torder')->where('status',1)->sum('should');//应发赠送总金额
        $total_day_data = Db::name('torder')->where('status',1)->where($day)->sum('price');//当天支付成功
        return view('',
            ['list'=>$table,
                'page'=>$table->render(),
                'count'=>$table->total(),
                'status'=>searchs_status(),
                'list_money'=>['success_money'=>$total_success_money,'unpaid_money'=>$total_unpaid_money,'day_data_money'=>$total_day_data,'real_money'=>$proNum]
            ]);
    }
}
