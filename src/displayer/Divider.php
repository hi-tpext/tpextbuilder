<?php

namespace tpext\builder\displayer;

class Divider extends Field
{
    protected $view = 'divider';

    protected $isInput = false;

    public function created($fieldType = '')
    {
        parent::created($fieldType);

        $this->value = $this->label ? $this->label : $this->name;

        $this->name = 'divider' . mt_rand(100, 999);

        $this->label = '';
    }
}
