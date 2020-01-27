<?php

namespace tpext\builder\common;

use tpext\myadmin\common\Plugin;

class Builder implements Renderable
{
    private $view = '';

    protected $title = null;

    protected $desc = null;

    protected $rows = [];

    protected $__row__ = null;

    public function __construct($title = '', $desc = '')
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

    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    public function render()
    {
        return Response::create($template, 'view')->assign($vars)->config($config);
    }
}
