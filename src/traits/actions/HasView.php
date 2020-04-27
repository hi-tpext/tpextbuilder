<?php

namespace tpext\builder\traits\actions;

/**
 * 查看
 */

trait HasView
{
    public function view($id)
    {
        if (request()->isPost()) {
            $this->error('不允许的操作');
        } else {
            $builder = $this->builder($this->pageTitle, $this->viewText);
            $data = $this->dataModel->get($id);
            $data = $this->dataModel->get($id);
            if (!$data) {
                return $builder->layer()->close(0, '数据不存在');
            }
            $form = $builder->form();
            $this->form = $form;
            $this->builForm(true, $data);
            $form->fill($data);
            $form->readonly();

            return $builder->render();
        }
    }
}
