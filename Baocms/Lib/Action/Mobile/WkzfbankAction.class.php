<?php
/**
 * Created by PhpStorm.
 * User: hyk
 * Date: 2018/6/20
 * Time: 9:11
 */

class BankController extends Controller {

	//todo 添加银行卡
	public function addBank() {
		// 实例化User对象
		$User = M("User"); 
		//接收银行卡信息
		$data['bk_info_name'] = $_POST["bk_info_name"];
		$data['bk_info_idCard'] = $_POST["bk_info_idCard"];
		$data['bk_info_bankNum'] = $_POST["bk_info_bankNum"];
		$data['bk_info_bankName'] = $_POST["bk_info_bankName"];
		$data['bk_info_phoneNum'] = $_POST["bk_info_phoneNum"];

		$randStr = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
		$rand = "yqp0001" . substr($randStr, 0, 6);
		//接口URL
		$urll = "http://mpay.gwpos.cn/709071.tran8";
		//业务参数
		$post_data = array("agtorg" => "yqp0001", "mercid" => "484584045119461", "merchantid" => $rand, "merchantname" => "青鹏测试", "certno" => $bk_info_idCard, "name" => $bk_info_name, "district" => "330106", "address" => "安阳市北关区", "bankno" => $bk_info_bankNum, "mobile" => $bk_info_phoneNum, "email" => "kaishenlaile@163.com", "province" => "330000", "city" => "330100");
		//生成签名不进行MD5算法加密
		$post_data["sign"] = get_md5(getSign($post_data));
		$request = xmlToArray(https_post($urll, getSign($post_data)));
		if ($request["RSPCOD"] == "000000") {
			/*7.7.1商户开通*/
			$urll = "http://mpay.gwpos.cn/709073.tran8";
			//业务参数
			$post_data = array("agtorg" => "yqp0001", "mercid" => "484584045119461", "merchantid" => $rand, "busytyp" => "YSJF", "top" => "99999900", "fixed" => "200", "rate" => "0.0055", "bottom" => "100");
			//生成签名不进行MD5算法加密
			$post_data["sign"] = get_md5(getSign($post_data));
			$requestt = xmlToArray(https_post($urll, getSign($post_data)));
			if ($requestt["RSPCOD"] == "000000") {
				$User->add($data);
				echo json_encode($requestt);
			} else {
				$requestt["kt_erro"] = -1;
				echo json_encode($requestt);
			}
		} else {
			$request["sq_erro"] = -1;
			echo json_encode($request);
		}
	}

	//xml转数组
	function xmlToArray($xml) {
		libxml_disable_entity_loader(true);
		$values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $values;
	}

	//获取签名字符串
	function getSign($arr_Allvalu) {
		//定义商户秘钥
		$qp_Key = "F1E9132EC2AA2F35";
		//定义签名字符串
		$sign_Str = "";
		//按照key的升序排列
		ksort($arr_Allvalu);
		//遍历数组
		foreach ($arr_Allvalu as $key => $value) {
			$lower_Key = strtolower($key);
			$sign_Str .= $lower_Key . "=" . $value . "&";
		}
		//拼接秘钥和字符串
		$sign_Str = $sign_Str . "key=" . $qp_Key;
		return $sign_Str;
	}

	//获取MD5
	function get_md5($sstring) {
		return strtoupper(md5($sstring));
	}

	//post请求方法
	function https_post($url, $data) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$ref = curl_exec($curl);

		if (curl_errno($curl)) {
			return 'Errno' . curl_error($curl);
		}
		curl_close($curl);
		return $ref;
	}

}
?>