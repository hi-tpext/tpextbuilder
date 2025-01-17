<?php

namespace tpext\builder\traits;

trait HasDom
{
    protected $class = '';

    protected $attr = '';

    protected $style = '';

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function class($val)
    {
        $this->class = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function attr($val)
    {
        $this->attr = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function style($val)
    {
        $this->style = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addClass($val)
    {
        $this->class .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addAttr($val)
    {
        $this->attr .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addStyle($val)
    {
        $this->style .= $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    public function getAttrWithStyle()
    {
        return implode(' ', array_unique(explode(' ', $this->attr))) . (empty($this->style) ? '' : ' style="' . $this->style . '"');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getClass()
    {
        $arr = explode(' ', $this->class);

        return ' ' . implode(' ', array_unique($arr));
    }
}
