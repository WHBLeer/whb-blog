<?php
namespace app\admin\controller;
use app\admin\model\Config as ConfigModel;
use think\Db;
use think\Cache;
use app\admin\controller\Base;
class Config extends  Base
{   
    // 列表页输出
    public function config(){     
    	// 输出数组信息
        $config = ConfigModel::paginate(50);
        $this->assign('config',$config);
        return $this->fetch('');
    }

    public function update()
    {
		$datas=[
			'WEB_NAME'             => input('WEB_NAME/a'),       //网站名：
		    'WEB_KEYWORDS'         => input('WEB_KEYWORDS/a'),   //网站关键字
		    'WEB_DESCRIPTION'      => input('WEB_DESCRIPTION/a'),    //网站描述
		    'WEB_STATUS'           => input('WEB_STATUS/a'), //网站状态1:开启 0:关闭
		    'WEB_CLOSE_WORD'       => input('WEB_CLOSE_WORD/a'), //网站关闭时提示文字
		    'WEB_ICP_NUMBER'       => input('WEB_ICP_NUMBER/a'),     // 网站ICP备案号
		    'WEB_GICP_NUMBER'      => input('WEB_GICP_NUMBER/a'),     // 网站公安网备案号
		    'TEXT_WATER_WORD'      => input('TEXT_WATER_WORD/a'),    //水印内容
		    'TEXT_WATER_TTF_PTH'   => input('TEXT_WATER_TTF_PTH/a'),   //字体路径
		    'TEXT_WATER_FONT_SIZE' => input('TEXT_WATER_FONT_SIZE/a'),   //字号
		    'TEXT_WATER_COLOR'     => input('TEXT_WATER_COLOR/a'),   //文字颜色
		    'TEXT_WATER_ANGLE'     => input('TEXT_WATER_ANGLE/a'),   //文字倾斜程度
		    'TEXT_WATER_LOCATE'    => input('TEXT_WATER_LOCATE/a'),  //水印位置 1：上左 2：上中 3：上右 4：中左 5：中中 6：中右 7：下左 8：下中 9：下右
		    'AUTHOR'               => input('AUTHOR/a'),         //默认作者
		    'ADMIN_EMAIL'          => input('ADMIN_EMAIL/a'),    // 站长邮箱
		    'COPYRIGHT_WORD'       => input('COPYRIGHT_WORD/a'), //文章保留版权提示
		    'EMAIL_SMTP'           => input('EMAIL_SMTP/a'), //  SMTP服务器
		    'EMAIL_USERNAME'       => input('EMAIL_USERNAME/a'), //  邮箱用户名
		    'EMAIL_PASSWORD'       => input('EMAIL_PASSWORD/a'), //  邮箱密码
		    'EMAIL_FROM_NAME'      => input('EMAIL_FROM_NAME/a'),    //  发件名
		    'COMMENT_REVIEW'       => input('COMMENT_REVIEW/a'), // 评论审核1:开启 0:关闭
		    'COMMENT_SEND_EMAIL'   => input('COMMENT_SEND_EMAIL/a'), // 被评论邮件通知1:开启 0:关闭
		    'WEB_STATISTICS'       => input('WEB_STATISTICS/a'), // 第三方统计代码
		    'BAIDU_SITE_URL'       => input('BAIDU_SITE_URL/a'), // 百度推送site提交接
		];

    	foreach ($datas as $key => $data) {
    		if ($key == 'WEB_KEYWORDS') {
    			$data = [
    				'id' => $data['id'],
    				'value' => str_replace('，', ',', $data['value'])
    			];
    		}
        	$save=db('config')->update($data);
    	}
    	// 写配置文件
        if(set_sys_config($datas)){
			$this->success('配置已更新！');
		}else{
			$this->error('配置更新失败！');
		}

    }


}