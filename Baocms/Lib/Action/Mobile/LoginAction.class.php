<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 15:47
 */

class LoginAction extends CommonAction{

    //todo 用户注册      Lee_zhj
    /*public function register(){
        //$input_name = $_POST['name'];
        //$input_password = $_POST['password'];
        $input_name = 'hhhhhhhhhhh';
        $input_password = '7777777777777777777';
        //判断name 与 password是否为空
        if (empty($input_name) || empty($input_password)){
            echo json_encode(['code'=>400,'message'=>'name or password cannot nil']);
            return;
        }

        $res = $this->userIsRegister($input_name);
        if ($res){
            echo json_encode(['code'=>400,'message'=>"$input_name is registered"]);
            return;
        }

        $tb_userlogin = M(userlogin);
        $userlogin['user_name'] = $input_name;
        $userlogin['user_pwd'] = $input_password;
        $tb_userlogin->add($userlogin);

    }*/


    //todo 用户登录      Lee_zhj
    public function login(){
        $data['input']=file_get_contents("php://input");
        $data['get']=$_GET;
        $userName=$data['get']['user_name'];
        $userPassword=$data['get']['user_pwd'];

//          $userName='15824678491';
//          $userPassword='qwer1234';


        if($userName!=""&&$userPassword!=""){
            $tb_userlogin = M(userlogin);
            $userloginmsg['user_name']=$userName;
            $userloginmsg['user_pwd']=$userPassword;
            $sqlnmsg=$tb_userlogin->where($userloginmsg)->Field('user_name,user_pwd')->select();
            $sqlid=$tb_userlogin->where($userloginmsg)->getField('ID');

            if($sqlnmsg){
                $getrandom['get_random']=$this->genRandomString();

                $tb_userlogin->where('ID='.$sqlid)->save($getrandom);
                $userrandom = $tb_userlogin->where('ID='.$sqlid)->getField('get_random');
                //var_dump($userrandom);

                $suc = 'success';
                $params = array($userrandom,$suc);
                //var_dump($params);

                die(json_encode($params));
            }else{
                echo "用户名或密码错误！";
            }
        }

    }

    //产生一个指定长度的随机字符串,并返回给用户
    private function genRandomString($len = 6) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        // 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }


}