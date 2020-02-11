<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;

class Row
{
    protected $cols = [];

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return void
     */
    public function column($size = 12)
    {
        $col = new Column($size);
        $this->cols[] = $col;
        return $col;
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Form
     */
    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Table
     */
    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    /**
     * 获取一个工具栏
     *
     * @return Toolbar
     */
    public function toolbar($size = 12)
    {
        return $this->column($size)->table();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCols()
    {
        return $this->cols;
    }

    public function beforRender()
    {
        foreach ($this->cols as $col) {
            $col->beforRender();
        }
    }


    public function render()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'row.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'cols' => $this->cols,
        ];

        return $viewshow->assign($vars)->getContent();
    }
}
