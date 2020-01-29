<?php

namespace tpext\builder\common;

use think\facade\View;
use think\response\View as ViewShow;
use tpext\builder\common\Plugin;
use tpext\myadmin\common\Module;

class Builder implements Renderable
{
    private $view = '';

    protected $title = '';

    protected $desc = null;

    protected $rows = [];

    protected $__row__ = null;

    protected $js = [];

    protected $css = [];

    public function __construct($title = 'tpext-builder', $desc = '')
    {
        $this->title = $title;
        $this->desc = $desc;
        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'content.html']);
    }

    public function row()
    {
        $row = new Row();
        $this->rows[] = $row;
        $this->__row__ = $row;
        return $row;
    }

    public function column($size = 12)
    {
        if (!$this->__row__) {
            $this->row();
        }

        return $this->__row__->column($size);
    }

    /**
     * 获取一个form
     *
     * @param integer col大小
     * @return Form
     */
    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    /**
     * 获取一个表格
     *
     * @param integer col大小
     * @return Table
     */
    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    public function render()
    {
        $vars = [
            'title' => $this->title,
            'desc' => $this->desc,
            'rows' => $this->rows,
        ];

        $config = [];

        $view = new ViewShow($this->view);

        $instance = Module::getInstance();

        $config = $instance->setConfig(['page_title' => $this->title, 'position' => '编辑']);

        View::share(['admin' => $config]);

        return $view->assign($vars)->config($config);
    }
}
