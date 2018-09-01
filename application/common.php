<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use phpmailer\phpmailer;
use hyperdown\hyperdown;
use watermark\watermark;
use sitemap\sitemap;
use think\Request;
use think\Url;
use think\Db;
use app\user\model\User;
define('author', '王子的公主');
if (!function_exists('send_email')) {
    /**
     * 公共发送邮件函数
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-17
     * @param    [type]                $subject [邮件主题]
     * @param    [type]                $content [邮件内容]
     * @param    [type]                $toname  [接收者]
     * @param    [type]                $toemail [接收者邮箱]
     * @return   [type]                         [description]
     */
    function send_email($subject,$content, $toname, $toemail){
        $scf = get_sys_config();
        $mail = new PHPMailer(true);
        $mail->isSMTP();                        // 使用SMTP服务
        $mail->CharSet      = "utf8";           // 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host         = $scf['EMAIL_SMTP'];// 发送方的SMTP服务器地址
        $mail->SMTPAuth     = true;             // 是否使用身份验证
        $mail->Username     = $scf['EMAIL_USERNAME'];// 发送方的163邮箱用户名
        $mail->Password     = $scf['EMAIL_PASSWORD'];//客户端授权密码
        $mail->SMTPSecure   = "ssl";            // 使用ssl协议方式
        $mail->Port         = 994;              // 163邮箱的ssl协议方式端口号是465/994
        $mail->setFrom($scf['EMAIL_USERNAME'],$scf['EMAIL_FROM_NAME']);// 设置发件人信息
        $mail->addAddress($toemail,$toname);    // 设置收件人信息
        $mail->addReplyTo($scf['EMAIL_USERNAME'],$scf['EMAIL_FROM_NAME']);// 设置回复人信息
        // $mail->addCC("xxx@163.com");         // 设置邮件抄送人
        // $mail->addBCC("xxx@163.com");            // 设置秘密抄送人
        // $mail->addAttachment("bug0.jpg");        // 添加附件
        $mail->Subject      = $subject;         // 邮件标题
        $mail->Body         = $content;         // 邮件正文
        //$mail->AltBody = "纯文本";               // 这个是设置纯文本方式显示的正文内容

        if(!$mail->send()){// 发送邮件
            return $mail->ErrorInfo;
        // echo "错误: ".$mail->ErrorInfo;// 输出错误信息
        }else{
            return 1;
        }
    }
}

if (!function_exists('dp_send_message')) {
    /**
     * 发送消息给用户
     * @param string $type 消息类型
     * @param string $content 消息内容
     * @param string $uids 用户id，可以是数组，也可以是逗号隔开的字符串
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool
     */
    function dp_send_message($type = '', $content = '', $uids = '') {
        $uids = is_array($uids) ? $uids : explode(',', $uids);
        $list = [];
        foreach ($uids as $uid) {
            $list[] = [
                'uid_receive' => $uid,
                'uid_send'    => UID,
                'type'        => $type,
                'content'     => $content,
            ];
        }

        $MessageModel = model('user/message');
        return false !== $MessageModel->saveAll($list);
    }
}
if (!function_exists('create_letter_avatar')) {
    /**
     * 生成首字图片
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-17
     * @param    string                $text [description]
     * @return   [type]                      [description]
     */
    function create_letter_avatar($text='')
    {
        $letter = mb_substr($text,0,1);
        $font =ROOT_PATH . 'public/fonts/yahei.ttf';
        $img = imagecreate(100, 100);
        $img_path = ROOT_PATH . 'public/uploads/avatars/';
        $img_name = md5(date('YmdHis')).'.gif';
        switch (rand(0,4)) {
            case 1:
                imagecolorallocate($img, 39,174,96);
                break;
            case 2:
                imagecolorallocate($img, 254,119,8);
                break;
            case 3:
                imagecolorallocate($img, 155,89,182);
                break;
            case 3:
                imagecolorallocate($img, 26,188,156);
                break;
            default:
                imagecolorallocate($img, 52,152,219);
                break;
        }
        //设置字体颜色
        $color = imagecolorallocate($img, 255, 255, 255);
        //将ttf文字写到图片中
        if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$letter)) {
            imagettftext($img, 40, 0, 23, 70, $color, $font, $letter);//中文
        } else {
            imagettftext($img, 45, 0, 27, 75, $color, $font, strtoupper($letter));//英文
        }
        //发送头信息
        header('Content-Type: image/gif');
        //输出图片
        imagegif($img,$img_path.$img_name);
        return DS.'uploads'.DS.'avatars'.DS.$img_name;
    }
}

