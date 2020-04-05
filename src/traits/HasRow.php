<?php

namespace tpext\builder\traits;

trait HasRow
{
    protected $name = '';

    protected $label = '';

    protected $cloSize = 12;

    protected $errorClass = '';

    /**
     * Displayer
     *
     * @var \tpext\builder\displayer\Field
     */
    protected $displayer;

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
    public function getName()
    {
        return $this->name;
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
     * @return string
     */
    public function getErrorClass()
    {
        return $this->errorClass;
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

    public function __toString()
    {
        return $this->render();
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
}
