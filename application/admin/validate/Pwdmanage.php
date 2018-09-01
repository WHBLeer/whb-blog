<?php
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Pwdmanage extends Validate
{
	protected $rule =[
		'sitename'=>'require',
		'link'=>'require',
		'username'=>'require',
		'password'=>'require',

	];
	// 验证信息提示
	protected $message = [
		'sitename' =>'站点名称必须填写',
		'link' =>'相册链接必须填写',
		'username' =>'用户名必须填写',
		'password' =>'用户密码必须填写',

	];
	// 验证场景
  	protected $scene = [
		'add' =>['sitename','link','username','password'],
		'edit' =>['sitename','link','username','password'],
  	];

}