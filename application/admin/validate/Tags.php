<?php
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Tags extends  Validate
{
	protected $rule =[
		'tagname'=>'require|max:10',
	];
	// 验证信息提示
	protected $message = [
		'tagname' =>'标签标题必须填写',
		'tagname.max' =>'标签标题不得大于10位',
	];
	// 验证场景
	protected $scene = [
		'add' =>['tagname'=>'require'],
		'edit' =>['tagname'=>'require'],
	];

}