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
    public function default($val = '') {
        $this->default = $val;
        return $this;
    }

    public function render()
    {
        $this->jsOptions = array_merge($this->jsOptions, [
            'fileNumLimit' => 1,
            'multiple' => false,
        ]);

        if (!empty($this->extKey)) {
            $this->getWrapper()->style('width:' . $this->jsOptions['thumbnailWidth'] . 'px;');
        }

        return parent::render();
    }
}
