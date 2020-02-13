<?php

namespace tpext\builder\displayer;

class Checkbox extends Field
{
    protected $view = 'checkbox';

    protected $class = 'lyear-checkbox checkbox-default';

    protected $options = [];
    
    protected $inline = true;

    protected $checkallBtn = false;

    protected $default = [];

    protected $checked = [];

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
     * @param boolean $val
     * @return $this
     */
    public function inline($val = true)
    {
        $this->inline = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function checkallBtn($val = true)
    {
        $this->checkallBtn = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    function default($val = []) {
        $this->default = $val;
        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!empty($this->value)) {
            $this->checked = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!empty($this->default)) {
            $this->checked = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        $vars = array_merge($vars, [
            'inline' => $this->inline ? 'checkbox-inline' : 'm-t-10',
            'checkallBtn' => $this->checkallBtn,
            'checked' => $this->checked,
            'options' => $this->options,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
