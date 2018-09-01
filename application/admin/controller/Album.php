<?php
namespace app\admin\controller;
use app\admin\model\Album as AlbumModel;
use app\admin\controller\Base;

use think\Db;

class Album extends  Base
{
    /**
     * 我的相册
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function lst(){     
        $list = AlbumModel::paginate(8); 
        // dump(db('Album')->fetchSql());
        $this->assign('list',$list);
        return $this->fetch();
    }  

    /**
     * 查看相册
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @param    string                $value [description]
     */
    public function show()
    {
        # code...
    }

    /**
     * 添加相册
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @param    string                $value [description]
     */
    public function add()
    {
        //获取提交的信息
        if(request()->isPost()){ 
            $data=[
                'albumname' =>input('albumname') ,
                'desc'      =>input('desc'),
                'admin'     =>input('admin')?:0,
                'crdate'    =>time(),
            ];

            // 不填写简介时，是用标题作为简介
            if (empty(input('desc'))) {
                $data['desc'] = trimall(input('albumname'));
            }

            // 若目录不存在则创建
            $savePath = ROOT_PATH . 'public' .DS. 'uploads' .DS. 'pictures/';
            if (!is_dir($savePath)) mkdir($savePath, 755,true);

            // 相册封面图片上传
            $file = request()->file('cover');
            if ($file) {
                $info = $file->move($savePath);
                $data['cover'] = DS . 'uploads' .DS. 'pictures/'.$info->getSaveName();
            }

            // 验证提交的信息
            $validate = \think\ Loader::validate('Album');
            // 显示错误信息
            if(!$validate->scene('add')->check($data)) {
                $this->error($validate->getError());
                die;
            }
            $pictures = request()->file('pictures');
            // 添加到数据库
            if(Db::name('Album')->insert($data)){
                $albumid = Db::name('Album')->getLastInsID();
                $result = self::uploadPic($albumid,$pictures);
                if ($result) {
                    return json_encode(
                        array(
                            'code'=>1,
                            'msg'=>'相册创建成功！',
                            'albumid'=>$albumid,
                            'url'=>url('album/lst')
                        )
                    );
                }else{
                    return json_encode(
                        array(
                            'code'=>0,
                            'msg'=>'相册创建成功！相册内容上传失败',
                            'albumid'=>$albumid,
                            'url'=>url('album/lst')
                        )
                    );
                }
                
            }else{
                return  $this->error('相册创建失败！');
            }
            return;
        }
        return $this->fetch('');
    }

    /**
     * 编辑相册
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    public function edit()
    {
        $albumid=input('id');
        $album=db('album')->find($albumid);
        //获取提交的信息
        if(request()->isPost()){ 
            $data=[
                'albumname' =>input('albumname') ,
                'desc'      =>input('desc'),
                'admin'     =>input('admin')?:0,
            ];

            // 不填写简介时，使用标题作为简介
            if (empty(input('desc'))) {
                $data['desc'] = trimall(input('albumname'));
            }

            // 若目录不存在则创建
            $savePath = ROOT_PATH . 'public' .DS. 'uploads' .DS. 'pictures/';
            if (!is_dir($savePath)) mkdir($savePath, 755,true);

            // 相册封面图片上传
            $file = request()->file('cover');
            if ($file) {
                $info = $file->move($savePath);
                $data['cover'] = DS . 'uploads' .DS. 'pictures/'.$info->getSaveName();
            }

            // 验证提交的信息
            $validate = \think\ Loader::validate('Album');
            // 显示错误信息
            if(!$validate->scene('add')->check($data)) {
                $this->error($validate->getError());
                die;
            }
            $pictures = request()->file('pictures');
            file_put_contents('album.log', date('Y-m-d H:i:s') . '===data===' . \json_encode($data) . chr(10).chr(10),FILE_APPEND | LOCK_EX);
            $res = db('Album')->update($data);
            if($res==1){
                if ($pictures) self::uploadPic($albumid,$pictures);
                $this->success('相册编辑成功！','lst');
            }elseif ($res==0) {
                $this->error('相册没有任何修改！');
            }else{
                $this->error('相册编辑失败！');
            }
            return;
        }
        $pictures = db('picture')->where('album_id ='.$albumid)->select();
        $this->assign('album',$album);
        $this->assign('pictures',$pictures);
        return $this->fetch('');
    }

    /**
     * 上传图片
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-19
     * @return   [type]                [description]
     */
    public function uploadPic($albumid,$files)
    {
        // 若目录不存在则创建
        $savePath = ROOT_PATH . 'public' .DS. 'uploads' .DS. 'pictures/';
        if (!is_dir($savePath)) mkdir($savePath, 755,true);
        // 相册中照片上传
        $saveURL  = SITE_URL . '/uploads' .DS. 'pictures/';

        $infoArr = array();
        foreach($files as $key => $pic){
            dump($pic);
            file_put_contents('album.log', date('Y-m-d H:i:s') . '===files===' . \json_encode($pic) . chr(10).chr(10),FILE_APPEND | LOCK_EX);
            $res = $this->qiniuUpload($pic);
            dump($res);
            // $infoArr[$key] = $pic->move($savePath);
            // $imgurl = $saveURL.$infoArr[$key]->getSaveName();
            // $data = [
            //     'pic_name' => $_FILES['pictures']['name'][$key],
            //     'pic_init' => '/uploads' .DS. 'pictures/'.$infoArr[$key]->getSaveName(),
            //     'pic_small'=> '',
            //     'pic_path' => $savePath.$infoArr[$key]->getSaveName(),
            //     'pic_size' => $_FILES['pictures']['size'][$key],
            //     'pic_type' => $_FILES['pictures']['type'][$key],
            //     'pic_desc' => '',
            //     'album_id' => $albumid,
            //     'crdate' => time(),
            // ];
            // $res = Db::name('picture')->insert($data); 
        }exit;

        return $infoArr;
    }

}