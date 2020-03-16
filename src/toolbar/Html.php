<?php

namespace tpext\builder\toolbar;

class Html extends Bar
{
    protected $view = 'html';

    public function __construct($html)
    {
        $this->label = $html;
    }

     /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
