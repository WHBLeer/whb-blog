<?php
namespace app\admin\controller;
use app\admin\model\Admin as AdminModel;
use app\admin\model\Config as ConfigModel;
use think\Db;
use think\Cache;
use app\admin\controller\Base;
class Admin extends  Base
{
    /**
     * 清除缓存
     */
    public function clear() {
        if (delete_dir_file(CACHE_PATH) || delete_dir_file(TEMP_PATH)) {
            $this->success('清除缓存成功!');
        } else {
            $this->error('清除缓存失败!');
        }
    }

    /**
     * 清除模版缓存 不删除temp目录
     */
    public function clearCache() {
        if (delete_dir_file(CACHE_PATH)) {
            $this->success('清除缓存成功!');
        } else {
            $this->error('清除缓存失败!');
        }
    }

    /**
     * 清除模版缓存 不删除cache目录 
     */
    public function clearTemp() {
        if (delete_dir_file(TEMP_PATH)) {
            $this->success('清除缓存成功!');
        } else {
            $this->error('清除缓存失败!');
        }
    }  
}