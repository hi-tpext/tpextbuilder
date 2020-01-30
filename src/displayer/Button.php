<?php

namespace tpext\builder\displayer;

class Button extends Field
{
    protected $view = 'button';

    protected $bottom = false;

    protected $size = [0, 12];

    protected $showLabel = false;

    protected $class = 'btn-default';

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return void
     */
    public function bottom($val = true)
    {
        $this->bottom = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isBottom()
    {
        return $this->bottom;
    }
}
