<?php

namespace tpext\builder\table;

use think\Collection;
use think\paginator\driver\Bootstrap;

class Paginator extends Bootstrap
{
    protected $paginatorClass = 'pagination-sm';

    protected $attr = '';

    protected $class = 'text-center';

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function class ($val)
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
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    public function options($val)
    {
        $this->options = array_merge($this->options, $val);
        $this->reset();
        return $this;
    }

    public function reset()
    {
        $this->lastPage = (int) ceil($this->total / $this->listRows);
        $this->currentPage = $this->setCurrentPage($this->currentPage);
        $this->hasMore = $this->currentPage < $this->lastPage;
    }
    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function setItems($items)
    {
        if (!$items instanceof Collection) {
            $items = Collection::make($items);
        }

        $this->items = $items;
        $this->reset();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function setTotal($val)
    {
        $this->total = $val;
        $this->reset();
        return $this;
    }

    public function render()
    {
        if (!$this->total) {
            return '';
        }

        $html = parent::render();

        if ($this->paginatorClass) {
            $html = preg_replace('/(.+)(pagination)(.+)/i', '$1$2 ' . $this->paginatorClass . '$3', $html);
        }

        return $html;
    }
}
