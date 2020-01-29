<?php

namespace tpext\builder\displayer;

class Divider extends Html
{
    public function render()
    {
        return "<div class='divider'>{$this->value}</div>";
    }
}
