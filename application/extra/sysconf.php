<?php
//**********************最后更新时间：2018年08月30日 09:36:41***************************
return array(
//此配置项为自动生成请勿直接修改；如需改动请在后台网站设置
//*************************************网站设置****************************************
    'WEB_NAME'             => '木木彡博客',       //网站名：
    'WEB_KEYWORDS'         => '个人博客,彬博客,木木彡博客,PHP博客,王宏彬,TYPO3,thinkPHP博客,分享博客,WHB,wanghongbin',   //网站关键字
    'WEB_DESCRIPTION'      => '木木彡博客,一个致力于分享,分享心得,分享技术,分享知识点的个人博客',    //网站描述
    'WEB_STATUS'           => '1', //网站状态1:开启 0:关闭
    'WEB_CLOSE_WORD'       => '网站升级中,请稍后再进行访问！！！', //网站关闭时提示文字
    'WEB_ICP_NUMBER'       => '宁ICP备17002278号-3',     // 网站ICP备案号
    'WEB_GICP_NUMBER'      => '',     // 网站公安网备案号
//*************************************水印设置****************************************
    'TEXT_WATER_WORD'      => 'whongbin.cn',    //水印内容
    'TEXT_WATER_TTF_PTH'   => '/static/admin/font/sverige.ttf',   //字体路径
    'TEXT_WATER_FONT_SIZE' => '18',   //字号
    'TEXT_WATER_COLOR'     => '#008CBA',   //文字颜色
    'TEXT_WATER_ANGLE'     => '0',   //文字倾斜程度
    'TEXT_WATER_LOCATE'    => '9',  //水印位置 1：上左 2：上中 3：上右 4：中左 5：中中 6：中右 7：下左 8：下中 9：下右
//*************************************优化推广****************************************
    'AUTHOR'               => '王宏彬',         //默认作者
    'ADMIN_EMAIL'          => 'whb199330@163.com',    // 站长邮箱
    'COPYRIGHT_WORD'       => '本文为木木彡博客原创文章,转载无需和我联系,但请注明来源', //文章保留版权提示
//***********************************邮箱设置***********************************************
    'EMAIL_SMTP'           => 'smtp.163.com', //  SMTP服务器
    'EMAIL_USERNAME'       => 'whb199330@163.com', //  邮箱用户名
    'EMAIL_PASSWORD'       => 'whb199330', //  邮箱密码
    'EMAIL_FROM_NAME'      => '周周同学',    //  发件名
//***********************************评论管理***********************************************
    'COMMENT_REVIEW'       => '1', // 评论审核1:开启 0:关闭
    'COMMENT_SEND_EMAIL'   => '1', // 被评论邮件通知1:开启 0:关闭
//***********************************其他第三方接口****************************************
    'WEB_STATISTICS'       => '<script src="https://s13.cnzz.com/z_stat.php?id=1274390860&web_id=1274390860" language="JavaScript"></script>', // 第三方统计代码
    'BAIDU_SITE_URL'       => '<script>(function(){var bp = document.createElement("script");var curProtocol = window.location.protocol.split(":")[0];if (curProtocol === "https") {bp.src = "https://zz.bdstatic.com/linksubmit/push.js";}else {bp.src = "http://push.zhanzhang.baidu.com/push.js";}var s = document.getElementsByTagName("script")[0];s.parentNode.insertBefore(bp, s);})();</script>', // 百度推送site提交接
);