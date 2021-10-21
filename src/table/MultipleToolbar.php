<?php

namespace tpext\builder\table;

use tpext\builder\common\Builder;
use tpext\builder\common\Toolbar;

class MultipleToolbar extends Toolbar
{
    protected $useSearch = false;

    protected $btnSearch = null;

    protected $useExport = true;

    protected $useChooseColumns = ['*'];

    protected $btnExport = null;

    protected $tableCols = [];

    /**
     * Undocumented function
     * 
     * @param array $cols
     * @return $this
     */
    public function setTableCols($cols)
    {
        $this->tableCols = $cols;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function useLayerAll($val, $size = [])
    {
        foreach ($this->elms as $elm) {
            $elm->useLayer($val, $size);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useSearch($val = true)
    {
        $this->useSearch = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useExport($val = true)
    {
        $this->useExport = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean|array|string $val 默认显示的字段，false则禁用
     * @return $this
     */
    public function useChooseColumns($val = ['*'])
    {
        if ($val === true) {
            $val = ['*'];
        } else if (empty($val)) {
            $val = [];
        } else if (is_string($val)) {
            $val = explode(',', $val);
        }

        $this->useChooseColumns = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getChooseColumns()
    {
        return is_array($this->useChooseColumns) ? $this->useChooseColumns : [];
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        if (empty($this->elms)) {
            $this->buttons();
        }

        if ($this->useChooseColumns) {
            $items = [];

            foreach ($this->tableCols as $col) {
                $name = $col->getName();
                $checked = $this->useChooseColumns[0] == '*' || in_array($name, $this->useChooseColumns);
                $items[] = [
                    'key' => $col->getName(),
                    'label' => preg_replace('/<[bh]r\s*\/?>/i','',$col->getLabel()),
                    'icon' => $checked ? 'mdi-checkbox-marked-outline' : 'mdi-checkbox-blank-outline',
                    'url' => '#',
                    'attr' => '',
                    'class' => $checked ? 'checked' : '',
                ];
            }

            $this->btnChooseColumns($items);
        }

        if ($this->useExport && !$this->btnExport) {
            $items = ['csv' => 'csv文件'];

            if (class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet') || class_exists('\\PHPExcel')) {
                $items = array_merge($items, [
                    'xls' => 'xls文件',
                    'xlsx' => 'xlsx文件',
                ]);
            }

            $this->btnExports($items);
        }

        if ($this->useSearch && !$this->btnSearch) {
            $this->btnToggleSearch();
        }

        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function buttons()
    {
        $this->btnAdd();
        $this->btnDelete();
        $this->btnRefresh();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnAdd($url = '', $label = '添加', $class = 'btn-primary', $icon = 'mdi-plus', $attr = '')
    {
        if (empty($url)) {
            $url = url('add')->__toString();
        }
        $this->linkBtn('add', $label)->href($url)->icon($icon)->addClass($class)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnDelete($postUrl = '', $label = '删除', $class = 'btn-danger', $icon = 'mdi-delete', $attr = '', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = url('delete')->__toString();
        }
        $this->linkBtn('delete', $label)->postChecked($postUrl, $confirm)->addClass($class)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnDisable($postUrl = '', $label = '禁用', $class = 'btn-warning', $icon = 'mdi-block-helper', $attr = '', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = url('enable', ['state' => 0])->__toString();
        }
        $this->linkBtn('disable', $label)->postChecked($postUrl, $confirm)->addClass($class)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnEnable($postUrl = '', $label = '启用', $class = 'btn-success', $icon = 'mdi-check', $attr = '', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = url('enable', ['state' => 1])->__toString();
        }
        $this->linkBtn('enable', $label)->postChecked($postUrl, $confirm)->addClass($class)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $enableTitle
     * @param string $disableTitle
     * @return $this
     */
    public function btnEnableAndDisable($enableTitle = '启用', $disableTitle = '禁用')
    {
        $this->btnEnable()->getCurrent()->attr('title="' . $enableTitle . '"')->label($enableTitle);
        $this->btnDisable()->getCurrent()->attr('title="' . $disableTitle . '"')->label($disableTitle);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnRefresh($label = '', $class = 'btn-cyan', $icon = 'mdi-refresh', $attr = 'title="刷新"')
    {
        $this->linkBtn('refresh', $label)->addClass($class)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnToggleSearch($label = '', $class = 'btn-secondary', $icon = 'mdi-magnify', $attr = 'title="搜索"')
    {
        $this->linkBtn('search', $label)->addClass($class)->icon($icon)->addClass('hidden')->addAttr($attr);

        $this->btnSearch = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $afterSuccessUrl
     * @param string|array acceptedExts
     * @param array layerSize
     * @param int fileSize MB
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param string $driver
     * @return $this
     */
    public function btnImport($afterSuccessUrl = '', $acceptedExts = "rar,zip,doc,docx,xls,xlsx,ppt,pptx,pdf", $layerSize = ['800px', '550px'], $fileSize = '20', $label = '导入', $class = 'btn-pink', $icon = 'mdi-cloud-upload', $attr = 'title="上传文件"', $driver = '\\tpext\\builder\\logic\\LocalStorage')
    {
        if (empty($afterSuccessUrl)) {
            $afterSuccessUrl = url('/admin/import/afterSuccess')->__toString();
        }

        if (is_array($acceptedExts)) {
            $acceptedExts = implode(',', $acceptedExts);
        }

        $afterSuccessUrl = urlencode($afterSuccessUrl);

        $afterSuccessUrl = preg_replace('/(.+?)(\.html)?$/', '$1', $afterSuccessUrl);

        $importpagetoken = session('importpagetoken') ? session('importpagetoken') : md5('importpagetoken' . time() . uniqid());

        session('importpagetoken', $importpagetoken);

        $driver = str_replace('\\', '-', $driver);

        $pagetoken = md5($importpagetoken . $acceptedExts . $fileSize);

        $url = url('/admin/import/page')->__toString() . '?successUrl=' . $afterSuccessUrl . '&acceptedExts=' . $acceptedExts . '&fileSize=' . $fileSize . '&pageToken=' . $pagetoken . '&driver=' . $driver;

        $this->linkBtn('import', $label)->useLayer(true, $layerSize)->href($url)->icon($icon)->addClass($class)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnExport($postUrl = '', $label = '导出', $class = 'btn-default', $icon = 'mdi-export', $attr = 'title="导出"')
    {
        if (empty($postUrl)) {
            $postUrl = url('export');
        }

        $this->btnExport = true;

        if (!Builder::checkUrl($postUrl)) {
            return $this;
        }

        $this->linkBtn('export', $label)->addClass($class)->icon($icon)->addAttr($attr . ' data-export-url="' . $postUrl . '"');
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $items ['csv' => 'CSV文件']
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnExports($items, $postUrl = '', $label = '导出', $class = 'btn-secondary', $icon = 'mdi-export', $attr = 'title="导出"')
    {
        if (empty($postUrl)) {
            $postUrl = url('export');
        }

        $this->btnExport = true;

        if (!Builder::checkUrl($postUrl)) {
            return $this;
        }

        $this->dropdownBtns('exports', $label)->items($items)->groupClass('drp-exports')->addClass($class)->icon($icon)
            ->addAttr($attr . ' data-export-url="' . $postUrl . '"')->pullRight();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $items
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnChooseColumns($items, $label = '显示', $class = 'btn-secondary', $icon = 'mdi-grid', $attr = 'title="选择要显示的列"')
    {
        $this->dropdownBtns('choose_columns', $label)->items($items)->groupClass('drp-choose_columns')
            ->addClass($class)->icon($icon)->addAttr($attr)->pullRight();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnLink($url, $label = '', $class = 'btn-secondary', $icon = 'mdi-checkbox-marked-outline', $attr = '')
    {
        $action = preg_replace('/.+?\/(\w+)(\.\w+)?$/', '$1', $url, -1, $count);

        if (!$count) {
            $action = mt_rand(10, 99);
        }

        $this->linkBtn($action, $label)->href($url)->icon($icon)->addClass($class)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     *
     */
    public function btnPostChecked($url, $label = '', $class = 'btn-secondary', $icon = 'mdi-checkbox-marked-outline', $attr = '', $confirm = true)
    {
        $action = preg_replace('/.+?\/(\w+)(\.\w+)?$/', '$1', $url, -1, $count);

        if (!$count) {
            $action = mt_rand(10, 99);
        }

        $this->linkBtn($action, $label)->postChecked($url, $confirm)->addClass($class)->icon($icon)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     *
     */
    public function btnOpenChecked($url, $label = '', $class = 'btn-secondary', $icon = 'mdi-checkbox-marked-outline', $attr = '')
    {
        $action = preg_replace('/.+?\/(\w+)(\.\w+)?$/', '$1', $url, -1, $count);

        if (!$count) {
            $action = mt_rand(10, 99);
        }

        $this->linkBtn($action, $label)->openChecked($url)->addClass($class)->icon($icon)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $items
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnActions($items, $label = '操作', $class = 'btn-secondary', $icon = '', $attr = 'title="批量操作"')
    {
        $this->multipleActions('multiple_actions', $label)->items($items)->addClass($class)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function html($val)
    {
        parent::html($val);
        return $this;
    }

    /**
     * Undocumented function
     * 换行
     * @return $this
     */
    public function br()
    {
        parent::html('<br/>');
        return $this;
    }
}
