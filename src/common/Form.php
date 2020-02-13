<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Plugin;
use tpext\builder\form\Row;
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

    public function render()
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
        ];

        return $viewshow->assign($vars)->getContent();
    }

    protected function searchScript()
    {
        $paginator = $this->search . '-paginator';

        $script = <<<EOT
        $('body').on('click', '#{$paginator} ul li a', function(){
            var page = $(this).attr('href').replace(/.*\?page=(\d+).*/,'$1');
            $('#form-__page__').val(page);
            $('#form-submit').trigger('click');
            return false;
        });
EOT;
        Builder::getInstance()->addScript($script);

        return $script;
    }

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
     * @param boolean $create
     * @return $this
     */
    public function bottomButtons($create = true)
    {
        if ($create) {
            $this->divider('', '', 12);
            $this->html('', '', 5);
            $this->button('submit', '提&nbsp;&nbsp;交', 1)->class('btn-success');
            $this->button('reset', '重&nbsp;&nbsp;置', 1)->class('btn-warning');
        }

        $this->botttomButtonsCalled = true;
        return $this;
    }

    public function searchButtons()
    {
        $this->botttomButtonsCalled = true;
        $this->html('', '', 1);
        $this->button('submit', '筛&nbsp;&nbsp;选', 1)->class('btn-success btn-sm');
        $this->button('button', '重&nbsp;&nbsp;置', 1)->class('btn-default btn-sm')->attr('onclick="location.replace(location.href)"');

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
            $this->bottomButtons(true);
        }

        if ($this->search) {
            $this->hidden('__page__', 1);
            $this->searchScript();
        }

        foreach ($this->rows as $row) {

            $row->beforRender();
        }
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
