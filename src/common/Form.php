<?php

namespace tpext\builder\common;

use tpext\builder\common\Plugin;
use think\response\View as ViewShow;

class Form
{

    protected $cols = [];

    public function render()
    {

        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'content.html']);

        $config = [];
        
        $view = new ViewShow($this->view);

        return $view->assign([])->config($config)->isContent(false)->getContent();
    }

}
