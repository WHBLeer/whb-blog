<?php
namespace app\index\controller;
use app\index\controller\Base;
class Cate  extends  Base
{
    public function index()
    {
    	$cateid=input('id');
    	//查询当前栏目名称
    	$cates=db('cate')->find($cateid);
    	$this->assign('cates',$cates);
    	//查询当前栏目下的文章
        $articleres=db('article')->alias('a')
            ->join('(select id,tagname from whb_tags) t','t.id in(a.tags)','LEFT')
            ->join('cate c','c.id=a.cate','LEFT')
            ->field('a.id, a.title, a.author, a.pic, a.time, a.keywords, a.cate, a.state, a.click, a.desc, c.catename ,case when t.tagname is null then "" else t.tagname end ')
            ->where('a.cate='.$cateid)
            ->where('a.id>1')
            ->order('a.click desc')
            ->paginate(12);
    	$this->assign('articleres',$articleres);
        return $this->fetch('');

    }  
}
