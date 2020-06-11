<?php

namespace tpext\builder\displayer;

class Hidden extends Field
{
    protected $view = 'hidden';

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function created()
    {
        $this->getWrapper()->addStyle('display:none;');
    }
}
