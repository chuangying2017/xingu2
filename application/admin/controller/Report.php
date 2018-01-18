<?php

namespace app\admin\controller;


use think\Db;

use think\Request;

class Report extends Common
{
        public function recharge(){
                $return = \app\admin\model\Report::select_s(request()->get());
                $name = Db::name('member');
                $admin = Db::name('admin');
                foreach($return[0] as $value){
                           $variable=$name->where('id',$value['uid'])->field('mobile')->find();
                          // $value['name']=$variable['name']?:'无';
                           $value['mobile']=$variable['mobile']?:'无';
                         //  $value['central'] = $value['central']?:'无';
                           $vv = $admin->where('id',$value['admin_id'])->field('username')->find();
                           $value['adminname']=$vv['username']?:"无";
                           $value['orders'] = $value['orders']?:'无';
                           $arrd[]=$value->toArray();
                }
       return view('index',
           [
               'count'=>$return[3],
               'list'=>$arrd,
               'arr'=>$return[1],
               'page'=>$return[2],
               'bin_type'=>benjin(),
               'chongzhitype'=>chongzhitype(),
               'getChongzhiBonusStatus'=>getChongzhiBonusStatus(),
               'money_level'=>benjin()
           ]
            );
        }

    //展示提现
//    public function tixian(){
//        $result = Db::name('deposit')
//            ->alias('a')
//            ->join('__MEMBER__ m','a.uid=m.id')
//            ->field('a.*,m.mobile as mb')
//            ->where()
//            ->paginate('20');
//        $count = 0;
//        for($i=0;$i<count($result);$i++){
//            if($result[$i]['status'] == 1){
//                $count +=1;
//            }
//        }
//        return view('',['page'=>$result->render(),'count'=>$result->total(),'list'=>$result,'zong'=>$count]);
//    }
//    //点击的时候已提现
//    public function webstus(){
//        if(Request::instance()->isAjax()){
//            $name = db('deposit')->update(['id'=>input('get.id'),'status'=>2]);
//            return $name?['status'=>1]:['status'=>2,'msg'=>'点击失效'];
//        }
//    }
//    //下载数据
//    public function xiazai(){
//
//        $xlsName  = "用户列表"; //设置要导出excel的表头
//        $xlsCell  = array(
//            array('id','顺序号'),
//            array('name','收款户名'),
//            array('crad','身份证号'),
//            array('mobile','手机号'),
//            //  array('name','用户账号'),
//            array('bank','收款银行'),
//            array('province','收款账号省份'),
//            array('city','收款账号地市'),
//            array('kaihubank','收款账号开户行'),
//            array('bank_crad','收款账号'),
//            array('money','收款金额'),
//            array('beiz','备注'),
//        );
//        $bank =\request()->param('bank');
//
//        if(!empty($bank)){
//            $xlsData = db()->query('SELECT * FROM `web_deposit` where  `status`=? and  LENGTH(bank_crad)=?',[1,$bank]);
//            if (count($xlsData) > 0) {
//                exportExcel($xlsName, $xlsCell, $xlsData);
//            }else{
//                exit('没有数据');
//            }
//        }else{
//            $xlsData1 = db()->query('SELECT * FROM `web_deposit` where  `status`=1 and  LENGTH(bank_crad)>16');
//            if (count($xlsData1)) {
//                exportExcel($xlsName, $xlsCell, $xlsData1);
//            }
//        }
//    }
//sasdsadsdadsad

   public function shibai_update(){//拒绝提现返回提现金额跟手续费
        if(Request::instance()->isAjax()){
            $input = input('get.');
            $result = Db::name('deposit')->find($input['id']);
            $num = $result['money']+$result['service_charge'];
            $momber = Db::name('member')->find($result['uid']);
            $usermoney['money'] = $momber['money']+$num;
            $edits = Db::name('member')->where('id',$result['uid'])->update($usermoney);
            if($edits){
                $data['status'] = 3;
                $data['huifu'] = $input['huifu'];
                $edit = Db::name('deposit')->where('id',$input['id'])->update($data);
                if($edit){
                    return ['status'=>1,'msg'=>'操作成功！'];
                }else{
                    return ['status'=>2,'msg'=>'操作失败！'];
                }
            }
        }
    }

    public function tixian_list(){
		$where = [];
		$posui = [];
        $keyword = input('get.');
        if($keyword){
            if($keyword['mobile']){
               $where['a.name|m.mobile'] = ['like','%'.$keyword['mobile'].'%'];
                $this -> assign('name',$keyword['mobile']);
				 $posui['mobile'] = $keyword['mobile'];
            }
            if($keyword['kaishi'] && $keyword['jiesu']){
                $where['a.create_date'] = ['between',[strtotime($keyword['kaishi'].' 00:00:01'),strtotime($keyword['jiesu'].'23:59:55')]];
                $this -> assign('kaishi',$keyword['kaishi']);
                $this -> assign('jiesu',$keyword['jiesu']);
				  $posui['kaishi'] = $keyword['kaishi'];
                $posui['jiesu'] = $keyword['jiesu'];
            }
        }
    	$deposit=Db::name('deposit');
        $result = $deposit
            ->alias('a')
            ->join('__MEMBER__ m','a.uid=m.id')
            ->field('a.*,m.mobile as mb')
            ->where('a.status',2)
			->where($where)
            ->paginate('20',false,array('query'=>$posui));
        $count = 0;
        for($i=0;$i<count($result);$i++){
            if($result[$i]['status'] == 2){
                $count +=1;
            }
        }
        return view('',['page'=>$result->render(),'count'=>$result->total(),'list'=>$result,'zong'=>$count]);
    }

