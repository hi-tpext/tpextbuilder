<?php

namespace tpext\builder\displayer;

use think\Model;
use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\form\FieldsContent;

class Fields extends Field
{
    protected $view = 'fields';

    protected $data = [];

    /**
     * Undocumented variable
     *
     * @var Form|Search
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

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Field ...$fields
     * @return $this
     */
    public function with(...$fields)
    {
        $this->form->fieldsEnd();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function getContent()
    {
        return $this->__items_content__;
    }

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function value($val)
    {
        return $this->fill($val);
    }

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @param boolean $overWrite
     * @return $this
     */
    public function fill($data = [], $overWrite = false)
    {
        if (!$overWrite && !empty($this->data)) {
            return $this;
        }

        if (!empty($this->name) && isset($data[$this->name]) &&
            (is_array($data[$this->name]) || $data[$this->name] instanceof Model)) {
            $this->data = $data[$this->name];
        } else {
            $this->data = $data;
        }

        $this->__fields_content__->fill($this->data);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function beforRender()
    {
        $this->__fields_content__->beforRender();
        parent::beforRender();
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
