<?php
class CommonAction extends Action
{
    protected $uid = 0;
    protected $member = array();
    protected $_CONFIG = array();
    protected $bizs = array();

    protected function _initialize()
    {
        define('__HOST__', 'http://' . $_SERVER['HTTP_HOST']);
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


    private function seo() {
        $this->assign('mobile_title', $this->mobile_title);
        $this->assign('mobile_keywords', $this->mobile_keywords);
        $this->assign('mobile_description', $this->mobile_description);
    }




	

    private function tmplToStr($str, $datas){
        preg_match_all('/{(.*?)}/', $str, $arr);
        foreach ($arr[1] as $k => $val) {
            $v = isset($datas[$val]) ? $datas[$val] : '';
            $str = str_replace($arr[0][$k], $v, $str);
        }
        return $str;
    }
    public function show($templateFile = ''){
        $this->seo();
        parent::display($templateFile);
    }
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = ''){
        $this->seo();
        parent::display($this->parseTemplate($templateFile), $charset, $contentType, $content = '', $prefix = '');
    }
    private function parseTemplate($template = ''){
        $depr = C('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);
        // 获取当前主题名称
        $theme = $this->getTemplateTheme();
        define('NOW_PATH', BASE_PATH . '/themes/' . $theme . 'Mobile/');
        // 获取当前主题的模版路径
        define('THEME_PATH', BASE_PATH . '/themes/default/Mobile/');
        define('APP_TMPL_PATH', __ROOT__ . '/themes/default/Mobile/');
        // 分析模板文件规则
        if ('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = strtolower(MODULE_NAME) . $depr . strtolower(ACTION_NAME);
        } elseif (false === strpos($template, '/')) {
            $template = strtolower(MODULE_NAME) . $depr . strtolower($template);
        }
        $file = NOW_PATH . $template . C('TMPL_TEMPLATE_SUFFIX');
        if (file_exists($file)) {
            return $file;
        }
        return THEME_PATH . $template . C('TMPL_TEMPLATE_SUFFIX');
    }
    private function getTemplateTheme(){
        define('THEME_NAME', 'default');
        if ($this->theme) {
            // 指定模板主题
            $theme = $this->theme;
        } else {
            /* 获取模板主题名称 */
            $theme = D('Template')->getDefaultTheme();
            if (C('TMPL_DETECT_THEME')) {
                // 自动侦测模板主题
                $t = C('VAR_TEMPLATE');
                if (isset($_GET[$t])) {
                    $theme = $_GET[$t];
                } elseif (cookie('think_template')) {
                    $theme = cookie('think_template');
                }
                if (!in_array($theme, explode(',', C('THEME_LIST')))) {
                    $theme = C('DEFAULT_THEME');
                }
                cookie('think_template', $theme, 864000);
            }
        }
        return $theme ? $theme . '/' : '';
    }
    protected function baoSuccess($message, $jumpUrl = '', $time = 3000){
        $str = '<script>';
        $str .= 'parent.success("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str .= '</script>';
        die($str);
    }
    protected function baoMsg($message, $jumpUrl = '', $time = 3000){
        $str = '<script>';
        $str .= 'parent.bmsg("' . $message . '","' . $jumpUrl . '","' . $time . '");';
        $str .= '</script>';
        die($str);
    }
    protected function baoErrorJump($message, $jumpUrl = '', $time = 3000){
        $str = '<script>';
        $str .= 'parent.error("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str .= '</script>';
        die($str);
    }
    protected function baoError($message, $time = 3000, $yzm = false){
        $str = '<script>';
        if ($yzm) {
            $str .= 'parent.error("' . $message . '",' . $time . ',"yzmCode()");';
        } else {
            $str .= 'parent.error("' . $message . '",' . $time . ');';
        }
        $str .= '</script>';
        die($str);
    }
    protected function baoAlert($message, $url = ''){
        $str = '<script>';
        $str .= 'parent.alert("' . $message . '");';
        if (!empty($url)) {
            $str .= 'parent.location.href="' . $url . '";';
        }
        $str .= '</script>';
        die($str);
    }
    protected function baoLoginSuccess(){
        //异步登录
        $str = '<script>';
        $str .= 'parent.parent.LoginSuccess();';
        $str .= '</script>';
        die($str);
    }
    protected function ajaxLogin(){
        $str = '<script>';
        $str .= 'parent.ajaxLogin();';
        $str .= '</script>';
        die($str);
    }
    protected function checkFields($data = array(), $fields = array()){
        foreach ($data as $k => $val) {
            if (!in_array($k, $fields)) {
                unset($data[$k]);
            }
        }
        return $data;
    }
    protected function ipToArea($_ip){
        return IpToArea($_ip);
    }
	
    protected function fengmiMsg($message, $jumpUrl = '', $time = 3000){
        $str = '<script>';
        $str .= 'parent.boxmsg("' . $message . '","' . $jumpUrl . '","' . $time . '");';
        $str .= '</script>';
        die($str);
    }
    protected function fengmiErrorJump($message, $jumpUrl = '', $time = 3000){
        $str = '<script>';
        $str .= 'parent.error("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str .= '</script>';
        die($str);
    }
    protected function fengmiAlert($message, $url = ''){
        $str = '<script>';
        $str .= 'parent.alert("' . $message . '");';
        if (!empty($url)) {
            $str .= 'parent.location.href="' . $url . '";';
        }
        $str .= '</script>';
        die($str);
    }
	
	
	
    protected function fengmiLoginSuccess(){
        //异步登录
        $str = '<script>';
        $str .= 'parent.parent.LoginSuccess();';
        $str .= '</script>';
        die($str);
    }
	protected function fengmiError($message, $time = 2000, $yzm = false, $parent = true){
		$parent = ($parent ? "parent." : "");
		$str = "<script>";
		if ($yzm) {
			$str .= $parent . "error(\"" . $message . "\"," . $time . ",\"verify()\");";
		}
		else {
			$str .= $parent . "error(\"" . $message . "\"," . $time . ");";
		}
		$str .= "</script>";
		exit($str);
	}
	
	protected function fengmiSuccess($message, $jumpUrl = "", $time = 2000, $parent = true){
		$parent = ($parent ? "parent." : "");
		$str = "<script>";
		$str .= $parent . "success(\"" . $message . "\"," . $time . ",'jump(\"" . $jumpUrl . "\")');";
		$str .= "</script>";
		exit($str);
	}
	
}