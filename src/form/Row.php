<?php

namespace tpext\builder\form;

use tpext\builder\common\Renderable;
use tpext\builder\displayer\Field;

class Row extends Wapper implements Renderable
{
    protected $attributes = [];

    protected $name = '';

    protected $label = '';

    protected $size = 12;

    protected $class = '';

    protected $attr = '';

    protected $error = '';

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

        $this->displayer = new Field($name, $label);
        $this->displayer->setWapper($this);

        return $this;
    }

    public function createDisplayer($class, $arguments)
    {
        $this->displayer = new $class($arguments[0], $arguments[1]);
        $this->displayer->setWapper($this);

        return $this->displayer;
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

    public function error($val)
    {
        $this->error = $val;
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

    public function getError()
    {
        return empty($this->error) ? '' : ' ' . $this->error;
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

    public function render()
    {
        return $this->displayer->render();
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
