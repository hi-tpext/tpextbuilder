<?php

namespace tpext\builder\form;

use think\response\View as ViewShow;
use tpext\builder\common\Plugin;
use tpext\builder\common\Renderable;

class FieldsContent extends Wapper implements Renderable
{
    protected $rows = [];

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->rows as $row) {

            $row->beforRender();
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Row $row
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
        return $this->rows;
    }

    public function render()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'form', 'fieldscontent.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'rows' => $this->rows,
        ];

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
