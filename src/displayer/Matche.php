<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Matche extends Raw
{
    use HasOptions;

    protected $view = 'matche';

    protected $isInput = false;

    protected $checked = '';

    public function beforRender()
    {
        $this->checked = !($this->value === '' || $this->value === null) ? $this->value : $this->default;
        return parent::beforRender();
    }

    public function renderValue()
    {
        $this->value = !($this->value === '' || $this->value === null) ? $this->value : $this->default;

        if (isset($this->options[$this->value])) {
            $this->value = $this->options[$this->value];
        } else if (isset($this->options['__default__'])) {
            $this->value = $this->options['__default__'];
        }

        return parent::renderValue();
    }

    public function yesOrNo()
    {
        $this->options = [1 => __blang('bilder_option_yes'), 0 => __blang('bilder_option_no')];
        return $this;
    }

    public function customVars()
    {
        $this->checked = (string)$this->checked;

        return array_merge(parent::customVars(), [
            'checked' => $this->checked,
        ]);
    }
}
