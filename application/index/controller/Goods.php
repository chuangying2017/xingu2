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

}
