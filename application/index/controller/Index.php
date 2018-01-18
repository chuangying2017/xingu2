<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Validate;
use think\Db;
class Index extends \app\common\controller\Common
{
    public function index()
    {
        $table_product=Db::name('infinite_class');
        $table_res=$table_product->where('pid',0)->where('status',1)->select();
//        dump($table_res);
        return view('',['list'=>$table_res]);
    }

      private static $mobileSegment = [
        '134', '135', '136', '137', '138', '139', '150', '151', '152', '157', '130', '131', '132', '155', '186', '133', '153', '189',
    ];

    //获取投资列表的轮换
    protected function lun_xun(){
        $prefix = self::$mobileSegment[array_rand(self::$mobileSegment)];
        $middle = mt_rand(2000, 9000);
        $suffix = mt_rand(2000, 9000);
        return $prefix . $middle . $suffix;
    }

    public function order_list(){
        $rand_num = array_merge(range('A','Z'),range('a','z'));
        $rand = $rand_num[array_rand($rand_num,1)].rand(0000,9999);
        return $rand;
    }
    public function order_listr($num){
        for($i=0;$i<$num;$i++){
            $arra_ss = ['100'];
            $array = array_rand($arra_ss,1);
            $arr[] = array($this->order_list(),substr_replace($this->lun_xun(),'****',3,-4),$arra_ss[$array],date('Y/m/d'));
        }
        return $arr;
    }


    public function pass_t(){
        if(Request::instance()->isAjax()) {
            $input = input('post.');
            $uid = Session::get('uid');
            $rule = [
                'password'=>['regex'=>'/^[a-z\d]{6,12}$/i'],
            ];
            $message = [
                'password'=>'安全密码为6-12位任意组成！',
            ];
            $validate = new Validate($rule, $message);
            if(!$validate->check($input)){
                return json_encode(['msg'=>$validate->getError()]);
            }
            $list = Db::name('member')->where('id',$uid)->update(['password_two'=>md5_pass(1,$input['password_two'])]);
            if($list > 0){
                return ['status'=>1,'msg'=>'设置成功'];
            }else{
                return ['status'=>2,'msg'=>'网络超时！请刷新重试....'];
            }
        }
    }

}
