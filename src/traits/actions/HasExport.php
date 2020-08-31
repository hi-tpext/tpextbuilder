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

        if ($this->dataModel) {
            $list = $this->dataModel->where($where)->order($sortOrder)->cursor();

            $data = [];

            foreach ($list as $li) {
                $data[] = $li;
            }

            // TODO 真正发挥cursor的性能优势

            $this->buildTable($data);
        } else {
            $data = $this->buildDataList();
        }

        $cols = $this->table->getCols();

        $displayers = $this->getDisplayers($cols);

        $__file_type__ = input('post.__file_type__', '');

        $logic = new Export;

        if ($__file_type__ == 'xls' || $__file_type__ == 'xlsx') {
            $logic->toExcel($this->pageTitle, $data, $displayers, $__file_type__);
        } else {
            $logic->toCsv($this->pageTitle, $data, $displayers);
        }
    }

    private function getDisplayers($cols, $displayers = [])
    {
        $displayer = null;

        foreach ($cols as $col) {

            $displayer = $col->getDisplayer();

            if ($displayer instanceof displayer\Fields) {
                $content = $displayer->getContent();
                $displayers = $this->getDisplayers($content->getCols(), $displayers);
                continue;
            }

            if (!$col instanceof TColumn) {
                continue;
            }

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

        return $displayers;
    }
}