if (!function_exists('water_text_mark')) {
    /**
     * 图片添加水印
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-23
     * @param    string                $img [description]
     */
    function water_text_mark($img='')
    {
        // 远程图片不需要水印
        if (httpcode($img)) return false;

        $scf = get_sys_config();
        $img_path = ROOT_PATH . 'public' .$img;
        // 获取图片信息
        list($img_w, $img_h, $img_t, $img_m) = getimagesize($img_path);
        $x = $img_w - 120;
        $y = $img_h - 20; //右下角
        // 在内存中创建图像
        $type = image_type_to_extension($img_t,false);
        $mime = getimagesize($img_path)['mime'];
        $fun  = "imagecreatefrom{$type}";
        // 图片复制到内存中
        $image = $fun($img_path);
        //打上文字
        $font = ROOT_PATH . 'public' .$scf['TEXT_WATER_TTF_PTH'];//字体
        $text = $scf['TEXT_WATER_WORD'];
        $size = $scf['TEXT_WATER_FONT_SIZE'];
        $colr = imagecolorallocatealpha($image, 255, 255, 255, 60);//字体颜色
        imagefttext($image, $size, 0, $x, $y, $colr, $font, $text);
        // 保存到本地
        // header('Content-Type: '.$mime);
        switch ( $img_t ) {
            case 1:
                imagegif( $image, $img_path );break;
            case 2:
                imagejpeg( $image, $img_path );break;
            case 3:
                imagepng( $image, $img_path ); break;
            default:
                return false;
        }
        // 释放内存
        imagedestroy($image);
        return $img;
    }
}

if (!function_exists('trimall')) {
    /**
     * 删除所有的空格
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @param    [type]                $str [description]
     * @return   [type]                     [description]
     */
    function trimall($str)
    {
        $oldchar=array(" ","　","\t","\n","\r");
        $newchar=array("","","","","");
        return str_replace($oldchar,$newchar,$str);
    }
}

if (!function_exists('re_substr')) {
    /**
     * 字符串截取，支持中文和其他编码
     *
     * @param string  $str 需要转换的字符串
     * @param integer $start 开始位置
     * @param string  $length 截取长度
     * @param boolean $suffix 截断显示字符
     * @param string  $charset 编码格式
     * @return string
     */
    function re_substr($str, $start = 0, $length, $suffix = true, $charset = "utf-8") {
        $slice = mb_substr($str, $start, $length, $charset);
        $omit = mb_strlen($str) >= $length ? '...' : '';
        return $suffix ? $slice.$omit : $slice;
    }
}

if (!function_exists('word_time')) {
    /**
     * 把日期或者时间戳转为距离现在的时间
     *
     * @param $time
     * @return bool|string
     */
    function word_time($time) {
        // 如果是日期格式的时间;则先转为时间戳
        if (!is_integer($time)) {
            $time = strtotime($time);
        }
        $int = time() - $time;
        if ($int <= 2){
            $str = sprintf('刚刚', $int);
        }elseif ($int < 60){
            $str = sprintf('%d秒前', $int);
        }elseif ($int < 3600){
            $str = sprintf('%d分钟前', floor($int / 60));
        }elseif ($int < 86400){
            $str = sprintf('%d小时前', floor($int / 3600));
        }elseif ($int < 1728000){
            $str = sprintf('%d天前', floor($int / 86400));
        }else{
            $str = date('Y-m-d H:i:s', $time);
        }
        return $str;
    }
}

if (!function_exists('markdown_to_html')) {
    /**
     * markdown转HTML
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @param    [type]                $markdown [description]
     * @return   [type]                          [description]
     */
    function markdown_to_html($markdown){
        // 正则匹配到全部的iframe
        preg_match_all('/&lt;iframe.*iframe&gt;/', $markdown, $iframe);
        // 如果有iframe 则先替换为临时字符串
        if (!empty($iframe[0])) {
            $tmp = [];
            // 组合临时字符串
            foreach ($iframe[0] as $k => $v) {
                $tmp[] = '【iframe'.$k.'】';
            }
            // 替换临时字符串
            $markdown = str_replace($iframe[0], $tmp, $markdown);
            // 讲iframe转义
            $replace = array_map(function ($v){
                return htmlspecialchars_decode($v);
            }, $iframe[0]);
        }

        // markdown转html
        $parser = new HyperDown();
        $html = $parser->makeHtml($markdown);
        $html = str_replace('<code class="', '<code class="lang-', $html);
        // 将临时字符串替换为iframe
        if (!empty($iframe[0])) {
            $html = str_replace($tmp, $replace, $html);
        }
        return $html;
    }
}

