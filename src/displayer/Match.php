<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Match extends Raw
{
    use HasOptions;

    public function renderValue()
    {
        $value = parent::renderValue();

        if (isset($this->options[$value])) {
            $value = $this->options[$value];
        } else if (isset($this->options['__default__'])) {
            $value = $this->options['__default__'];
        }

        return $value;
    }
}
