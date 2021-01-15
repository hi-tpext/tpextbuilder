<?php

namespace tpext\builder\traits\actions;

use tpext\builder\displayer;
use tpext\builder\logic\Export;
use tpext\builder\table\TColumn;

/**
 * 导出
 */

trait HasExport
{
    /**
     * 导出时在这里面的是允许的，其余都不允许
     *
     * @var array
     */
    protected $exportOnly = [];

    /**
     * 导出时在这里面的是不允许的，其余都允许
     *
     * @var array
     */
    protected $exportExcept = [];

    public function export()
    {
        request()->withPost(request()->get());//兼容以post方式获取参数

        $this->isExporting = true;
        $this->table = $this->builder()->table();
        $sortOrder = input('__sort__', $this->sortOrder ? $this->sortOrder : $this->getPk() . ' desc');

        $__ids__ = input('__ids__');

        if (!empty($__ids__)) {
            $where = [[$this->getPk(), 'in', array_filter(explode(',', $__ids__))]];
        } else {
            $where = $this->filterWhere();
        }

        if ($this->dataModel) {
            if (method_exists($this->dataModel, 'asTreeList')) { //如果此模型使用了`tpext\builder\traits\TreeModel`,显示为树形结构
                $table = $this->table;

                $table->sortable([]);

                $data = $this->dataModel->getLineData();

                if ($this->isExporting) {
                    $__ids__ = input('__ids__');
                    if (!empty($__ids__)) {
                        $ids = explode(',', $__ids__);
                        $newd = [];
                        foreach ($data as $d) {
                            if (in_array($d['id'], $ids)) {
                                $newd[] = $d;
                            }
                        }
                        $data = $newd;
                    }
                }

                $this->buildTable($data);
            } else {
                $list = $this->dataModel->with($this->indexWith)->where($where)->order($sortOrder)->cursor();
                $data = [];

                foreach ($list as $li) {
                    $data[] = $li;
                }

                // TODO 真正发挥cursor的性能优势

                $this->buildTable($data);
            }
        } else {
            $data = $this->buildDataList();
        }

        $cols = $this->table->getCols();

        $displayers = $this->getDisplayers($cols);

        $__file_type__ = input('__file_type__', '');

        $logic = new Export;

        if ($__file_type__ == 'xls' || $__file_type__ == 'xlsx') {
            return $logic->toExcel($this->pageTitle, $data, $displayers, $__file_type__);
        } else if ($__file_type__ == 'csv') {
            return $logic->toCsv($this->pageTitle, $data, $displayers);
        } else {
            return $this->exportTo($data, $displayers, $__file_type__);
        }
    }

    /**
     * Undocumented function
     *
     * @param string $fileType 其他类型的导出
     * @return mixed
     */
    protected function exportTo($data, $displayers, $__file_type__)
    {
        $logic = new Export;
        return $logic->toCsv($this->pageTitle, $data, $displayers);
    }

    private function getDisplayers($cols, $displayers = [])
    {
        $displayer = null;

        $fieldName = '';

        foreach ($cols as $col) {

            $displayer = $col->getDisplayer();

            $fieldName = $displayer->getName();

            if (!empty($this->exportOnly) && !in_array($fieldName, $this->exportOnly)) {
                continue;
            }

            if (!empty($this->exportExcept) && in_array($fieldName, $this->exportExcept)) {
                continue;
            }

            if ($displayer instanceof displayer\Fields) {
                $content = $displayer->getContent();
                $displayers = $this->getDisplayers($content->getCols(), $displayers);
                continue;
            }

            if (!$col instanceof TColumn) {
                continue;
            }

            if ($displayer instanceof displayer\Checkbox || $displayer instanceof displayer\MultipleSelect) {

                $displayer = (new displayer\Matches($fieldName, $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof displayer\Radio) {

                $displayer = (new displayer\Match($fieldName, $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof displayer\SwitchBtn) {

                $pair = $displayer->getPair();
                $options = [$pair[0] => '是', $pair[1] => '否'];
                $displayer = (new displayer\Match($fieldName, $col->getLabel()))->options($options);
            }
            $displayers[] = $displayer;
        }

        return $displayers;
    }
}
