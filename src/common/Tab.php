<?php

namespace tpext\builder\common;

use think\facade\View;
use think\response\View as ViewShow;
use tpext\builder\form\Tab as FormTab;

class Tab implements Renderable
{
    private $view = '';

    protected $class = 'nav-justified';

    protected $rows = [];

    protected $labels = [];

    protected $active = '';

    protected $id = '';

    public function getId()
    {
        if (empty($this->id)) {
            $this->id = mt_rand(1000, 9999);
        }

        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param boolean $isActive
     * @param string $name
     * @return Row
     */
    public function add($label, $isActive = false, $name = '')
    {
        if (empty($name)) {
            $name = '' . count($this->rows);
        }

        if (empty($this->active) && count($this->rows) == 0) {
            $this->active = $name;
        }

        if ($isActive) {
            $this->active = $name;
        }

        $row = new Row();
        $this->rows[$name] = $row;
        $this->labels[$name] = $label;
        return $row;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param boolean $isActive
     * @param string $name
     * @return FormTab
     */
    public function addFromContent($label, $isActive = false, $name = '')
    {
        if (empty($name)) {
            $name = '' . count($this->rows);
        }

        if ($isActive) {
            $this->active = $name;
        }

        $content = new FormTab();

        $this->rows[$name] = $content;
        $this->labels[$name] = $label;
        return $content;
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
    public function active($val)
    {
        $this->active = $val;
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
        $this->wapper->errorClass($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getClass()
    {
        return empty($this->class) ? '' : ' ' . $this->class;
    }

    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row->beforRender();
        }
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render($partial = false)
    {
        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'tab.html']);

        $vars = [
            'labels' => $this->labels,
            'rows' => $this->rows,
            'active' => $this->active,
            'id' => $this->getId(),
            'class' => $this->class,
        ];

        $viewshow = new ViewShow($this->view);

        if ($partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
    }
}
