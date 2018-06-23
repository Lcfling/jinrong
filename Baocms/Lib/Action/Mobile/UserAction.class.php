<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/16
 * Time: 10:17
 */

class UserAction extends CommonAction {

    //todo 个人中心首页信息 ZF
    public function index(){


        //$user_id=$_POST['ID'];//获取用户
         $user_id=554;
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->select();

        //返回数据
       $data['list']=$userlist;

        die(json_encode($data));


    }
    //  todo 账户管理收益明细 ZF
    public function earnings(){

      //  $user_id=$_POST['id']; // 获取用户id
         $user_id=782;
        // 通过id查找自己的收益来源!
        $sql2="select a.*,b.user_lv,b.vip_rank from tb_fanyong as a JOIN tb_user as b on a.openID=b.ID where a.p1id=$user_id or a.p2id=$user_id or a.p3id=$user_id;";
        $res=mysql_query($sql2);
        while($row=mysql_fetch_row($res,MYSQL_ASSOC))
        {
            $listmoney[] = $row;
        }

        //查询总收益
        $tb_fanyong=M('fanyong');
        $fanyong1['p1id']=$user_id;
        $money1=$tb_fanyong->where($fanyong1)->field('sum(p1fy)+sum(p1gl) as money')->select();

//        $sql1=" select sum(p1fy) as money from tb_fanyong where p1id='$user_id'";

        $fanyong2['p2id']=$user_id;
        $money2=$tb_fanyong->where($fanyong2)->field('sum(p2fy)+sum(p2gl) as money')->select();

//        $sql2="select sum(p2fy) from as money from tb_fanyong where p2id='$user_id'";


        $fanyong3['p3id']=$user_id;
        $money3=$tb_fanyong->where($fanyong3)->field('sum(p3fy)+sum(p3gl) as money')->select();

//        $sql3="select sum(p3fy) from as money from tb_fanyong where p3id='$user_id'";

        $money=$money1[0]['money']+$money2[0]['money']+$money3[0]['money'];


        $data['list']=$listmoney;
        $data['money']=$money;

        die(json_encode($data));
    }

    //todo   账户管理提现记录 ZF
    public function deposit(){

     //   $openid=$_POST['openID'];
        $openid="oFaDv1VQlb6QH2ng5SXBPQr3IZto";
        // 通过openid查询提现记录
        $tb_record=M('record');
        $record['openID']=$openid;
        $tixian=$tb_record->where($record)->select();

//        $sql="select * from tb_record where openID='$openid'";

        //返回数据
        die(json_encode($tixian));
    }

    //todo 我的银行卡 ZF
    public function bank(){

       // $openid=$_POST['openID'];
        $openid="oFaDv1RcAzzXoYj6_h8JAQN4ZbmU";
        // 通过openid查询自己的银行卡
        $tb_bank=M('bank');
        $bank['openID']=$openid;
        $banklist=$tb_bank->where($bank)->select();

//        $sql="select * from tb_bank  where openID='$openid'";

        $data['list']=$banklist;
        die(json_encode($data));
    }

    //todo 删除银行卡 ZF
    public function deletebank(){

      //  $openid=$_POST['openID']; // 用户openid
        $openid="oFaDv1TCsGqDVkZT-bLT2LtsvIas";

       // $id=$_POST['id']; //银行卡id
        $id=131;
        //删除银行卡
        $tb_bank=M('bank');
        $list=$tb_bank->where("id=$id and openID='$openid'")->delete();

//        $sql="delete from tb_bank where id=$id and openID='$openid'";

         if ($list){
             $data['msg']="删除成功";
             $data['error']=1;
             die(json_encode($data));
         }else{
             $data['msg']="删除失败";
             $data['error']=2;
             die(json_encode($data));
         }
        var_dump($list);
    }

