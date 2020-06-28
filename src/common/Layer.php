<?php
namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Module;

class Layer
{
    /**
     * Undocumented variable
     *
     * @var ViewShow
     */
    private $viewShow;

    public function getViewShow()
    {
        return $this->viewShow;
    }

    public function close($success = true, $msg = '操作成功')
    {
        if (request()->isAjax()) {
            return json([
                'code' => $success ? 1 : 0,
                'msg' => $msg,
                'layer_close' => 1,
            ]);
        }

        $view = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'layer', 'close.html']);

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
        ];

        $this->viewShow = new ViewShow($view);

        $this->viewShow->assign($vars);

        return $this->viewShow;
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

        $view = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'layer', 'closego.html']);

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
            'url' => $url,
        ];

        $this->viewShow = new ViewShow($view);

        $this->viewShow->assign($vars);

        return $this->viewShow;
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

        $view = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'layer', 'closerefresh.html']);

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
        ];

        $this->viewShow = new ViewShow($view);

        $this->viewShow->assign($vars);

        return $this->viewShow;
    }
}
