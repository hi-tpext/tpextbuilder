<?php

namespace tpext\builder\displayer;

class Loads extends Load
{
    protected $view = 'load';

    protected $isInput = false;
    
    /**
     * Undocumented function
     * '、'
     * @param string $val
     * @return $this
     */
    public function separator($val = '、')
    {
        $this->jsOptions['ajax']['separator'] = $val;
        return $this;
    }

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

    public function customVars()
    {
        if (is_array($this->value)) {
            $this->value = implode(',', $this->value);
        }

        if (is_array($this->default)) {
            $this->default = implode(',', $this->default);
        }

        return parent::customVars();
    }
}
