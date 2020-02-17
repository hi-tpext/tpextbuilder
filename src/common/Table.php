<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\displayer\Field;
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

    protected $headTextAlign = 'center';

    protected $textAlign = 'center';

    protected $verticalAlign = 'middle';

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

    protected $emptyText = "暂未数据~";

    protected $toolbar = null;

    protected $useToolbar = true;

    protected $actionbar = null;

    protected $useActionbar = true;

    protected $actionRowText = '操作';

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
        Builder::getInstance()->addJs($this->js);
        Builder::getInstance()->addCss($this->css);

        if ($this->useToolbar) {
            $this->getToolbar()->beforRender();
        }

        if (empty($this->searchForm)) {
            $form = Builder::getInstance()->form();
            $form->addClass('hide');
            $this->searchForm($form);
            $form->beforRender();
        }

        return $this;
    }

    protected function initData()
    {
        $emptyCols = empty($this->cols);

        $this->list = [];

        $pk = strtolower($this->pk);

        $actionbar = $this->getActionbar();
        $actionbar->pk($this->pk);

        foreach ($this->data as $row => $cols) {

            foreach ($cols as $col => $value) {

                if (strtolower($col) == $pk) {

                    $this->ids[$row] = $value;
                }

                if (!isset($this->cols[$col])) {

                    if ($emptyCols) {
                        $this->cols[$col] = new Field($col, ucfirst($col));
                    } else {
                        continue;
                    }
                }

                $colunm = $this->cols[$col];

                $colunm->beforRender();

                $displayer = $colunm->getDisplayer();

                $displayer
                    ->value($value)
                    ->tableRowKey('-' . $row . time())
                    ->showLabel(false)
                    ->size(0, 12)
                    ->beforRender();

                $script = $displayer->getScript();

                if (!empty($script)) {
                    $this->script = array_merge($this->script, $script);
                }

                $this->cols[$col]->addStyle('vertical-align:' . $this->verticalAlign . ';' . 'text-align:' . $this->textAlign . ';');

                $this->list[$row][$col] = [
                    'displayer' => $displayer,
                    'value' => $displayer->render(),
                    'attr' => $displayer->getAttr(),
                    'wapper' => $this->cols[$col],
                ];
            }

            if ($this->useActionbar && isset($this->ids[$row])) {

                $this->actionbars[$row] = $actionbar->rowid($this->ids[$row])->beforRender()->render();
            }
        }
    }

    public function render()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'table.html']);

        $viewshow = new ViewShow($template);

        $this->initData();

        $this->paginator->setItems($this->data);

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
            'chekcboxtd' => 'style="width:40px;vertical-align:' . $this->verticalAlign . ';"',
            'id' => $this->id,
            'paginator' => $this->paginator,
            'partial' => $this->partial ? 1 : 0,
            'script' => implode('', array_unique($this->script)),
            'toolbar' => $this->useToolbar && !$this->partial ? $this->toolbar : null,
            'actionbars' => $this->actionbars,
            'actionRowText' => $this->actionRowText
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
