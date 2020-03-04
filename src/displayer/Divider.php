<?php

namespace tpext\builder\displayer;

class Divider extends Field
{
    protected $view = 'divider';

    public function created()
    {
        parent::created();

        $this->value = $this->name;

        $this->label = '';
    }
}
