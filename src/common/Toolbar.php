<?php

namespace tpext\builder\common;

use tpext\builder\inface\Renderable;
use tpext\builder\toolbar\Bar;
use tpext\builder\toolbar\BWrapper;
use tpext\builder\traits\HasDom;

class Toolbar extends BWrapper implements Renderable
{
    use HasDom;

    protected $view = '';

    protected $elms = [];

    /**
     * 当前元素
     *
     * @var Bar
     */
    protected $__elm__;

    protected $extKey = '';

    protected $elmsRight = [];

    protected $elmsLeft = [];

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function extKey($val)
    {
        $this->extKey = $val;
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
     * @return array
     */
    public function getElms()
    {
        return $this->elms;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->elms);
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clear()
    {
        $this->__elm__ = null;
        $this->elms = [];
        $this->elmsRight = [];
        $this->elmsLeft = [];

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $this->elmsLeft = $this->elmsRight = [];

        foreach ($this->elms as $elm) {

            if ($this->extKey) {
                $elm->extKey($this->extKey);
            }

            if ($elm->isPullRight()) {
                $this->elmsRight[] = $elm;
            } else {
                $this->elmsLeft[] = $elm;
            }

            $elm->beforRender();
        }

        return $this;
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'toolbar.html';

        $viewshow = view($template);

        $vars = [
            'elms' => $this->elms,
            'elmsLeft' => $this->elmsLeft,
            'elmsRight' => $this->elmsRight,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
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

            $class = static::$displayersMap[$name];

            $this->__elm__ = new $class($arguments[0], $count > 1 ? $arguments[1] : '');

            $this->__elm__->created();

            $this->elms[] = $this->__elm__;

            return $this->__elm__;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }

    /**
     * 以下为代理方法，当前[$__elm__]生效
     *
     */

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function pullRight($val = true)
    {
        if ($this->__elm__) {
            $this->__elm__->pullRight($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function barPullRight($val = true)
    {
        if ($this->__elm__) {
            $this->__elm__->pullRight($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function barUseLayer($val, $size = [])
    {
        if ($this->__elm__) {
            $this->__elm__->useLayer($val, $size);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function barLayerSize($size = [])
    {
        if ($this->__elm__) {
            $this->__elm__->useLayer(true, $size);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barLabel($val)
    {
        if ($this->__elm__) {
            $this->__elm__->label($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barIcon($val)
    {
        if ($this->__elm__) {
            $this->__elm__->icon($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barHref($val)
    {
        if ($this->__elm__) {
            $this->__elm__->href($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barName($val)
    {
        if ($this->__elm__) {
            $this->__elm__->href($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function barClass($val)
    {
        if ($this->__elm__) {
            $this->__elm__->class($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barAttr($val)
    {
        if ($this->__elm__) {
            $this->__elm__->attr($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barStyle($val)
    {
        if ($this->__elm__) {
            $this->__elm__->style($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barAddClass($val)
    {
        if ($this->__elm__) {
            $this->__elm__->addClass($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barAddAttr($val)
    {
        if ($this->__elm__) {
            $this->__elm__->addAttr($val);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function barAddStyle($val)
    {
        if ($this->__elm__) {
            $this->__elm__->addStyle($val);
        }
        return $this;
    }
}
