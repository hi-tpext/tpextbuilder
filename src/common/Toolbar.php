<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\toolbar\Bar;
use tpext\builder\toolbar\Wapper;

class Toolbar extends Wapper implements Renderable
{
    protected $view = '';

    protected $class = '';

    protected $attr = '';

    protected $elms = [];

    protected $__elm__;

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
     * @return Bar
     */
    public function getCurrent()
    {
        return $this->__elm__;
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

    public function render()
    {
        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'toolbar.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'elms' => $this->elms,
            'class' => $this->class,
            'attr' => $this->attr,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $class = static::$displayerMap[$name];

            $this->__elm__ = new $class($arguments[0], $count > 1 ? $arguments[1] : '');

            $this->__elm__->created();

            $this->elms[] = $this->__elm__;

            return $this->__elm__;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
