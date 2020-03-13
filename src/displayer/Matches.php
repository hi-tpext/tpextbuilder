<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Matches extends Raw
{
    use HasOptions;

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $values = explode(',', $vars['value']);
        $texts = [];

        foreach ($values as $value) {
            if (isset($this->options[$value])) {
                $texts[] = $this->options[$value];
            }
        }

        $this->value = $vars['value'] = implode(', ', $texts);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

}
