<?php

namespace tpext\builder\traits;

use think\Model;
use tpext\builder\common\Builder;

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
    protected $add = '添加';
    protected $edit = '编辑';
    protected $index = '列表';

    /**
     * 允许行内编辑的字段，留空则不限制
     *
     * @var array
     */
    protected $allowFields = [];

    public function index()
    {

    }

    public function add()
    {
        if (request()->isPost()) {
            return $this->save();
        } else {
            return $this->form($this->add);
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

            return $this->form($this->edit, $data);
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
     * @param string $action
     * @param array $data
     * @return mixed
     */
    protected function table($data = [])
    {
        $builder = $this->builder($this->pageTitle, $this->index);

        $table = $builder->form();
        $table = $this->createForm($table, $this->isEdit($data));

        $form->fill($data);

        return $builder->render();
    }

    /**
     * Undocumented function
     *
     * @param string $action
     * @param array $data
     * @return mixed
     */
    protected function form($action, $data = [])
    {
        $builder = $this->builder($this->pageTitle, $action);

        $form = $builder->form();
        $form = $this->createForm($form, $this->isEdit($data));

        $form->fill($data);

        return $builder->render();
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return boolean
     */
    protected function isEdit($data)
    {
        return isset($data[$this->dataModel->getPk()]);
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
