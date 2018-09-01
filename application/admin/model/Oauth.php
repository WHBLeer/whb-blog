<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Debug;

class Oauth extends Model
{
	public function gitee($data){
		$map['otype'] = ['eq',1]; 
		$map['uniqueid'] = $data['id'];
        $oauth=db('Oauth')->where($map)->find();
		if($oauth){
			session('username',$oauth['nickname']);
			session('useremail',$oauth['email']);
			session('useravatar',$oauth['avatar']);
			session('uid',$oauth['id']);
			Db::name('Oauth')->where('uniqueid','=', $data['id'])->update(
				['lastlogin' => time()]);
			return 3; //信息正确
		}else{
			$data = array(
				'nickname' => $data['login'],
				'avatar' => $data['avatar_url'],
				'email' => $data['email'],
				'blog' => $data['blog'],
				'desc' => $data['bio'],
				'crdate' => time(),
				'lastlogin' => time(),
				'otype' => 1,
				'uniqueid' => $data['id'],
			);
			if(Db::name('Oauth')->insert($data)){
                $id = Db::name('Oauth')->getLastInsID();
				session('username',$data['nickname']);
				session('useremail',$data['email']);
				session('useravatar',$data['avatar']);
				session('uid',$id);
				return 3; //注册成功
			}
		}
	}

	public function github($data){
		// dump($data);exit;
		$map['otype'] = ['eq',2]; 
		$map['uniqueid'] = $data['id'];
        $oauth=db('Oauth')->where($map)->find();
		if($oauth){
			session('username',$oauth['nickname']);
			session('useremail',$oauth['email']);
			session('useravatar',$oauth['avatar']);
			session('uid',$oauth['id']);
			Db::name('Oauth')->where('uniqueid','=', $data['id'])->update(
				['lastlogin' => time()]);
			return 3; //信息正确
		}else{
			$data = array(
				'nickname' => $data['login'],
				'avatar' => $data['avatar_url'],
				'email' => $data['email']?:'',
				'blog' => $data['blog']?:'',
				'desc' => $data['bio']?:'',
				'crdate' => time(),
				'lastlogin' => time(),
				'otype' => 2,
				'uniqueid' => $data['id'],
			);
			if(Db::name('Oauth')->insert($data)){
                $id = Db::name('Oauth')->getLastInsID();
				session('username',$data['nickname']);
				session('useremail',$data['email']);
				session('useravatar',$data['avatar']);
				session('uid',$id);
				return 3; //注册成功
			}
		}
	}

}
