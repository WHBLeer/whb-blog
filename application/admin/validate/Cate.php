<?php
namespace app\admin\validate;
use think\Validate;
use think\Db;

class Cate extends  Validate
{
  protected $rule =[
'catename'=>'require|max:25',

  ];
  // 验证信息提示
  	protected $message = [
'catename' =>'栏目标题必须填写',
  'catename.max' =>'栏目标题不得大于25位',

  	];
  	// 验证场景
  protected $scene = [
'add' =>['catename'=>'require'],
'edit' =>['catename'=>'require'],


  ];

}