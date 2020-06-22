<?php

namespace tpext\builder\displayer;

class Image extends File
{
    public function render()
    {
        $this->image();

        $this->canUpload = !$this->readonly && $this->canUpload && empty($this->extKey);

        if (!$this->canUpload) {
            if (empty($this->default)) {
                $this->default = '/assets/tpextbuilder/images/default.png';
            }
        }

        return parent::render();
    }
}
