<?php



class WeixinAction extends CommonAction {




    public function wxlogin(){

        $code=$_POST['code'];

        $appid="wx57faf750ee231971";
        $appsecret="930362097e58afb918f53e82d9b74426";

        //通过code获取access_token的接口。
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";

        $access_token= $this->httpget($access_token_url);

        if ($access_token['errcode']){

            die(json_encode($access_token));
        }

        //刷新或续期access_token使用
        $access_token_url1="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$appid."&grant_type=refresh_token&refresh_token=".$access_token['refresh_token'];
        $access_token1=$this->httpget($access_token_url1);

        if ($access_token1['errcode']){
            die(json_encode($access_token1));
        }

        //检验授权凭证（access_token）是否有效
        $access_token_url2="https://api.weixin.qq.com/sns/auth?access_token=".$access_token1['access_token']."&openid=".$access_token1['openid'];
        $access_token2=$this->httpget($access_token_url2);

        if ($access_token_url2['errcode'] != 0){
            die(json_encode($access_token2));
        }

        //获取用户个人信息（UnionID机制）
        $access_token_url3="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token1['access_token']."&openid=".$access_token1['openid'];
        $userdata=$this->httpget($access_token_url3);

        if ($userdata['errcode']){
            die(json_encode($userdata));
        }

        $qqq=$userdata['unionid'];

        //判断用户是否首次登陆


//        $sql="select * from tb_user where unionid='$qqq' ";
//        $userlist=mysql_query($sql);
//        $user1= mysql_fetch_array($userlist);

        $tb_user=M("user");
        $user['unionid']=$qqq;
        $user1=$tb_user->where($user)->select();



        if ($user1['ID']>0){

            $user['user_id']=$user1['unionid'];
            $user['password']=$user1['randpwd'];
            $user['error']="success";
            $user['msg']="";
            die(json_encode($user)) ;

        }else{

            $nickname=$userdata['nickname']; //用户昵称
            $headimgurl=$userdata['headimgurl'];//用户头像
            $unionid=$userdata['unionid']; //用户统一标识
            $randpwd=time(); //随机密码
            $openid=$userdata['openid'];

            date_default_timezone_set('PRC');
            $time=date("Y-m-d H:i:s");
            //$time=date("Y-m-d H:i:s",time());

            $sql="INSERT INTO  tb_user (openID,nickName,headerImg,payDate,user_lv,pay_num,vip_rank,p1id,p2id,p3id,p4id,ulimit,if_pay,unionid,randpwd) VALUES ('$openid','$nickname','$headimgurl','$time',1,0,0,'554','0','0','0',0,0,'$unionid','$randpwd')";

            $userlist=mysql_query($sql);
            if ($userlist){
                $user['user_id']=$unionid;
                $user['password']=$randpwd;
                $user['error']="success";
                $user['msg']='';
                die(json_encode($user));
            }else{
                $user['user_id']='';
                $user['password']='';
                $user['error']="fault";
                $user['msg']='';
                die(json_encode($user));

            }
        }
    }

    public function  httpget($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data,true);
        return $data;
    }


}