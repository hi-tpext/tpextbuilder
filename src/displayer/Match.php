<?php

namespace tpext\builder\displayer;

class Match extends Field
{
    protected $options = [];

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function options($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        if(isset($this->options[$vars['value']]))
        {
            $vars['value'] = $this->options[$vars['value']];
        }

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

}
