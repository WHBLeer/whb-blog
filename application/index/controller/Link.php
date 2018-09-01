<?php
namespace app\index\controller;
use app\index\controller\Base;
class Link  extends  Base
{
	/**
	 * 友情链接
	 * @Author   wanghongbin
	 * @Email    wanghongbin@ngoos.org
	 * @DateTime 2018-07-23
	 * @return   [type]                [description]
	 */
    public function index()
    {
        $links  = db('links')->order('id asc')->select();
        $this->assign('links',$links);
		return $this->fetch();

    }  
}
