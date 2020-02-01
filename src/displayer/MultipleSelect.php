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
        if ($this->select2) {
            $this->select2Script();
        }

        $vars = $this->commonVars();

        if (!empty($this->value)) {
            $this->checked = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!empty($this->default)) {
            $this->checked = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        $this->isGroup();

        $vars = array_merge($vars, [
            'checked' => $this->checked,
            'select2' => $this->select2,
            'group' => $this->group,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
