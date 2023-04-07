<?php

namespace tpext\builder\displayer;

class Text extends Field
{
    protected $view = 'text';

    protected $befor = '';

    protected $after = '';

    protected $maxlength = 0;

    protected $placeholder = '';

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

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function placeholder($val)
    {
        $this->placeholder = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @return $this
     */
    public function befor($html)
    {
        $this->befor = $html;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $text
     * @return $this
     */
    public function after($html)
    {
        $this->after = $html;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $text
     * @return $this
     */
    public function beforSymbol($text)
    {
        $this->befor = '<span class="input-group-addon">' . $text . '</span>';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @return $this
     */
    public function afterSymbol($text)
    {
        $this->after = '<span class="input-group-addon">' . $text . '</span>';
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
            'placeholder' => $this->placeholder ?: __blang('bilder_please_enter') . $this->label
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