    public function tixian_shibai(){
 		$where = [];
		$posui = [];
        $keyword = input('get.');
        if($keyword){
            if($keyword['mobile']){
               $where['a.name|m.mobile'] = ['like','%'.$keyword['mobile'].'%'];
                $this -> assign('name',$keyword['mobile']);
				 $posui['mobile'] = $keyword['mobile'];
            }
            if($keyword['kaishi'] && $keyword['jiesu']){
                $where['a.create_date'] = ['between',[strtotime($keyword['kaishi'].' 00:00:01'),strtotime($keyword['jiesu'].'23:59:55')]];
                $this -> assign('kaishi',$keyword['kaishi']);
                $this -> assign('jiesu',$keyword['jiesu']);
				  $posui['kaishi'] = $keyword['kaishi'];
                $posui['jiesu'] = $keyword['jiesu'];
            }
        }
    	$deposit=Db::name('deposit');
        $result = $deposit
            ->alias('a')
            ->join('__MEMBER__ m','a.uid=m.id')
            ->field('a.*,m.mobile as mb')
            ->where('a.status',3)
			->where($where)
            ->paginate('20',false,array('query'=>$posui));
        $count = 0;
        for($i=0;$i<count($result);$i++){
            if($result[$i]['status'] == 3){
                $count +=1;
            }
        }
        return view('',['page'=>$result->render(),'count'=>$result->total(),'list'=>$result,'zong'=>$count]);
    }

    //展示提现
    public function tixian(){
		$where = [];
		$posui = [];
        $keyword = input('get.');
        if($keyword){
            if($keyword['mobile']){
               $where['a.name|m.mobile'] = ['like','%'.$keyword['mobile'].'%'];
                $this -> assign('name',$keyword['mobile']);
				 $posui['mobile'] = $keyword['mobile'];
            }
            if($keyword['kaishi'] && $keyword['jiesu']){
                $where['a.create_date'] = ['between',[strtotime($keyword['kaishi'].' 00:00:01'),strtotime($keyword['jiesu'].'23:59:55')]];
                $this -> assign('kaishi',$keyword['kaishi']);
                $this -> assign('jiesu',$keyword['jiesu']);
				  $posui['kaishi'] = $keyword['kaishi'];
                $posui['jiesu'] = $keyword['jiesu'];
            }
        }
    	$deposit=Db::name('deposit');
        $result = $deposit
            ->alias('a')
            ->join('__MEMBER__ m','a.uid=m.id')
            ->field('a.*,m.mobile as mb')
            ->where('a.status',1)
			->where($where)
            ->paginate('20',false,array('query'=>$posui));
        $count = 0;
        for($i=0;$i<count($result);$i++){
            if($result[$i]['status'] == 1){
                $count +=1;
            }
        }
		$day['create_date'] = array('between',strtotime(date('Y-m-d').' 00:00:00').','.strtotime(date('Y-m-d').' 23:59:59'));
        $total_day_data = Db::name('deposit')->where(['status'=>2])->where($day)->sum('money');//今日已提现
		$total_day_datas = Db::name('deposit')->where(['status'=>1])->where($day)->sum('money');//今日已提现
		$count_deposit_money_deal = $deposit->where('status',1)->sum('money');//待处理金额
		$count_deposit_money_success = $deposit->where('status',2)->sum('money');//提现成功的金额
		$count_deposit_money_failed = $deposit->where('status',3)->sum('money');//拒绝提现金额
        return view('',['page'=>$result->render(),'count'=>$result->total(),
		'list'=>$result,
		'zong'=>$count,
		'money_deal'=>$count_deposit_money_deal,
		'money_success'=>$count_deposit_money_success,
		'money_failed'=>$count_deposit_money_failed,'day_money_deposit'=>$total_day_data,'day_money_deposits'=>$total_day_datas]);
    }
    //点击的时候已提现
    public function webstus(){
        if(Request::instance()->isAjax()){
            $name = db('deposit')->update(['id'=>input('get.id'),'status'=>2]);
            return $name?['status'=>1]:['status'=>2,'msg'=>'点击失效'];
        }
    }
    //下载待处理数据
    public function xiazai(){
        $xlsName  = ""; //设置要导出excel的表头
        $xlsCell  = array(
            array('lius','*商户流水号'),
            array('shou_type','*收款方类型：企业/个人'),
            array('xinz','*账户性质（储蓄卡/信用卡）'),
//            array('mobile','手机号'),
            array('name','*收款方姓名'),
//            array('crad','*身份证'),
            array('bank','*开户银行名称'),
            array('bank_crad','*银行账号'),
            array('','银行所在省份'),
            array('','银行所在市'),
            array('','支行名称（对公代付必填）'),
            array('money','*金额（单位：元；精确到分）'),
            array('kaihubank','联行号'),
            array('','用途【（5万元（含）以上必填，不超过20个字符）】'),
            array('beiz','备注'),
        );
        $bank =\request()->param('bank');

        if(!empty($bank)){
            $xlsData = db()->query('SELECT * FROM `web_deposit` where  `status`=? and  LENGTH(bank_crad)=?',[1,$bank]);
            if (count($xlsData) > 0) {
                exportExcel($xlsName, $xlsCell, $xlsData);
            }else{
                exit('没有数据');
            }
        }else{
            $xlsData1 = db()->query('SELECT * FROM `web_deposit` where  `status`=1 and  LENGTH(bank_crad)>16');
            if (count($xlsData1)) {
                exportExcel($xlsName, $xlsCell, $xlsData1);
            }
        }
    }

