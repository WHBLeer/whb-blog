<?php
namespace app\admin\controller;
use app\admin\model\Cate as CateModel;
use app\admin\controller\Base;

use think\Db;
class Cate extends Base{
	/**
     * [列表页输出]
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-18
     * @return   [type]                [description]
     */
    public function lst(){     
    	// 输出数组信息
        $list = CateModel::paginate(10);
        $this->assign('list',$list);

        return $this->fetch();
    }
    /**
     * 管理员添加
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-18
     */
    public function add()
    {     
        //获取提交的信息
    	if(request()->isPost()){ 
            $cates = array_unique(array_filter(input('catename/a')));
            foreach ($cates as $key => $cate) {
                // 判断重复
                $res = db('cate')->where('catename','=',$cate)->select();
                if (count($res)==0) {
                    $data=['catename'=>$cate];   
                    // 验证提交的信息
                    $validate = \think\ Loader::validate('Cate');
                    // 显示错误信息
                    if(!$validate->scene('add')->check($data)) {
                        $this->error($validate->getError());
                        die;
                    }
                    // 添加到数据库
                    db('cate')->insert($data);
                }
            }
            // 添加到数据库
    		if(Db::name('cate')->getLastInsID()){
        		return $this->success('添加成功!','lst');
        	}else{
        		return  $this->error('添加失败!');
        	}
        }
        return $this->fetch('');

    }
    
    /**
     * 管理员修改
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-18
     * @return   [type]                [description]
     */
    public function edit(){
        $id=input('id');
    	$cate=db('cate')->find($id);
    	if(request()->isPost()){
    		$data=[
                'id'=>input('id'),
                'catename'=>input('catename'),   	
    		];
    		
			$validate = \think\ Loader::validate('Cate');
			// 显示错误信息
			if(!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
                die;
			}
    		if(db('cate')->update($data)){
    			$this->success('修改成功！','lst');
    		}else{
    			$this->error('未做任何修改！');
    		}
    		return;
    	}
    
    	$this->assign('cate',$cate);  	
        return $this->fetch('');

    }
       // 删除操作
    public function del(){
    	$id=input('id');
    	if($id ){
    		if(db('cate')->delete(input('id'))){
    			$this->success('删除栏目成功！','lst');
    		}else{
    			$this->error('删除栏目失败！');
    		}
    	}
    	
    }
}