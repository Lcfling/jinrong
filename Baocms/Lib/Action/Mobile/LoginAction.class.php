<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 18:18
 */

class LoginAction extends CommonAction
{

    //todo 用户登录       Lee_zhj
    public function login()
    {

        $this->display();
    }

    //todo 登录处理       Lee_zhj
    public function do_login()
    {

        $username = I('username');
        $password = I('password');
        $model = new $model('User');
        $user = $model->where(array('username' => $username))->find();
        if (empty($user)||$user['password'] != md5($password)) {
            $this->error('账户或密码错误！');
        }

        //写入session
        session('user.userId',$user['user_Id']);
        session('user.username',$user['username']);

        //跳转首页
        $this->redirect('Index/index');

    }

    //todo 退出登录       Lee_zhj
    public function loginout()
    {
        if (!session('user.userId')) {
            $this->error('请登录！');
        }

        session_destroy();
        $this->success('退出登录成功！',U('Index.index'));
    }

}

















