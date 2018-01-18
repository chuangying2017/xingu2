<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Validate;
use think\Db;

class Distribute extends Controller
{
    public function index(){
        Db::startTrans();

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
                try {
                    $resu_one = Db::name('torder')->where('id', $list[$i]['id'])->update(['real' => $pos, 'type' => 1]);//更新数据库实发实发数额，并且更改类型，未发放完默认是0，发放完则是1

                    $data['uid'] = $list[$i]['uid'];//获得该订单的会员

                    $data['create_date'] = time();//获得当前发放时间

                    $data['type'] = 5;//设置奖金表类型 5是每日投资返利

                    $data['order_id'] = $list[$i]['id'];

                    $resu_two =  Db::name('bonus')->insert($data);//添加数据区奖金表

                    $member = Db::name('member')->where('id', $list[$i]['uid'])->find();//查询用户表

                    $da['money'] = $member['money'] + $data['money'];//把当日发放的数额加入余额

                    $da['real'] = $member['real'] + $data['money'];//会员表产品实发总数额加上当天发放的数额

                    $da['frozenmoney'] = $member['frozenmoney'] - $data['money'];//会员表冻结钱袋减去当天发放的数额

                    $resu_three =  Db::name('member')->where('id', $list[$i]['uid'])->update(['money' => $da['money'], 'real' => $da['real'], 'frozenmoney' => $da['frozenmoney']]);

                    if($resu_one && $resu_two && $resu_three){

                        Db::commit();

                        \think\Log::init(['type'=>'File','path'=>APP_PATH.'return_jiang_log_s/']);

                        \think\Log::info('info');

                    }else{

                        \think\Log::error('更新失败');

                    }


                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();

                    \think\Log::init(['type'=>'File','path'=>APP_PATH.'return_jiang_log/']);

                    \think\Log::error($e->getMessage());

                }

            }else{//如果实发总数额不大于应发数额,则发放平均每日发放的应发数额

                try {
//                dump('321');

                   $resu_one =  Db::name('torder')->where('id', $list[$i]['id'])->setField('real', $znum);

                    $data['uid'] = $list[$i]['uid'];

                    $data['create_date'] = time();

                    $data['type'] = 5;

//                dump($data);

                    $resu_two =  Db::name('bonus')->insert(['uid' => $data['uid'], 'create_date' => $data['create_date'], 'type' => $data['type'], 'money' => $data['money'], 'order_id' => $list[$i]['id']]);//添加数据区奖金表


                    $member = Db::name('member')->where('id', $list[$i]['uid'])->find();//查询用户表

                    $da['money'] = $member['money'] + $data['money'];//把当日发放的数额加入余额

                    $da['real'] = $member['real'] + $data['money'];//会员表产品实发总数额加上当天发放的数额

                    $da['frozenmoney'] = $member['frozenmoney'] - $data['money'];//会员表冻结钱袋减去当天发放的数额

                    $resu_three = Db::name('member')->where('id', $list[$i]['uid'])->update(['money' => $da['money'], 'real' => $da['real'], 'frozenmoney' => $da['frozenmoney']]);


                    if($resu_one && $resu_two && $resu_three){
                        Db::commit();

                        \think\Log::init(['type'=>'File','path'=>APP_PATH.'return_jiang_log_s/']);

                        \think\Log::info('info');
                    }else{

                        \think\Log::error('更新失败');

                    }


                } catch (\Exception $e) {

                    Db::rollback();

                    \think\Log::init(['type'=>'File','path'=>APP_PATH.'return_jiang_log/']);

                    \think\Log::error($e->getMessage());
                }

            }
        }

    }

}