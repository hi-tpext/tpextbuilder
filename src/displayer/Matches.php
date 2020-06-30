<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Matches extends Raw
{
    use HasOptions;

    public function renderValue()
    {
        $values = explode(',', $this->value);
        $texts = [];

        foreach ($values as $value) {
            if (isset($this->options[$value])) {
                $texts[] = $this->options[$value];
            }
        }

        $this->value = implode(',', $texts);

        return parent::renderValue();
    }
}
