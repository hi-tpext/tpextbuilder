<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Matche extends Raw
{
    use HasOptions;

    public function renderValue()
    {
        if (isset($this->options[$this->value])) {
            $this->value = $this->options[$this->value];
        } else if (isset($this->options['__default__'])) {
            $this->value = $this->options['__default__'];
        }

        return parent::renderValue();
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = false)
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function disabled($val = false)
    {
        return $this;
    }
}
