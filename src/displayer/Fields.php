<?php

namespace tpext\builder\displayer;

use think\Model;
use tpext\builder\common\Form;
use tpext\builder\form\FieldsContent;

class Fields extends Field
{
    protected $view = 'fields';

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $form;

    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields_content__;

    public function created()
    {
        parent::created();

        $this->form = $this->getWapper()->getForm();
        $this->__fields_content__ = $this->form->createFields();

        $this->name = 'fields' . mt_rand(100, 999);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Field ...$fields
     * @return void
     */
    public function with(...$fields)
    {
        $this->form->fieldsEnd();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->__fields_content__->fill($data);

        return $this;
    }

    public function beforRender()
    {
        $this->__fields_content__->beforRender();
        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'fields_content' => $this->__fields_content__,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
