<?php
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Admin extends  Validate
{
  protected $rule =[
'username'=>'require|max:25|unique:admin',
'password'=>'require',
  ];
  // 验证信息提示
  	protected $message = [
'username.require' =>'管理员名称必须填写',
  'username.max' =>'管理员名称不得大于25位',
   'username.unique' =>'管理员名称不得重复',
'password.require' =>'管理员密码必须填写',
  	];
  	// 验证场景
  protected $scene = [
'add' =>['username'=>'require|max:25|unique:admin','password'],
'edit' =>['username'=>'require|max:25|unique:admin'],


  ];

}