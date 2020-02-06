<?php

namespace tpext\builder\displayer;

class Textarea extends Field
{
    protected $view = 'textarea';

    protected $maxlength = 0;

    protected $js = [
        '/assets/tpextbuilder/js/bootstrap-maxlength/bootstrap-maxlength.min.js',
    ];

    /**
     * Undocumented function
     *
     * @param integer $val
     * @return $this
     */
    public function maxlength($val = 0)
    {
        $this->maxlength = $val;
        return $this;
    }

    public function render()
    {

        if ($this->maxlength > 0) {
            $this->attr .= ' maxlength="' . $this->maxlength . '"';
        }

        return parent::render();
    }
}
