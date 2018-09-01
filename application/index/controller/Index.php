<?php
namespace app\index\controller;
use app\index\controller\Base;
class Index  extends  Base
{
	/**
	 * 博客首页
	 * @Author   wanghongbin
	 * @Email    wanghongbin@ngoos.org
	 * @DateTime 2018-07-23
	 * @return   [type]                [description]
	 */
    public function index()
    {        
        $articleres=db('article')->alias('a')
            ->join('(select id,tagname from whb_tags) t','t.id in(a.tags)','LEFT')
            ->join('cate c','c.id=a.cate','LEFT')
            ->field('a.id, a.title, a.author, a.pic, a.time, a.keywords, a.cate, a.state, a.click, a.desc, c.catename ,case when t.tagname is null then "" else t.tagname end ')
            ->where('a.id','>','1')
            ->order('a.time desc')
            ->order('a.likes desc')
            ->paginate(12);
            // ->paginate(6);
        // $articleres=db('article')->order('id desc')->paginate(30);
        $this->assign('articleres',$articleres);
		return $this->fetch();

    }  
}
