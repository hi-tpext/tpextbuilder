<?php
namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Module;

class Layer
{
    private $view = '';

    public function close($success = true, $msg = '操作成功')
    {
        if (request()->isAjax()) {
            return json([
                'code' => $success ? 1 : 0,
                'msg' => $msg,
                'layer_close' => 1,
            ]);
        }

        $this->view = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'layer', 'close.html']);

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
        ];

        $view = new ViewShow($this->view);

        return $view->assign($vars);
    }

    public function closeGo($success = true, $msg = '操作成功', $url)
    {
        if (request()->isAjax()) {
            return json([
                'code' => $success ? 1 : 0,
                'msg' => $msg,
                'layer_close_go' => $url,
            ]);
        }

        $this->view = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'layer', 'closego.html']);

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
            'url' => $url,
        ];

        $view = new ViewShow($this->view);

        return $view->assign($vars);
    }

    public function closeRefresh($success = true, $msg = '操作成功')
    {
        if (request()->isAjax()) {
            return json([
                'code' => $success ? 1 : 0,
                'msg' => $msg,
                'layer_close_refresh' => 1,
            ]);
        }

        $this->view = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'layer', 'closerefresh.html']);

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
        ];

        $view = new ViewShow($this->view);

        return $view->assign($vars);
    }
}
