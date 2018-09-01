<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\admin\model\Article as ArticleModel;
use app\admin\model\Comment as CommentModel;

class Article extends  Base
{
    protected $favicon_url = 'https://api.byi.pw/favicon/';
    /**
     * 博客详情
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-08
     * @return   [type]                [description]
     */
    public function detail()
    {
        $id=input('id');
        db('article')->where('id', $id)->setInc('click');
        $article=db('article')->find($id);
        //使用limit方法查询该栏目的上一篇文章的id值
        $prev= db('article')->where('id','<',$id)->where('cate','=',$article['cate'])->order('id desc')->limit(1)->select();
        //使用limit方法查询该栏目的下一篇文章的id值
        $next= db('article')->where('id','>',$id)->where('cate','=',$article['cate'])->order('id asc')->limit(1)->select();
        if (empty($prev)) $prev[] = 'no_result';
        if (empty($next)) $next[] = 'no_result';

        $parents= db('comment')->where('article',$id)->where('parent','=',0)->order('time desc')->select();
        $childs= db('comment')->where('article',$id)->where('parent','>',0)->order('time desc')->select();
        foreach ($childs as $key => $child) {
            for ($i=0; $i < count($parents); $i++) { 
                if ($child['parent'] == $parents[$i]['id']) {
                    $parents[$i]['childs'][] = $child;
                }
            }
        }

        $this->assign('comments',$parents);
        $this->assign('prev',$prev[0]);
        $this->assign('next',$next[0]);
        $this->assign('article',$article);
        return $this->fetch();
    }

    // 喜欢
    public function likes()
    {
        $id=input('id');
        db('article')->where('id', $id)->setInc('click');
        $article=db('article')->find($id);
        return json_encode(['data'=>$data,'code'=>200,'message'=>'已点赞！']);
    }

    // 添加评论
    public function comment()
    {
        //获取提交的信息
        $avatar = create_letter_avatar(input('nickname')?:'User');
        $comment = comment_to_html(input('comment'));
        $conf = get_sys_config();
        $status = 0;
        if ($conf['COMMENT_REVIEW']) $status = 1;

        $data=[
            'nickname'  => input('nickname'),
            'email'     => input('email'),
            'url'       => input('url')?:'',
            'article'   => input('article'),
            'parent'    => input('parent'),
            'comment'   => $comment,
            'notify'    => input('notify'),
            'status'    => $status,
            'time'      => time(),
            'avatar'    => $avatar,
        ];
        
        // 添加到数据库
        if($id = db('comment')->insertGetId($data)){
            $comment =db('comment')->find($id);
            $commentDatas = [
                'ID' => $comment['id'],
                'TIME' => word_time($comment['time']),
                'NICKNAME' => $comment['nickname'],
                'AVATAR' => $comment['avatar'],
                'ARTICLE' => $comment['article'],
                'COMMENT' => $comment['comment'],
                'NOWPAGE' => $comment['id'],
            ];
            if (input('parent')>0) {
                $commentContent =file_get_contents(MOULD_PATH.DS.'/comment-children.tmp');
                foreach ($commentDatas as $key => $value) {
                    $commentContent = str_replace('###'.$key.'###', $value, $commentContent);
                }
                return json_encode(array('pid'=> input('parent'),'tt'=>$commentContent));
                // return '<ul class="children">'.$commentContent.'</ul>';
            } else {
                $commentContent = file_get_contents(MOULD_PATH.DS.'/comment-parent.tmp');
                foreach ($commentDatas as $key => $value) {
                    $commentContent = str_replace('###'.$key.'###', $value, $commentContent);
                }
                return $commentContent;
                // return '<ol class="comment-list">'.$commentContent.'</ol>';
            }
            
        }else{
            return json_encode(['code'=>200,'message'=>'评论提交错误！']);
        }
        return;
    }

}
