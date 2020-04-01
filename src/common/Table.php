<?php

namespace tpext\builder\common;

use think\Collection;
use think\response\View as ViewShow;
use tpext\builder\table\Actionbar;
use tpext\builder\table\MultipleToolbar;
use tpext\builder\table\Paginator;
use tpext\builder\table\TColumn;
use tpext\builder\table\TWapper;
use tpext\builder\traits\HasDom;

/**
 * Table class
 */
class Table extends TWapper implements Renderable
{
    use HasDom;

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

    protected $headers = [];

    protected $list = [];

    protected $cols = [];

    protected $data = [];

    protected $pk = 'id';

    protected $ids = [];

    protected $actionbars = [];

    protected $useCheckbox = true;

    protected $emptyText = "<p class='text-center'><span>暂无相关数据~</span></p>";

    /**
     * Undocumented variable
     *
     * @var MultipleToolbar
     */
    protected $toolbar = null;

    protected $useToolbar = true;

    /**
     * Undocumented variable
     *
     * @var Actionbar
     */
    protected $actionbar = null;

    protected $useActionbar = true;

    protected $actionRowText = '操作';

    protected $isInitData = false;

    protected $sortable = ['id'];

    protected $sortOrder = '';

    protected $rand = 0;

    protected $searchRand = 0;

    /**
     * Undocumented variable
     *
     * @var Paginator
     */
    protected $paginator;

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $searchForm = null;

    protected $partial = false;

    public function __construct()
    {
        $this->class = 'table-striped table-hover table-bordered';
        $this->rand = input('__table__', mt_rand(1000, 9999));
        $this->id .= $this->rand;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getRand()
    {
        return $this->rand;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param \tpext\builder\table\TColumn $col
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
        return $this->cols;
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
     * @return $this
     */
    public function tableId($val)
    {
        $this->id = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getTableId()
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
        $this->useCheckbox = $val;
        return $this;
    }

    /**
     * Undocumented function
     * @param boolean $val
     * @return $this
     */
    public function useCheckbox($val)
    {
        $this->useCheckbox = $val;
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
     * @param string|array $val
     * @return $this
     */
    public function sortable($val)
    {
        if (!is_array($val)) {
            $val = explode(',', $val);
        }

        $this->sortable = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function emptyText($val)
    {
        $this->emptyText = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection $data
     * @return $this
     */
    public function data($data = [])
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array
     * @return $this
     */
    public function setHeaders($val)
    {
        $this->headers = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->data = $data;
        if (count($data) > 0 && empty($this->cols)) {
            $cols = [];

            if ($data && $data instanceof Collection) {
                $cols = array_keys($data->toArray()[0]);
            } else {
                $cols = array_keys($data[0]);
            }

            foreach ($cols as $col) {
                $this->show($col, ucfirst($col));
            }
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function sortOrder($val)
    {
        $this->sortOrder = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array|Collection
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
            $paginator->paginatorClass($paginatorClass);
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
     * 获取一个toolbar
     *
     * @return MultipleToolbar
     */
    public function getToolbar()
    {
        if (empty($this->toolbar)) {
            $this->toolbar = new MultipleToolbar();
            $this->toolbar->extKey($this->rand);
        }

        return $this->toolbar;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
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
     * 获取一个搜索
     *
     * @return Search
     */
    public function getSearch()
    {
        if (empty($this->searchForm)) {
            $this->searchForm = new Search();
            $this->searchForm->search($this);
            $this->searchRand = $this->searchForm->getRand();
        }
        return $this->searchForm;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useActionbar($val)
    {
        $this->useActionbar = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
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
            $this->getSearch();
            $this->searchForm->addClass('form-empty');
        }

        $this->searchForm->beforRender();

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

                if (!$colunm instanceof TColumn) {
                    continue;
                }

                $displayer = $colunm->getDisplayer();

                $displayer->clearScript();

                $displayer
                    ->fill($data)
                    ->extKey($this->rand . '-' . $key)
                    ->extNameKey('-' . $key)
                    ->showLabel(false)
                    ->size(0, 0)
                    ->beforRender();

                $this->list[$key][$col] = [
                    'displayer' => $displayer,
                    'value' => $displayer->render(),
                    'attr' => $displayer->getAttr(),
                    'wapper' => $this->cols[$col],
                ];
            }

            if ($this->useActionbar && isset($this->ids[$key])) {

                $actionbar->extKey($this->rand . '-' . $key)->rowdata($data)->beforRender();

                $this->actionbars[$key] = $actionbar->render();
            }
        }

        $this->isInitData = true;
    }

    /**
     * Undocumented function
     *
     * @return string|ViewShow
     */
    public function render()
    {
        if (!$this->isInitData) {
            $this->initData();
        }

        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'table.html']);

        $viewshow = new ViewShow($template);

        if (!$this->paginator) {
            $this->paginator = Paginator::make($this->data, count($this->data), 1, count($this->data));
        }

        $sort = input('__sort__', $this->sortOrder);
        $sortKey = '';
        $sortOrder = '';

        if ($sort) {
            $arr = explode(' ', $sort);
            if (count($arr) == 2) {
                $sortKey = $arr[0];
                $sortOrder = $arr[1];
                if (!in_array($sortKey, $this->sortable)) {
                    $this->sortable[] = $sortKey;
                }
            }
        }

        $vars = [
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'headers' => $this->headers,
            'cols' => $this->cols,
            'list' => $this->list,
            'data' => $this->data,
            'emptyText' => $this->emptyText,
            'headTextAlign' => $this->headTextAlign,
            'ids' => $this->ids,
            'sortable' => $this->sortable,
            'sortKey' => $sortKey,
            'sortOrder' => $sortOrder,
            'sort' => $sort,
            'useCheckbox' => $this->useCheckbox && $this->useToolbar,
            'name' => time() . mt_rand(1000, 9999),
            'tdClass' => $this->verticalAlign . ' ' . $this->textAlign,
            'verticalAlign' => $this->verticalAlign,
            'textAlign' => $this->textAlign,
            'id' => $this->id,
            'paginator' => $this->paginator,
            'partial' => $this->partial ? 1 : 0,
            'searchForm' => !$this->partial ? $this->searchForm : null,
            'toolbar' => $this->useToolbar && !$this->partial ? $this->toolbar : null,
            'actionbars' => $this->actionbars,
            'actionRowText' => $this->actionRowText,
            'rand' => $this->rand,
            'searchRand' => $this->searchRand,
        ];

        if ($this->partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $col = new TColumn($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 0, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            $col->setTable($this);

            $this->cols[$arguments[0]] = $col;

            $this->headers[$arguments[0]] = $col->getLabel();

            return $col->$name($arguments[0], $col->getLabel());
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
