<?php

namespace tpext\builder\displayer;

class Radio extends Field
{
    protected $view = 'radio';

    protected $inline = true;

    protected $checked = '';

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
     * @param string|int|mixed $val
     * @return $this
     */
    public function checked($val = '')
    {
        $this->checked = $val;
        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (empty($this->checked) && !empty($this->value)) {
            $this->checked = is_array($this->value) ? $this->value : explode(',', $this->value);
        }

        $vars = array_merge($vars, [
            'inline' => $this->inline ? 'radio-inline' : 'm-t-10',
            'checked' => $this->checked,
        ]);

        $config = [];

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->config($config)->getContent();
    }
}
