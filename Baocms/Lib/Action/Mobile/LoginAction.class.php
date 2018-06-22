<?php
/**
 * Created by PhpStorm.
 * User: hyk
 * Date: 2018/6/20
 * Time: 9:11
 */

class LoginController extends Controller {
	
//todo 登录校验   hyk
public function login(){
	if(!$this->isPost()){
        echo('页面不存在');
		exit;
    }
	//接收用户名,密码,自动登录
	$uname = $_POST["uname"];
    $pwd = $_POST["pwd"];
    $auto = $_POST["yes_no"];
	$arr = array('uname' => $uname,'pwd'=> $pwd);
	
	//根据条件查询并实例化user对象
	$user=M('User')->where($arr)->find();
	
	//判断是否存在user对象.
	if($user){
		$uid = $user[id];
		$uname = "";
        if($user[nickname]){
            $uname=$user[nickname];
        }

		//存入session
		session('uid',$user[id]);
        session('uname',$uname);
		//判断用户是否勾选为下次自动登录
		if($auto=='on'){
            cookie('uid',$uid,7*24*3600);
            cookie('uname',$uname,7*24*3600);
        }
		//登录成功跳转到首页
        redirect("index.php");
	} else {
		redirect(U('login'),2,'用户名或密码错误,跳转回登陆页面,重新登录...');
	}
	
}

//用户点击退出
public function logout(){
    session('uid',null);
    session('uname',null);
    cookie('uid',null);
    cookie('uname',null);
    redirect("index.php");
}

?>