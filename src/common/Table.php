<?php

namespace tpext\builder\common;

use think\Collection;
use tpext\think\View;
use tpext\common\ExtLoader;
use tpext\builder\table\TEmpty;
use tpext\builder\table\TColumn;
use tpext\builder\traits\HasDom;
use tpext\builder\table\TWrapper;
use tpext\builder\displayer\Field;
use tpext\builder\table\Actionbar;
use tpext\builder\table\Paginator;
use tpext\builder\inface\Renderable;
use tpext\builder\table\FieldsContent;
use tpext\builder\toolbar\DropdownBtns;
use tpext\builder\table\MultipleToolbar;
use tpext\builder\displayer\MultipleFile;

/**
 * Table class
 */
class Table extends TWrapper implements Renderable
{
    use HasDom;

    protected $js = [];

    protected $css = [];

    protected $id = 'the-table';

    protected $headTextAlign = 'text-center';

    protected $textAlign = 'text-center';

    protected $verticalAlign = 'vertical-middle';

    protected $headers = [];

    protected $list = [];

    /**
     * Undocumented variable
     *
     * @var Field[] 
     */
    protected $cols = [];

    protected $displayers = [];

    protected $data = [];

    protected $pk = 'id';

    protected $ids = [];

    protected $actionbars = [];

    protected $checked = [];

    protected $useCheckbox = true;

    protected $pageSize = 0;

    protected $emptyText = '';

    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields__ = null;

    /**
     * Undocumented variable
     *
     * @var MultipleToolbar
     */
    protected $toolbar = null;

    protected $useToolbar = true;

    protected $lockForExporting = false;

    /**
     * Undocumented variable
     *
     * @var Actionbar
     */
    protected $actionbar = null;

    protected $useActionbar = true;

    protected $actionRowText = '';

    protected $isInitData = false;

    protected $sortable = ['id'];

    protected $sortOrder = '';

    protected $partial = false;

    /**
     * Undocumented variable
     *
     * @var Row
     */
    protected $addTop;

    /**
     * Undocumented variable
     *
     * @var Row
     */
    protected $addBottom;

    /**
     * Undocumented variable
     *
     * @var Paginator
     */
    protected $paginator;

    /**
     * Undocumented variable
     *
     * @var Search
     */
    protected $searchForm = null;

    /**
     * Undocumented variable
     *
     * @var DropdownBtns
     */
    protected $pagesizeDropdown = null;

    protected $usePagesizeDropdown = true;

