<?php
namespace app\admin\controller;
use app\admin\model\Pwdmanage as PwdmanageModel;
use app\admin\controller\Base;

use think\Db;

class Pwdmanage extends  Base
{
    /**
     * 我的密码
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function lst(){     
        $list = PwdmanageModel::paginate(12); 
        // dump(db('Album')->fetchSql());
        $this->assign('list',$list);
        return $this->fetch();
    }  

    /**
     * 添加密码
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @param    string                $value [description]
     */
    public function add()
    {
        //获取提交的信息
        if(request()->isPost()){ 
            $data=[
                'sitename'=>input('sitename') ,              
                'link'=>input('link') ,              
                'username'=>input('username') ,              
                'password'=>input('password') ,              
            ];   
            // 验证提交的信息
            $validate = \think\ Loader::validate('Pwdmanage');
            // 显示错误信息
            if(!$validate->scene('add')->check($data)) {
             $this->error($validate->getError());
             die;
            }
            // 添加到数据库
            if(Db::name('pwdmanage')->insert($data)){
                return $this->success('添加成功','lst');
            }else{
                return  $this->error('添加失败');
            }
            return;
        }
        return $this->fetch('');
    }

    /**
     * 编辑密码
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function edit()
    {
        $id=input('id');
        $password=db('pwdmanage')->find($id);
        if(request()->isPost()){
            $data=[
                'id'=>input('id'),   
                'sitename'=>input('sitename'),      
                'link'=>input('link') ,                       
                'username'=>input('username'),      
                'password'=>input('password') ,
            ];
            
            $validate = \think\ Loader::validate('Pwdmanage');
            // 显示错误信息
            if(!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
                die;
            }

            if(db('pwdmanage')->update($data)){
                $this->success('修改成功！','lst');
            }else{
                $this->error('修改失败！');
            }
            return;
        }
    
        $this->assign('password',$password);    
        return $this->fetch('');
    }

    // 删除操作
    public function del(){
        $id=input('id');
        if($id ){
            if(db('pwdmanage')->delete(input('id'))){
                $this->success('删除密码成功！','lst');
            }else{
                $this->error('删除密码失败！');
            }
        }
        
    }
}
