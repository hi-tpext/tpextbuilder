<?php

namespace tpext\builder\displayer;

class Matches extends Field
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

        $values = explode(',', $vars['value']);
        $texts = [];

        foreach ($values as $value) {
            if (isset($this->options[$value])) {
                $texts[] = $this->options[$value];
            }
        }

        $vars['value'] = implode(', ', $texts);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

}
