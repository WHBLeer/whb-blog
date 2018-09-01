<?php
namespace app\admin\controller;
use app\admin\model\Comment as CommentModel;
use app\admin\controller\Base;

use think\Db;
class Comment extends  Base
{
	/**
     * [列表页输出]
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-18
     * @return   [type]                [description]
     */
    public function index()
    {     
        // tp5关联数据表
        $list=db('comment')->alias('c')
            ->join('article a','a.id=c.article','LEFT')
            ->field('c.id, c.article, c.comment, c.status, c.notify, c.time, c.parent, a.title ')
            ->order('c.status asc')
            ->paginate(12);

        // 调试SQL
        // $list = CommentModel::paginate(12); 
        // dump($list);exit;
        // return json_encode($list);
        $this->assign('list',$list);
        
        return $this->fetch();
    }

    /**
     * 管理员审核
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function check(){
        $data=[
            'id'=>input('id'), 
            'status'=>input('status'),  
        ];

        $res = db('comment')->update($data);
        if($res==1){
            $this->success('评论审核成功！','index');
        }else{
            $this->error('评论审核失败！');
        }
    }

    /**
     * 删除操作
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function del(){
    	$id=input('id');
    	if($id){
    		if(db('comment')->delete(input('id'))){
    			$this->success('删除评论成功！','lst');
    		}else{
    			$this->error('删除评论失败！');
    		}
    	}
    	
    }
    
}