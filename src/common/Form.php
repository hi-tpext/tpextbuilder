<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Plugin;
use tpext\builder\form\Row;
use tpext\builder\form\Tab as FormTab;
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

    protected $ajax = true;

    protected $search;

    /**
     * Undocumented variable
     *
     * @var Tab
     */
    protected $__tab__;

    /**
     * Undocumented function
     *
     * @param \tpext\builder\form\Row $row
     * @return void
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
     * @param string $name
     * @return FormTab
     */
    public function tab($label, $name = '', $active = false)
    {
        if (empty($this->__tab__)) {
            $this->__tab__ = new Tab();
            $this->rows[] = $this->__tab__;
        }

        $fromTab = $this->__tab__->addFromContent($label, $name, $active);
        return $fromTab;
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
            $this->divider('', '', 12);
            $this->html('', '', 5)->showLabel(false);
            $this->button('submit', '提&nbsp;&nbsp;交', 1)->class('btn-success');
            $this->button('reset', '重&nbsp;&nbsp;置', 1)->class('btn-warning');
        }

        $this->botttomButtonsCalled = true;
        return $this;
    }

    public function searchButtons()
    {
        $this->html('', '', 5)->showLabel(false);
        $this->button('submit', '筛&nbsp;&nbsp;选', 1)->class('btn-success btn-sm');
        $this->button('button', '重&nbsp;&nbsp;置', 1)->class('btn-default btn-sm')->attr('onclick="location.replace(location.href)"');

        $this->button('refresh', 'refresh', 1)->class('hide');

        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $label
     * @return void
     */
    public function submitBtn($label = '提&nbsp;&nbsp;交', $size = 1, $class = 'btn-success')
    {
        $this->button('submit', $label, $size)->class($class)->loading();
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $label
     * @return void
     */
    public function resetBtn($label = '重&nbsp;&nbsp;置', $size = 1, $class = 'btn-warning')
    {
        $this->button('submit', $label, $size)->class($class);
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $label
     * @param string $attr
     * @return void
     */
    public function backBtn($label = '返&nbsp;&nbsp;回', $size = 1, $class = 'btn-default btn-go-back', $attr = 'onclick="history.go(-1);')
    {
        $this->button('button', $label, $size)->class($class)->attr($attr);
    }

    public function beforRender()
    {
        $token = Builder::getInstance()->getCsrfToken();

        $this->hidden('__token__', $token);

        if (!$this->botttomButtonsCalled) {
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
                $row->getDisplayer()->fullSize(3);
            }

            $row->beforRender();
        }
    }

    protected function searchScript()
    {
        $form = $this->getId();

        $script = <<<EOT
        $('body').on('click', '#{$this->search} ul li a', function(){
            var page = $(this).attr('href').replace(/.*\?page=(\d+).*/,'$1');
            $('#form-__page__').val(page);
            $('#{$form}').trigger('submit');
            return false;
        });

        $('body').on('click', '#tool-refresh,#form-refresh', function(){
            $('#{$form}').trigger('submit');
        });

        $('body').on('click', '#form-submit', function(){
            $('#form-__page__').val(1);
        });
EOT;
        Builder::getInstance()->addScript($script);

        return $script;
    }

    public function render($partial = false)
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'form.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'rows' => $this->rows,
            'action' => $this->action,
            'method' => $this->method,
            'class' => $this->class,
            'attr' => $this->attr,
            'id' => $this->id,
            'ajax' => ($this->ajax || !empty($this->search)),
            'search' => $this->search,
            'partial' => $partial,
        ];

        if ($partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $row = new Row($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 12, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            $this->rows[] = $row;

            return $row->$name($arguments[0], $row->getLabel());
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
