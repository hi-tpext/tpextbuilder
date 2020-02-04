<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Plugin;
use tpext\builder\form\Row;
use tpext\builder\form\Wapper;

class Form extends Wapper implements Renderable
{
    protected $view = '';

    protected $action = '';

    protected $method = 'post';

    protected $rows = [];

    protected $botttomButtonsCalled = false;

    public function render()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'form.html']);

        $config = [];

        $viewshow = new ViewShow($template);

        $vars = [
            'rows' => $this->rows,
            'action' => $this->action,
        ];

        return $viewshow->assign($vars)->config($config)->getContent();
    }

    public function addRow($row)
    {
        $this->rows[] = $row;
        return $this;
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
     * @param boolean $create
     * @return $this
     */
    public function bottomButtons($create = true)
    {
        if ($create) {
            $this->divider('', '', 12);
            $this->html('', '', 5);
            $this->button('submit', '提&nbsp;&nbsp;交', 1)->class('btn-success')->loading();
            $this->button('reset', '重&nbsp;&nbsp;置', 1)->class('btn-warning');
            $this->button('button', '返&nbsp;&nbsp;回', 1)->class('btn-default btn-go-back')->attr('onclick="history.go(-1);"');
        }

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
        $this->button('submit', $label, $size)->class($class)->attr($attr);
    }

    public function beforRender()
    {
        $token = Builder::getInstance()->getCsrfToken();

        $this->hidden('__token__', $token);

        if (!$this->botttomButtonsCalled) {
            $this->bottomButtons(true);
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

            return $row->$name($arguments[0], $count > 1 ? $arguments[1] : '');
        }

        throw new \UnexpectedValueException('未知调用');
    }
}
