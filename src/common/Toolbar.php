<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Plugin;
use tpext\builder\toolbar\Wapper;

class Toolbar extends Wapper implements Renderable
{
    protected $view = '';

    protected $class = '';

    protected $attr = '';

    protected $elms = [];

    public function render()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'toolbar.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'elms' => $this->elms,
            'class' => $this->class,
            'attr' => $this->attr,
        ];

        return $viewshow->assign($vars)->getContent();
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
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    public function beforRender()
    {
        foreach ($this->elms as $elm) {

            $elm->beforRender();
        }
    }

    public function buttons()
    {
        $this->btnAdd();
        $this->btnEnable();
        $this->btnDisable();
        $this->btnDelete();
        $this->btnRefresh();
    }

    public function btnAdd($label = '添加', $class = 'btn-primary')
    {
        $this->linkBtn('add', $label)->icon('mdi-plus')->class($class);
    }

    public function btnDelete($label = '删除', $class = 'btn-danger')
    {
        $this->linkBtn('delete', $label)->icon('mdi-delete')->class($class)->postChecked(url('delete'));
    }

    public function btnDisable($label = '禁用', $class = 'btn-warning')
    {
        $this->linkBtn('disable', $label)->icon('mdi-block-helper')->class($class)->postChecked(url('disable'));
    }

    public function btnEnable($label = '启用', $class = 'btn-success')
    {
        $this->linkBtn('enable', $label)->icon('mdi-check')->class($class)->postChecked(url('enable'));
    }

    public function btnRefresh($label = '', $class = 'btn-default')
    {
        $this->linkBtn('refresh', $label)->icon('mdi-refresh')->class($class)->attr('title="刷新"');
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
