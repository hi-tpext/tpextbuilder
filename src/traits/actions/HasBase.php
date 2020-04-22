<?php

namespace tpext\builder\traits\actions;

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
    protected $indexText = '列表';
    protected $pagesize = 14;
    protected $sortOrder = 'id desc';
    protected $enableField = 'enable';

    /**
     * Undocumented variable
     *
     * @var \tpext\builder\common\Form
     */
    protected $form;
    /**
     * Undocumented variable
     *
     * @var \tpext\builder\common\Search
     */
    protected $search;
    /**
     * Undocumented variable
     *
     * @var \tpext\builder\common\Table
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
     * @param boolean $isEdit
     * @param array $data
     * @return void
     */
    protected function builForm($isEdit, &$data = [])
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
    protected function builSearch()
    {
        $search = $this->search;
        //$search->text('some_key_words')->placeholder('输入姓名|电话|邮箱查询');
    }

    /**
     * 保存数据 范例
     *
     * @param integer $id
     * @return mixed
     */
    private function save($id = 0)
    {
        $data = request()->only([
            'some_fields',
            // ... more_fields
        ], 'post');

        $result = $this->validate($data, [
            'some_fields|AreYouOK' => 'require',
        ]);

        if (true !== $result) {
            $this->error($result);
        }

        if ($id) {
            $res = $this->dataModel->update($data, ['id' => $id]);
        } else {
            $res = $this->dataModel->create($data);
        }
        if (!$res) {
            $this->error('保存失败');
        }

        return $this->builder()->layer()->closeRefresh(1, '保存成功');
    }

    protected function filterWhere()
    {
        $where = [];
        $searchData = request()->only([
            'some_key_words',
            // ... more_key_words
        ], 'post');
        $where = [];
        if (!empty($searchData['some_key_words'])) {
            //$where[] = ['phone|username|email', 'like', '%' . $searchData['some_key_words'] . '%'];
        }

        return $where;
    }

    /**
     * Undocumented function
     *
     * @return array|\think\Collection
     */
    protected function buildDataList()
    {
        $page = input('__page__/d', 1);
        $page = $page < 1 ? 1 : $page;
        $sortOrder = input('__sort__', $this->sortOrder ? $this->sortOrder : $this->dataModel->getPk() . ' desc');
        $where = $this->filterWhere();
        $table = $this->table;

        $data = $this->dataModel->where($where)->order($sortOrder)->limit(($page - 1) * $this->pagesize, $this->pagesize)->select();
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
     * @return \tpext\builder\common\Builder
     */
    protected function builder($title = '', $desc = '')
    {
        return \tpext\builder\common\Builder::getInstance($title, $desc);
    }
}
