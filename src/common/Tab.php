<?php

namespace tpext\builder\common;

use think\Model;
use tpext\builder\form\FieldsContent;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\think\View;

class Tab extends Widget implements Renderable
{
    use HasDom;

    private $view = 'tab';

    protected $rows = [];

    protected $labels = [];

    protected $active = '';

    protected $id = '';

    protected $partial = false;

    protected $vertical = false;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $__fields__ = [];

    protected $content;

    public function getId()
    {
        if (empty($this->id)) {
            $this->id = 'tab-' . mt_rand(1000, 9999);
        }

        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $href
     * @param boolean $isActive
     * @param string $name
     * @return $this
     */
    public function addLink($label, $href, $isActive = false, $name = '')
    {
        if (empty($name)) {
            $name = (count($this->labels) + 1);
        }

        if (empty($this->active) && count($this->labels) == 0) {
            $this->active = $name;
        }

        if ($isActive) {
            $this->active = $name;
        }

        $this->labels[$name] = ['content' => $label, 'active' => '', 'href' => $href, 'attr' => ''];

        return $this;
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
            $name = (count($this->labels) + 1);
        }

        if (empty($this->active) && count($this->labels) == 0) {
            $this->active = $name;
        }

        if ($isActive) {
            $this->active = $name;
        }

        $row = Row::make();

        $this->rows[$name] = ['content' => $row, 'active' => ''];
        $this->labels[$name] = ['content' => $label, 'active' => '', 'href' => '#' . $this->getId() . '-' . $name, 'attr' => 'data-toggle="tab"'];

        return $row;
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Form
     */
    public function form($label, $isActive = false, $name = '', $size = 12)
    {
        return $this->add($label, $isActive, $name)->form($size);
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Table
     */
    public function table($label, $isActive = false, $name = '', $size = 12)
    {
        return $this->add($label, $isActive, $name)->table($size);
    }

    /**
     * Undocumented function
     *
     * @return Content
     */
    public function content($label, $isActive = false, $name = '', $size = 12)
    {
        return $this->add($label, $isActive, $name)->content($size);
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function partial($val = true)
    {
        $this->partial = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function vertical($val = true)
    {
        $this->vertical = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param boolean $isActive
     * @param string $name
     * @return FieldsContent
     */
    public function addFieldsContent($label, $isActive = false, $name = '')
    {
        if (empty($name)) {
            $name = (count($this->rows) + 1);
        }

        if (empty($this->active) && count($this->rows) == 0) {
            $this->active = $name;
        }

        if ($isActive) {
            $this->active = $name;
        }

        $content = new FieldsContent();
        $this->__fields__[] = $content;

        $this->rows[$name] = ['content' => $content, 'active' => ''];
        $this->labels[$name] = ['content' => $label, 'active' => '', 'href' => '#' . $this->getId() . '-' . $name, 'attr' => 'data-toggle="tab"'];

        return $content;
    }

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function fill($data = [])
    {
        foreach ($this->__fields__ as $content) {
            $content->fill($data);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = true)
    {
        foreach ($this->__fields__ as $content) {
            $content->readonly($val);
        }
        return $this;
    }

    public function isFieldsGroup()
    {
        return true;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function active($val)
    {
        $names = array_keys($this->labels);

        if (in_array($val, $names)) {
            $this->active = $val;
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row['content']->beforRender();
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [];
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . $this->view . '.html';

        $this->labels[$this->active]['active'] = 'active';
        isset($this->rows[$this->active]) ? $this->rows[$this->active]['active'] = 'in active' : false;

        $vars = [
            'labels' => $this->labels,
            'rows' => $this->rows,
            'active' => $this->active,
            'id' => $this->getId(),
            'class' => $this->class . ($this->vertical ? ' tabs-vertical' : ' tabs-horizontal'),
            'attr' => $this->getAttrWithStyle(),
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        $viewshow = new View($template);

        if ($this->partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    public function destroy()
    {
        $this->rows = null;
    }
}
