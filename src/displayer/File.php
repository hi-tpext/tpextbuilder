<?php

namespace tpext\builder\displayer;

class File extends MultipleFile
{
    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function default($val = '') {
        $this->default = $val;
        return $this;
    }

    public function render()
    {
        $this->jsOptions = array_merge($this->jsOptions, [
            'limit' => 1,
            'multiple' => false,
        ]);

        return parent::render();
    }
}
