<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Matches extends Raw
{
    use HasOptions;

    public function renderValue()
    {
        $value = parent::renderValue();

        $values = explode(',', $value);
        $texts = [];

        foreach ($values as $value) {
            if (isset($this->options[$value])) {
                $texts[] = $this->options[$value];
            }
        }

        return implode(', ', $texts);
    }
}
