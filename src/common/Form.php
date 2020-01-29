<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Plugin;
use tpext\builder\form\Row;
use tpext\builder\form\Wapper;

class Form extends Wapper implements Renderable
{
    protected $view = '';

    public $action = '';

    protected $rows = [];

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
    }

    public function action($val)
    {
        $this->action = $val;
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
