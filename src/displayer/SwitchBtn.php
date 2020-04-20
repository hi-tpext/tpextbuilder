<?php

namespace tpext\builder\displayer;

class SwitchBtn extends Field
{
    protected $view = 'switchbtn';

    protected $class = 'switch-solid switch-primary';

    protected $checked = '';

    public function render()
    {
        $vars = $this->commonVars();

        if (!($this->value === '' || $this->value === null)) {
            $this->checked = $this->value;
        } else {
            $this->checked = $this->default;
        }

        $vars = array_merge($vars, [
            'checked' => $this->checked,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
