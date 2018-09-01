<?php
namespace app\admin\controller;
use app\admin\model\Article as ArticleModel;
use app\admin\controller\Base;

use think\Db;
class Article extends  Base
{
	/**
     * [列表页输出]
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-18
     * @return   [type]                [description]
     */
    public function lst()
    {     
        // tp5关联数据表
        $list=db('article')->alias('a')
            ->join('(select id,tagname from whb_tags) t','t.id in(a.tags)','LEFT')
            ->join('cate c','c.id=a.cate','LEFT')
            ->field('a.id, a.title, a.author, a.pic, a.time, a.keywords, a.cate, a.state, c.catename ,case when t.tagname is null then "" else t.tagname end ')
            ->order('a.time desc')
            ->paginate(12);

        // 调试SQL
        // $list = ArticleModel::with('cate')->paginate(12); 
        // dump($list);exit;
        // return json_encode($list);
        $this->assign('list',$list);
        
        return $this->fetch();
    }

    /**
     * 管理员添加
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-18
     */
    public function add(){   
        //获取提交的信息
    	if(request()->isPost()){ 
    		$data=[
    			'title'=>input('title') ,              
    			'author'=>input('author'),
                'desc'=>input('desc'),
                'keywords'=>str_replace('，',',',input('keywords')),
                'cate'=>input('cate'),
                'content'=>markdown_to_html(input('mdcontent')),
                'mdcontent'=>input('mdcontent'),
                'time'=>time(),
                'state'=>input('state')?:0,
                // 'tags' => implode(',',input('tags/a')),
    		];
            if(input('state')=='on'){
                $data['state']=1;
            }

            // 不填写简介时，则截取文章内容的前200字作为简介
            if (empty(input('desc'))) {
                $desc = preg_replace(array('/[~*>#-]*/', '/!?\[.*\]\(.*\)/', '/\[.*\]/'), '', input('mdcontent'));
                $data['desc'] = trimall(re_substr($desc, 0, 180, true));
            }
            
            $file = request()->file('pic');
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $savePath = ROOT_PATH . 'public' . DS . 'uploads/';
                if (!is_dir($savePath)) mkdir($savePath, 755,true);
                $info = $file->move($savePath);
                if($info){
                    $data['pic'] = DS . 'uploads/'.$info->getSaveName();
                }else{
                    $data['pic'] = self::articleImages(input('mdcontent'));
                }
            }

            // 验证提交的信息
			$validate = \think\ Loader::validate('Article');
			// 显示错误信息
			if(!$validate->scene('add')->check($data)) {
                $this->error($validate->getError());
                die;
			}
            // 添加到数据库
    		if(Db::name('Article')->insert($data)){
                $id = Db::name('Article')->getLastInsID();
                $this->postArticle($id);
        		return $this->success('添加成功','lst');
        	}else{
        		return  $this->error('添加失败');
        	}
        	return;
        }
        $cateres=db('cate')->select();
        $tags=db('tags')->select();
        $this->assign('cateres',$cateres);
        $this->assign('tags',$tags);
        $this->assign('author',author);
        return $this->fetch('');
    }

    /**
     * 管理员修改
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function edit(){
        $id=input('id');
        $article=db('article')->find($id);
        if(request()->isPost()){
            $data=[
                'id'=>input('id'), 
                'title'=>input('title'),       
                'author'=>input('author'), 
                'desc'=>input('desc'),
                'keywords'=>str_replace('，', ',', input('keywords')),
                'content'=>markdown_to_html(input('mdcontent')),
                'mdcontent'=>input('mdcontent'),
                'cate'=>input('cate'),
                'state'=>input('state'),
                // 'tags' => implode(',',input('tags/a')),
            ];

            if(input('state')=='on'){
                $data['state']=1;
            }

            // 不填写简介时，则截取文章内容的前200字作为简介
            if (empty(input('desc'))) {
                $desc = preg_replace(array('/[~*>#-]*/', '/!?\[.*\]\(.*\)/', '/\[.*\]/'), '', input('mdcontent'));
                $data['desc'] = trimall(re_substr($desc, 0, 180, true));
            }

            // 图片上传
            if(isset($_FILES['pic']) && $_FILES['pic']['tmp_name']){
                $savePath = ROOT_PATH . 'public' . DS . 'uploads/';
                // 若目录不存在则创建
                if (!is_dir($savePath)) mkdir($savePath, 755,true);
                $file=request()->file('pic');
                $info = $file->move($savePath);
                // 图片添加水印
                $data['pic'] = water_text_mark(DS . 'uploads/'.$info->getSaveName());
            }else{
                if (input('oldpic')) {
                    $data['pic'] = input('oldpic');
                }else{
                    $data['pic'] = self::articleImages(input('mdcontent'));
                }
            }
            $validate = \think\Loader::validate('article');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->getDbError()); die;
            }

            $res = db('article')->update($data);
            if($res==1){
                // 添加站点地图
                $this->success('修改文章成功！','lst');
            }elseif ($res==0) {
                $this->error('文章没有任何修改！');
            }else{
                $this->error('修改文章失败！');
            }
            return;
        }
        $cateres = db('cate')->select();
        $tags    = db('tags')->select();
        $select2 = '$(".select2").val([' . $article['tags'] . ']).select2();';
        $this->assign('cateres',$cateres);
        $this->assign('tags',$tags);
        $this->assign('article',$article);
        $this->assign('select2',$select2);
        return $this->fetch();
    }

    /**
     * 删除操作
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function del(){
    	$id=input('id');
    	if($id){
    		if(db('article')->delete(input('id'))){
    			$this->success('删除文章成功！','lst');
    		}else{
    			$this->error('删除文章失败！');
    		}
    	}
    	
    }
    
    public function articleImages($content)
    {
        // 获取文章中的全部图片
        preg_match_all('/!\[.*?\]\((\S*).*\)/i', $content, $images);
        if (empty($images[1])) {
            $pic = DS.'uploads/default/default.jpg';
        } else {
            foreach ($images[1] as $k => $v) {
                $image = explode(' ', $v);
                // 取第一张图片作为封面图
                if ($k == 0) {
                    $pic = $image[0];
                    break;
                }
            }
        }
        return $pic;
    }

    /**
     * 配合 editor.Md 文件上传方法
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function editorMdUpload(){
        $name = 'editormd-image-file';
        $savePath = ROOT_PATH . 'public' . DS . 'uploads/';
        // 若目录不存在则创建
        if (!is_dir($savePath)) mkdir($savePath, 755,true);
        $saveURL  = SITE_URL . DS . 'uploads/';

        $suc = 0; $msg = ''; $url = '';
        if (isset($_FILES[$name])){        
            $file = request()->file($name);
            if($info = $file->move($savePath)){
                $suc = 1;
                $url = $saveURL.$info->getSaveName();
                $img = water_text_mark(DS . 'uploads/'.$info->getSaveName());
            }else{
                $msg="系统异常，文件保存失败";
            }
        } else {
            $msg = 'Not File';
        }

        $data = array(
            'success' => $suc,  //1：上传成功  0：上传失败
            'message' => $msg,
            'url'     => DS.'uploads'.DS.$info->getSaveName()
        );
        header('Content-Type:application/json;charset=utf8');
        exit(json_encode($data));
    }

    /**
     * 主动推送到百度
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-16
     * @param    [type]                $id [description]
     * @return   [type]                    [description]
     */
    public function postArticle($id)
    {
        $urls = array(SITE_URL . url('index/Article/detail',array('id'=>$id)),);

        $api = 'http://data.zz.baidu.com/urls?site=https://www.whongbin.cn&token=khMnOVISUGilWIoz';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return ;
                            
    }
}