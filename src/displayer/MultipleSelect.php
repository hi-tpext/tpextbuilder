<?php

namespace tpext\builder\displayer;

class MultipleSelect extends Select
{
    protected $view = 'multipleselect';

    protected $attr = 'size="1"';

    protected $default = [];

    protected $checked = [];

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

        $this->isGroup();

        $vars = array_merge($vars, [
            'checked' => $this->checked,
            'select2' => $this->select2,
            'group' => $this->group,
            'options' => $this->options,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
