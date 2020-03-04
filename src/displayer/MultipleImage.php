<?php

namespace tpext\builder\displayer;

class MultipleImage extends MultipleFile
{
    public function render()
    {
        $this->image();

        return parent::render();
    }
}
