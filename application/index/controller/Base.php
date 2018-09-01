<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Base  extends  Controller
{
    /**
     * 初始化方法
     * @author 蔡伟明 <314013107@qq.com>
     */
    protected function _initialize()
    {
        //屏蔽所有IE浏览器
        $isIE = strpos($_SERVER['HTTP_USER_AGENT'],"Triden");
        if ($isIE) $this->redirect(SITE_URL.DS.'killie',302);

        // 系统开关
        $conf = \think\Config::get('sysconf');
        if (!$conf['WEB_STATUS']) $this->redirect(SITE_URL.DS.'repair',302);

        // 配置
        $this ->assign('conf',$conf); 
        $cateres= db('cate')->order('id asc')->select();
        $this->assign('cateres',$cateres);
    }
}
