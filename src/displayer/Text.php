<?php

namespace tpext\builder\displayer;

class Text extends Field
{
    protected $view = 'text';

    protected $befor = '';

    protected $after = '';

    /**
     * Undocumented function
     *
     * @param string $html
     * @return void
     */
    public function befor($html)
    {
        $this->befor = $html;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @return void
     */
    public function after($html)
    {
        $this->after = $html;
        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'befor' => $this->befor,
            'after' => $this->after,
        ]);

        $config = [];

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->config($config)->getContent();
    }
}
