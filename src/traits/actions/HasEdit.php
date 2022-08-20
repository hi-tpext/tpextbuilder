<?php

namespace tpext\builder\traits\actions;

define('FORM_EDIT', 1);
/**
 * 编辑
 */

trait HasEdit
{
    public function edit()
    {
        $id = input('id');

        if (request()->isGet()) {

            $builder = $this->builder($this->pageTitle, $this->editText, 'edit');

            $data = $this->dataModel->where($this->getPk(), $id)->find();
            if (!$data) {
                return $builder->layer()->close(0, '数据不存在');
            }

            $form = $builder->form();
            $this->form = $form;
            $this->isEdit = 1;
            $this->buildForm($this->isEdit, $data);
            $form->fill($data);
            $form->method('put');

            return $builder->render();
        }

        $this->checkToken();

        return $this->save($id);
    }
}
