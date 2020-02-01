<?php

namespace tpext\builder\displayer;

class Text extends Field
{
    protected $view = 'text';

    protected $befor = '';

    protected $after = '';

    protected $maxlength = 0;

    protected $js = [
        '/assets/tpextbuilder/js/bootstrap-maxlength/bootstrap-maxlength.min.js',
    ];

    /**
     * Undocumented function
     *
     * @param integer $val
     * @return void
     */
    public function maxlength($val = 0)
    {
        $this->maxlength = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @return void
     */
    public function befor($html)
    {
        $this->befor = $html;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @return void
     */
    public function after($html)
    {
        $this->after = $html;
        return $this;
    }

    public function render()
    {

        if ($this->maxlength > 0) {
            $this->attr .= ' maxlength="' . $this->maxlength . '"';
        }

        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'befor' => $this->befor,
            'after' => $this->after,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
