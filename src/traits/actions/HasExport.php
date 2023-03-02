<?php

namespace tpext\builder\traits\actions;

use tpext\think\App;
use tpext\builder\displayer;
use tpext\builder\logic\Export;
use tpext\builder\common\Module;
use tpext\builder\table\TColumn;

/**
 * 导出
 */

trait HasExport
{
    /**
     * 导出时在这里面的是默认的，其余都不允许
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

    /**
     * 只导出用户前端页面选择的列
     *
     * @var bool|null
     */
    protected $exportOnlyChoosedColumns = null;

    public function export()
    {
        ini_set('max_execution_time', 0);

        if ($path = input('get.path')) { //文件下载
            $filePath = App::getRuntimePath() . 'export/' . $path;
            if (!is_file($filePath)) {
                return '<h3>文件不存在</h3>' . 'runtime/export/' . $path;
            }

            $ftime = filectime($filePath) ?: 0;

            if (time() - $ftime > 24 * 60 * 60) {
                return '<h3>文件已过期</h3>有效期为一天';
            }

            $name = preg_replace('/\d+\/(.+?\.\w+)$/', '$1', $path);

            return download($filePath, $name);
        }

        if (is_null($this->exportOnlyChoosedColumns)) { //null 时读全局配置
            $this->exportOnlyChoosedColumns = Module::config('export_only_choosed_columns', 1) == 1;
        }

        if ($this->exportOnlyChoosedColumns) {
            $__columns__ = input('get.__columns__', '*');
            if ($__columns__) {
                $this->exportOnly  = explode(',', $__columns__);
            }
        }

        request()->withPost(request()->get()); //兼容以post方式获取参数

        $this->isExporting = true;
        $this->table = $this->builder()->table();
        $data = [];
        $__ids__ = input('get.__ids__');

        if ($this->asTreeList()) { //如果此模型使用了`tpext\builder\traits\TreeModel`,显示为树形结构
            $data = $this->dataModel->getLineData();

            if (!empty($__ids__)) {
                $ids = explode(',', $__ids__);
                $newd = [];
                foreach ($data as $d) {
                    if (in_array($d[$this->getPk()], $ids)) {
                        $newd[] = $d;
                    }
                }
                $data = $newd;
            }
        } else {

            if (!empty($__ids__)) {
                $where = [[$this->getPk(), 'in', array_filter(explode(',', $__ids__))]];
            } else {
                $where = $this->filterWhere();
            }
            $sortOrder = $this->getSortorder();
            $this->pagesize = 99999999;

            $total = -1;
            $data = $this->buildDataList($where, $sortOrder, 1, $total);
        }

        $empty = [];
        $this->buildTable($empty, true);

        $cols = $this->table->getCols();

        $displayers = $this->getDisplayers($cols);

        $__file_type__ = input('get.__file_type__', '');

        $logic = new Export;

        $buildTable = function ($data) {
            $this->buildTable($data, true);
        };

        if ($__file_type__ == 'xls' || $__file_type__ == 'xlsx') {
            return $logic->toExcel($this->pageTitle, $data, $displayers, $__file_type__, $buildTable);
        } else if ($__file_type__ == 'csv') {
            return $logic->toCsv($this->pageTitle, $data, $displayers, $buildTable);
        } else {
            return $this->exportTo($data, $displayers, $__file_type__, $buildTable);
        }
    }

    /**
     * Undocumented function
     *
     * @param array|\Iterator $data
     * @param array $displayers
     * @param string $__file_type__
     * @param \Closure $buildTable
     * @return mixed
     */
    protected function exportTo($data, $displayers, $__file_type__, $buildTable)
    {
        $logic = new Export;
        return $logic->toCsv($this->pageTitle, $data, $displayers, $buildTable);
    }

    /**
     * Undocumented function
     *
     * @param array $cols
     * @param array $displayers
     * @return array
     */
    private function getDisplayers($cols, $displayers = [])
    {
        $displayer = null;

        $fieldName = '';

        foreach ($cols as $col) {

            $displayer = $col->getDisplayer();

            $fieldName = $displayer->getOriginName();

            if (!empty($this->exportOnly) && $this->exportOnly[0] != '*' && !in_array($fieldName, $this->exportOnly)) {
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
            } else if ($displayer instanceof displayer\Radio || $displayer instanceof displayer\Select) {

                $displayer = (new displayer\Matche($fieldName, $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof displayer\SwitchBtn) {

                $pair = $displayer->getPair();
                $options = [$pair[0] => '是', $pair[1] => '否'];
                $displayer = (new displayer\Matche($fieldName, $col->getLabel()))->options($options);
            }
            $displayers[] = $displayer;
        }

        return $displayers;
    }
}
