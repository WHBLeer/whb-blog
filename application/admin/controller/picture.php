<?php
namespace app\admin\controller;
use app\admin\controller\Base;

class Picture  extends  Base
{

    public function index()
    {
        return $this->fetch();

    }  
}
