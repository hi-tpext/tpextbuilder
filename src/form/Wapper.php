<?php

namespace tpext\builder\form;

/**
 * Class Wapper.
 *
 * @method \tpext\builder\displayer\Field          field($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Text           text($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Checkbox       checkbox($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Radio          radio($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Button         button($type, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Select         select($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\MultipleSelect multipleSelect($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Textarea       textarea($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Hidden         hidden($name)
 * @method \tpext\builder\displayer\Color          color($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\RangeSlider    rangeSlider($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\File           file($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Image          image($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Date           date($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Datetime       datetime($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Time           time($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Year           year($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Month          month($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\DateRange      dateRange($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\DateTimeRange  datetimeRange($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\TimeRange      timeRange($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Number         number($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\SwitchBtn      switchBtn($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Rate           rate($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Divider        divider($text, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Password       password($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Decimal        decimal($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Html           html($html, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Raw            raw($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Show           show($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Tags           tags($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Icon           icon($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\MultipleImage  multipleImage($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\MultipleFile   multipleFile($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\WangEditor     wangEditor($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Tinymce        tinymce($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\UEditor        ueditor($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\WangEditor     editor($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\CKEditor       ckeditor($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\MDEditor       mdeditor($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Match          match($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 * @method \tpext\builder\displayer\Matches        matches($name, $label = '', $cloSize = 12, $colClass = '', $colAttr = '')
 */

class Wapper
{
    protected $dfaultDisplayerSize = null;

    protected static $displayers = [];

    protected static $displayerMap = [
        'field' => \tpext\builder\displayer\Field::class,
        'text' => \tpext\builder\displayer\Text::class,
        'textarea' => \tpext\builder\displayer\Textarea::class,
        'html' => \tpext\builder\displayer\Html::class,
        'divider' => \tpext\builder\displayer\Divider::class,
        'raw' => \tpext\builder\displayer\Raw::class,
        'checkbox' => \tpext\builder\displayer\Checkbox::class,
        'radio' => \tpext\builder\displayer\Radio::class,
        'button' => \tpext\builder\displayer\Button::class,
        'select' => \tpext\builder\displayer\Select::class,
        'multipleSelect' => \tpext\builder\displayer\MultipleSelect::class,
        'hidden' => \tpext\builder\displayer\Hidden::class,
        'switchBtn' => \tpext\builder\displayer\SwitchBtn::class,
        'tags' => \tpext\builder\displayer\Tags::class,
        'datetime' => \tpext\builder\displayer\DateTime::class,
        'date' => \tpext\builder\displayer\Date::class,
        'time' => \tpext\builder\displayer\Time::class,
        'datetimeRange' => \tpext\builder\displayer\DateTimeRange::class,
        'dateRange' => \tpext\builder\displayer\DateRange::class,
        'timeRange' => \tpext\builder\displayer\TimeRange::class,
        'color' => \tpext\builder\displayer\Color::class,
        'number' => \tpext\builder\displayer\Number::class,
        'icon' => \tpext\builder\displayer\Icon::class,
        'wangEditor' => \tpext\builder\displayer\WangEditor::class,
        'tinymce' => \tpext\builder\displayer\Tinymce::class,
        'ueditor' => \tpext\builder\displayer\UEditor::class,
        'ckeditor' => \tpext\builder\displayer\CKEditor::class,
        'mdeditor' => \tpext\builder\displayer\MDEditor::class,
        'editor' => \tpext\builder\displayer\WangEditor::class,
        'rate' => \tpext\builder\displayer\Rate::class,
        'month' => \tpext\builder\displayer\Month::class,
        'year' => \tpext\builder\displayer\Year::class,
        'multipleFile' => \tpext\builder\displayer\MultipleFile::class,
        'file' => \tpext\builder\displayer\File::class,
        'multipleImage' => \tpext\builder\displayer\MultipleImage::class,
        'image' => \tpext\builder\displayer\Image::class,
        'rangeSlider' => \tpext\builder\displayer\RangeSlider::class,
        'match' => \tpext\builder\displayer\Match::class,
        'matches' => \tpext\builder\displayer\Matches::class,
        'show' => \tpext\builder\displayer\Show::class,
        'password' => \tpext\builder\displayer\Password::class,
    ];

    protected static $defaultFieldClass = [];

    protected static $using = [];

    public static function addUsing($class)
    {
        static::$using[] = $class;
    }

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

    public static function getDisplayerMap()
    {
        return static::$displayerMap;
    }

    public static function getUsing()
    {
        return static::$using;
    }

    public static function setdefaultFieldClass($pair)
    {
        static::$defaultFieldClass = array_merge(static::$defaultFieldClass, $pair);
    }
}
