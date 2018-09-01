<?php
namespace app\index\controller;
use app\index\controller\Base;
class Words  extends  Base
{
	/**
	 * 碎言
	 * @Author   wanghongbin
	 * @Email    wanghongbin@ngoos.org
	 * @DateTime 2018-07-23
	 * @return   [type]                [description]
	 */
    public function index()
    {
		$words = db('Words')->where(array())->order('time desc')->paginate(10);

		$this->assign('words',$words);
		return $this->fetch();

    }  

    /**
     * 博客详情
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-08
     * @return   [type]                [description]
     */
    public function detail()
    {
        $id=input('id');
        $word=db('words')->find($id);
        $this->assign('word',$word);
        return $this->fetch();
    }
}
