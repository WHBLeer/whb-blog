<?php
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Links extends  Validate
{
	protected $rule =[
		'title'=>'require|max:25',
		'url'=>'require',
	];
	// 验证信息提示
	protected $message = [
		'title.require' =>'链接标题必须填写',
		'title.max' =>'链接标题不得大于25位',
		'url.require' =>'链接地址必须填写',
	];
	// 验证场景
	protected $scene = [
		'add' =>['title'=>'require','url'],
		'edit' =>['title'=>'require'],
	];

}