    //todo 个人设置  ZF
    public function setdata(){

        //获取用户修改的资料
//        $userID=$_POST['ID']; // 用户id
//        $headerImg=$_POST['headerImg'];//头像
//        $userName=$_POST['name'];//名称
//        $wxImg=$_POST['wxImg'];//微信二维码
//        $wxNumber=$_POST['wxNumber'];//微信号


        $userID=811;
        $headerImg="http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLeKdLZTWmcxJgnrOvtXScI6za5NyCLcFw7OcMRTHnsegt03gCGmmo0RRYsMZRO9GBL9C7ibqP3Oicg/132";
        $userName="张贱贱002";
        $wxImg="www.baidu.com";
        $wxNumber="185037233336";




        //修改用户资料
        $tb_user=M('user');
        $user['nickName']=$userName;
        $user['headerImg']=$headerImg;
        $user['user_wxnum']=$wxNumber;
        $user['wx_erweima']=$wxImg;
        $list=$tb_user->where('ID='.$userID)->save($user);

      //  $sql=" UPDATE tb_user set nickName='$userName',headerImg='$headerImg',user_wxnum='$wxNumber',wx_erweima='$wxImg' where ID=".$userID;
        //返回数据
       if ($list){
           $data['msg']="修改成功!";
           $data['error']=1;
           die(json_encode($data));
       }else{
           $data['msg']="修改失败";
           $data['error']=2;
           die(json_encode($data));
       }
       var_dump($data);

    }

    //todo 密码修改 ZF
    public function setpwd(){

//        $userID=$_POST['ID']; //用户ID
//        $formerPwd=$_POST['formerPwd'];//旧密码
//        $newmerPwd=$_POST['newmerPwd'];//新密码
        $userID=811;
        $formerPwd="123456";
        $newmerPwd="000222";


       //查询用户的旧密码
        $tb_user=M("user");
        $user['ID']=$userID;
        $list=$tb_user->where($user)->select();


//        $sql=" select user_pwd from tb_user where id=".$userID;

        //判断用户发过来的旧密码是否与数据库密码一致
        if ($list[0]['user_pwd'] == $formerPwd){
            //密码一致 修改密码
            $userpwd['user_pwd']=$newmerPwd;
            $list1=$tb_user->where('ID='.$userID)->save($userpwd);
            //$sql1=" UPDATE tb_user set user_pwd='$newmerPwd' where id=".$userID;
            //返回数据
            if ($list1){
                $data['msg']="修改成功!";
                $data['error']=1;
                die(json_encode($data));
            }else{
                $data['msg']="修改失败";
                $data['error']=3;
                die(json_encode($data));
            }
        }else{
            $data['msg']="修改失败,密码错误";
            $data['error']=2;
            die(json_encode($data));
        }
            print_r($data);
    }

   //todo 专属推荐人 ZF
    public function referrer(){
        //获取用户id
        //$userID=$_POST['ID'];
        $userID=782;
        //通过id查找自己的上4级
        $sql1="select a.p1id,b.* from tb_user as a join tb_user as b on a.p1id = b.ID where a.ID=".$userID;
        //返回数据
         $p1id=mysql_fetch_array(mysql_query($sql1),MYSQL_ASSOC);

        $sql2="select a.p2id,b.* from tb_user as a join tb_user as b on a.p2id = b.ID where a.ID=".$userID;
        //返回数据
        $p2id=mysql_fetch_array(mysql_query($sql2),MYSQL_ASSOC);

        $sql3="select a.p3id,b.* from tb_user as a join tb_user as b on a.p3id = b.ID where a.ID=".$userID;
        //返回数据
        $p3id=mysql_fetch_array(mysql_query($sql3),MYSQL_ASSOC);

        $sql4="select a.p4id,b.* from tb_user as a join tb_user as b on a.p4id = b.ID where a.ID=".$userID;
        //返回数据
        $p4id=mysql_fetch_array(mysql_query($sql4),MYSQL_ASSOC);


            $data['p1id']=$p1id;
            $data['p2id']=$p2id;
            $data['p3id']=$p3id;
            $data['p4id']=$p4id;

             $data['error']=1;
             die(json_encode($data));
    }

    //todo 团队管理 ZF
    public function  team(){
        //   通过id查找自己的团队成员
       // $user_id=$_POST['ID'];
        $user_id=554;
        $tb_user=M('user');
        $user['p1id']=$user_id;
        $user['vip_rank']=0;
        $list1=$tb_user->where($user)->select();//查询实习会员

//        $sql=" select * from tb_user where p1id=$user_id and vip_rank=0 ";

        $user1['p1id']=$user_id;
        $user1['vip_rank']=array('GT','0');
        $list2=$tb_user->where($user1)->select();//查询非实习会员
//        $sql1=" select * from tb_user where p1id=$user_id and vip_rank>0 ";

        //返回数据
        $data['list1']=$list1;
        $data['list2']=$list2;

        die(json_encode($data));

    }

    //todo 排行榜 ZF
    public function rankinglist(){

        // 获取用户总收益排行 前10名
        $tb_user=M("user");
        $data=$tb_user->order('ulimit desc')->limit(10)->select();
        print_r($data);
        die();

        die(json_encode($data));

    }

}