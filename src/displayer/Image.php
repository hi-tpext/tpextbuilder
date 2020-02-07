<?php

namespace tpext\builder\displayer;

class Image extends File
{
    public function render()
    {
        $this->image();

        return parent::render();
    }
}
