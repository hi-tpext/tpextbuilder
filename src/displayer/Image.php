<?php

namespace tpext\builder\displayer;

class Image extends File
{
    public function created($fieldType = '')
    {
        parent::created($fieldType);
        $this->jsOptions['fileSingleSizeLimit'] = 2 * 1024 * 1024;
    }

    public function render()
    {
        $this->image();

        $this->canUpload = !$this->readonly && $this->canUpload && ($this->isInTable || empty($this->extKey) || stripos($this->extKey, '-watch-') !== false);

        if (!$this->canUpload) {
            if (empty($this->default)) {
                $this->default = '/assets/tpextbuilder/images/default.png';
            }
        }

        $this->jsOptions['isImage'] = true;

        return parent::render();
    }
}
