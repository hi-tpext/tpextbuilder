<?php

namespace tpext\builder\toolbar;

/**
 * Class Wapper.
 *
 * @method \tpext\builder\toolbar\LinkBtn          linkBtn($name, $label)
 * @method \tpext\builder\toolbar\ActionBtn        actionBtn($name, $label)
 * @method \tpext\builder\toolbar\DropdownBtns     dropdownBtns($items, $label)
 * @method \tpext\builder\toolbar\MultipleActions  multipleActions($items, $label)
 * @method \tpext\builder\toolbar\Actions          actions($items, $label)
 * @method \tpext\builder\toolbar\Html             html($html)
 */

class BWapper
{
    protected static $displayers = [];

    protected static $displayerMap = [
        'linkBtn' => \tpext\builder\toolbar\LinkBtn::class,
        'actionBtn' => \tpext\builder\toolbar\ActionBtn::class,
        'dropdownBtns' => \tpext\builder\toolbar\DropdownBtns::class,
        'multipleActions' => \tpext\builder\toolbar\MultipleActions::class,
        'actions' => \tpext\builder\toolbar\Actions::class,
        'html' => \tpext\builder\toolbar\Html::class,
    ];

    protected static $defaultFieldClass = [
        \tpext\builder\toolbar\LinkBtn::class => 'btn-xs',
        \tpext\builder\toolbar\ActionBtn::class => 'btn-xs',
        \tpext\builder\toolbar\DropdownBtns::class => 'btn-xs',
        \tpext\builder\toolbar\MultipleActions::class => 'btn-xs',
        \tpext\builder\toolbar\Actions::class => 'btn-xs',
    ];

    /**
     * Undocumented function
     *
     * @param string $name
     * @return boolean
     */
    public static function isDisplayer($name)
    {
        if (empty(static::$displayers)) {
            static::$displayers = array_keys(static::$displayerMap);
        }

        return in_array($name, static::$displayers);
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return string
     */
    public static function hasDefaultFieldClass($type)
    {
        if (isset(static::$defaultFieldClass[$type])) {
            return static::$defaultFieldClass[$type];
        }

        return '';
    }

    /**
     * Undocumented function
     *
     * @param array $pair
     * @return void
     */
    public static function extend($pair)
    {
        static::$displayerMap = array_merge(static::$displayerMap, $pair);
    }

    /**
     * Undocumented function
     *
     * @param array $pair
     * @return void
     */
    public static function setdefaultFieldClass($pair)
    {
        static::$defaultFieldClass = array_merge(static::$defaultFieldClass, $pair);
    }
}