    /**
     * Undocumented variable
     *
     * @var TEmpty
     */
    protected $tEmpty = null;

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function created()
    {
        $this->class = 'table-striped table-hover table-bordered table-condensed table-responsive';
        $this->id = input('get.__table__', 'the-table');

        $this->emptyText = Module::config('table_empty_text');
        $this->actionRowText = __blang('bilder_action_operation');

        $this->tEmpty = new TEmpty;

        return $this;
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
     *
     * @return array
     */
    public function getDisplayers()
    {
        return $this->displayers;
    }

    /**
     * Undocumented function
     * vertical-middle | vertical-mtop | vertical-bottom
     * @param string $val
     * @return $this
     */
    public function verticalAlign($val)
    {
        $this->verticalAlign = $val;
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
        $this->textAlign = $val;
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
        $this->headTextAlign = $val;
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
     * @param array|string $val
     * @return $this
     */
    public function checked($val)
    {
        $this->checked = is_array($val) ? $val : explode(',', $val);
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
     * @param array|Collection|\IteratorAggregate $data
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
     * @param bool $val
     * @return $this
     */
    public function lockForExporting($val = true)
    {
        $this->lockForExporting = $val;

        if ($this->toolbar) {
            $this->toolbar->lockForExporting($val);
        }
        if ($this->actionbar) {
            $this->actionbar->lockForExporting($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $val
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
     * @param array|Collection|\IteratorAggregate $data
     * @return $this
     */
    public function fill($data = [])
    {
        if (empty($data)) {
            return $this;
        }
        $this->data = $data;
        if (count($data) > 0 && empty($this->cols)) {
            $cols = [];
            $first = $data[0];
            if (is_object($first) && method_exists($first, 'toArray')) {
                $first = $first->toArray();
            }
            $cols = array_keys($first);
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
     * @return array|Collection|\IteratorAggregate
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getChooseColumns()
    {
        return $this->getToolbar()->getChooseColumns();
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
        if (!$pageSize) {
            $pageSize = 10;
        }

        $paginator = new Paginator($this->data, $pageSize, input('get.__page__/d', 1), $dataTotal);

        if ($dataTotal < 10) {
            $this->usePagesizeDropdown = false;
        }

        if ($paginatorClass) {
            $paginator->paginatorClass($paginatorClass);
        }

        $this->pageSize = $pageSize;

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
            $this->toolbar = Widget::makeWidget('MultipleToolbar');
            $this->toolbar->extKey('-' . $this->id);
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
    public function useExport($val = true)
    {
        $this->getToolbar()->useExport($val);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean|array|string $val 默认显示的字段，false则禁用
     * @return $this
     */
    public function useChooseColumns($val = true)
    {
        $this->getToolbar()->useChooseColumns($val);

        return $this;
    }

    /**
     * 弃用，使用｀useExport｀代替
     * @deprecated 1.8.93
     * @param boolean $val
     * @return $this
     */
    public function hasExport($val = true)
    {
        $this->getToolbar()->useExport($val);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return bool
     */
    public function isLockForExporting()
    {
        return $this->lockForExporting;
    }

    /**
     * 获取一个actionbar
     *
     * @return Actionbar
     */
    public function getActionbar()
    {
        if (empty($this->actionbar)) {
            $this->actionbar = Widget::makeWidget('Actionbar');
        }

        return $this->actionbar;
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
     * @param array|boolean $items
     * @return DropdownBtns|null
     */
    public function pagesizeDropdown($items)
    {
        if ($items === false) {
            $this->usePagesizeDropdown = false;
            return null;
        }

        if (empty($this->pagesizeDropdown)) {
            $this->pagesizeDropdown = new DropdownBtns('pagesize', __blang('bilder_paginator_num_per_page', ['num' => $this->pageSize]));
        }

        $this->pagesizeDropdown->items($items)->class('btn-xs btn-default')->addGroupClass('dropup pull-right m-r-10');

        return $this->pagesizeDropdown;
    }

    /**
     * 获取一个搜索
     *
     * @return Search
     */
    public function getSearch()
    {
        if (empty($this->searchForm)) {
            $this->searchForm = Widget::makeWidget('Search');
            $this->searchForm->search($this);
        }
        return $this->searchForm;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        ExtLoader::trigger('tpext_table_befor_render', $this);

        $this->initData();

        if (!request()->isAjax()) {

            Builder::getInstance()->addJs($this->js);
            Builder::getInstance()->addCss($this->css);

            if ($this->useToolbar) {
                $toolbar = $this->getToolbar();

                $toolbar->useSearch(!empty($this->searchForm));
                $toolbar->setTableCols($this->cols);
                $toolbar->beforRender();
            }

            if (empty($this->searchForm)) {
                $this->getSearch();
                $this->searchForm->addClass('form-empty');
            }

            $this->searchForm->beforRender();

            $this->tableScript();
        }

        if ($this->addTop) {
            $this->addTop->beforRender();
        }

        if ($this->addBottom) {
            $this->addBottom->beforRender();
        }

        return $this;
    }

    protected function tableScript()
    {
        $table = $this->getTableId();

        $script = <<<EOT

        $('body').on('dblclick', '#{$table} tbody tr', function(){
            if($(this).find('td a.dbl-click').not('.hidden,.disabled').length)
            {
                $(this).find('td a.dbl-click').trigger('click');
            }
            else if($(this).find('td a.action-edit').not('.hidden,.disabled').length)
            {
                $(this).find('td a.action-edit').trigger('click');
            }
            else if($(this).find('td a.action-view').not('.hidden,.disabled').length)
            {
                $(this).find('td a.action-view').trigger('click');
            }
            return false;
        });

        $('body').on('click', '#{$table} tbody tr td', function(){
            if($(this).hasClass('table-checkbox') || $(this).hasClass('row-__action__'))
            {
                return;
            }
            if($(this).find('input,textarea,select').length)
            {
                return;
            }
            if($(this).siblings('td.table-checkbox').length)
            {
                var box = $(this).siblings('td.table-checkbox').find('input:checkbox');
                box.prop('checked', !box.is(':checked'));
                box.trigger('change');
            }
        });

        var checkall = $('#{$table} input.checkall');
        var checkboxes = $('.' + checkall.data('check'));
        var count = checkboxes.size();

        checkall.on('change', function () {
            var ischecked = checkall.is(':checked');
            checkboxes.each(function (ii, ee) {
                if ($(ee).attr('disabled') !== undefined || $(ee).attr('readonly') !== undefined) {
                    return;
                }
                $(ee).prop('checked', ischecked).trigger('change');
            });
        });

        checkboxes.on('change', function () {
            var ss = 0;
            checkboxes.each(function (ii, ee) {
                if ($(ee).is(':checked')) {
                    ss += 1;
                    $(ee).parentsUntil('tbody', 'tr').addClass('checked');
                }
                else
                {
                    $(ee).parentsUntil('tbody', 'tr').removeClass('checked');
                }
            });
            checkall.prop('checked', ss == count);
        });

EOT;
        Builder::getInstance()->addScript($script);

        return $script;
    }

    protected function initData()
    {
        ExtLoader::trigger('tpext_table_init_data', $this);

        $this->list = [];

        $pk = $this->pk;

        $actionbar = $this->getActionbar();

        $actionbar->pk($this->pk);

        $cols = array_keys($this->cols);

        $rows = 0;

        $chooseColumns = ['*'];
        if (request()->isAjax()) {
            $__columns__ = input('get.__columns__', '*');
            if ($__columns__) {
                $chooseColumns  = explode(',', $__columns__);
            }
            $colAttr = [];
            foreach ($cols as $col) {
                $colunm = $this->cols[$col];
                if (!($colunm instanceof TColumn)) {
                    continue;
                }
                $colAttr = $colunm->getColAttr();

                if ($colAttr['sortable']) {
                    $this->sortable[] = $colunm->getName();
                }
            }
        } else {
            $colAttr = [];

            $columns = [];

            foreach ($cols as $col) {
                $colunm = $this->cols[$col];
                if (!($colunm instanceof TColumn)) {
                    continue;
                }
                $colAttr = $colunm->getColAttr();

                if ($colAttr['sortable']) {
                    $this->sortable[] = $colunm->getName();
                }

                if (!$colAttr['hidden']) {
                    $columns[] = $colunm->getName();
                }
            }

            $useChooseColumns = $this->getToolbar()->getChooseColumns();

            if ($useChooseColumns) {
                if ($useChooseColumns[0] == '*') { //*号代表全部，转换为具体的字段列表
                    $this->getToolbar()->useChooseColumns($columns);
                    $chooseColumns = $columns;
                }
            } else {
                $chooseColumns = false;
            }
        }

        foreach ($this->data as $key => $data) {
            $rows += 1;

            if (isset($data[$pk])) {

                $this->ids[$key] = $data[$pk];
            } else {
                $this->ids[$key] = $key;
            }

            foreach ($cols as $col) {
                $colunm = $this->cols[$col];

                if (!($colunm instanceof TColumn)) {
                    continue;
                }

                $displayer = $colunm->getDisplayer();

                if ($chooseColumns && $chooseColumns[0] != '*'  && !in_array($col, $chooseColumns)) {
                    if (isset($this->headers[$col])) {
                        $displayer->beforRender();
                        unset($this->headers[$col]);
                    }
                    continue;
                }

                $displayer->clearScript();

                $displayer
                    ->value('')
                    ->fill($data)
                    ->extKey('-' . $this->id . '-' . $key)
                    ->extNameKey('-' . $key)
                    ->showLabel(false)
                    ->size('0', '12 col-lg-12 col-sm-12 col-xs-12')
                    ->beforRender();

                $this->list[$key][$col] = [
                    'label' => $displayer->getLabel(),
                    'displayer' => $displayer,
                    'value' => $displayer->render(),
                    'attr' => $displayer->getAttrWithStyle(),
                    'wrapper' => $colunm,
                ];
            }

            if ($this->useActionbar) {

                $actionbar->extKey('-' . $this->id . '-' . $key)->rowData($data)->beforRender();

                $this->actionbars[$key] = $actionbar->render();
            }
        }

        if ($rows == 0) { // 数据为空，但某些js脚本是需要的，空跑一遍，把js脚本加载
            foreach ($cols as $col) {
                if ($chooseColumns && $chooseColumns[0] != '*'  && !in_array($col, $chooseColumns)) {
                    unset($this->headers[$col]);
                }
                $colunm = $this->cols[$col];
                if (!($colunm instanceof TColumn)) {
                    continue;
                }
                $displayer = $colunm->getDisplayer();
                $displayer->beforRender();
            }

            if ($this->useActionbar) {

                $actionbar->extKey('-' . $this->id . '-' . 0)->beforRender();

                $this->actionbars[0] = $actionbar->render();
            }
        }

        $this->isInitData = true;
    }

    /**
     * Undocumented function
     *
     * @return Row
     */
    public function addTop()
    {
        if (empty($this->addTop)) {
            $this->addTop = Row::make();
            $this->addTop->class('table-top');
        }

        return $this->addTop;
    }

    /**
     * Undocumented function
     *
     * @return Row
     */
    public function addBottom()
    {
        if (empty($this->addBottom)) {
            $this->addBottom = Row::make();
            $this->addBottom->class('table-bottom');
        }

        return $this->addBottom;
    }

    /**
     * Undocumented function
     * @param string $name
     * 
     * @return FieldsContent
     */
    public function createFields()
    {
        $this->__fields__ = new FieldsContent();
        $this->__fields__->setTable($this);
        return $this->__fields__;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function fieldsEnd()
    {
        $this->__fields__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [];
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getViewemplate()
    {
        $template = Module::getInstance()->getViewsPath() . 'table.html';

        return $template;
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        if ($this->lockForExporting) {
            return 'lockForExporting';
        }

        if (!$this->isInitData) {
            $this->initData();
        }

        $viewshow = new View($this->getViewemplate());

        $count = count($this->data);
        if (!$this->paginator) {
            $this->pageSize = $count ? $count : 10;
            $this->paginator = new Paginator($this->data, $this->pageSize, 1, $count);
            $this->usePagesizeDropdown = false;
        }

        if ($this->paginator->total() <= 6) {
            $this->usePagesizeDropdown = false;
        }

        $sort = input('get.__sort__', $this->sortOrder);
        $sortKey = '';
        $sortOrder = '';

        if ($sort) {
            $arr = explode(' ', $sort);
            if (count($arr) == 2) {
                $sortKey = $arr[0];
                $sortOrder = $arr[1];
                if (!empty($this->sortable) && !in_array($sortKey, $this->sortable)) {
                    $this->sortable[] = $sortKey;
                }
            }
        }

        if ($this->usePagesizeDropdown && $this->pageSize && empty($this->pagesizeDropdown)) {
            $items = [
                0 => __blang('bilder_pagesize_default'), 6 => '6', 10 => '10', 14 => '14', 20 => '20', 30 => '30', 40 => '40', 50 => '50', 60 => '60', 90 => '90', 120 => '120', 200 => '200', 350 => '350',
            ];

            ksort($items);

            $this->pagesizeDropdown($items);
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
            'checked' => $this->checked,
            'pagesizeDropdown' => $this->usePagesizeDropdown ? $this->pagesizeDropdown : null,
            'addTop' => $this->addTop,
            'addBottom' => $this->addBottom,
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

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
        if ($this->lockForExporting) {
            return  $this->tEmpty;
        }

        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $col = TColumn::make($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 0);

            $col->setTable($this);

            $displayer = null;

            if ($this->__fields__) {
                $this->__fields__->addCol($col);
            } else {
                $this->cols[$arguments[0]] = $col;
                $this->headers[$arguments[0]] = $col->getLabel();
                $displayer = $col->$name($arguments[0], $col->getLabel());
            }

            $displayer = $col->$name($arguments[0], $col->getLabel());

            if ($displayer instanceof MultipleFile) { //表格中默认禁止直接上传图片
                $displayer->canUpload(false);
                $displayer->jsOptions(['istable' => 1]);
            }

            $this->displayers[$name . $arguments[0]] = $displayer;

            return $displayer;
        }

        throw new \InvalidArgumentException(__blang('bilder_invalid_argument_exception') . ' : ' . $name);
    }

    /**
     * 创建自身
     *
     * @param mixed $arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return Widget::makeWidget('Table', $arguments);
    }

    public function destroy()
    {
        $this->__fields__ = null;
        $this->toolbar = null;
        $this->actionbar = null;
        $this->pagesizeDropdown = null;
        foreach ($this->cols as $col) {
            $col->destroy();
        }
        $this->cols = null;
        $this->data = null;
        $this->tEmpty = null;
        if ($this->searchForm) {
            $this->searchForm->destroy();
            $this->searchForm = null;
        }
        if ($this->addTop) {
            $this->addTop->destroy();
            $this->addTop = null;
        }
        if ($this->addBottom) {
            $this->addBottom->destroy();
            $this->addBottom = null;
        }
    }
}
