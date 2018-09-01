<?php

/**
 * @Date:   2018-07-16 17:23:37
 * @Author: Wang HongBin | <whb199330@163.com>
 * @Last Modified by:   Wang HongBin
 * @Last Modified time: 2018-08-27 09:03:27
 *                .-~~~~~~~~~-._       _.-~~~~~~~~~-.
 *            __.'              ~.   .~              `.__
 *          .'//       NO         \./        BUG       \\`.
 *        .'//                     |                     \\`.
 *      .'// .-~"""""""~~~~-._     |     _,-~~~~"""""""~-. \\`.
 *    .'//.-"                 `-.  |  .-'                 "-.\\`.
 *  .'//______.============-..   \ | /   ..-============.______\\`.
 *.'______________________________\|/______________________________`.
 */
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Db;

class Index extends Base
{
    // 后台首页
    public function index()
    {
        $comments=db('comment')->alias('c')
            ->field('c.id, c.comment, c.nickname, c.email, c.url, c.status, c.time')
            ->order('c.time desc')
            ->paginate(5);
        // 或者批量赋值
        $this->assign([
            'comments'  => $comments
        ]);
        // 模板输出
        return $this->fetch();
    }

}