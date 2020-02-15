<?php

namespace tpext\builder\table;

use tpext\builder\form\Row;

class Column extends Row
{
    public function beforRender()
    {
        return $this->displayer
            ->showLabel(false)
            ->size(0, 12);
    }
}
