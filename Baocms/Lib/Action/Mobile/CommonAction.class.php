<?php
class CommonAction extends Action
{
    protected $uid = 0;
    protected $member = array();
    protected $_CONFIG = array();
    protected $bizs = array();

    protected function _initialize()
    {
        
        define('IN_MOBILE', true);
        searchWordFrom();
        $this->uid = getUid();
        if (!empty($this->uid)) {
            $member = $MEMBER = $this->member = D('Users')->find($this->uid);
            //客户端缓存会员数据
            $member['password'] = '';
            $member['token'] = '';
            cookie('member', $member);
        }
        $this->_CONFIG = D('Setting')->fetchAll();
        define('__HOST__', $this->_CONFIG['site']['host']);
        if (!empty($city['name'])) {
            $this->_CONFIG['site']['cityname'] = $city['name'];
        }
        $this->assign('MEMBER', $this->member);
        $this->assign('today', TODAY);
        //兼容模版的其他写法
        $this->assign('nowtime', NOW_TIME);
        $this->assign('ctl', strtolower(MODULE_NAME));
        //主要方便调用
        $this->assign('act', ACTION_NAME);
    }







	





	
	

	

	//lcf $error success faild
	public function jsonout($error="faild",$msg="",$data){
        $result=array();
        $result['error']=$error;
        $result['msg']=$msg;
        $result['data']=$data;
        die(json_encode($result));
    }
	
}