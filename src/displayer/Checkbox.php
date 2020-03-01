<?php

namespace tpext\builder\displayer;

class Checkbox extends Field
{
    protected $view = 'checkbox';

    protected $class = 'lyear-checkbox checkbox-default';

    protected $options = [];

    protected $inline = true;

    protected $checkallBtn = '';

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
     * @param string $val
     * @return $this
     */
    public function checkallBtn($val = '全选')
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

        if (!($this->value === '' || $this->value === null || $this->value === [])) {
            $this->checked = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!($this->default === '' || $this->default === null || $this->default === [])) {
            $this->checked = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        $checkall = false;

        if ($this->checkallBtn) {
            $count = 0;
            foreach ($this->options as $key => $op) {
                if (in_array($key, $this->checked)) {
                    $count += 1;
                }
            }
            $checkall = $count == count($this->options);
        }

        $vars = array_merge($vars, [
            'inline' => $this->inline ? 'checkbox-inline' : 'm-t-10',
            'checkallBtn' => $this->checkallBtn,
            'checkall' => $checkall,
            'checked' => $this->checked,
            'options' => $this->options,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
