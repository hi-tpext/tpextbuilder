<?php

namespace tpext\builder\traits;

trait TreeModel
{
    /**
     * 默认查询条件
     *
     * @var array
     */
    protected $treeScope = []; //如 [['enable', 'eq', 1]]

    /**
     * 树 Text 字段 如 'name'
     *
     * @var string
     */
    protected $treeTextField = 'name';

    /**
     * 树 id 字段
     *
     * @var string
     */
    protected $treeIdField = 'id';

    /**
     * 树 上级id字段 如 parend_id pid
     *
     * @var string
     */
    protected $treeParentIdField = 'parent_id';

    /**
     * 排序字段　如 sort
     *
     * @var string
     */
    protected $treeSortField = '';

    /**
     * 多维结构数据
     *
     * @var array
     */
    protected $treeData = [];

    /**
     * 一维结构数据
     *
     * @var array
     */
    protected $lineData = [];

    /**
     * 一维结构key:value数据
     *
     * @var array
     */
    protected $optionsData = [];

    protected $lineType = 0;

    protected $except = 0;

    /**
     * 是否显示为树行
     *
     * @return boolean
     */
    final public function asTreeList()
    {
        return true;
    }

    /**
     * 初始化
     *
     * @return void
     */
    protected function treeInit()
    {
        //如
        // $this->treeScope = [['enable', 'eq', 1]];
        // $this->treeTextField = 'title';
        // $this->treeIdField = 'id';
        // $this->treeParentIdField = 'pid';
        // $this->treeSortField ='sort';
    }

    /**
     * 获取多维结构数据 text当&nbsp;(适合放列表页面)
     *
     * @return array
     */
    public function getLineData()
    {
        $this->treeInit();
        $this->lineType = 1;
        $this->except = 0;

        if (!empty($this->lineData)) {
            return $this->lineData;
        }

        $this->lineData = [];
        $this->builderData();

        return $this->lineData;
    }

    /**
     * 获取多维结构数据 text当&nbsp;(适合放列表页面)
     *
     * @param integer $except　排除的(select中的当前id)
     * @return array
     */
    public function getOptions($except = 0)
    {
        $this->treeInit();
        $this->lineType = 2;
        $this->except = $except;

        if (!empty($this->optionsData)) {
            return $this->optionsData;
        }

        if (empty($this->lineData)) {
            $this->lineData = [];
            $this->builderData();
        }

        $this->optionsData = [];
        foreach ($this->lineData as $d) {
            $this->optionsData[$d['__id__']] = $d['__text__'];
        }

        return $this->optionsData;
    }

    /**
     * 获取多维结构数据
     *
     * @return array
     */
    public function getTreeData()
    {
        $this->treeInit();
        $this->except = 0;

        if (!empty($this->treeData)) {
            return $this->treeData;
        }

        $this->treeData = [];
        $this->builderData();

        return $this->treeData;
    }

    protected function builderData()
    {
        $allTreeData = static::where($this->treeScope)->select();

        $roots = [];

        foreach ($allTreeData as $d) {

            if ($d[$this->treeParentIdField] != 0) {
                continue;
            }

            if ($d[$this->treeIdField] == $this->except) {
                continue;
            }

            $roots[] = $d;
        }

        unset($d);

        if ($this->treeSortField) {
            $volume = [];
            foreach ($roots as $key => $row) {
                $volume[$key] = $row[$this->treeSortField];
            }
            array_multisort($volume, SORT_ASC, $roots); //升序排列
        }

        foreach ($roots as $d) {

            $this->treeData[] = $d;
            $this->lineData[] = $d;

            $d['__deep__'] = 0;
            $d['__id__'] = $d[$this->treeIdField];
            $d['__text__'] = $d[$this->treeTextField];
            $d['__children__'] = $this->getChildrenData($allTreeData, $d[$this->treeIdField]);

        }
    }

    /**
     * Undocumented function
     *
     * @param array $allTreeData
     * @param integer $pid
     * @param integer $deep
     * @return array
     */
    protected function getChildrenData($allTreeData, $pid, $deep = 1)
    {

        $data = [];
        $deep += 1;

        foreach ($allTreeData as $d) {

            if ($d[$this->treeIdField] == $this->except) {
                continue;
            }

            if ($d[$this->treeParentIdField] == $pid) {

                $data[] = $d;
            }
        }

        if ($this->treeSortField) {
            $volume = [];

            foreach ($data as $key => $row) {
                $volume[$key] = $row[$this->treeSortField];
            }
            array_multisort($volume, SORT_ASC, $data); //升序排列
        }

        $children = [];

        foreach ($data as $d) {
            $d['__deep__'] = $deep;
            $d['__id__'] = $d[$this->treeIdField];

            if ($this->lineType) {
                if ($this->lineType == 1) {
                    $d['__text__'] = str_repeat('&nbsp;', ($deep - 1) * 5) . '├─' . $d[$this->treeTextField];
                } else {
                    $d['__text__'] = str_repeat('──', ($deep - 1)) . '├─' . $d[$this->treeTextField];
                }

                $this->lineData[] = $d;
                continue;
            }

            $d['__text__'] = $d[$this->treeTextField];
            $d['__children__'] = $this->getChildrenData($allTreeData, $d[$this->treeIdField]);

            $children[] = $d;
        }

        return $children;
    }
}
