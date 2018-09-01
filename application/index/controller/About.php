<?php
namespace app\index\controller;
use app\index\controller\Base;
class About  extends  Base
{
	// 关于我
    public function index()
    {
        db('article')->where('id', 1)->setInc('click');
        $about=db('article')->find(1);
        
        $comments= db('comment')->where('article','=',1)->select();
        // $comments = get_comment_list();
        $this->assign('comments',$comments);
        $this->assign('about',$about);
        return $this->fetch('index');
    }
    
    // 简历
    public function resume()
    {
        return $this->fetch('resume');
    }

    // 慎点
    public function skin()
    {
        return $this->fetch('skin');
    }
}
