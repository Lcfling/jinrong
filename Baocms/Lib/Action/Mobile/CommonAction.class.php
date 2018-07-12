<?php
class CommonAction extends Action
{
    protected $uid = 0;
    protected $member = array();
    protected $_CONFIG = array();
    protected $bizs = array();

    protected function _initialize()
    {
        header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
        header('Access-Control-Allow-Methods:GET, POST');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

    }


	//lcf $error success faild
	public function jsonout($error="faild",$msg="",$data){
        $result=array();
        $result['error']=$error;
        $result['msg']=$msg;
        $result['data']=$data;
        die(json_encode($result));
    }

    public function login_verify($ID,$randpwd){

	    $tb_user=M('user');
	    $user['ID']=$ID;
	    $user['randpwd']=$randpwd;
	    $list=$tb_user->where($user)->select();
	    if ($list){
	        $data['error']="登陆状态";
	        $data['msg']=1;
	        return $data;
        }else{
            $data['error']="未登陆状态";
            $data['msg']=2;
            return $data;
        }
    }




	
}