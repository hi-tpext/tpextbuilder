<?php

namespace tpext\builder\common;

use tpext\builder\common\Form;
use tpext\builder\common\Table;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\tree\JSTree;
use tpext\builder\tree\ZTree;
use tpext\common\ExtLoader;

class Column
{
    use HasDom;

    public $size = 12;

    protected $elms = [];

    protected static $widgets = [];

    protected static $widgetsMap = [
        'Form' => \tpext\builder\common\Form::class,
        'Table' => \tpext\builder\common\Table::class,
        'Toolbar' => \tpext\builder\common\Toolbar::class,
        'JSTree' => \tpext\builder\tree\JSTree::class,
        'ZTree' => \tpext\builder\tree\ZTree::class,
        'Content' => \tpext\builder\common\Content::class,
        'Tab' => \tpext\builder\common\Tab::class,
        'Row' => \tpext\builder\common\Row::class,
    ];

    public function __construct($size = 12)
    {
        $this->size = $size;
    }

    /**
     * Undocumented function
     *
     * @param array $pair
     * @return void
     */
    public static function extend($pair)
    {
        static::$widgetsMap = array_merge(static::$widgetsMap, $pair);
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function getWidgetMap()
    {
        return static::$widgetsMap;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return boolean
     */
    public static function isWidget($name)
    {
        if (empty(static::$widgets)) {
            static::$widgets = array_keys(static::$widgetsMap);
        }

        return in_array($name, static::$widgets);
    }



    /**
     * Undocumented function
     *
     * @param string $name
     * @param mixed $arguments
     * 
     * @return mixed
     */
    protected function createWidget($name, $arguments = [])
    {
        $widget = new static::$widgetsMap[$name]($arguments);
        ExtLoader::trigger('tpext_create_' . strtolower($name), $widget);
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
     * @return integer
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
            $elm->beforRender();
        }

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (static::isWidget($name)) {

            return $this->createWidget($name, $arguments);
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
