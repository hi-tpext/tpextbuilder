<?php

namespace tpext\builder\traits;

use think\Model;
use tpext\builder\common\Builder;
use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\common\Table;

trait HasBuilder
{
    /**
     * 数据模型
     *
     * @var Model
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
    protected $sortOrder = 'sort asc';
    protected $enableField = 'enable';

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
     * 构建表单
     *
     * @param boolean $isEdit
     * @param array $data
     */
    protected function builForm($isEdit, &$data = [])
    {
        $form = $this->form;
    }

    /**
     * 构建表格
     *
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
        $search->text('some_key_words')->placeholder('输入姓名|电话|邮箱查询');
    }

    /**
     * 保存数据 范例
     *
     * @param integer $id
     * @return void
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
            $res = $this->dataModel->where(['id' => $id])->update($data);
        } else {
            $res = $this->dataModel->create($data);
        }
        if (!$res) {
            $this->error('保存失败');
        }

        return $this->builder()->layer()->closeRefresh(1, '保存成功');
    }

    /**
     * 判断是否可以删除
     *
     * @param [type] $id
     * @return boolean
     */
    protected function canDel($id)
    {
        if (!empty($this->delNotAllowed) && in_array($id, $this->delNotAllowed)) {
            return false;
        }
        // 其他逻辑
        return true;
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
     * @param Table $table
     * @return array|Collection
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

    /*******通用方法******/

    public function index()
    {
        $builder = $this->builder($this->pageTitle, $this->indexText);

        $this->table = $builder->table();
        $this->table->pk($this->dataModel->getPk());
        $this->search = $this->table->getSearch();

        $this->builSearch();
        $this->buildDataList();

        if (request()->isAjax()) {
            return $this->table->partial()->render();
        }

        return $builder->render();
    }

    public function add()
    {
        if (request()->isPost()) {
            return $this->save();
        } else {
            $builder = $this->builder($this->pageTitle, $this->addText);
            $form = $builder->form();
            $this->form = $form;
            $this->builForm(false, $data);
            return $builder->render();
        }
    }

    public function edit($id)
    {
        if (request()->isPost()) {
            return $this->save($id);
        } else {
            $data = $this->dataModel->get($id);
            if (!$data) {
                $this->error('数据不存在');
            }
            $builder = $this->builder($this->pageTitle, $this->editText);
            $form = $builder->form();
            $this->form = $form;
            $this->builForm(true, $data);
            $form->fill($data);
            return $builder->render();
        }
    }

    public function autopost()
    {
        $id = input('post.id/d', '');
        $name = input('post.name', '');
        $value = input('post.value', '');

        if (empty($id) || empty($name)) {
            $this->error('参数有误');
        }
        if (!empty($this->postAllowFields) && !in_array($name, $this->postAllowFields)) {
            $this->error('不允许的操作');
        }
        $res = $this->dataModel->where([$this->dataModel->getPk() => $id])->update([$name => $value]);

        if ($res) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败，或无更改');
        }
    }

    public function enable()
    {
        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error('参数有误');
        }
        $res = 0;
        foreach ($ids as $id) {
            if ($this->dataModel->where([$this->dataModel->getPk() => $id])->update([$this->enableField => 1])) {
                $res += 1;
            }
        }
        if ($res) {
            $this->success('成功操作' . $res . '条数据');
        } else {
            $this->error('操作失败');
        }
    }

    public function disable()
    {
        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error('参数有误');
        }
        $res = 0;
        foreach ($ids as $id) {
            if ($this->dataModel->where([$this->dataModel->getPk() => $id])->update([$this->enableField => 0])) {
                $res += 1;
            }
        }
        if ($res) {
            $this->success('成功操作' . $res . '条数据');
        } else {
            $this->error('操作失败');
        }
    }

    public function delete()
    {
        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error('参数有误');
        }
        $res = 0;
        foreach ($ids as $id) {
            if (!$this->canDel($id)) {
                continue;
            }
            if ($this->dataModel->destroy($id)) {
                $res += 1;
            }
        }
        if ($res) {
            $this->success('成功删除' . $res . '条数据');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * Undocumented function
     *
     * @param string $title
     * @param string $desc
     * @return Builder
     */
    protected function builder($title = '', $desc = '')
    {
        return Builder::getInstance($title, $desc);
    }
}
