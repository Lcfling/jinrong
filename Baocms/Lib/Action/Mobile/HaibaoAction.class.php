<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/22
 * Time: 15:12
 */

class HaibaoAction extends CommonAction {

  public function index(){
      session_start();
   //   require "connectdb.php";
      $ID=$_POST['ID'];
      $openid=$_POST['openID'];
      $userNickname=$_POST['nickname'];
//      $ID=$_SESSION['ID'];
//      $openid=$_SESSION['openid'];
//      $userNickname=$_SESSION['nickname'];
//
      if (strlen($userNickname)>18) $userNickname=substr($userNickname,0,18) . '...';
      if(empty($ID)){
          exit;
      }

      if($this->is_weixin()){
          echo "<script>var jswx=\"yes\";</script>";//value js
      }else {
          echo "<script>var jswx=\"no\";</script>";//value js
      }

      $sql="select openid from haibao where openid='$openid'";
      $go=mysql_query($sql) or die("ERROR1");
      $sql_num=mysql_num_rows($go);

      $ACC_TOKEN=$this->acctoken();

      require_once 'phpqrcode/phpqrcode.php';
      $value= $url = "http://jinfu.yiaigo.com/addshangji_index.php?ushar=".$ID;
      $errorCorrectionLevel = 'L';    //容错级别
      $matrixPointSize = 5;           //生成图片大小

      //生成二维码图片
      $filename ='images/haibao/'.$ID.'.png';
      QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2);



      $config = array(
          'text'=>array(
              array(
                  'text'=>$userNickname,
                  'left'=>10,
                  'top'=>1110,
                  'fontPath'=>'simhei.ttf',     	//字体文件
                  'fontSize'=>30,            			 	//字号
                  'fontColor'=>'f,f,f', 				//字体颜色
                  'angle'=>0,
              )
          ),
          'image'=>array(
              array(
                  'url'=>"http://jinfu.yiaigo.com/images/haibao/".$ID.".png",//erweima图片资源路径
                  'left'=>500,
                  'top'=>1005,
                  'stream'=>0,							//图片资源是否是字符串图像流
                  'right'=>0,
                  'bottom'=>0,
                  'width'=>120,
                  'height'=>120,
                  'opacity'=>100
              ),
              /* array(
                   'url'=>'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1525951192723&di=638102da56e5dad9056a26d4e10a6f7c&imgtype=0&src=http%3A%2F%2Fpic.58pic.com%2F58pic%2F13%2F74%2F66%2F70E58PICHRi_1024.jpg',//touxiang
                   'left'=>120,
                   'top'=>70,
                   'right'=>0,
                   'stream'=>0,
                   'bottom'=>0,
                   'width'=>55,
                   'height'=>55,
                   'opacity'=>100
               ),
       */    ),
          'background'=>'bg.jpg',
      );

      $filename="C:\phpstudy\WWW\jinfu\images\haibao\\".$ID.".jpg";
      $this->createPoster($config,$filename);

  }



   public function is_weixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    public function acctoken(){
        $myfile = fopen("accessToken/newfile.txt", "r") or die("Unable to open file!");
        $ACC_TOKEN=fread($myfile,filesize("accessToken/newfile.txt"));
        fclose($myfile);
        return $ACC_TOKEN;
    }

    public function createPoster($config=array(),$filename=""){
        //如果要看报什么错，可以先注释掉这个header
        if(empty($filename)) header("content-type: image/png");

        $imageDefault = array(
            'left'=>0,
            'top'=>0,
            'right'=>0,
            'bottom'=>0,
            'width'=>100,
            'height'=>100,
            'opacity'=>100
        );
        $textDefault =  array(
            'text'=>'',
            'left'=>0,
            'top'=>0,
            'fontSize'=>32,             //字号
            'fontColor'=>'255,255,255', //字体颜色
            'angle'=>0,
        );

        $background = $config['background'];//海报最底层得背景

        //背景方法
        $backgroundInfo = getimagesize($background);
        $backgroundFun = 'imagecreatefrom'.image_type_to_extension($backgroundInfo[2], false);
        $background = $backgroundFun($background);

        $backgroundWidth = imagesx($background);    //背景宽度
        $backgroundHeight = imagesy($background);   //背景高度

        $imageRes = imageCreatetruecolor($backgroundWidth,$backgroundHeight);
        $color = imagecolorallocate($imageRes, 0, 0, 0);
        imagefill($imageRes, 0, 0, $color);

        // imageColorTransparent($imageRes, $color);    //颜色透明

        imagecopyresampled($imageRes,$background,0,0,0,0,imagesx($background),imagesy($background),imagesx($background),imagesy($background));

        //处理了图片
        if(!empty($config['image'])){
            foreach ($config['image'] as $key => $val) {
                $val = array_merge($imageDefault,$val);

                $info = getimagesize($val['url']);
                $function = 'imagecreatefrom'.image_type_to_extension($info[2], false);
                if($val['stream']){		//如果传的是字符串图像流
                    $info = getimagesizefromstring($val['url']);
                    $function = 'imagecreatefromstring';
                }
                $res = $function($val['url']);
                $resWidth = $info[0];
                $resHeight = $info[1];
                //建立画板 ，缩放图片至指定尺寸
                $canvas=imagecreatetruecolor($val['width'], $val['height']);
                imagefill($canvas, 0, 0, $color);
                //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
                imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'],$resWidth,$resHeight);
                $val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']) - $val['width']:$val['left'];
                $val['top'] = $val['top']<0?$backgroundHeight- abs($val['top']) - $val['height']:$val['top'];
                //放置图像
                imagecopymerge($imageRes,$canvas, $val['left'],$val['top'],$val['right'],$val['bottom'],$val['width'],$val['height'],$val['opacity']);//左，上，右，下，宽度，高度，透明度
            }
        }

        //处理文字
        if(!empty($config['text'])){
            foreach ($config['text'] as $key => $val) {
                $val = array_merge($textDefault,$val);
                list($R,$G,$B) = explode(',', $val['fontColor']);
                $fontColor = imagecolorallocate($imageRes, $R, $G, $B);
                $val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']):$val['left'];
                $val['top'] = $val['top']<0?$backgroundHeight- abs($val['top']):$val['top'];
                imagettftext($imageRes,$val['fontSize'],$val['angle'],$val['left'],$val['top'],$fontColor,$val['fontPath'],$val['text']);
            }
        }



        //生成图片
        if(!empty($filename)){
            $res = imagejpeg ($imageRes,$filename,90); //保存到本地
            imagedestroy($imageRes);
            if(!$res) return false;
            return $filename;
        }else{
            imagejpeg ($imageRes);			//在浏览器上显示
            imagedestroy($imageRes);
        }
    }
}