if (!function_exists('comment_to_html')) {
    /**
     * markdown转HTML
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @param    [type]                $markdown [description]
     * @return   [type]                          [description]
     */
    function comment_to_html($comment){
        // 正则匹配到全部的图片
        preg_match_all('/\[img\](.*?)\[\/img\]/i', $comment, $imgs);
        if (!empty($imgs[0])) {
            for ($i=0; $i < count($imgs[0]); $i++) { 
                $imgdom = '<a href="'.$imgs[1][$i].'" class="comment-t-img-a" data-fancybox="images" data-no-instant="true"><i class="fa fa-picture-o" aria-hidden="true"></i> 查看图片</a>';
                $comment = str_replace($imgs[0][$i], $imgdom, $comment);
            }
        }

        // 正则匹配全部的超链接
        preg_match_all('/\[url=(.*?)\](.*?)\[\/url\]/i', $comment, $links);
        if (!empty($links[0])) {
            for ($i=0; $i < count($links[0]); $i++) { 
                $linkdom = '<a href="'.$links[1][$i].'" target="_blank" class="comment-t-a links" rel="nofollow noopener">'.$links[2][$i].'</a>';
                $comment = str_replace($links[0][$i], $linkdom, $comment);
            }
        }
        return $comment;
    }
}

if (!function_exists('upload')) {
    /**
     * 上传函数，可以多个,也可以单个,图片和文件也行
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-24
     * @param    [type]                $files [description]
     * @param    [type]                $size  [description]
     * @param    [type]                $ext   [description]
     * @return   [type]                       [description]
     */
    function upload($files,$size,$ext){
        foreach($files as $file){
            $info = $file->validate(['size'=>$size,'ext'=>$ext])->move( '../public/uploads');
            if($info){
                $image.=$info->getFilename().","; 
                $image = substr($image,0,strlen($image)-1); //截取去掉最后/第一个字符","
                echo $image;
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }    
        }
    }
}

if (!function_exists('save_img_curl')) {
    /**
     * 通过curl获取图片保存到服务器
     * @access public
     * @param string $url 目标url
     * @param string $upload_dir 图片保存地址
     * @param string $filename 图片名称
     * @param int $timeout 超时时间
     * @return string
     */
    function save_img_curl($url,$upload_dir,$filename,$timeout=''){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
        $img=curl_exec($ch); 
        curl_close($ch); 
        
        $fp2=@fopen($upload_dir.'/'.$filename,'a'); 
        fwrite($fp2,$img); 
        fclose($fp2); 
        unset($img,$url);
        
        return $filename;
    }
    # code...
}
if (!function_exists('create_thumbnail')) {
    /**
     * 生成缩略图
     * @param string $path 目标路径
     * @param string $filename 图片名
     * @return void
     */
    function create_thumbnail($path,$filename){
        $thumbnail_path1="images_183";
        $thumbnail_path=array($thumbnail_path1);

        list($src_w,$src_h,$imagetype)=getimagesize($path."/".$filename);
        $mime=image_type_to_mime_type($imagetype);

        $createfun=str_replace("/","createfrom",$mime);
        $outfun=str_replace("/",null,$mime);

        $src_image=$createfun($path."/".$filename);

        $dst_183_image=imagecreatetruecolor(183,140);

        imagecopyresampled($dst_183_image, $src_image, 0, 0, 0, 0,183, 140, $src_w, $src_h);

        for($i=0;$i<1;$i++){
            if(!file_exists($path.'/'.$thumbnail_path[$i])){
                mkdir($path.'/'.$thumbnail_path[$i],0777);
            }
        }

        $outfun($dst_183_image,$path.'/'.$thumbnail_path1.'/'.$filename);

        imagedestroy($src_image);
        imagedestroy($dst_183_image);
    }
}
if (!function_exists('current_browser')) {
    /**
     * 获取浏览器
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-19
     * @return   [type]                [description]
     */
    function current_browser() {
        global $_SERVER;
        $agent  = $_SERVER['HTTP_USER_AGENT'];
        $browser  = '';
        $browser_ver  = '';
        if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
            $browser  = 'OmniWeb';
            $browser_ver   = $regs[2];
        }

        if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
            $browser  = 'Netscape';
            $browser_ver   = $regs[2];
        }

        if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
            $browser  = 'Safari';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
            $browser  = 'Internet Explorer';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
            $browser  = 'Opera';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
            $browser  = '(Internet Explorer ' .$browser_ver. ') NetCaptor';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/Maxthon/i', $agent, $regs)) {
            $browser  = '(Internet Explorer ' .$browser_ver. ') Maxthon';
            $browser_ver   = '';
        }
        if (preg_match('/360SE/i', $agent, $regs)) {
            $browser       = '(Internet Explorer ' .$browser_ver. ') 360SE';
            $browser_ver   = '';
        }
        if (preg_match('/SE 2.x/i', $agent, $regs)) {
            $browser       = '(Internet Explorer ' .$browser_ver. ') 搜狗';
            $browser_ver   = '';
        }

        if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
            $browser  = 'FireFox';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
            $browser  = 'Lynx';
            $browser_ver   = $regs[1];
        }

        if(preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)){
            $browser  = 'Chrome';
            $browser_ver   = $regs[1];

        }

        if ($browser != '') {
            return $browser.'/'.$browser_ver;
        } else {
            return 'unknow browser & version';
        }
    }
}