    //下载成功数据
    public function cheng_xiazai(){
        $xlsName  = "用户列表"; //设置要导出excel的表头
        $xlsCell  = array(
            array('id','顺序号'),
            array('name','收款户名'),
            array('crad','身份证号'),
            array('mobile','手机号'),
            //  array('name','用户账号'),
            array('bank','收款银行'),
            array('province','收款账号省份'),
            array('city','收款账号地市'),
            array('kaihubank','收款账号开户行'),
            array('bank_crad','收款账号'),
            array('money','收款金额'),
            array('beiz','备注'),
        );
        $bank =\request()->param('bank');

        if(!empty($bank)){
            $xlsData = db()->query('SELECT * FROM `web_deposit` where  `status`=? and  LENGTH(bank_crad)=?',[2,$bank]);
            if (count($xlsData) > 0) {
                exportExcel($xlsName, $xlsCell, $xlsData);
            }else{
                exit('没有数据');
            }
        }else{
            $xlsData1 = db()->query('SELECT * FROM `web_deposit` where  `status`=2 and  LENGTH(bank_crad)>16');
            if (count($xlsData1)) {
                exportExcel($xlsName, $xlsCell, $xlsData1);
            }
        }
    }

    //下载失败数据
    public function shibai_xiazai(){
        $xlsName  = "用户列表"; //设置要导出excel的表头
        $xlsCell  = array(
            array('id','顺序号'),
            array('name','收款户名'),
            array('crad','身份证号'),
            array('mobile','手机号'),
            //  array('name','用户账号'),
            array('bank','收款银行'),
            array('province','收款账号省份'),
            array('city','收款账号地市'),
            array('kaihubank','收款账号开户行'),
            array('bank_crad','收款账号'),
            array('money','收款金额'),
            array('beiz','备注'),
        );
        $bank =\request()->param('bank');

        if(!empty($bank)){
            $xlsData = db()->query('SELECT * FROM `web_deposit` where  `status`=? and  LENGTH(bank_crad)=?',[3,$bank]);
            if (count($xlsData) > 0) {
                exportExcel($xlsName, $xlsCell, $xlsData);
            }else{
                exit('没有数据');
            }
        }else{
            $xlsData1 = db()->query('SELECT * FROM `web_deposit` where  `status`=3 and  LENGTH(bank_crad)>16');
            if (count($xlsData1)) {
                exportExcel($xlsName, $xlsCell, $xlsData1);
            }
        }
    }



    //展示投资返利
    public function touzi(){
        $select = Db::name('bonujilu')
            ->alias('a')
            ->join('__ORDER__ o','a.pid=o.id','left')
            ->join('__MEMBER__ m','a.uid=m.id','left')
            ->join('__PRODUCT__ p','o.pid=p.id','left')
            ->field('p.title,o.total,m.username,m.mobile,a.*')
            ->where()
            ->paginate('20');
        return view('',['list'=>$select,'count'=>$select->total()]);
    }

    public function jifen(){
        $result = Db::table('web_member')
            ->alias('a')
            ->join('web_bouns b','a.id=b.uid','LEFT')
            ->field('a.id,a.integral,a.name,a.email,b.nitegral_balance')
            ->select();
        $this->assign('list', $result);
        return $this->fetch();
    }

    public function jifen_Deteli($uid){//会员积分明细
        $list = Db::name('integral')->where('uid',$uid)->field('integral,create_time,type')->select();
        $resu = Db::name('suiwu')->where('uid',$uid)->field('geren as integral,create_time,type')->select();
        $result = Db::name('suiwu')->where('shang_level',$uid)->field('jieshao as integral,create_time')->select();
        $num = count($result);
        for($i=0;$i<$num;$i++){
            $result[$i]['type'] = 3;
        }
        $c = array_merge($list,$resu);
        $xin = array_merge($c,$result);
//        dump($xin);
        foreach($xin as $key=>$value){
            $create[$key]=$value['create_time'];
        }
        array_multisort($create,SORT_DESC,$xin);
//        dump($xin);
        $num = count($xin);
//        dump($num);
        $this -> assign('num',$num);
        $this -> assign('list',$xin);
        return $this->fetch();
    }

}
