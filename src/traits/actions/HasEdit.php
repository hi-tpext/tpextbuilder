<?php

namespace tpext\builder\traits\actions;

/**
 * 编辑
 */

trait HasEdit
{
    public function edit($id)
    {
        if (request()->isAjax()) {

            $this->checkToken();

            return $this->save($id);
        }

        $builder = $this->builder($this->pageTitle, $this->editText, 'edit');
        $data = $this->dataModel->find($id);
        if (!$data) {
            return $builder->layer()->close(0, '数据不存在');
        }
        $form = $builder->form();
        $this->form = $form;
        $this->buildForm(1, $data);
        $form->fill($data);
        $form->method('put');

        return $builder->render();
    }
}
