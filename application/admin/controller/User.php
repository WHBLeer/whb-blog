<?php
namespace app\admin\controller;
/**
 * @Date:   2018-07-17 16:40:57
 * @Author: Wang HongBin | <whb199330@163.com>
 * @Last Modified by:   Wang HongBin
 * @Last Modified time: 2018-08-29 16:08:11
 *                .-~~~~~~~~~-._       _.-~~~~~~~~~-.
 *            __.'              ~.   .~              `.__
 *          .'//       NO         \./        BUG       \\`.
 *        .'//                     |                     \\`.
 *      .'// .-~"""""""~~~~-._     |     _,-~~~~"""""""~-. \\`.
 *    .'//.-"                 `-.  |  .-'                 "-.\\`.
 *  .'//______.============-..   \ | /   ..-============.______\\`.
 *.'______________________________\|/______________________________`.
 */

use think\Controller;
use think\Db;
use app\admin\model\Admin;
class User extends Controller
{   
	public function _initialize(){
        $v = Db::query('select VERSION() as ver');
        $scf = get_sys_config();
    	$this ->assign([
            'system' => PHP_OS,
            'webServer' => $_SERVER['SERVER_SOFTWARE'],
            'php' => PHP_VERSION,
            'mysql' => $v[0]['ver'],
            'browser' => current_browser(),
            'conf' => $scf
        ]);	
    }

	// 用户信息
    public function info()
    {
        $admin = new Admin();
        $result = $admin->info();
        $this->assign('userinfo',$result);
        return $this->fetch('info');
    }

    // 编辑用户信息
    public function edit()
    {
    	if(request()->isPost()){
    		$admin = new Admin();
			$data = input('post.');

            $result = $admin->edit($data);
    		if ($result) {
    			return $this->fetch('info');
            } else {
                // 登录失败
        		return $this->error('数据错误');
        	}
     	}
        return $this->fetch('info');
    }

    // 用户登录
    public function login()
    {
    	if(request()->isPost()){
    		$admin=new Admin();
			$data=input('post.');
            $num=$admin->login($data);
    		if ($num==1) {
                // 用户不存在
                return $this->error('该用户不存在！');
        	} else if ($num==3) {
                // 登录成功
                return $this->success('登录成功,正在为您跳转...','index/index');
                // $this->redirect('index/index');
            } else if ($num==4) {
                // 验证码错误
                return $this->error('验证码错误！');
            } else {
                // 登录失败
        		return $this->error('用户名或密码错误！');
        	}
     	}
        $gitee_request_url = 'https://gitee.com/oauth/authorize?client_id=3c53525ed7308b12782a78a4a6610fb613981845a0863a54cd48b7f16725bab5&redirect_uri=https://www.whongbin.cn/admin/oauth/gitee.html&response_type=code';
        $github_request_url = 'https://github.com/login/oauth/authorize?client_id=4bae95759f9bbc70220d&redirect_uri=https://www.whongbin.cn/admin/oauth/github.html';
        $this->assign('gitee_request_url',$gitee_request_url);
        $this->assign('github_request_url',$github_request_url);

        return $this->fetch('login');

    }  

    // 用户注册
    public function register()
    {
    	if(request()->isPost()){
    		$admin=new Admin();
			$data=input('post.');
            $num=$admin->register($data);
    		if ($num==3) {
        		// $this->$this->success('注册成功,正在为您跳转...','index/index');
                $this->redirect('index/index');
        	}elseif ($num==1) {
        		// 注册失败
        		$this->$this->error('用户已存在！');
        	} else if ($num==4) {
                // 验证码错误
                return $this->error('验证码错误！');
            } else {
                // 注册失败
        		$this->$this->error('系统写入数据错误！');
        	}
     	}
        return $this->fetch('register');

    }  

    // 找回密码
    public function forgot()
    {
    	if(request()->isPost()){
    		$admin=new Admin();
			$data=input('post.');
            $num=$admin->forgot($data);
    		if ($num==1) {
        		// return $this->success('邮件已发送，请注意查收！','index/index');
                $this->redirect('index/index');
        	}else{
        		// 注册失败
        		return $this->error('邮箱不存在！');
        	}
     	}
        return $this->fetch('forgot');

    } 

    // 退出登录
    public function logout(){
        session(null);
        $this->redirect('index/index');
    }
}
/*array(38) {
  ["id"] => int(1525918)
  ["login"] => string(8) "TYPO3_GO"
  ["name"] => string(7) "WhbLeer"
  ["avatar_url"] => string(40) "https://gitee.com/assets/no_portrait.png"
  ["url"] => string(39) "https://gitee.com/api/v5/users/TYPO3_GO"
  ["html_url"] => string(26) "https://gitee.com/TYPO3_GO"
  ["followers_url"] => string(49) "https://gitee.com/api/v5/users/TYPO3_GO/followers"
  ["following_url"] => string(66) "https://gitee.com/api/v5/users/TYPO3_GO/following_url{/other_user}"
  ["gists_url"] => string(55) "https://gitee.com/api/v5/users/TYPO3_GO/gists{/gist_id}"
  ["starred_url"] => string(62) "https://gitee.com/api/v5/users/TYPO3_GO/starred{/owner}{/repo}"
  ["subscriptions_url"] => string(53) "https://gitee.com/api/v5/users/TYPO3_GO/subscriptions"
  ["organizations_url"] => string(44) "https://gitee.com/api/v5/users/TYPO3_GO/orgs"
  ["repos_url"] => string(45) "https://gitee.com/api/v5/users/TYPO3_GO/repos"
  ["events_url"] => string(56) "https://gitee.com/api/v5/users/TYPO3_GO/events{/privacy}"
  ["received_events_url"] => string(55) "https://gitee.com/api/v5/users/TYPO3_GO/received_events"
  ["type"] => string(4) "User"
  ["site_admin"] => bool(false)
  ["blog"] => string(23) "https://www.whongbin.cn"
  ["weibo"] => string(30) "https://weibo.com/aion816/home"
  ["bio"] => string(168) "一不小心在成为一个不正经的程序员道路上越走越远...PHP（拍黄片）==>Python（爬妹子图，爬小电影）。其实...我是个正经程序员"
  ["public_repos"] => int(30)
  ["public_gists"] => int(0)
  ["followers"] => int(0)
  ["following"] => int(1)
  ["stared"] => int(14)
  ["watched"] => int(35)
  ["created_at"] => string(25) "2017-09-11T09:26:35+08:00"
  ["updated_at"] => string(25) "2018-08-21T15:57:51+08:00"
  ["email"] => string(21) "wanghongbin@ngoos.org"
  ["unconfirmed_email"] => NULL
  ["phone"] => string(11) "18795260614"
  ["private_token"] => string(20) "jjCjh32EPHaziZMs319y"
  ["total_repos"] => int(1000)
  ["owned_repos"] => int(27)
  ["total_private_repos"] => int(1000)
  ["owned_private_repos"] => int(5)
  ["private_gists"] => int(0)
  ["address"] => NULL
}*/