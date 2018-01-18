<?php
namespace Org\service;
class Service {

    public function ceshi(){
        return "测试一下方法";
    }
	/**
	 * 	作用：生成签名
	 */
	public function getSign($Obj,$key){
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		ksort($Parameters);
		$String = $this->biludstring($Parameters, false);
		$String = $String."&key=$key";
		$result_ = md5($String);
		return $result_;
	}

	public function biludstring($paraMap, $urlencode){
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
			if(($v||$v==0)&&$v!==''&&$v!==null)
			{
				if($urlencode)
				{
					$v = urlencode($v);
				}
				$buff .= $k . "=" . $v . "&";
			}
		}
		$reqPar='';
		if (strlen($buff) > 0)
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}

	public function httpGet($url){
		//初始化
		$ch = curl_init();
		
		//设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, "http://www.jb51.net");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		//执行并获取HTML文档内容
		$res = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);

		return $res;
	}

	public function httpPost($url,$post_data){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// post数据
		curl_setopt($ch, CURLOPT_POST, 1);
		// post的变量
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

		$res = curl_exec($ch);
		curl_close($ch);

		return $res;
	}
}