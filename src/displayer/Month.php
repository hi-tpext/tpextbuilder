<?php

namespace tpext\builder\displayer;

class Month extends Date
{
    protected $format = 'MM';

    /**
     * Undocumented function
     * @param string $val MM
     * @return $this
     */
    public function format($val)
    {
        $this->format = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function timespan($val = 'm')
    {
        $this->timespan = $val;
        return $this;
    }
}
