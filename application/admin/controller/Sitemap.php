<?php

/**
 * @Date:   2018-07-16 17:23:37
 * @Author: Wang HongBin | <whb199330@163.com>
 * @Last Modified by:   Wang HongBin
 * @Last Modified time: 2018-08-30 13:55:38
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
use think\Controller;
use think\Db;
class Sitemap  extends  Controller
{
    /**
     * 生成网站地图推送给百度、google
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-02
     * @return   [type]                [description]
     */
    public function index()
    {
        $articleres = Db::query('select * from whb_article where 1');
        $items = array(array(
                "loc" => SITE_URL,
                "priority" => 0,
                'lastmod' => date('Y-m-d H:i:s', time()), 
                "changefreq" => "Always",
            )
        );
        foreach ($articleres as $key => $article) {
        	$item = array(
                'loc' => SITE_URL.DS.'index/article/detail/id/'.$article['id'].'.html',
                'priority' => 1, 
                'lastmod' => date('Y-m-d H:i:s',(int)$article['time']),
                'changefreq' => 'daily'
            );
            array_push($items, $item);
        }
        // 添加站点地图xml
        create_sitemap($items);
    }

    /**
     * 生成HTML
     * @Author   wanghongbin
     * @Email    wanghongbin@ngoos.org
     * @DateTime 2018-08-09
     * @param    array                 $items [description]
     * @return   [type]                       [description]
     */
    public function site()
    {
        $conf = get_sys_config();
        // 文章列表
        $articleres = Db::query('select * from whb_article where id!=1 order by time desc');
        $articlelists = array();
        foreach ($articleres as $key => $item) {
            $articlelists[] = '<li><a href="'.SITE_URL.DS.'index/article/detail/id/'.$item['id'].'.html'.'" title="'.$item['title'].'">'.$item['title'].'<span>'.date('Y-m-d H:i:s',$item['time']).'</span></a></li>';
        }
        // 分类列表
        $categorys = Db::query('select * from whb_cate where 1');
        $categorylists = array();
        foreach ($categorys as $key => $item) {
            $categorylists[] = '<li><a href="'.SITE_URL.DS.'index/cate/index/id/'.$item['id'].'.html'.'" title="'.$item['catename'].'">'.$item['catename'].'</a></li>';
        }

        // 标签列表
        // $tags = Db::query('select * from whb_tags where 1');
        $taglists = array();

        $words =db('article')->where('id!=1')->field('keywords')->order('id asc')->select();
        $wordsStr = implode(',', array_column($words, 'keywords'));
        $wordsArr = array_unique(explode(',', $wordsStr));
        foreach ($wordsArr as $key => $item) {
            $taglists[] = '<li><a href="'.SITE_URL.DS.'index/search/index.html?keywords='.$item.'" title="'.$item.'">'.$item.'</a></li>';
        }

        $sitemapContent = file_get_contents(MOULD_PATH.DS.'/sitemap.htmlt');

        $sitemapContent = str_replace('###ICPNUMBER###', $conf['WEB_ICP_NUMBER'], $sitemapContent);
        $sitemapContent = str_replace('###PAGETITLE###', $conf['WEB_NAME'], $sitemapContent);
        $sitemapContent = str_replace('###SITEMAPTITLE###', $conf['WEB_NAME'].' SiteMap', $sitemapContent);
        $sitemapContent = str_replace('###CREATETIME###', date('Y-m-d H:i:s'), $sitemapContent);
        $sitemapContent = str_replace('###ARTICLELISTS###', implode('', $articlelists), $sitemapContent);
        $sitemapContent = str_replace('###CATEGORYLISTS###', implode('', $categorylists), $sitemapContent);
        $sitemapContent = str_replace('###TAGLIST###', implode('', $taglists), $sitemapContent);
        file_put_contents(ROOT_PATH . 'public' . DS . 'sitemap.html',$sitemapContent);
    }
}