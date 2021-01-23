<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Radio extends Field
{
    use HasOptions;

    protected $view = 'radio';

    protected $class = 'radio-default';

    protected $inline = true;

    protected $checked = '';

    protected $disabledOptions = [];

    protected $readonlyOptions = [];

    protected $blockStyle = false;

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function inline($val = true)
    {
        $this->inline = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function blockStyle($val = true)
    {
        $this->blockStyle = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|array $val
     * @return $this
     */
    public function disabledOptions($val)
    {
        $this->inline = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|array $val
     * @return $this
     */
    public function readonlyOptions($val)
    {
        $this->readonlyOptions = $val;
        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!($this->value === '' || $this->value === null)) {
            $this->checked = $this->value;
        } else {
            $this->checked = $this->default;
        }

        if ($this->disabledOptions && is_string($this->disabledOptions)) {
            $this->disabledOptions = explode(',', $this->disabledOptions);
        }

        if ($this->readonlyOptions && is_string($this->readonlyOptions)) {
            $this->readonlyOptions = explode(',', $this->readonlyOptions);
        }

        foreach ($this->disabledOptions as &$di) {
            $di = '-' . $di;
        }

        foreach ($this->readonlyOptions as &$ro) {
            $ro = '-' . $ro;
        }

        unset($ck, $di, $ro);

        $vars = array_merge($vars, [
            'inline' => $this->inline && !$this->blockStyle ? 'radio-inline' : '',
            'blockStyle' => $this->blockStyle ? 'radio-block' : 'lyear-radio',
            'checked' => '-' . $this->checked,
            'options' => $this->options,
            'disabledOptions' => $this->disabledOptions,
            'readonlyOptions' => $this->readonlyOptions,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
