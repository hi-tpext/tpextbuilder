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
     * @param array|string $val
     * @return $this
     */
    public function default($val = [])
    {
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

        $dataSelected = $this->checked;

        foreach ($this->checked as &$ck) {
            $ck = '-' . $ck;
        }

        unset($ck);

        $vars = array_merge($vars, [
            'checked' => $this->checked,
            'dataSelected' => implode(',', $dataSelected), //已经手动在后端给了选项的，不再ajax加载默认值
            'select2' => $this->select2,
            'group' => $this->group,
            'options' => $this->options,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
