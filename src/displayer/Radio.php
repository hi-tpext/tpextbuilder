<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Radio extends Field
{
    use HasOptions;
    
    protected $view = 'radio';

    protected $class = 'lyear-radio radio-default';

    protected $inline = true;

    protected $checked = '';
   
    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function inline($val = true)
    {
        $this->inline = $val;
        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!($this->value === '' || $this->value === null)) {
            $this->checked = $this->value;
        } else {
            $this->checked = $this->default;
        }

        $vars = array_merge($vars, [
            'inline' => $this->inline ? 'radio-inline' : 'm-t-10',
            'checked' => $this->checked,
            'options' => $this->options,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
