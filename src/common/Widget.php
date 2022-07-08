<?php

namespace tpext\builder\common;

use tpext\common\ExtLoader;
use \tpext\builder\common\SizeAdapter;

class Widget
{
    protected static $widgets = [];

    protected static $sizeAdapter = null;

    protected static $widgetsMap = [
        //widgets
        'Form' => \tpext\builder\common\Form::class,
        'Table' => \tpext\builder\common\Table::class,
        'Search' => \tpext\builder\common\Search::class,
        'Toolbar' => \tpext\builder\common\Toolbar::class,
        'JSTree' => \tpext\builder\tree\JSTree::class,
        'ZTree' => \tpext\builder\tree\ZTree::class,
        'Content' => \tpext\builder\common\Content::class,
        'Tab' => \tpext\builder\common\Tab::class,
        'Row' => \tpext\builder\common\Row::class,
        'Column' => \tpext\builder\common\Column::class,
        //tools
        'SizeAdapter' => \tpext\builder\common\SizeAdapter::class,
        'Layer' => \tpext\builder\common\Layer::class,
        'FRow' => \tpext\builder\form\FRow::class,
        'SRow' => \tpext\builder\search\SRow::class,
        'TColumn' => \tpext\builder\table\TColumn::class,
        'Actionbar' => \tpext\builder\table\Actionbar::class,
        'MultipleToolbar' => \tpext\builder\table\MultipleToolbar::class,
    ];

    /**
     * Undocumented function
     *
     * @param array $pair
     * @return void
     */
    public static function extend($pair)
    {
        self::$widgetsMap = array_merge(self::$widgetsMap, $pair);
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function getWidgetsMap()
    {
        return self::$widgetsMap;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return boolean
     */
    public static function isWidget($name)
    {
        if (empty(self::$widgets)) {
            self::$widgets = array_keys(self::$widgetsMap);
        }

        return in_array($name, self::$widgets);
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return string
     */
    public static function getWidgetClass($name)
    {
        return self::$widgetsMap[$name];
    }

    /**
     * Undocumented function
     *
     * @param mixed ...$arguments
     * @return SizeAdapter
     */
    public static function getSizeAdapter(...$arguments)
    {
        if (!self::$sizeAdapter) {
            self::$sizeAdapter = self::makeWidget('SizeAdapter', $arguments);
        }

        return self::$sizeAdapter;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function created()
    {
        return $this;
    }

    /**
     * 创建自身
     *
     * @param mixed $arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return self::makeWidget(class_basename(get_called_class()), $arguments);
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param array $arguments
     * 
     * @return mixed
     */
    public static function makeWidget($name, $arguments = [])
    {
        if (!is_array($arguments)) {
            $arguments = [$arguments];
        }

        $widget = new self::$widgetsMap[$name](...$arguments);

        ExtLoader::trigger('tpext_widget_created', $widget);

        $widget->created();

        return $widget;
    }
}
