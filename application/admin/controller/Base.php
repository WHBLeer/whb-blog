<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Config;
//引入七牛云的相关文件
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class Base extends  Controller
{
    public function _initialize(){
        if(!session('username')){
        	$this->redirect('user/login');
        }
        $this->serverInfo();
        $scf = get_sys_config();
        $this->assign('conf',$scf);
    }

    // 当前系统运行环境
    public function serverInfo()
    {
        $v = Db::query('select VERSION() as ver');
    	$this ->assign([
            'system' => PHP_OS,
            'webServer' => $_SERVER['SERVER_SOFTWARE'],
            'php' => PHP_VERSION,
            'mysql' => $v[0]['ver'],
            'browser' => current_browser()
        ]);	
    }

    // 七牛云存储
    public function qiniuUpload($file=null)
    {
        if (!$file) {
            $file = request()->file('file');
        }
        // 要上传图片的本地路径
        $filePath = $file->getRealPath();
        $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀
        // 上传到七牛后保存的文件名
        $key =substr(md5($file->getRealPath()) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;
        // 需要填写你的 Access Key 和 Secret Key
        $qiniu = config('qiniu');
        $accessKey = $qiniu['accesskey'];
        $secretKey = $qiniu['secretkey'];
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        // 要上传的空间
        $bucket = $qiniu['bucket'];
        $domain = $qiniu['DOMAIN'];
        $token = $auth->uploadToken($bucket);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return ["err"=>1,"msg"=>$err,"data"=>""];
        } else {
            //返回图片的完整URL
            return ["err"=>0,"msg"=>"上传完成","data"=>($domain .'/'. $ret['key'])];
        }
    }
}