<?php
namespace app\admin\controller;
use app\admin\model\Words as WordsModel;
use app\admin\controller\Base;
use think\Db;

class Words extends  Base
{
	// 列表页输出
    public function index(){     
        // 输出数组信息
        $list =WordsModel::paginate(15);
        $this->assign('list',$list);

        return $this->fetch();
    }
    // 管理员添加
    public function add(){     
        //获取提交的信息
        if(request()->isPost()){ 

            $data=[
                'content'=>input('content') ,              
                'author'=>input('author'),
                'time'=>time(),
            ];   
            // 验证提交的信息
            $validate = \think\ Loader::validate('Words');
            // 显示错误信息
            if(!$validate->scene('add')->check($data)) {
                $this->error($validate->getError());
                die;
            }
            // 添加到数据库
            if(Db::name('Words')->insert($data)){
                return $this->success('添加成功','index');
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
        $word=db('Words')->find($id);
        if(request()->isPost()){
            $data=[
                'id'=>input('id') ,              
                'content'=>input('content') ,              
            ]; 
            $validate = \think\ Loader::validate('Words');
            // 显示错误信息
            if(!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
                die;
            }

            if(db('Words')->update($data)){
                $this->success('修改成功！','index');
            }else{
                $this->error('修改失败！');
            }
            return;
        }

        $this->assign('word',$word);  	
        return $this->fetch('');

    }
    // 删除操作
    public function del(){
        $id=input('id');
        if($id){
            if(db('Words')->delete(input('id'))){
                $this->success('删除链接成功！','index');
            }else{
                $this->error('删除链接失败！');
            }
        }

    }

}