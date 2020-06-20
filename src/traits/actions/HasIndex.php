<?php

namespace tpext\builder\traits\actions;

/**
 * 列表
 */

trait HasIndex
{
    use HasExport;

    public function index()
    {
        $builder = $this->builder($this->pageTitle, $this->indexText);

        $this->table = $builder->table();
        $this->table->pk($this->getPk());
        $this->search = $this->table->getSearch();

        $this->table->getToolbar()->hasExport(true);

        $this->builSearch();
        $this->buildDataList();

        if (request()->isAjax()) {
            return $this->table->partial()->render();
        }

        return $builder->render();
    }
}
