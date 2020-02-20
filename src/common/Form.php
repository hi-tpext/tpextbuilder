<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Plugin;
use tpext\builder\form\FieldsContent;
use tpext\builder\form\Row;
use tpext\builder\form\Step;
use tpext\builder\form\Wapper;

/**
 * Form class
 */
class Form extends Wapper implements Renderable
{
    protected $view = '';

    protected $action = '';

    protected $id = 'the-form';

    protected $class = 'form-horizontal';

    protected $attr = '';

    protected $method = 'post';

    protected $rows = [];

    protected $botttomButtonsCalled = false;

    protected $ajax = false;

    protected $search;

    protected $defaultDisplayerSize = null;

    /**
     * Undocumented variable
     *
     * @var Tab
     */
    protected $tab = null;

    /**
     * Undocumented variable
     *
     * @var Step
     */
    protected $step = null;

    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields_content__ = null;

    /**
     * Undocumented function
     *
     * @param \tpext\builder\form\Row $row
     * @return $this
     */
    public function addRow($row)
    {
        $this->rows[] = $row;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows();
    }

    /**
     * Undocumented function
     *
     * @param Table $val
     * @return $this
     */
    public function search($val)
    {
        $this->search = $val->getId();
        $this->ajax = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this;
     */
    public function ajax($val)
    {
        $this->ajax = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this;
     */
    public function id($val)
    {
        $this->id = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function action($val)
    {
        $this->action = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function method($val)
    {
        $this->method = $val;
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
     * @return Tab
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param boolean $active
     * @param string $name
     * @return $this
     */
    public function tab($label, $active = false, $name = '')
    {
        if (empty($this->tab)) {
            $this->tab = new Tab();
            $this->rows[] = $this->tab;
        }

        $this->__fields_content__ = $this->tab->addFieldsContent($label, $active, $name);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Step
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $description
     * @param boolean $active
     * @param string $name
     * @return $this
     */
    public function step($label, $description = '', $active = false, $name = '')
    {
        if (empty($this->step)) {
            $this->step = new Step();
            $this->rows[] = $this->step;
        }

        $this->__fields_content__ = $this->step->addFieldsContent($label, $description, $active, $name);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function fieldsContentEnd()
    {
        $this->__fields_content__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function getFieldsContent()
    {
        return $this->__fields_content__;
    }

    /**
     * Undocumented function
     *
     * @param integer $label
     * @param integer $element
     * @return $this
     */
    public function defaultDisplayerSize($label = 2, $element = 8)
    {
        $this->defaultDisplayerSize = [$label, $element];
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
        foreach ($this->rows as $row) {
            $row->fill($data);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $create
     * @return $this
     */
    public function bottomButtons($create = true)
    {
        if ($create) {
            $this->fieldsContentEnd();
            $this->divider('', '', 12);
            $this->html('', '', 5)->showLabel(false);
            $this->btnSubmit();
            $this->btnReset();
        }

        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function searchButtons()
    {
        $this->html('', '', 5)->showLabel(false);
        $this->button('submit', '筛&nbsp;&nbsp;选', 1)->class('btn-success btn-sm');
        $this->button('button', '重&nbsp;&nbsp;置', 1)->class('btn-default btn-sm')->attr('onclick="location.replace(location.href)"');

        $this->button('refresh', 'refresh', 1)->class('hidden');

        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $class
     * @return $this
     */
    public function btnSubmit($label = '提&nbsp;&nbsp;交', $size = 1, $class = 'btn-success')
    {
        $this->button('submit', $label, $size)->class($class)->loading();
        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $class
     * @return $this
     */
    public function btnReset($label = '重&nbsp;&nbsp;置', $size = 1, $class = 'btn-warning')
    {
        $this->button('submit', $label, $size)->class($class);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $class
     * @param string $attr
     * @return $this
     */
    public function btnBack($label = '返&nbsp;&nbsp;回', $size = 1, $class = 'btn-default btn-go-back', $attr = 'onclick="history.go(-1);')
    {
        $this->button('button', $label, $size)->class($class)->attr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $class
     * @return $this
     */
    public function btnLayerClose($label = '返&nbsp;&nbsp;回', $size = 1, $class = 'btn-default')
    {
        $this->button('button', $label, $size)->class($class . ' btn-close-layer');
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        if (!$this->botttomButtonsCalled && empty($this->step)) {
            if ($this->search) {
                $this->searchButtons();
            } else {
                $this->bottomButtons(true);
            }
        }

        if ($this->search) {
            $this->hidden('__page__', 1);
            $this->addClass(' search-form');
            $this->searchScript();
        }

        foreach ($this->rows as $row) {
            if ($this->search) {
                $row->getDisplayer()->fullSize(3)->autoPost(false);
            }

            $row->beforRender();
        }

        return $this;
    }

    protected function searchScript()
    {
        $form = $this->getId();

        $script = <<<EOT
        $('body').on('click', '#{$this->search} ul li a', function(){
            var page = $(this).attr('href').replace(/.*\?page=(\d+).*/,'$1');
            $('#form-__page__').val(page);
            $('#{$form} form').trigger('submit');
            return false;
        });

        $('body').on('click', '#btn-tool-refresh,#form-refresh', function(){
            $('#{$form} form').trigger('submit');
        });

        if($('#{$form} form').hasClass('form-empty'))
        {
            $('#btn-tool-search').remove();
        }

        $('body').on('click', '#btn-tool-search', function(){
            if($('#{$form} form').hasClass('hidden'))
            {
                $('#{$form} form').removeClass('hidden');
            }
            else
            {
                $('#{$form} form').slideToggle(300);
            }
        });

        $('body').on('click', '#form-submit', function(){
            $('#form-__page__').val(1);
        });

EOT;
        Builder::getInstance()->addScript($script);

        return $script;
    }

    public function render()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'form.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'rows' => $this->rows,
            'action' => $this->action,
            'method' => strtoupper($this->method),
            'class' => $this->class,
            'attr' => $this->attr,
            'id' => $this->id,
            'ajax' => ($this->ajax || !empty($this->search) ? 1 : 0),
            'search' => $this->search,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $row = new Row($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 12, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            if ($this->__fields_content__) {
                $this->__fields_content__->addRow($row);
            } else {
                $this->rows[] = $row;
            }

            $displayer = $row->$name($arguments[0], $row->getLabel());

            if ($this->defaultDisplayerSize) {
                $displayer->size($this->defaultDisplayerSize[0], $this->defaultDisplayerSize[1]);
            }

            return $displayer;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
