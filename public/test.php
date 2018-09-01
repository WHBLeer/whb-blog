<?php

         
$comment = "[url=https://www.whongbin.cn/index/article/detail/id/26.html]啦啦啦[/url] ";
preg_match_all('/\[url=(.*?)\](.*?)\[\/url\]/i', $comment, $imgs);
echo "<pre>";
var_dump($imgs);
echo "</pre>";
// for ($i=0; $i < count($imgs[0]); $i++) { 
// 	// $imgdom = '<img src="'.$imgs[1][$i].'" alt="'.$imgs[1][$i].'" ><i class="fa fa-picture-o"></i>查看图片</img>';
// 	$imgdom = '<a href="'.$imgs[1][$i].'" class="comment-t-img-a" data-fancybox="images" data-no-instant="true"><i class="fa fa-picture-o" aria-hidden="true"></i> 查看图片</a>';
// 	$comment = str_replace($imgs[0][$i], $imgdom, $comment);
// }
// echo $comment;