if (!function_exists('get_complete_url')) {
    /**
     * 完整URL链接
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-20
     * @param    string                $url [description]
     * @return   [type]                     [description]
     */
    function get_complete_url($url='')
    {
        if(preg_match("/^(http:\/\/|https:\/\/).*$/",$url)){
            return $url;
        }else{
            if (httpcode('https://'.$url)==200) {
                return 'https://'.$url;
            }
            return 'http://'.$url;
        }
    }
}

if (!function_exists("create_sitemap")) {
    /**
     * 生成站点地图
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-25
     * @param    string                $domain [description]
     * @param    array                 $items  [description]
     * @return   [type]                        [description]
     */
    function create_sitemap($items=array()) {
        $sitemap = new Sitemap();
        // 生成sitemap item
        foreach ($items as $item) {
            $sitemap->AddItem($item['loc'], $item['priority']?:1, $item['lastmod']?:date('Y-m-d H:i:s', time()), $item['changefreq']?:'daily');
        }
        $sitemap->SaveToFile('sitemap.xml');
        $sitemap->xml2html('sitemap.xml');
    }
}

if (!function_exists("xml2array")) {
    /**
     * [xml2array description]
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-25
     * @param    [type]                $xml [description]
     * @return   [type]                     [description]
     */
    function xml2array($xmlobject) {
        if ($xmlobject) {
            foreach ((array)$xmlobject as $k=>$v) {
                if ($k!='lastmod') {
                    $data[$k] = str_replace(SITE_URL, '', !is_string($v) ? xml2array($v) : $v);
                }
            }
            return $data;
        }
    }
}
if (!function_exists("createXSL2Html")) {
    /**
     * 转化 xml + xsl 为 html 
     * @param unknown $xmlFile      sitemap.xml 源文件
     * @param unknown $xslFile      sitemap-xml.xsl 源文件
     * @param unknown $htmlFile     sitemap.html 生成文件
     * @param string $isopen_htmlfile   是否打开生成文件 sitemap.html
     */
    function createXSL2Html($xmlFile, $xslFile, $htmlFile, $isopen_htmlfile=false) {
        
        header("Content-Type: text/html; charset=UTF-8");
        $xml = new DOMDocument();
        $xml->Load($xmlFile);
        $xsl = new DOMDocument();
        $xsl->Load($xslFile);
        $xslproc = new XSLTProcessor();
        $xslproc->importStylesheet($xsl);
    //  echo $xslproc->transformToXML($xml);
        
        $f = fopen($htmlFile, 'w+');
        fwrite($f, $xslproc->transformToXML($xml));
        fclose($f);
        
        // 是否打开生成的文件 sitemap.html
        if($isopen_htmlfile) {
            echo "<script>window.open('".$htmlFile."')</script>";
            echo "<br>Create sitemap.html Success";
        }
    }
}

if (!function_exists('httpcode')) {
    /**
     * 判断链接是否可用
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-20
     * @param    [type]                $url [description]
     * @return   [type]                     [description]
     */
    function httpcode($url=''){
        $ch = curl_init();
        $timeout = 3;
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpcode;
    }
}

