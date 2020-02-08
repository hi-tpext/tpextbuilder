<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\displayer\Field;
use tpext\builder\form\Wapper;
use tpext\builder\table\Column;

class Table extends Wapper implements Renderable
{
    protected $view = '';

    protected $verticalAlign = 'middle';

    protected $class = 'table-striped table-hover';

    protected $attr = '';

    protected $headers = [];

    protected $cols = [];

    protected $data = [];

    protected $emptyText = "暂未数据~";

    public function beforRender()
    {
        foreach ($this->cols as $col) {

            $col->beforRender();
        }
    }

    public function render()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'table.html']);

        $viewshow = new ViewShow($template);

        $list = [];

        foreach ($this->data as $row => $cols) {

            foreach ($cols as $col => $value) {

                $displayer = isset($this->cols[$col]) ? $this->cols[$col]->getDisplayer() : new Field($col, ucfirst($col));

                $displayer->showLabel(false)
                    ->size(12, 12)
                    ->addAttr('style="vertical-align:' . $this->verticalAlign . ';"');

                $list[$row][$col] = [
                    'displayer' => $displayer,
                    'value' => $value,
                ];
            }
        }

        $vars = [
            'class' => $this->class,
            'attr' => $this->attr,
            'headers' => $this->headers,
            'cols' => $this->cols,
            'list' => $list,
            'data' => $this->data,
            'emptyText' => $this->emptyText,
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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function verticalAlign($val)
    {
        return $this->verticalAlign = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            //$row = new Row($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 12, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            //$this->rows[] = $row;

            //return $row->$name($arguments[0], $count > 1 ? $arguments[1] : '');

            $label = $count > 1 ? $arguments[1] : '';

            if (empty($label)) {
                $label = ucfirst($name);
            }

            $col = new Column($name, $label, $count > 2 ? $arguments[2] : 12, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');
            $this->cols[$arguments[0]] = $col;
            $this->headers[] = $label;
            return $col->$name($arguments[0], $label)->extKey(1);
        }

        throw new \UnexpectedValueException('未知调用');
    }
}
