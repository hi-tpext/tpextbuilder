<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\form\Wapper;
use tpext\builder\table\Actionbar;
use tpext\builder\table\Column;
use tpext\builder\table\MultipleToolbar;
use tpext\builder\table\Paginator;

/**
 * Table class
 */
class Table extends Wapper implements Renderable
{
    protected $view = '';

    protected $id = 'the-table';

    protected $js = [
        '/assets/tpextbuilder/js/jquery-toolbar/jquery.toolbar.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/jquery-toolbar/jquery-toolbar.min.css',
    ];

    protected $headTextAlign = 'text-center';

    protected $textAlign = 'text-center';

    protected $verticalAlign = 'vertical-middle';

    protected $class = 'table-striped table-hover table-bordered';

    protected $attr = '';

    protected $headers = [];

    protected $cols = [];

    protected $data = [];

    protected $lit = [];

    protected $pk = 'id';

    protected $ids = [];

    protected $actionbars = [];

    protected $rowCheckbox = true;

    protected $emptyText = "<p class='text-center'><span>暂无数据~</span></p>";

    protected $toolbar = null;

    protected $useToolbar = true;

    protected $actionbar = null;

    protected $useActionbar = true;

    protected $actionRowText = '操作';

    protected $isInitData = false;

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $searchForm = null;

    protected $script = [];

    protected $partial = false;

    /**
     * Undocumented function
     *
     * @param string $name
     * @param \tpext\builder\table\Column $col
     * @return $this
     */
    public function addCol($name, $col)
    {
        $this->cols[$name] = $col;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCols()
    {
        return $this->cols();
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
     * vertical-middle | vertical-mtop | vertical-bottom
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
     * text-left | text-center | text-right
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
     * text-left | text-center | text-right
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

        if (count($data) > 0 && empty($this->cols)) {
            $cols = array_keys($data[0]);

            foreach ($cols as $col) {
                $this->show($col, ucfirst($col));
            }
        }

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

    /**
     * Undocumented function
     *
     * @param int $dataTotal
     * @param integer $pageSize
     * @param string $paginatorClass
     * @return $this
     */
    public function paginator($dataTotal, $pageSize = 10, $paginatorClass = '')
    {
        $paginator = Paginator::make($this->data, $pageSize, input('__page__', 1), $dataTotal);

        if ($paginatorClass) {
            $paginator->class($paginatorClass);
        }

        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * Undocumented function
     *
     * @param From $form
     * @return $this
     */
    public function searchForm($form)
    {
        $form->search($this);
        $this->searchForm = $form;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Form
     */
    public function getSearch()
    {
        return $this->form;
    }

    /**
     * 获取一个toolbar
     *
     * @return MultipleToolbar
     */
    public function getToolbar()
    {
        if (empty($this->toolbar)) {
            $this->toolbar = new MultipleToolbar();
        }

        return $this->toolbar;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this;
     */
    public function useToolbar($val)
    {
        $this->useToolbar = $val;
        return $this;
    }

    /**
     * 获取一个actionbar
     *
     * @return Actionbar
     */
    public function getActionbar()
    {
        if (empty($this->actionbar)) {
            $this->actionbar = new Actionbar();
        }

        return $this->actionbar;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this;
     */
    public function useActionbar($val)
    {
        $this->useActionbar = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this;
     */
    protected function actionRowText($val)
    {
        $this->actionRowText = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $this->initData();

        Builder::getInstance()->addJs($this->js);
        Builder::getInstance()->addCss($this->css);

        if ($this->useToolbar) {
            $this->getToolbar()->hasSearch(!empty($this->searchForm))->beforRender();
        }

        if ($this->useActionbar) {
            $this->getActionbar()->beforRender();
        }

        if (empty($this->searchForm)) {
            $form = Builder::getInstance()->form();
            $this->searchForm($form);
            $form->addClass('form-empty');
            $form->beforRender();
        }

        $this->searchForm->addClass('hidden');

        return $this;
    }

    protected function initData()
    {
        $this->list = [];

        $pk = $this->pk;

        $actionbar = $this->getActionbar();

        $actionbar->pk($this->pk);

        $cols = array_keys($this->cols);

        foreach ($this->data as $key => $data) {

            if (isset($data[$pk])) {

                $this->ids[$key] = $data[$pk];
            } else {
                $this->ids[$key] = $key;
            }

            foreach ($cols as $col) {

                $colunm = $this->cols[$col];

                $colunm->beforRender();

                $displayer = $colunm->getDisplayer();

                $displayer
                    ->fill($data)
                    ->tableRowKey('-' . $key)
                    ->showLabel(false)
                    ->size(0, 12)
                    ->beforRender();

                $script = $displayer->getScript();

                if (!empty($script)) {
                    $this->script = array_merge($this->script, $script);
                }

                $this->list[$key][$col] = [
                    'displayer' => $displayer,
                    'value' => $displayer->render(),
                    'attr' => $displayer->getAttr(),
                    'wapper' => $this->cols[$col],
                ];
            }

            if ($this->useActionbar && isset($this->ids[$key])) {

                $actionbar->tableRowKey('-' . $key)->rowdata($data)->beforRender();

                $this->actionbars[$key] = $actionbar->render();
            }
        }

        $this->isInitData = true;
    }

    public function render()
    {
        if (!$this->isInitData) {
            $this->initData();
        }

        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'table.html']);

        $viewshow = new ViewShow($template);

        $this->paginator->setItems($this->data);

        $vars = [
            'class' => $this->class,
            'attr' => $this->attr,
            'headers' => $this->headers,
            'cols' => $this->cols,
            'list' => $this->list,
            'data' => $this->data,
            'emptyText' => $this->emptyText,
            'headTextAlign' => $this->headTextAlign,
            'ids' => $this->ids,
            'rowCheckbox' => $this->rowCheckbox && $this->useToolbar,
            'name' => time() . mt_rand(1000, 9999),
            'verticalAlign' => $this->verticalAlign,
            'textAlign' => $this->textAlign,
            'id' => $this->id,
            'paginator' => $this->paginator,
            'partial' => $this->partial ? 1 : 0,
            'toolbar' => $this->useToolbar && !$this->partial ? $this->toolbar : null,
            'actionbars' => $this->actionbars,
            'actionRowText' => $this->actionRowText,
        ];

        if ($this->partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
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

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
