<?php
namespace app\admin\controller;
use app\admin\model\Links as LinksModel;
use app\admin\controller\Base;
use think\Db;

class Links extends  Base
{
	// 列表页输出
    public function lst(){     
        // 输出数组信息
        $list =LinksModel::paginate(10);
        $this->assign('list',$list);

        return $this->fetch();
    }
    // 管理员添加
    public function add(){     
        //获取提交的信息
        if(request()->isPost()){ 

            $data=[
                'title'=>input('title') ,              
                'url'=>get_complete_url(input('url')),
                'desc'=>(input('desc')),
            ];   
            // 验证提交的信息
            $validate = \think\ Loader::validate('links');
            // 显示错误信息
            if(!$validate->scene('add')->check($data)) {
                $this->error($validate->getError());
                die;
            }
            // 添加到数据库
            if(Db::name('links')->insert($data)){
                return $this->success('添加成功','lst');
            }else{
                return  $this->error('添加失败');
            }
            return;
        }
        return $this->fetch('');
    }

    //管理员修改 
    public function edit(){
        $id=input('id');
        $links=db('links')->find($id);
        if(request()->isPost()){
            $data=[
            'id'=>input('id'),
            'title'=>input('title'),   	
            'url'=>get_complete_url(input('url')),  
            'desc'=>input('desc'),  
            ];
            $validate = \think\ Loader::validate('links');
            // 显示错误信息
            if(!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
                die;
            }

            if(db('links')->update($data)){
                $this->success('修改成功！','lst');
            }else{
                $this->error('修改失败！');
            }
            return;
        }

        $this->assign('links',$links);  	
        return $this->fetch('');

    }
    // 删除操作
    public function del(){
        $id=input('id');
        if($id){
            if(db('links')->delete(input('id'))){
                $this->success('删除链接成功！','lst');
            }else{
                $this->error('删除链接失败！');
            }
        }

    }

}