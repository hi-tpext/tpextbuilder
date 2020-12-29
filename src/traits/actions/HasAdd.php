<?php

namespace tpext\builder\traits\actions;

/**
 * 添加
 */

trait HasAdd
{
    public function add()
    {
        if (request()->isGet()) {

            $builder = $this->builder($this->pageTitle, $this->addText, 'add');
            $form = $builder->form();
            $data = [];
            $this->form = $form;
            $this->buildForm(0, $data);
            $form->fill($data);
            $form->method('post');

            return $builder->render();
        }

        $this->checkToken();

        return $this->save();
    }
}
