<?php

namespace tpext\builder\form;

use tpext\builder\common\Renderable;

class Row extends Wapper implements Renderable
{
    protected $attributes = [];

    protected $name = '';

    protected $label = '';

    protected $size = 12;

    protected $class = '';

    protected $attr = '';

    protected $errorClass = '';

    /**
     * Displayer
     *
     * @var \tpext\builder\displayer\Field
     */
    protected $displayer;

    protected $options = [];

    public function __construct($name, $label = '', $colSize = 12, $colClass = '', $colAttr = '')
    {
        if (empty($label)) {
            $label = ucfirst($name);
        }

        $this->name = $name;
        $this->label = $label;
        $this->size = $colSize;
        $this->class = $colClass;
        $this->attr = $colAttr;

        $this->createDisplayer(\tpext\builder\displayer\Field::class, [$name, $label]);

        return $this;
    }

    public function createDisplayer($class, $arguments)
    {
        $displayer = new $class($arguments[0], $arguments[1]);
        $displayer->setWapper($this);
        $displayer->created();

        $this->displayer = $displayer;

        return $displayer;
    }

    public function options($options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function size($val)
    {
        $this->size = $val;
        return $this;
    }

    public function errorClass($val)
    {
        $this->errorClass = $val;
        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getClass()
    {
        return empty($this->class) ? '' : ' ' . $this->class;
    }

    public function getErrorClass()
    {
        return empty($this->errorClass) ? '' : ' ' . $this->errorClass;
    }

    public function getAttr()
    {
        return empty($this->attr) ? '' : ' ' . $this->attr;
    }

    public function addClass($val)
    {
        $this->class .= ' ' . $val;
        return $this;
    }

    public function addAttr($val)
    {
        $this->attr .= ' ' . $val;
        return $this;
    }

    public function getDisplayer()
    {
        return $this->displayer();
    }

    public function render()
    {
        return $this->displayer->render();
    }

    public function publishAssets()
    {
        $this->displayer->publishAssets();
    }

    public function __call($name, $arguments)
    {
        if (static::isDisplayer($name)) {

            $class = static::$displayerMap[$name];

            return $this->createDisplayer($class, $arguments);
        }

        throw new \UnexpectedValueException('未知调用');
    }
}
