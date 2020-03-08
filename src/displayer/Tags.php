<?php

namespace tpext\builder\displayer;

class Tags extends Field
{
    protected $view = 'tags';

    protected $js = [
        '/assets/tpextbuilder/js/jquery-tags-input/jquery.tagsinput.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/jquery-tags-input/jquery.tagsinput.min.css',
    ];

    protected $placeholder = '';

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function placeholder($val)
    {
        $this->placeholder = $val;
        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'placeholder' => $this->placeholder
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
