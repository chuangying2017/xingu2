<?php
namespace app\index\controller;

use app\common\controller\Common;
use Org\service\Service;
use think\Build;
use think\cache\driver\Redis;
use think\Controller;
use think\Db;
use think\Log;
use think\Request;
use think\Session;
use think\Url;
use think\view\driver\Think;

class Goods extends Common
{

//    public function product(){
//        $table_product=Db::name('infinite_class');
//            $table_res=$table_product->where('pid',0)->where('status',1)->select();
////        dump($table_res);
//        return view('',['list'=>$table_res]);
//    }

    //异步加载数据
    public function ajax_product(){
        if(Request::instance()->isAjax()){
           // $redis_object = new Redis();
            $id = 'fen'.input('post.id');
          //  if(!$table_product=$redis_object->get($id)){
                $table_product=Db::name('product')->alias('p')
                    ->join('web_img g','p.id=g.pid','left')
                    ->field('p.*,g.path_img')
                    ->where('p.type_id='.input('post.id'))
                    ->order('p.price')
                    ->select();
        //        $redis_object->set($id,$table_product);
      //      }
            foreach ($table_product as &$value){
                $value['url'] = Url::build('index/Goods/purchase',['sid'=>base64_encode($value['id'])]);
            }
            return ['status'=>1,'data'=>$table_product,'chang'=>count($table_product)];
        }
    }


    public function purchase(){//购买商品
        $table_product = Db::name('product')->alias('p')
            ->join('web_img g','p.id=g.pid','left')
            ->where('p.id',base64_decode(input('sid')))
            ->field('p.*,g.path_img')
            ->find();
        if(!$table_product){
            $this->error('页面不存在','index/index/index');
        }
        $this->assign('list',$table_product);
        return view();
    }
    //接收购买的数据
    public function buy_data(){
            if(Request::instance()->isAjax()){
                return \app\index\logic\Goods::Goodsnvestment(input('post.'));//投资
            }
    }


    public function buy_money_fa(){//发放赠送金额


        $list = Db::name('torder')
        ->alias('a')
        ->join('__PRODUCT__ b','a.pid=b.id')
        ->field('a.*,b.name_bei,b.name_tian,b.title')
        ->where('a.status',1)
        ->where('a.type',.0)
        ->select();

        for($i = 0;$i < count($list);$i++){

            $data['money'] = $list[$i]['should']/$list[$i]['name_tian'];//应赠送的数额/产品的返还天数获得当天应该获得的应得赠送数额

            $znum = $data['money']+$list[$i]['real'];//实发数额+当天实发数额 = 今天和之前总共拿到的实发总数额

//            dump($znum);

            if($znum >= $list[$i]['should']){//如果总实发数额大于等于应发数额说明今天是该订单最后一笔每日返还
//                dump('123');
                $data['money'] = $list[$i]['should'] - $list[$i]['real'];//则应发数额减去之前已实发的数额,得到今日发放的数额

                $pos = $list[$i]['real']+$data['money'];//获得等于应发数额的数额

                Db::name('torder')->where('id',$list[$i]['id'])->update(['real'=>$pos,'type'=>1]);//更新数据库实发实发数额，并且更改类型，未发放完默认是0，发放完则是1

                $data['uid'] = $list[$i]['uid'];//获得该订单的会员

                $data['create_date'] = time();//获得当前发放时间

                $data['type'] = 5;//设置奖金表类型 5是每日投资返利

                $data['order_id'] = $list[$i]['id'];

                Db::name('bonus')->insert($data);//添加数据区奖金表

                $member = Db::name('member')->where('id',$list[$i]['uid'])->find();//查询用户表

                $da['money'] = $member['money']+$data['money'];//把当日发放的数额加入余额

                $da['real'] = $member['real']+$data['money'];//会员表产品实发总数额加上当天发放的数额

                $da['frozenmoney'] = $member['frozenmoney']-$data['money'];//会员表冻结钱袋减去当天发放的数额

                Db::name('member')->where('id',$list[$i]['uid'])->update(['money'=> $da['money'],'real'=> $da['real'],'frozenmoney'=>$da['frozenmoney']]);

            }else{//如果实发总数额不大于应发数额,则发放平均每日发放的应发数额


//                dump('321');

                Db::name('torder')->where('id',$list[$i]['id'])->setField('real',$znum);

                $data['uid'] = $list[$i]['uid'];

                $data['create_date'] = time();

                $data['type'] = 5;

//                dump($data);

                Db::name('bonus')->insert(['uid'=>$data['uid'],'create_date'=>$data['create_date'],'type'=>$data['type'],'money'=>$data['money'],'order_id'=>$list[$i]['id']]);//添加数据区奖金表


                $member = Db::name('member')->where('id',$list[$i]['uid'])->find();//查询用户表

                $da['money'] = $member['money']+$data['money'];//把当日发放的数额加入余额

                $da['real'] = $member['real']+$data['money'];//会员表产品实发总数额加上当天发放的数额

                $da['frozenmoney'] = $member['frozenmoney']-$data['money'];//会员表冻结钱袋减去当天发放的数额

                Db::name('member')->where('id',$list[$i]['uid'])->update(['money'=> $da['money'],'real'=> $da['real'],'frozenmoney'=>$da['frozenmoney']]);

            }
        }

//        dump($list);
        return view();

    }

    //展示产品
    public function product_message(){
            $product = Db::name('product');
            $result_img_product=$product->alias('p')
                ->join('web_img w','p.id=w.pid')
                ->where('p.status',1)
                ->field('p.*,w.path_img')->select();
            echo json_encode($result_img_product);
    }

    //购买产品
    public function buy_product($type=null){
            //$type == a1 使用余额复投 否者就是新投
           if(Request::instance()->isGet()){
               $post_data = input('param.');
               Log::info($post_data);
               return \app\index\logic\Goods::buy_product_data($post_data);
           }else{
               return json_encode(['status'=>2,'msg'=>'wangwu']);
           }
    }
}