if (!function_exists('set_sys_config')) {
    /**
     * 写入自定义配置
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-20
     * @param    array                 $data [description]
     */
    function set_sys_config($data=array())
    {
        // if (empty($data)) return false;

        // $filename = get_complete_url($_SERVER['HTTP_HOST'].'/sysconf.phpt');
        $filename = MOULD_PATH.DS.'sysconf.phpt';
        $saveflie = APP_PATH.DS.'extra'.DS.'sysconf.php';
        $fileContent = file_get_contents($filename);
        // 写入配置
        foreach ($data as $key => $val) {
            $fileContent = str_replace('##'.strtoupper($key).'##',$val['value'],$fileContent);
        }

        // 更新修改时间
        $fileContent = str_replace('##EDITDATE##',date('Y年m月d日 H:i:s'),$fileContent);
        if (file_put_contents($saveflie, $fileContent)) return true;
        else return false;
    }
}

if (!function_exists('get_sys_config')) {
    /**
     * 获取自定义配置
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-20
     */
    function get_sys_config()
    {
        $conf = \think\Config::get('sysconf');
        return $conf;
    }
}

if (!function_exists('comment_total')) {
    /**
     * 获取评论总数
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-27
     * @param    integer               $article [description]
     * @return   [type]                         [description]
     */
    function comment_total($article=0,$new=0)
    {
        if ($article!=0) {
            $arr = db('comment')->where("article = ".$article)->select();
        }else{
            if ($new == 1) {
                $arr = db('comment')->where("FROM_UNIXTIME(time,'%Y-%m-%d')=curdate()")->select();
            }else{
                $arr = db('comment')->where("article != 1")->select();
            }
        }
        echo count($arr);
    }
}

if (!function_exists('article_total')) {
    /**
     * 获取评论总数
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-27
     * @param    integer               $article [description]
     * @return   [type]                         [description]
     */
    function article_total($new = 0)
    {
        if ($new == 1) {
            $arr = db('article')->where("FROM_UNIXTIME(time,'%Y-%m-%d')=curdate()")->select();
        }else{
            $arr = db('article')->where("id != 1")->select();
        }
        echo count($arr);
    }
}

if (!function_exists('picture_total')) {
    /**
     * 获取评论总数
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-27
     * @param    integer               $article [description]
     * @return   [type]                         [description]
     */
    function picture_total($new = 0)
    {
        if ($new == 1) {
            $arr = db('picture')->where("FROM_UNIXTIME(crdate,'%Y-%m-%d')=curdate()")->select();
        }else{
            $arr = db('picture')->select();
        }
        echo count($arr);
    }
}

if (!function_exists('get_comment_list')) {
    /**
     * 获取自定义配置
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-07-20
     */
    function get_comment_list($parent = 0,&$result = array())
    {
        $arr = db('comment')->where("parent = '".$parent."'")->order("time desc")->select();
        if(empty($arr)) return array();
        foreach ($arr as $cm) {
            $thisArr = &$result[];
            $cm["children"] = get_comment_list($cm["id"],$thisArr);
            $thisArr = $cm;
        }
        return $result;
    }
}
if (!function_exists('delete_dir_file')) {

    /**
     * 循环删除目录和文件
     * @param string $dir_name
     * @return bool
     */
    function delete_dir_file($dir_name) {
        $result = false;
        if(is_dir($dir_name)){
            if ($handle = opendir($dir_name)) {
                while (false !== ($item = readdir($handle))) {
                    if ($item != '.' && $item != '..') {
                        if (is_dir($dir_name . DS . $item)) {
                            delete_dir_file($dir_name . DS . $item);
                        } else {
                            unlink($dir_name . DS . $item);
                        }
                    }
                }
                closedir($handle);
                if (rmdir($dir_name)) {
                    $result = true;
                }
            }
        }
     
        return $result;
    }
}

if (!function_exists('getHttpResponsePOST')) {
    /**
     * 远程获取数据，POST模式
     * @param $url 指定URL完整路径地址
     * @param $param 请求的数据
     * return 远程输出的数据
     */
    function getHttpResponsePOST($url = '', $param = array()) {
        if (empty($url) || empty($param)) {
            return false;
        }
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $data;
    }
}
if (!function_exists('getHttpResponseGET')) {
    /**
     * 远程获取数据，GET模式
     * @param $url 指定URL完整路径地址
     * @param $header 头部
     * return 远程输出的数据
     */
    function getHttpResponseGET($url,$header=null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if(!empty($header)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        // echo curl_getinfo($curl);
        curl_close($curl);
        unset($curl);
        return $output;
    }
}

/**
 * 获取访问用户ip
 */
function get_ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}