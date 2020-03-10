<?php

namespace tpext\builder\traits;

use think\Model;
use tpext\builder\common\Builder;
use tpext\builder\common\Form;
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

    /**
     * 允许行内编辑的字段，留空则不限制
     *
     * @var array
     */
    protected $allowFields = [];

    public function index()
    {
        $builder = $this->builder($this->pageTitle, $this->indexText);

        $search = $this->builSearch($builder->form());
        $table = $this->buildTable($builder->table());

        $table->searchForm($search);

        $table->data($data);

        if (request()->isAjax()) {
            return $table->partial()->render();
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
            $form = $this->builForm($form, false);
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
            $form = $this->builForm($form, true);
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
        if (!empty($this->allowFields) && !in_array($name, $this->allowFields)) {
            $this->error('不允许的操作');
        }
        $res = $this->dataModel->where([$this->dataModel->getPk() => $id])->update([$name => $value]);

        if ($res) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败，或无更改');
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

    /*******辅助方法******/

    /**
     * Undocumented function
     *
     * @param Form $form
     * @param boolean $isEdit
     * @return Form
     */
    protected function builForm($form, $isEdit)
    {
        return $form;
    }

    /**
     * Undocumented function
     *
     * @param Table $table
     * @return Table
     */
    protected function buildTable($table)
    {
        return $table;
    }

    /**
     * Undocumented function
     *
     * @param Form $form
     * @return Form
     */
    protected function builSearch($form)
    {
        //无搜索就返回 null
        return null;
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
