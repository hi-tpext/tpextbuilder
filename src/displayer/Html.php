<?php

namespace tpext\builder\displayer;

class Html extends Field
{
    protected $view = 'html';

    public function created()
    {
        parent::created();
        
        $this->size(0, 12);

        $this->value = $this->name;
    }
}
