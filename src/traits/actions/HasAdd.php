<?php

namespace tpext\builder\traits\actions;

/**
 * 添加
 */

trait HasAdd
{
    public function add()
    {
        if (request()->isPost()) {
            return $this->save();
        } else {
            $builder = $this->builder($this->pageTitle, $this->addText);
            $form = $builder->form();
            $data = [];
            $this->form = $form;
            $this->builForm(0, $data);
            $form->fill($data);
            return $builder->render();
        }
    }
}
