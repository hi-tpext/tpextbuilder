<?php

namespace tpext\builder\traits\actions;

use tpext\builder\logic\Export;
/**
 * 导出
 */

trait HasExport
{
    public function export()
    {
        $this->isExporting = true;

        $builder = $this->builder();
        $this->table = $builder->table();
        $this->buildDataList();

        $cols = $this->table->getCols();
        $displayers = [];

        foreach ($cols as $col) {

            $displayer = $col->getDisplayer();

            if ($displayer instanceof \tpext\builder\displayer\Checkbox || $displayer instanceof \tpext\builder\displayer\MultipleSelect) {

                $displayer = (new \tpext\builder\displayer\Matches($displayer->getName(), $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof \tpext\builder\displayer\Radio) {

                $displayer = (new \tpext\builder\displayer\Match($displayer->getName(), $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof \tpext\builder\displayer\SwitchBtn) {

                $pair = $displayer->getPair();
                $options = [$pair[0] => '是', $pair[1] => '否'];
                $displayer = (new \tpext\builder\displayer\Match($displayer->getName(), $col->getLabel()))->options($options);
            }

            $displayers[] = $displayer;
        }

        $__file_type__ = input('post.__file_type__', '');

        $data = $this->table->getData();
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
