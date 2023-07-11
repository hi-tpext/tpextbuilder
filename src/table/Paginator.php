<?php

namespace tpext\builder\table;

use think\Collection;
use think\paginator\driver\Bootstrap;
use tpext\builder\traits\HasDom;

class Paginator extends Bootstrap
{
    use HasDom;

    protected $paginatorClass = 'pagination-sm';

    protected $summary = true;

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
        if (!($items instanceof Collection)) {
            $items = Collection::make($items);
        }

        $this->items = $items;
        $this->reset();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function summary($val)
    {
        $this->summary = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function paginatorClass($val)
    {
        $this->paginatorClass = $val;
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

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getClass()
    {
        if (!$this->class) {
            $this->class = 'text-center';
        }

        return $this->class;
    }

    public function isEmpty()
    {
        if ($this->currentPage > 1) {
            return false;
        }
        return parent::isEmpty();
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        if (!$this->total) {
            return '';
        }

        $html = parent::render();

        if ($this->paginatorClass) {
            $html = preg_replace('/(.+)(pagination)(.+)/i', '$1$2 ' . $this->paginatorClass . '$3', $html);
        }

        if ($this->summary) {
            $a = ($this->currentPage - 1) * $this->listRows + 1;
            $b = $a - 1 + $this->items->count();
            if ($this->total != $this->items->count()) {
                $html = "<span class='pagination-summary'>" . __blang('bilder_paginator_summary', ['total' => $this->total, 'from' => $a, 'to' => $b]) . "</span>" . $html;
                if ($this->lastPage > 10) {
                    $gotoPage = "<li><a data-last='{$this->lastPage}' class='goto-page'>&nbsp;&nbsp;" . __blang('bilder_paginator_goto') . "&nbsp;&nbsp;</a></li>";
                    $html = preg_replace('/^(.+)<\/ul>$/', '$1' . $gotoPage . '</ul>', $html);
                }
            } else {
                $html = "<span class='pagination-summary'>" . __blang('bilder_paginator_total', ['total' => $this->total]) . "</span>" . $html;
            }
        }

        return $html;
    }
}
