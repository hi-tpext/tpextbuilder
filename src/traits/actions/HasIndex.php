<?php

namespace tpext\builder\traits\actions;

/**
 * 列表
 */

trait HasIndex
{
    use HasExport;
    use HasSelectPage;

    public function index()
    {
        $builder = $this->builder($this->pageTitle, $this->indexText);

        $table = $builder->table();
        $table->pk($this->getPk());
        $this->table = $table;
        $this->search = $table->getSearch();

        $this->builSearch();
        $this->buildDataList();

        if (request()->isAjax()) {
            return $this->table->partial()->render();
        }

        return $builder->render();
    }
}
