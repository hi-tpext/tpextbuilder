<?php

namespace tpext\builder\displayer;

class Divider extends Field
{
    protected $view = 'divider';

    public function created($fieldType = '')
    {
        parent::created($fieldType);

        $this->value = $this->label ? $this->label : $this->name;

        $this->name = 'divider' . mt_rand(100, 999);

        $this->label = '';
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
