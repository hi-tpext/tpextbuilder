<?php

namespace tpext\builder\traits\actions;

use tpext\builder\common\Builder;
use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\common\Table;
use tpext\builder\logic\Filter;

/**
 * 基础
 */

trait HasBase
{
    /**
     * 数据模型
     *
     * @var \think\Model
     */
    protected $dataModel;
    /**
     * 页面标题
     *
     * @var string
     */
    protected $pageTitle = 'Page';
    protected $addText = '添加';
    protected $editText = '编辑';
    protected $viewText = '查看';
    protected $indexText = '列表';
    protected $pagesize = 14;
    protected $sortOrder = 'id desc';
    protected $enableField = 'enable';
    protected $pk = 'id';
    protected $isExporting = false;

    /**
     * 列表页关联加载 如 ['category', 'logs']
     *
     * @var string
     */
    protected $indexWith = [];

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $form;
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
     * 不允许删除的
     *
     * @var array
     */
    protected $delNotAllowed = [];
    /**
     * 允许行内编辑的字段，留空则不限制
     *
     * @var array
     */
    protected $postAllowFields = [];

    /** 初始化页面，覆盖默认
     *public function initialize()
     *{
     *   $this->dataModel = new MyModel;
     *   $this->pageTitle = 'A Page';
     *   $this->addText = '添加';
     *   $this->editText = '编辑';
     *   $this->indexText = '列表';
     *   $this->pagesize = 14;
     *   $this->sortOrder = 'id desc';
     *
     *   $this->delNotAllowed = [1, 3, 4];
     *
     *   $this->postAllowFields = ['name', 'phone'];
     *}
     */

    /*******辅助方法******/

    /**
     * Undocumented function
     *
     * @param boolean|int $isEdit 0:add,1:edit,2:view
     * @param array $data
     * @return void
     */
    protected function buildForm($isEdit, &$data = [])
    {
        $form = $this->form;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return void
     */
    protected function buildTable(&$data = [])
    {
        $table = $this->table;
    }

    /**
     * 构建搜索 范例
     *
     * @return void
     */
    protected function buildSearch()
    {
        $search = $this->search;
    }

    /**
     * 保存数据 范例
     *
     * @param integer $id
     * @return mixed
     */
    protected function save($id = 0)
    {
        $data = request()->post();

        return $this->doSave($data, $id);
    }

    protected function doSave($data, $id = 0)
    {
        $res = 0;

        if ($id) {
            $exists = $this->dataModel->where([$this->getPk() => $id])->find();
            if ($exists) {
                $res = $exists->force()->save($data);
            }

        } else {
            $res = $this->dataModel->exists(false)->save($data);
        }

        if (!$res) {
            $this->error('保存失败');
        }

        return $this->builder()->layer()->closeRefresh(1, '保存成功');
    }

    protected function filterWhere()
    {
        $this->search = new Search();

        $this->buildSearch();

        $logic = new Filter;

        $searchData = request()->post();

        $where = $logic->getQuery($this->search, $searchData);

        return $where;
    }

    /**
     * Undocumented function
     *
     * @return array|\think\Collection
     */
    protected function buildDataList()
    {
        $page = input('post.__page__/d', 1);
        $page = $page < 1 ? 1 : $page;
        $sortOrder = input('__sort__', $this->sortOrder ? $this->sortOrder : $this->getPk() . ' desc');

        $where = $this->filterWhere();

        $table = $this->table;

        $pagesize = input('post.__pagesize__/d', 0);

        $this->pagesize = $pagesize ?: $this->pagesize;

        $data = $this->dataModel->with($this->indexWith)->where($where)->order($sortOrder)->limit(($page - 1) * $this->pagesize, $this->pagesize)->select();

        $this->buildTable($data);
        $table->fill($data);
        $table->paginator($this->dataModel->where($where)->count(), $this->pagesize);
        $table->sortOrder($sortOrder);

        return $data;
    }

    /**
     * Undocumented function
     *
     * @param string $title
     * @param string $desc
     * @param string $type index/add/edit/view
     * @return Builder
     */
    protected function builder($title = '', $desc = '', $type = '')
    {
        $builder = Builder::getInstance($title, $desc);

        $this->creating($builder, $type);

        return $builder;
    }

    /**
     * Undocumented function
     *
     * @param Builder $builder
     * @param string $type index/add/edit/view
     * @return void
     */
    protected function creating($builder, $type = '')
    {
        //其他用户自定义初始化
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getPk()
    {
        if (empty($this->pk)) {
            if ($this->dataModel) {
                $this->pk = $this->dataModel->getPk();
                $this->pk = !empty($this->pk) && is_string($this->pk) ? $this->pk : 'id';
            } else {
                $this->pk = 'id';
            }
        }

        return $this->pk;
    }
}
