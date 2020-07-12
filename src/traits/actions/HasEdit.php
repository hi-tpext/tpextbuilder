<?php

namespace tpext\builder\traits\actions;

/**
 * 编辑
 */

trait HasEdit
{
    public function edit($id)
    {
        if (request()->isPost()) {
            return $this->save($id);
        }

        $builder = $this->builder($this->pageTitle, $this->editText);
        $data = $this->dataModel->get($id);
        if (!$data) {
            return $builder->layer()->close(0, '数据不存在');
        }
        $form = $builder->form();
        $this->form = $form;
        $this->buildForm(1, $data);
        $form->fill($data);

        return $builder->render();
    }
}
