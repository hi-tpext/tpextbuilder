<?php
namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Plugin;

class Layer
{
    private $view = '';

    public function close($success = true, $msg = '操作成功')
    {
        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'layer', 'close.html']);

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
        ];

        $view = new ViewShow($this->view);

        return $view->assign($vars);
    }

    public function closeRefresh($success = true, $msg = '操作成功')
    {
        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'layer', 'closeRefresh.html']);

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
        ];

        $view = new ViewShow($this->view);

        return $view->assign($vars);
    }
}
