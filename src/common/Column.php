<?php

namespace tpext\builder\common;

use tpext\builder\common\Form;
use tpext\builder\common\Table;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\tree\JSTree;
use tpext\builder\tree\ZTree;

class Column extends Widget
{
    use HasDom;

    public $size = 12;

    protected $elms = [];

    public function __construct($size = 12)
    {
        $this->size = $size;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param mixed $arguments
     * 
     * @return mixed
     */
    protected function createWidget($name, ...$arguments)
    {
        $widget = Widget::makeWidget($name, $arguments);
        $this->elms[] = $widget;
        return $widget;
    }

    /**
     * Undocumented function
     *
     * @param Renderable $rendable
     * @return $this
     */
    public function append($rendable)
    {
        $this->elms[] = $rendable;
        return $this;
    }

    /**
     * 获取一个form
     *
     * @return Form
     */

    public function form()
    {
        return $this->createWidget('Form');
    }

    /**
     * 获取一个表格
     *
     * @return Table
     */
    public function table()
    {
        return $this->createWidget('Table');
    }

    /**
     * 获取一个Toolbar
     *
     * @return Toolbar
     */
    public function toolbar()
    {
        return $this->createWidget('Toolbar');
    }

    /**
     * 获取一个ZTree
     *
     * @return ZTree
     */
    public function tree()
    {
        return $this->zTree();
    }

    /**
     * 获取一个ZTree
     *
     * @return ZTree
     */
    public function zTree()
    {
        return $this->createWidget('ZTree');
    }

    /**
     * 获取一个jsTree
     *
     * @return JSTree
     */
    public function jsTree()
    {
        return $this->createWidget('JSTree');
    }

    /**
     * 获取一个自定义内容
     *
     * @return Content
     */
    public function content()
    {
        return $this->createWidget('Content');
    }

    /**
     * 获取一个 tab
     *
     * @return Tab
     */
    public function tab()
    {
        return $this->createWidget('Tab');
    }

    /**
     * 获取一新行
     *
     * @return Row
     */
    public function row()
    {
        return $this->createWidget('Row');
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getElms()
    {
        return $this->elms;
    }

    /**
     * Undocumented function
     *
     * @return int|string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->elms as $elm) {
            if (!($elm instanceof Renderable)) {
                continue;
            }
            $elm->beforRender();
        }

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (self::isWidget($name)) {

            $widget = $this->createWidget($name, $arguments);

            return $widget;
        }

        throw new \InvalidArgumentException(__blang('bilder_invalid_argument_exception') . ' : ' . $name);
    }

    public function destroy()
    {
        foreach ($this->elms as $elm) {
            if (method_exists($elm, 'destroy')) {
                $elm->destroy();
            }
        }

        $this->elms = null;
    }
}
