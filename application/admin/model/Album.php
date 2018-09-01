<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Debug;

class Album extends Model
{
	public function picture()
    {
        return $this->hasMany('Picture','albid');
    }
}
