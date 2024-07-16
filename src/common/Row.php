<?php

namespace tpext\builder\common;

use tpext\builder\traits\HasDom;
use tpext\builder\tree\ZTree;
use tpext\builder\tree\JSTree;
use tpext\think\View;

class Row extends Widget
{
    use HasDom;

    /**
     * Undocumented variable
     *
     * @var Column[] 
     */
    protected $cols = [];

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Column
     */
    public function column($size = 12)
    {
        $col = self::makeWidget('Column', $size);
        $this->cols[] = $col;
        return $col;
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Form
     */
    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Table
     */
    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    /**
     * 获取一个工具栏
     *
     * @param integer|string $size
     * @return Toolbar
     */
    public function toolbar($size = 12)
    {
        return $this->column($size)->toolbar();
    }

    /**
     * 默认获取一个ZTree树
     *
     * @param integer|string $size
     * @return ZTree
     */
    public function tree($size = 12)
    {
        return $this->column($size)->tree();
    }

    /**
     * 获取一个ZTree树
     *
     * @param integer|string $size
     * @return ZTree
     */
    public function zTree($size = 12)
    {
        return $this->column($size)->zTree();
    }

    /**
     * 获取一个JSTree树
     *
     * @param integer|string $size
     * @return JSTree
     */
    public function jsTree($size = 12)
    {
        return $this->column($size)->jsTree();
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Content
     */
    public function content($size = 12)
    {
        return $this->column($size)->content();
    }

    /**
     * Undocumented function
     *
     * @param string $template
     * @param array $vars
     * @param integer|string $size col大小
     * @return $this
     */
    public function fetch($template = '', $vars = [], $size = 12)
    {
        $this->content($size)->fetch($template, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param array $vars
     * @param integer|string $size col大小
     * @return $this
     */
    public function display($content = '', $vars = [], $size = 12)
    {
        $this->content($size)->display($content, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
     * @return Tab
     */
    public function tab($size = 12)
    {
        return $this->column($size)->tab();
    }

    /**
     * 获取一Swiper
     *
     * @param integer|string $size col大小
     * @return Swiper
     */
    public function swiper($size = 12)
    {
        return $this->column($size)->swiper();
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

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->cols as $col) {
            $col->beforRender();
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [];
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'row.html';

        $viewshow = new View($template);

        $vars = [
            'cols' => $this->cols,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function destroy()
    {
        foreach ($this->cols as $col) {
            $col->destroy();
        }

        $this->cols = null;
    }
}
