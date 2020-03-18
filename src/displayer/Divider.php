<?php

namespace tpext\builder\displayer;

class Divider extends Field
{
    protected $view = 'divider';

    public function created()
    {
        parent::created();

        $this->value = $this->label ? $this->label : $this->name;

        $this->name = 'divider' . mt_rand(100, 999);

        $this->label = '';
    }
}
