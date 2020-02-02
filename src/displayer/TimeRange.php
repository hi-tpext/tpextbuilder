<?php

namespace tpext\builder\displayer;

class TimeRange extends DateTimeRange
{
    protected $format = 'HH:mm:ss';

    protected $befor = '<span class="input-group-addon"><i class="mdi mdi-clock"></i></span>';

    protected $timePicker = true;

    /**
     * Undocumented function
     * HH:mm:ss
     * @param string $val
     * @return $this
     */
    public function format($val)
    {
        $this->format = $val;
        return $this;
    }
}
