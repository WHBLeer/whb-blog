<?php
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Words extends  Validate
{
	protected $rule =[
		'content'=>'require|max:100',
	];
	// 验证信息提示
	protected $message = [
		'content.require' =>'碎言内容必须填写',
		'content.max' =>'碎言内容不得大于25位',
	];
	// 验证场景
	protected $scene = [
		'add' =>['content'=>'require'],
		'edit' =>['content'=>'require'],
	];

}