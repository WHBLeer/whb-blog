<?php
namespace app\admin\controller;
use app\admin\model\Tags as TagsModel;
use app\admin\controller\Base;

use think\Db;
class Tags extends Base{
	/**
     * [列表页输出]
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-18
     * @return   [type]                [description]
     */
    public function lst(){     
    	// 输出数组信息
        $list = TagsModel::paginate(100);
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
            $tags = array_unique(array_filter(input('tagname/a')));
            $state = false;
            foreach ($tags as $key => $tag) {
                // 判断重复
                $res = db('tags')->where('tagname','=',$tag)->select();
                if (count($res)==0) {
                    $maxid = Db::query('select max(id) max from whb_tags');
                    $data=[
                        'id'=>$maxid[0]['max']+1,
                        'tagname'=>$tag,
                    ];   
                    // 验证提交的信息
                    $validate = \think\ Loader::validate('Tags');
                    // 显示错误信息
                    if(!$validate->scene('add')->check($data)) {
                        $this->error($validate->getError());
                        die;
                    }
                    // 添加到数据库
                    db('tags')->insert($data);
                    $state = true;
                }
            }
            // 添加到数据库
    		if($state){
        		return $this->success('添加成功','lst');
        	}else{
        		return  $this->error('添加失败');
        	}
        	return;
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
    	$tags=db('tags')->find($id);
    	if(request()->isPost()){
    		$data=[
                'id'=>input('id'),
                'tagsname'=>input('tagsname'),   	
    		];
    		
			$validate = \think\ Loader::validate('Tags');
			// 显示错误信息
			if(!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
                die;
			}

    		if(db('tags')->update($data)){
    			$this->success('修改成功！','lst');
    		}else{
    			$this->error('未做任何修改！');
    		}
    		return;
    	}
    
    	$this->assign('tags',$tags);  	
        return $this->fetch('');

    }
       // 删除操作
    public function del(){
    	$id=input('id');
    	if($id ){
    		if(db('tags')->delete(input('id'))){
    			$this->success('删除栏目成功！','lst');
    		}else{
    			$this->error('删除栏目失败！');
    		}
    	}
    	
    }
}