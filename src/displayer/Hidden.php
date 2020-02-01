<?php

namespace tpext\builder\displayer;

class Hidden extends Field
{
    protected $view = 'hidden';

    public function __construct($name, $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function created()
    {
        $this->getWapper()->addAttr('style="display:none;"');
    }
}
