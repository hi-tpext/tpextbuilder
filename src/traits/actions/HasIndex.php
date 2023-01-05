<?php

namespace tpext\builder\traits\actions;

use think\db\Query;
use tpext\builder\logic\Filter;
use tpext\builder\common\Search;
use tpext\builder\common\Table;

/**
 * 列表
 */

trait HasIndex
{
    use HasExport;
    use HasSelectPage;
    use HasLeftTree;

    protected $indexText = '列表';
    protected $pagesize = 14;
    protected $sortOrder = 'id desc';
    protected $useSearch = true;

    /**
     * 表格是否为导出模式
     *
     * @var boolean
     */
    protected $isExporting = false;

    /**
     * Undocumented variable
     *
     * @var Search
     */
    protected $search;

    /**
     * Undocumented variable
     *
     * @var Table
     */
    protected $table;

    /**
     * 列表页关联加载 如 ['category']
     * @var array
     */
    protected $indexWith = [];

    /**
     * 列表页排除的字段，过长的文本字段影响查询速度，而列表页又不显示此字段时可以把它排除
     * @var array|string
     */
    protected $indexFieldsExcept = '';

    /**
     * 允许行内编辑的字段，留空则不限制
     *
     * @var array
     */
    protected $postAllowFields = [];

    /**
     * 不允许删除的id
     *
     * @var array
     */
    protected $delNotAllowed = [];

    public function index()
    {
        if (request()->isAjax()) {
            request()->withPost(request()->get()); //兼容以post方式获取参数
        }

        $builder = $this->builder($this->pageTitle, $this->indexText, 'index');

        $this->createTable($builder);

        $this->initTable();
        if (request()->isAjax()) {
            return $this->table->partial()->render();
        }

        if ($this->useSearch) {
            $this->search = $this->table->getSearch();
            $this->buildSearch();
        }

        return $builder->render();
    }

    protected function createTable($builder)
    {
        if ($this->treeModel && $this->treeKey) { //左侧树模型

            $tree = null;
            if ($this->treeType == 'ztree') {
                $tree = $builder->zTree('1 left-tree');
            } else {
                $tree = $builder->jsTree('1 left-tree');
            }

            $tree->fill(
                $this->treeModel->where($this->treeScope)->select(),
                $this->treeTextField,
                $this->treeIdField,
                $this->treeParentIdField,
                $this->treeRootText
            );

            $tree->trigger('.row-' . $this->treeKey);
            $tree->expandAll($this->treeExpandAll);

            $this->table = $builder->table('1 right-table');
        } else {
            $this->table = $builder->table();
        }
    }

    /**
     * Undocumented function
     *
     * @return array|Query
     */
    protected function filterWhere()
    {
        $this->search = new Search();

        $this->buildSearch();

        $logic = new Filter;

        $searchData = request()->get();

        $where = $logic->getQuery($this->search, $searchData);

        return $where;
    }

    /**
     * 生成并填充数据
     *
     * @return void
     */
    protected function initTable()
    {
        $table = $this->table;

        $this->table->pk($this->getPk());

        $data = [];

        $this->isExporting = false;

        if ($this->asTreeList()) { //如果此模型使用了`tpext\builder\traits\TreeModel`,显示为树形结构
            $table->sortable([]);
            $data = $this->dataModel->getLineData();
            $this->buildTable($data, false);
            $table->fill($data);
        } else {
            $where = $this->filterWhere();
            $sortOrder = $this->getSortorder();
            $page = input('get.__page__/d', 1);
            $page = $page < 1 ? 1 : $page;

            $pagesize = input('get.__pagesize__/d', 0);
            $this->pagesize = $pagesize ?: $this->pagesize;

            $total = -1;

            $data = $this->buildDataList($where, $sortOrder, $page, $total);

            if ($total == -1) {
                trace('你使用的是旧版本的buildDataList()方法，建议升级写法新的写法:buildDataList($where = [], $sortOrder = \'\', $page = 1, &$total = -1)');
                //兼容旧的程序，
                //旧的`buildDataList`方法不传任何参数，所以不会改变$total的值。
                //如果是旧的`buildDataList`，会做更多事情，比如`buildTable`,`fill`,`paginator`,`sortOrder`等，
                //在此判断避免重复，
                //往后的代码中，`buildDataList`只处理数据，不涉及其他。
            } else {
                $this->buildTable($data, false);
                $table->fill($data);
                $table->paginator($total, $this->pagesize);
                $table->sortOrder($sortOrder);
            }
        }
    }

    /**
     * 模型是否使用了`tpext\builder\traits\TreeModel`,显示为树形结构
     *
     * @return boolean
     */
    protected function asTreeList()
    {
        return $this->dataModel && method_exists($this->dataModel, 'asTreeList') && $this->dataModel->asTreeList();
    }

    /**
     * 排序
     *
     * @return string
     */
    protected function getSortOrder()
    {
        $sortOrder = input('get.__sort__', $this->sortOrder ? $this->sortOrder : $this->getPk() . ' desc');

        //可重写此方法，比如用户点击除了create_time以外的任何个字段排序，都再加一个`create_time`倒序。

        // if (!strstr($sortOrder, 'create_time')) {
        //     return $sortOrder .',create_time desc';
        // }

        return $sortOrder;
    }

    /**
     * 生成数据，如数据不是从`$this->dataModel`得来时，可重写此方法
     * 比如使用db()助手方法、多表join、或以一个自定义数组为数据源
     *
     * @param array $where
     * @param string $sortOrder
     * @param integer $page
     * @param integer $total
     * @return array|\think\Collection|\Generator
     */
    protected function buildDataList($where = [], $sortOrder = '', $page = 1, &$total = -1)
    {
        $data = [];
        $total = 0;
        if (!$this->dataModel) {
            return $data;
        }

        if ($this->isExporting) { //如果是导出
            $data = $this->dataModel->with($this->indexWith)
                ->where($where)
                ->order($sortOrder)
                ->field(empty($this->indexFieldsExcept))
                ->withoutField($this->indexFieldsExcept)
                ->cursor(); //select和cursor均可，最好是返回cursor
        } else {
            $data = $this->dataModel->with($this->indexWith)
                ->where($where)->order($sortOrder)
                ->limit(($page - 1) * $this->pagesize, $this->pagesize)
                ->field(empty($this->indexFieldsExcept))
                ->withoutField($this->indexFieldsExcept)
                ->select();
        }

        $total = $this->dataModel->where($where)->count();

        return $data;
    }
}
