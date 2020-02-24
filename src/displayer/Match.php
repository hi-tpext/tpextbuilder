<?php

namespace tpext\builder\displayer;

class Match extends Raw
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
        if (isset($this->options[$vars['value']])) {
            $this->value = $vars['value'] = $this->options[$vars['value']];
        } else if (isset($this->options['__default__'])) {
            $this->value = $vars['value'] = $this->options['__default__'];
        }

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

}
