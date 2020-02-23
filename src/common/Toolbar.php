<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\toolbar\Wapper;

class Toolbar extends Wapper implements Renderable
{
    protected $view = '';

    protected $class = '';

    protected $attr = '';

    protected $elms = [];

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

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $class = static::$displayerMap[$name];

            $elm = new $class($arguments[0], $count > 1 ? $arguments[1] : '');

            $elm->created();

            $this->elms[] = $elm;

            return $elm;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
