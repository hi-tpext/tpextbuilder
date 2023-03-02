<?php

namespace tpext\builder\logic;

use think\Collection;
use tpext\think\App;

class Export
{
    /**
     * Undocumented variable
     *
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet|\PHPExcel_Worksheet
     */
    private $worksheet = null;

    /**
     * Undocumented function
     * @param string $title
     * @param array|Collection|\Generator $data
     * @param array $displayers
     * @param \Closure|null $buildTable
     * @return mixed
     */
    public function toCsv($title, $data, $displayers, $buildTable = null)
    {
        $title = str_replace([' ', '.', '!', '@', '＃', '$', '%', '^', '&', '*', '(', ')', '{', '}', '【', '】', '[', ']'], '', trim($title));

        if (ob_get_contents()) {
            ob_end_clean();
        }

        $fname = '';
        if (request()->isAjax()) {
            $dir = App::getRuntimePath() . 'export/' . date('Ymd') . '/';

            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    return json(['code' => 0, 'msg' => '创建目录失败']);
                }
            }

            $fname = $dir . $title . "-" . date('Ymd-His') . mt_rand(100, 999) . ".csv";
            $fp = fopen($fname, 'w');
        } else {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . $title . "-" . date('Ymd-His')  . ".csv");
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
            if ($buildTable) {
                $buildTable([$d]);
            }

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
                $text = $this->replace($text);
                if (is_numeric($text) && !strstr($text, '.')) {
                    $text .= "\t";
                }
                $row[$key] = mb_convert_encoding($text, "GBK", "UTF-8");
            }
            fputcsv($fp, $row);
        }
        unset($row, $text);
        fclose($fp);
        if ($fname) {
            $file = str_replace(App::getRuntimePath() . 'export/', '', $fname);
            return json(['code' => 1, 'msg' => '文件已生成', 'data' => url('export') . '?path=' . $file]);
        }
    }

    private function replace($text)
    {
        $text = preg_replace('/<[bh]r\s*\/?>/im', ' | ', $text);
        $text = preg_replace('/<i\s+[^<>]*?class=[\'\"]\w+\s+(\w+\-[\w\-]+)[\'\"][^<>]*?>(.*?)<\/i>/im', '$1', $text);
        $text = preg_replace('/<([a-zA-z]+?)\s+[^<>]*?>(.*?)<\/\1>/im', '$2', $text);
        $text = str_replace(['&nbsp;', '&gt;', '&lt;'], [' ', '>', '<'], $text);

        return $text;
    }

    /**
     * Undocumented function
     * @param string $title
     * @param array|Collection|\Generator $data
     * @param array $displayers
     * @param string $type
     * @param \Closure|null $buildTable
     * @return mixed
     */
    public function toExcel($title, $data, $displayers, $type, $buildTable = null)
    {
        $title = str_replace([' ', '.', '!', '@', '＃', '$', '%', '^', '&', '*', '(', ')', '{', '}', '【', '】', '[', ']'], '', trim($title));

        if (ob_get_contents()) {
            ob_end_clean();
        }
        $lib = '';

        $obj = null;
        if (class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $obj = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $this->worksheet = $obj->getActiveSheet();
            $lib = 'PhpOffice';
        } else if (class_exists('\\PHPExcel')) {
            $obj = new \PHPExcel();
            $this->worksheet = $obj->getActiveSheet();
            $lib = 'PHPExcel';
        } else {
            return json(['code' => 0, 'msg' => '未安装PHPExcel或PhpOffice', 'data' => '']);
        }

        $this->worksheet->setTitle($title);

        // 列标
        $list = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', //够用就行
        ];

        // 填充第一行数据

        foreach ($displayers as $k => $displayer) {
            $label = $displayer->getLabel();
            $label = preg_replace('/id/i', '编号', $label);
            $this->worksheet->setCellValue($list[$k] . '1', $label);
        }
        $num = 0;
        $text = null;
        $c = 0;
        foreach ($data as $d) {
            if ($buildTable) {
                $buildTable([$d]);
            }

            $c = 0;
            foreach ($displayers as $key => $displayer) {
                $text = $displayer->fill($d)->renderValue();
                $text = $this->replace($text);
                if ($lib == 'PhpOffice') {
                    $this->worksheet->setCellValueExplicit($list[$c] . ($num + 2), $text, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $this->worksheet->setCellValueExplicit($list[$c] . ($num + 2), $text, \PHPExcel_Cell_DataType::TYPE_STRING); //将其设置为文本格式
                }

                $c++;
            }
            $num++;
        }

        unset($text);
        $objWriter = null;
        if ($type == 'xls') {
            if ($lib == 'PhpOffice') {
                $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xls($obj);
                $objWriter->setPreCalculateFormulas(false);
            } else {
                $objWriter = new \PHPExcel_Writer_Excel5($obj);
                $objWriter->setPreCalculateFormulas(false);
            }

            if (request()->isAjax()) {
                $dir = App::getRuntimePath() . 'export/' . date('Ymd') . '/';

                if (!is_dir($dir)) {
                    if (!mkdir($dir, 0755, true)) {
                        return json(['code' => 0, 'msg' => '创建目录失败']);
                    }
                }

                $fname = $dir . $title . "-" . date('Ymd-His') . mt_rand(100, 999) . ".xls";
                $objWriter->save($fname);

                $file = str_replace(App::getRuntimePath() . 'export/', '', $fname);
                return json(['code' => 1, 'msg' => '文件已生成', 'data' => url('export') . '?path=' . $file]);
            } else {
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $title . "-" . date('Ymd-His')  . '.xls');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
            }
        } else {
            if ($lib == 'PhpOffice') {
                $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($obj);
                $objWriter->setPreCalculateFormulas(false);
            } else {
                $objWriter = new \PHPExcel_Writer_Excel2007($obj);
                $objWriter->setPreCalculateFormulas(false);
            }

            if (request()->isAjax()) {
                $dir = App::getRuntimePath() . 'export/' . date('Ymd') . '/';
                if (!is_dir($dir)) {
                    if (!mkdir($dir, 0755, true)) {
                        return json(['code' => 0, 'msg' => '创建目录失败']);
                    }
                }

                $fname = $dir . $title . "-" . date('Ymd-His') . mt_rand(100, 999) . ".xlsx";
                $objWriter->save($fname);

                $file = str_replace(App::getRuntimePath() . 'export/', '', $fname);
                return json(['code' => 1, 'msg' => '文件已生成', 'data' => url('export') . '?path=' . $file]);
            } else {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $title . "-" . date('Ymd-His')  . '.xlsx');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param string $title
     * @param array|Collection|\Generator $data
     * @param string $codeField 数据中要编码的字段名
     * @param integer $QR_ECLEVEL QR_ECLEVEL_L=0,QR_ECLEVEL_M=1,QR_ECLEVEL_Q=2,QR_ECLEVEL_H=3;
     * @param integer $size 二维码大小
     * @param bool $requireLib 是否需要再引入`phpqrcode`库，如果在调用此方法前已经[require_once]引入了相关库，则设置为`false`
     * @param \Closure $buildTable
     * @return mixed
     */
    public function toQrcode($title = '二维码', $data = [], $codeField = 'code', $QR_ECLEVEL = 3, $size = 5, $requireLib = true, $buildTable = null)
    {
        if ($requireLib) {
            require_once App::getRootPath() . 'extend/phpqrcode/phpqrcode.php';
        }

        $time = date('YmdHis') . '_' . mt_rand(100, 999);

        $dir = App::getRuntimePath() . 'export/' . date('Ymd') . '/';
        $dir2 = App::getRuntimePath() .  'export/' . date('Ymd') . '/qr' . $time . '/';

        if (!is_dir($dir2)) {
            if (!mkdir($dir2, 0755, true)) {
                return json(['code' => 0, 'msg' => '创建目录失败']);
            }
        }

        $files = [];
        foreach ($data as $d) {
            if ($buildTable) {
                $buildTable([$d]);
            }

            $fillename = $dir2 . $d[$codeField] . '.png';
            \QRcode::png($d[$codeField], $fillename, $QR_ECLEVEL, $size);
            $files[] = $fillename;
        }

        $zip = new \ZipArchive();

        $zfile = $dir . $title . '_共' . count($data) . '个' . date('Ymd-His') . mt_rand(100, 999) . '.zip';

        $zip->open($zfile, \ZipArchive::CREATE);  //打开压缩包

        foreach ($files as $imgFile) {
            $zip->addFile($imgFile, basename($imgFile));  //向压缩包中添加文件
        }

        $zip->close(); //关闭压缩包

        try {
            $this->deleteDir($dir2);
        } catch (\Exception $e) {
            trace($e->getMessage());
        }

        $file = str_replace(App::getRuntimePath() . 'export/', '', $zfile);

        if (request()->isAjax()) {
            return json(['code' => 1, 'msg' => '文件已生成', 'data' => url('export') . '?path=' . $file]);
        } else {
            return redirect(url('export') . '?path=' . $file);
        }
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return mixed
     */
    public function deleteDir($path)
    {
        if (is_dir($path)) {

            $dir = opendir($path);

            while (false !== ($file = readdir($dir))) {

                if (($file != '.') && ($file != '..')) {

                    $sonDir = $path . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($sonDir)) {
                        $this->deleteDir($sonDir);
                    } else {
                        unlink($sonDir);
                    }
                }
            }
            closedir($dir);
            rmdir($path);
        }
    }
}
