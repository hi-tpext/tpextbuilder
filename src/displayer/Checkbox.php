<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;
use tpext\builder\traits\HasWhen;

class Checkbox extends Field
{
    use HasOptions;
    use HasWhen;

    protected $view = 'checkbox';

    protected $inline = true;

    protected $checkallBtn = '';

    protected $default = [];

    protected $checked = [];

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
        $this->disabledOptions = $val;
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

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function checkallBtn($val = '全选')
    {
        $this->checkallBtn = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function default($val = [])
    {
        $this->default = $val;
        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!($this->value === '' || $this->value === null || $this->value === [])) {
            $this->checked = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!($this->default === '' || $this->default === null || $this->default === [])) {
            $this->checked = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        $checkall = false;

        if ($this->checkallBtn) {
            $count = 0;
            foreach ($this->options as $key => $op) {
                if (in_array($key, $this->checked)) {
                    $count += 1;
                }
            }
            $checkall = $count > 0 && $count == count($this->options);
        }

        foreach ($this->checked as &$ck) {
            $ck = '-' . $ck;
        }

        unset($ck);

        if ($this->disabledOptions && !is_array($this->disabledOptions)) {
            $this->disabledOptions = explode(',', $this->disabledOptions);
        }

        if ($this->readonlyOptions && !is_array($this->readonlyOptions)) {
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
            'inline' => $this->inline && !$this->blockStyle ? 'checkbox-inline' : '',
            'blockStyle' => $this->blockStyle ? 'checkbox-block' : 'lyear-checkbox',
            'checkallBtn' => $this->checkallBtn,
            'checkall' => $checkall,
            'checked' => $this->checked,
            'options' => $this->options,
            'disabledOptions' => $this->disabledOptions,
            'readonlyOptions' => $this->readonlyOptions,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

    public function beforRender()
    {
        $this->whenScript();
        return parent::beforRender();
    }
}
