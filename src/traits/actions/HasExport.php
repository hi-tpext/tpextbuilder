<?php

namespace tpext\builder\traits\actions;

use tpext\builder\displayer;
use tpext\builder\logic\Export;

/**
 * 导出
 */

trait HasExport
{
    public function export()
    {
        $this->isExporting = true;
        $this->table = $this->builder()->table();
        $sortOrder = input('__sort__', $this->sortOrder ? $this->sortOrder : $this->getPk() . ' desc');

        $__ids__ = input('post.__ids__');

        if (!empty($__ids__)) {
            $where = [[$this->getPk(), 'in', array_filter(explode(',', $__ids__))]];
        } else {
            $where = $this->filterWhere();
        }

        $data = $this->dataModel->where($where)->order($sortOrder)->cursor();

        $this->buildTable();

        $cols = $this->table->getCols();

        $displayers = [];

        foreach ($cols as $col) {

            $displayer = $col->getDisplayer();

            if ($displayer instanceof displayer\Checkbox || $displayer instanceof displayer\MultipleSelect) {

                $displayer = (new displayer\Matches($displayer->getName(), $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof displayer\Radio) {

                $displayer = (new displayer\Match($displayer->getName(), $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof displayer\SwitchBtn) {

                $pair = $displayer->getPair();
                $options = [$pair[0] => '是', $pair[1] => '否'];
                $displayer = (new displayer\Match($displayer->getName(), $col->getLabel()))->options($options);
            }

            $displayers[] = $displayer;
        }

        $__file_type__ = input('post.__file_type__', '');

        $logic = new Export;

        if ($__file_type__ == 'xls' || $__file_type__ == 'xlsx') {
            $logic->toExcel($this->pageTitle, $data, $displayers, $__file_type__);
        } else {
            $logic->toCsv($this->pageTitle, $data, $displayers);
        }
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return array
     */
    protected function handler($data)
    {
        return $data;
    }
}
