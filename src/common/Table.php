<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\form\Wapper;
use tpext\builder\table\Column;

class Table extends Wapper implements Renderable
{
    protected $view = '';

    protected $js = [
        '/assets/tpextbuilder/js/jquery-toolbar/jquery.toolbar.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/jquery-toolbar/jquery-toolbar.min.css',
    ];

    protected $headTextAlign = 'left';

    protected $textAlign = 'left';

    protected $verticalAlign = 'middle';

    protected $class = 'table-striped table-hover table-bordered';

    protected $attr = '';

    protected $headers = [];

    protected $cols = [];

    protected $data = [];

    protected $lit = [];

    protected $pk = 'id';

    protected $ids = [];

    protected $rowCheckbox = true;

    protected $emptyText = "暂未数据~";

    public function beforRender()
    {
        Builder::getInstance()->addJs($this->js);
        Builder::getInstance()->addCss($this->css);

        $this->list = [];

        $pk = strtolower($this->pk);

        foreach ($this->data as $row => $cols) {

            foreach ($cols as $col => $value) {

                if (strtolower($col) == $pk) {

                    $this->ids[$row] = $value;
                }

                if (!isset($this->cols[$col])) {

                    continue;
                }

                $displayer = $this->cols[$col]->getDisplayer();

                $displayer
                    ->showLabel(false)
                    ->size([0, 12])
                    ->value($value)
                    ->tableRowKey('-' . $row);

                $displayer->beforRender();

                $this->cols[$col]->addStyle('vertical-align:' . $this->verticalAlign . ';' . 'text-align:' . $this->textAlign . ';');

                $this->list[$row][$col] = [
                    'displayer' => $displayer,
                    'value' => $displayer->render(),
                    'attr' => $displayer->getAttr(),
                    'wapper' => $this->cols[$col],
                ];
            }
        }
    }

    public function render()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'table.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'class' => $this->class,
            'attr' => $this->attr,
            'headers' => $this->headers,
            'cols' => $this->cols,
            'list' => $this->list,
            'data' => $this->data,
            'emptyText' => $this->emptyText,
            'headStyle' => 'style="text-align:' . $this->headTextAlign . ';"',
            'ids' => $this->ids,
            'rowCheckbox' => $this->rowCheckbox && !empty($this->ids),
            'name' => time() . mt_rand(1000, 9999),
        ];

        return $viewshow->assign($vars)->getContent();
    }

    /**
     * Undocumented function
     * 主键, 默认 为 'id'
     * @param string $val
     * @return $this
     */
    public function pk($val)
    {
        $this->pk = $val;
        return $this;
    }

    /**
     * Undocumented function
     * @param boolean $val
     * @return $this
     */
    public function rowCheckbox($val)
    {
        $this->rowCheckbox = $val;
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
     * @param string $val
     * @return $this
     */
    public function textAlign($val)
    {
        return $this->textAlign = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function headTextAlign($val)
    {
        return $this->headTextAlign = $val;
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

            $col = new Column($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 0, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            $this->cols[$arguments[0]] = $col;

            $this->headers[] = $col->getLabel();

            return $col->$name($arguments[0], $col->getLabel());
        }

        throw new \UnexpectedValueException('未知调用');
    }
}
