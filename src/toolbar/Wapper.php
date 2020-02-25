<?php

namespace tpext\builder\toolbar;

/**
 * Class Wapper.
 *
 * @method \tpext\builder\toolbar\LinkBtn          linkBtn($name, $label)
 * @method \tpext\builder\toolbar\ActionBtn        actionBtn($name, $label)
 * @method \tpext\builder\toolbar\ImportBtn        importBtn($name, $label)
 * @method \tpext\builder\toolbar\Html             html($html)
 */

class Wapper
{
    protected static $displayers = [];

    protected static $displayerMap = [
        'linkBtn' => \tpext\builder\toolbar\LinkBtn::class,
        'actionBtn' => \tpext\builder\toolbar\ActionBtn::class,
        'html' => \tpext\builder\toolbar\Html::class,
    ];

    protected static $defaultFieldClass = [
        'linkBtn' => 'btn-sm',
    ];

    public static function isDisplayer($name)
    {
        if (empty(static::$displayers)) {
            static::$displayers = array_keys(static::$displayerMap);
        }

        return in_array($name, static::$displayers);
    }

    public static function hasDefaultFieldClass($type)
    {
        if (isset(static::$defaultFieldClass[$type])) {
            return static::$defaultFieldClass[$type];
        }

        return '';
    }

    public static function extend($pair)
    {
        static::$displayerMap = array_merge(static::$displayerMap, $pair);
    }

    public static function setdefaultFieldClass($pair)
    {
        static::$defaultFieldClass = array_merge(static::$defaultFieldClass, $pair);
    }
}
