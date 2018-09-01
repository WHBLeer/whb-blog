<?php
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Album extends  Validate
{
	protected $rule =[
		'albumname'=>'require|max:15',
	];
  // 验证信息提示
  	protected $message = [
		'albumname.require' =>'相册名称必须填写',
		'albumname.max' =>'相册名称不得大于15位',
  	];
  	// 验证场景
  	protected $scene = [
		'add' =>['albumname'=>'require'],
		'edit' =>['albumname'=>'require'],
  	];

}