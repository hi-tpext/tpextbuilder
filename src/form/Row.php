<?php

namespace tpext\builder\form;

use tpext\builder\common\Renderable;

class Row extends Wapper implements Renderable
{
    protected $attributes = [];

    protected $name = '';

    protected $label = '';

    protected $cloSize = 12;

    protected $class = '';

    protected $attr = '';

    protected $style = '';

    protected $errorClass = '';

    /**
     * Displayer
     *
     * @var \tpext\builder\displayer\Field
     */
    protected $displayer;

    public function __construct($name, $label = '', $colSize = 12, $colClass = '', $colAttr = '')
    {
        if (empty($label)) {
            $label = ucfirst($name);
        }

        $this->name = $name;
        $this->label = $label;
        $this->cloSize = $colSize;
        $this->class = $colClass;
        $this->attr = $colAttr;

        $this->createDisplayer(\tpext\builder\displayer\Field::class, [$name, $label]);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function cloSize($val)
    {
        $this->size = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function style($val)
    {
        $this->style = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function class ($val)
    {
        $this->class = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function attr($val)
    {
        $this->attr = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function errorClass($val)
    {
        $this->errorClass = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addClass($val)
    {
        $this->class .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addAttr($val)
    {
        $this->attr .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addStyle($val)
    {
        $this->style .= $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getColSize()
    {
        return $this->cloSize;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getClass()
    {
        $arr = explode(' ', $this->class);

        return 'row-' . $this->name . ' ' . implode(' ', array_unique($arr));
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getErrorClass()
    {
        return $this->errorClass;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getAttr()
    {
        return (empty($this->attr) ? '' : ' ' . $this->attr) . (empty($this->style) ? '' : ' style="' . $this->style . '"');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Undocumented function
     *
     * @return \tpext\builder\displayer\Field
     */
    public function getDisplayer()
    {
        return $this->displayer;
    }

    public function render()
    {
        return $this->displayer->render();
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $this->displayer->beforRender();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->displayer->fill($data);
        return $this;
    }

    public function createDisplayer($class, $arguments)
    {
        $displayer = new $class($arguments[0], $arguments[1]);
        $displayer->setWapper($this);
        $displayer->created();

        $this->displayer = $displayer;

        static::addUsing($displayer);

        return $displayer;
    }

    public function __call($name, $arguments)
    {
        if (static::isDisplayer($name)) {

            $class = static::$displayerMap[$name];

            return $this->createDisplayer($class, $arguments);
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
