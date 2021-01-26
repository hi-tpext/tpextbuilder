<?php

namespace tpext\builder\traits\actions;

/**
 * 列表
 */

trait HasIndex
{
    use HasExport;
    use HasSelectPage;
    use HasLeftTree;

    public function index()
    {
        if (request()->isAjax()) {
            request()->withPost(request()->get()); //兼容以post方式获取参数
        }

        $builder = $this->builder($this->pageTitle, $this->indexText, 'index');

        $table = null;

        if ($this->treeModel && $this->treeKey) {

            $tree = null;

            if ($this->treeType == 'ztree') {
                $tree = $builder->ztree('1 left-tree');
            } else {
                $tree = $builder->jsTree('1 left-tree');
            }

            $tree->fill($this->treeModel->where($this->treeScope)->select(), $this->treeTextField, $this->treeIdField, $this->treeParentIdField, $this->treeRootText);

            $tree->trigger('.row-' . $this->treeKey);

            $tree->expandAll($this->treeExpandAll);

            $table = $builder->table('1 right-table');
        } else {
            $table = $builder->table();
        }

        $table->pk($this->getPk());
        $this->table = $table;
        $this->search = $table->getSearch();

        $this->buildSearch();
        $this->buildDataList();

        if (request()->isAjax()) {
            return $this->table->partial()->render();
        }

        return $builder->render();
    }
}
