<?php

namespace tpext\builder\displayer;

class Text extends Field
{
    protected $view = 'text';

    public function render()
    {
        $vars = $this->commonVars();
        
        $config = [];

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->config($config)->getContent();
    }
}
