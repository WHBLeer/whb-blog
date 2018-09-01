<?php
namespace app\index\controller;
use app\index\controller\Base;
class Search  extends  Base
{
    public function index()
    {
        $keywords=input('keywords');
        if($keywords){
            $map['id'] = ['gt',1]; 
            $map2 = [
                ['title'=>['like', '%'.$keywords.'%']],
                ['keywords'=>['like', '%'.$keywords.'%']],
                ['content'=>['like', '%'.$keywords.'%']],
            ];
            $searchres=db('article')->where($map)->where(
                function($q) use($map2){
                    foreach ($map2 as $key => $val) {
                        $q->whereOr($val);
                    }
                })
            ->paginate(12);
            
            $this->assign(array(
                'searchres'=>$searchres,
                'keywords'=>$keywords,
            ));

        }else{
            $this->assign(array(
                'searchres'=>null,
                'keywords'=>'',
            ));
        } 
        $words =db('article')->where('id!=1')->field('keywords')->order('id asc')->select();
        $wordsStr = implode(',', array_column($words, 'keywords'));
        $wordsArr = array_unique(explode(',', $wordsStr));
        $this->assign('words',$wordsArr);
        return $this->fetch('index');
    }
     
}
