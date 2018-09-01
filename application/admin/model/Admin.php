<?php
namespace app\admin\model;
/**
 * @Date:   2018-07-17 13:58:30
 * @Author: Wang HongBin | <whb199330@163.com>
 * @Last Modified by:   Wang HongBin
 * @Last Modified time: 2018-07-24 13:42:46
 *                .-~~~~~~~~~-._       _.-~~~~~~~~~-.
 *            __.'              ~.   .~              `.__
 *          .'//       NO         \./        BUG       \\`.
 *        .'//                     |                     \\`.
 *      .'// .-~"""""""~~~~-._     |     _,-~~~~"""""""~-. \\`.
 *    .'//.-"                 `-.  |  .-'                 "-.\\`.
 *  .'//______.============-..   \ | /   ..-============.______\\`.
 *.'______________________________\|/______________________________`.
 */

use think\Model;
use think\Db;
use think\Debug;

class Admin extends Model
{

	public function info(){
		$user=Db::name('Admin')->where('username','=', session('username'))->find();
		if($user){
			return $user;
		}else{
			return null; //用户不存在
		}
	}

	public function edit($data){
		$user=Db::name('Admin')
			->where('username','=', $data['username'])
			->update(
				[
					'email' => $data['email'],
					'password' => md5($data['password'])
				]
			);
		if($user){
			return $user;
		}else{
			return null; //用户不存在
		}
	}

	public function login($data){
		
		$captcha = new \think\captcha\Captcha();
        if (!$captcha->check($data['code'])) {
            return 4;
        } 
		$user=Db::name('Admin')->where('username','=',$data['username'])->find();
		if($user){
			if($user['password'] == md5($data['password'])){
				session('username',$user['username']);
				session('useremail',$user['email']);
				session('useravatar',$user['avatar']);
				session('uid',$user['id']);
				return 3; //信息正确
			}else{
				return 2; //密码错误
			}
		}else{
			return 1; //用户不存在
		}
	}

	public function register($data){
		$captcha = new \think\captcha\Captcha();
        if (!$captcha->check($data['code'])) {
            return 4;
        } 
		$user=Db::name('Admin')->where('username','=',$data['username'])->find();
		if($user){
			return 1; //用户已存在
		}else{
			$data = array(
				'username' => $data['username'],
				'password' => md5($data['password']),
				'email' => $data['email'],
				'avatar' => '',
			);
			$newUser = Db::name('admin')->insert($data);
			if($newUser){
				session('username',$newUser['username']);
				session('useremail',$newUser['email']);
				session('useravatar',$newUser['avatar']);
				session('uid',$newUser['id']);
				return 3; //注册成功
			}else{
				return 2; //注册失败
			}
		}
	}

	public function forgot($data){
		$user=Db::name('Admin')->where('email','=',$data['email'])->find();
		if($user){
			//用户已存在，发送邮件
			$res = send_email('密码找回测试邮件','密码找回', 'whongbin', 'wanghongbin@ngoos.org');
			return 1; 
		}else{
			return 2; //用户不存在
		}
	}
}
