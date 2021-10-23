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

    /**
     * 导出时分页大小
     *
     * @var integer
     */
    protected $exportPageszie = 10000;

    public function export()
    {
        ini_set('max_execution_time', 0);

        if ($path = input('get.path')) { //文件下载
            $filePath = app()->getRuntimePath() . 'export/' . $path;
            if (!is_file($filePath)) {
                exit('<h3>文件不存在</h3>' . 'runtime/export/' . $path);
            }

            $ftime = filectime($filePath) ?: 0;

            if (time() - $ftime > 24 * 60 * 60) {
                exit('<h3>文件已过期</h3>有效期为一天');
            }

            $name = preg_replace('/\d+\/(.+?)\.\w+$/', '$1', $path);

            return download($filePath, $name);
        }

        return $this->expordData();
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    protected function expordData()
    {
        request()->withPost(request()->get()); //兼容以post方式获取参数

        $this->isExporting = false;
        $this->table = $this->builder()->table();
        $data = [];
        $__ids__ = input('get.__ids__');

        $buildTable = false;
        $isOver = false;

        $total = 0;
        $page = 1;

        if ($this->asTreeList()) { //如果此模型使用了`tpext\builder\traits\TreeModel`,显示为树形结构
            $data = $this->dataModel->getLineData();
            $buildTable = true;
            $isOver = true;
        } else {

            if (!empty($__ids__)) {
                $where = [[$this->getPk(), 'in', array_filter(explode(',', $__ids__))]];
            } else {
                $where = $this->filterWhere();
            }
            $sortOrder = $this->getSortorder();
            $page = input('get.__page__/d', 1);
            $page = $page < 1 ? 1 : $page;

            $pagesize = input('get.__export_pageszie__/d', 0);
            $this->pagesize = $pagesize ?: $this->exportPageszie;

            $data = $this->buildDataList($where, $sortOrder, $page, $total);

            if ($data instanceof \Iterator) {
                $newArr = [];
                foreach ($data as $d) {
                    $newArr[] = $d;
                }
                $data = $newArr;
            }

            $rowSize = count($data);

            $this->isExporting = false;

            if ($total == -1) {
                $buildTable = false;
                $isOver =  $rowSize < $this->pagesize; //判断是否全部导出了
                //兼容旧的程序，
                //旧的`buildDataList`方法不传任何参数，所以不会改变$total的值。
                //如果是旧的`buildDataList`，会做更多事情，比如`buildTable`,`fill`,`paginator`,`sortOrder`等，
                //在此判断避免重复，
                //往后的代码中，`buildDataList`只处理数据，不涉及其他。
            } else {
                $buildTable = true;
                $isOver = $rowSize == $total || $rowSize < $this->pagesize; //判断是否全部导出了
            }
        }

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

        if ($buildTable) {
            $this->buildTable($data, true);
        }

        $cols = $this->table->getCols();

        $displayers = $this->getDisplayers($cols);

        $__file_type__ = input('get.__file_type__', '');

        $logic = new Export;

        $dir = app()->getRuntimePath() . 'export/' . date('Ymd') . '/';

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return json(['code' => 0, 'msg' => '创建目录失败']);
            }
        }

        $__filename__ = input('get.__filename__', date('Ymd-His') . mt_rand(100, 999));

        $pathinfo = [
            'dir' => $dir,
            'name' => $__filename__,
            'start' => $this->pagesize * ($page - 1)
        ];

        $resData = [];
        if ($__file_type__ == 'xls' || $__file_type__ == 'xlsx') {
            $resData = $logic->toExcel($this->pageTitle, $data, $displayers, $__file_type__, $pathinfo);
        } else if ($__file_type__ == 'csv') {
            $resData = $logic->toCsv($this->pageTitle, $data, $displayers, $pathinfo);
        } else {
            $resData = $this->exportTo($data, $displayers, $__file_type__, $pathinfo);
        }

        $builder = $this->builder('', '', 'export');
        $builder->clearRows();

        if ($resData['code'] == 1) {
            if ($isOver) {
                $path = $resData['data'];
                $name = preg_replace('/.+?([^\/]+)$/', '$1', $path);
                $url = url('export') . '?path=' . $path;

                $builder->content()->display('<div class="alert alert-success " role="alert" style="width:94%;margin:2%;">'
                    . '<script>function closeLayer(){var index = parent.layer.getFrameIndex(window.name);parent.layer.close(index);}</script>'
                    . '<p>文件已生成，点击下载：</p><a onclick="closeLayer()" target="_blank" href="' . $url . '">' . $name . '</a></div>');
            } else {
                $sumary = ($this->pagesize * $page) . '/' . $total;

                $page += 1;
                $req = request()->get();
                $req['__page__'] = $page;
                $req['__filename__'] = $__filename__;
                $req['__export_pageszie__'] = $this->pagesize;
                $url = url('export') . '?' . http_build_query($req);

                return  '<!DOCTYPE html><html lang="zh">'
                    . '<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no" /><title>导出</title>'
                    . '<style>html,body{width:100%;height:100%;padding:0;margin:0;overflow:hidden;}</style>'
                    . '</head><body>'
                    . '<div style="width:94%;height:100%;margin:0 2%;text-align:center;">'
                    . '<p style="padding:10px 20px;color:green;">生成数据中:' . $sumary . '...</p></div>'
                    . "<script>location.href='{$url}';</script>"
                    . '</body></html>';
            }
        } else {
            $builder->content()->display($data['msg']);
            $builder->content()->display('<div class="alert alert-danger " role="alert" style="width:94%;margin:2%;">'
                . '<p>导出失败，' . $data['msg'] . '</p></div>');
        }

        return $builder;
    }

    /**
     * Undocumented function
     * 其他类型的导出，若添加了其他类型的导出选项，需要重写控制器中的此方法，否则任然导出csv
     * @param array $data 数据
     * @param array $displayers 字段的field数组
     * @param string $fileType 其他文件类型
     * @param array $pathinfo 路径信息
     * @return array ['code' => 1, 'msg' => '文件已生成', 'data' => $fname]
     */
    protected function exportTo($data, $displayers, $__file_type__, $pathinfo)
    {
        $logic = new Export;
        return $logic->toCsv($this->pageTitle, $data, $displayers, $pathinfo);
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
