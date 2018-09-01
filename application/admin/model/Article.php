<?php
namespace app\admin\model;
use think\Model;
class Article extends Model
{
	// 文章数据表关联栏目数据表
  	public function cate(){
  		// 多篇文章属于一个分类
        return $this->belongsTo('Cate','cate', 'id');
  	}
	
	// 文章数据表关联标签数据表
  	public function tags(){
  		return $this->hasMany('Tags', 'id', 'id');
  	}

    // 文章数据表关联评论数据表
    public function comment(){
      return $this->hasMany('Comment', 'id', 'id');
    }

    public static function getComment($id) 
    {
        $comment = self::with('comment')->find($id); // 通过 with 使用关联模型，参数为关联关系的方法名
        return $tag;
    }

  	public static function getTags($id)	
    {
        $tag = self::with('tags')->find($id); // 通过 with 使用关联模型，参数为关联关系的方法名
        return $tag;
    }

    public static function getCate()  
    {
        $cate = self::with('cate')->select(); // 通过 with 使用关联模型，参数为关联关系的方法名
        return $cate;
    }
}
