<?php
namespace app\admin\controller;
/**
 * @Date:   2018-07-17 16:40:57
 * @Author: Wang HongBin | <whb199330@163.com>
 * @Last Modified by:   Wang HongBin
 * @Last Modified time: 2018-08-29 15:39:10
 */

use think\Controller;
use think\Db;
use app\admin\model\Oauth as OauthModel;
use Auth\GithubConnect;
class Oauth extends Controller
{   
    const GITEE_CLIENT_ID = '3c53525ed7308b12782a78a4a6610fb613981845a0863a54cd48b7f16725bab5';
    const GITEE_CLIENT_SECRET = '8176f7555b1aa29c03ed7cc84150127a8630ba2333fac337291dc5f4718e3b7f';

    const GITHUB_CLIENT_ID = '4bae95759f9bbc70220d';
    const GITHUB_CLIENT_SECRET = '6681bffc4ba4c0e35f23b24ce0cdf4a9c333a5a3';

    // 用户登录
    public function gitee()
    {
        if (isset($_GET['code'])) {
            $access_token_url = 'https://gitee.com/oauth/token';
            $params = array(
                'grant_type'    => 'authorization_code',
                'code'          => $_GET['code'],
                'client_id'     => self::GITEE_CLIENT_ID,
                'redirect_uri'  => 'https://www.whongbin.cn/admin/oauth/gitee.html',
                'client_secret' => self::GITEE_CLIENT_SECRET
            );
            $res = json_decode(getHttpResponsePOST($access_token_url, $params),true);
            $access_token = $res['access_token'];
            if ($access_token) {
                $info_url = 'https://gitee.com/api/v5/user?access_token='.$access_token;
                $info = json_decode(getHttpResponseGET($info_url),true);
	            $Oauth = new OauthModel();
	            $num = $Oauth->gitee($info);
	            if ($num==3) {
	                // 登录成功
	                $this->redirect('index/index');
	            }
            }
        }
    }  

    // 用户登录
    public function github()
    {
        if (isset($_GET['code'])) {
            $access_token_url = 'https://github.com/login/oauth/access_token';
            $params = array(
                'client_id'     => self::GITHUB_CLIENT_ID,
                'client_secret' => self::GITHUB_CLIENT_SECRET,
                'code'          => $_GET['code'],
            );
            $access_token = getHttpResponsePOST($access_token_url, $params);
            if ($access_token) {
                $info_url = 'https://api.github.com/user?'.$access_token;
				$data = array();
				parse_str($access_token,$data);
				$token = $data['access_token'];
				$url = "https://api.github.com/user?access_token=".$token;
                $headers[] = 'Authorization: token '.$token;
				$headers[] = "User-Agent: 木木彡博客";
				$result = getHttpResponseGET($info_url,$headers);
                $info = json_decode($result,true);
                if (isset($info['id'])) {
		            $Oauth = new OauthModel();
		            $num = $Oauth->github($info);
		            if ($num==3) {
		                // 登录成功
		                echo "<script>alert(0);</script>";

		                $this->redirect('index/index');
		            }
	            }
            }
        }
    } 

}
