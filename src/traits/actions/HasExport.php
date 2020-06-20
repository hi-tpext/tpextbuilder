<?php

namespace tpext\builder\traits\actions;

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
        $this->table->pk($this->getPk());
        $this->buildDataList();

        $cols = $this->table->getCols();
        $displayers = [];

        foreach ($cols as $col) {

            $displayer = $col->getDisplayer();

            if ($displayer instanceof \tpext\builder\displayer\Checkbox) {
                $displayer = (new \tpext\builder\displayer\Matches($displayer->getName(), $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof \tpext\builder\displayer\Radio) {
                $displayer = (new \tpext\builder\displayer\Match($displayer->getName(), $col->getLabel()))->options($displayer->getOptions());
            } else if ($displayer instanceof \tpext\builder\displayer\SwitchBtn) {
                $pair = $displayer->getPair();
                $options = [$pair[0] => '是', $pair[1] => '否'];
                $displayer = (new \tpext\builder\displayer\Matches($displayer->getName(), $col->getLabel()))->options($options);
            }

            $displayers[] = $displayer;
        }

        $sortOrder = input('__sort__', $this->sortOrder ? $this->sortOrder : $this->getPk() . ' desc');

        $__ids__ = input('post.__ids__');

        $__file_type__ = input('post.__file_type__', 'csv');

        $where = [];

        if (!empty($__ids__)) {
            $where[] = ['id', 'in', $__ids__];
        } else {
            $where = $this->filterWhere();
        }

        $data = $this->dataModel->where($where)->order($sortOrder)->select();

        $data = $this->handler($data);

        $this->buildTable($data);

        if ($__file_type__ == 'csv') {
            $this->toCsv($data, $displayers);
        } else if ($__file_type__ == 'xls' || $__file_type__ == 'xlsx') {
            $this->toExcel($data, $displayers, $__file_type__);
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

    /**
     * Undocumented function
     *
     * @param array $data
     * @param array $displayers
     * @return void
     */
    protected function toCsv($data, $displayers)
    {
        ob_end_clean();

        $fname = '';
        if (request()->isAjax()) {
            $dir = './uploads/export/' . date('Ymd') . '/';

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $fname = $dir . $this->pageTitle . "-" . date('Ymd-His') . ".csv";
            $fp = fopen($fname, 'w');
        } else {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . $this->pageTitle . "-" . date('Ymd-His') . ".csv");
            header('Cache-Control: max-age=0');
            $fp = fopen('php://output', 'a');
        }

        $headerData = [];

        foreach ($displayers as $key => $displayer) {
            $label = $displayer->getLabel();
            $label = preg_replace('/id/i', '编号', $label);
            $headerData[$key] = mb_convert_encoding($label, "GBK", "UTF-8");
        }

        fputcsv($fp, $headerData);

        //来源网络
        $num = 0;
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 5000;
        $row = null;
        $text = null;
        foreach ($data as $d) {
            $num++;
            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
                $num = 0;
            }
            $row = [];
            foreach ($displayers as $key => $displayer) {
                $text = $displayer->fill($d)->renderValue();
                $text = preg_replace('/<[bh]r\s*\/?>/im', ' | ', $text);
                $text = preg_replace('/<([a-zA-z]+?)\s+[^<>]*?>(.+?)<\/\1>/im', '$2', $text);
                $row[$key] = mb_convert_encoding($text, "GBK", "UTF-8");
            }

            fputcsv($fp, $row);
        }
        unset($row, $text);
        fclose($fp);
        if ($fname) {
            exit(json_encode(['code' => 1, 'msg' => '文件已生成', 'data' => ltrim($fname, '.')]));
        }
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @param array $displayers
     * @return void
     */
    public function toExcel($data, $displayers, $type = 'xls')
    {
        $obj = new \PHPExcel();

        // 以下内容是excel文件的信息描述信息
        $obj->getProperties()->setCreator(''); //设置创建者
        $obj->getProperties()->setLastModifiedBy(''); //设置修改者
        $obj->getProperties()->setTitle(''); //设置标题
        $obj->getProperties()->setSubject(''); //设置主题
        $obj->getProperties()->setDescription(''); //设置描述
        $obj->getProperties()->setKeywords(''); //设置关键词
        $obj->getProperties()->setCategory(''); //设置类型

        // 设置当前sheet
        $obj->setActiveSheetIndex(0);

        // 设置当前sheet的名称
        $obj->getActiveSheet()->setTitle('faultlist');

        // 列标
        $list = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', //够用就行
        ];

        // 填充第一行数据

        foreach ($displayers as $k => $displayer) {
            $label = $displayer->getLabel();
            $label = preg_replace('/id/i', '编号', $label);
            $obj->getActiveSheet()
                ->setCellValue($list[$k] . '1', $label);
        }
        $num = 0;
        $text = null;
        $c = 0;
        foreach ($data as $d) {
            $c = 0;
            foreach ($displayers as $key => $displayer) {
                $text = $displayer->fill($d)->renderValue();
                $text = preg_replace('/<[bh]r\s*\/?>/im', ' | ', $text);
                $text = preg_replace('/<([a-zA-z]+?)\s+[^<>]*?>(.+?)<\/\1>/im', '$2', $text);
                $obj->getActiveSheet()->setCellValue($list[$c] . ($num + 2), $text, \PHPExcel_Cell_DataType::TYPE_STRING); //将其设置为文本格式
                $c++;
            }
            $num++;
        }
        unset($text);
        // 导出
        ob_end_clean();
        if ($type == 'xls') {
            $objWriter = new \PHPExcel_Writer_Excel5($obj);
            if (request()->isAjax()) {
                $dir = './uploads/export/' . date('Ymd') . '/';

                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $fname = $dir . $this->pageTitle . "-" . date('Ymd-His') . ".xls";
                $objWriter->save($fname);

                exit(json_encode(['code' => 1, 'msg' => '文件已生成', 'data' => ltrim($fname, '.')]));

            } else {
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $this->pageTitle . "-" . date('Ymd-His') . '.xls');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                exit;
            }

        } elseif ($type == 'xlsx') {
            $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
            if (request()->isAjax()) {
                $dir = './uploads/export/' . date('Ymd') . '/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $fname = $dir . $this->pageTitle . "-" . date('Ymd-His') . ".xlsx";
                $objWriter->save($fname);

                exit(json_encode(['code' => 1, 'msg' => '文件已生成', 'data' => ltrim($fname, '.')]));

            } else {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $this->pageTitle . "-" . date('Ymd-His') . '.xlsx');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                exit;
            }
            exit;
        }
    }
}
