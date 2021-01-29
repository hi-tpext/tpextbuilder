<?php

namespace tpext\builder\traits\actions;

/**
 * 下拉数据源
 */

trait HasSelectPage
{
    /**
     * 默认查询条件
     *
     * @var array
     */
    protected $selectScope = []; //如 [['enable', 'eq', 1]]

    /**
     * 模糊查询字段，如 'name|title'
     *
     * @var string
     */
    protected $selectSearch = '';

    /**
     * 默认 Text 字段 如 '{id}#{nickname}'
     *
     * @var string
     */
    protected $selectTextField = '';

    /**
     * 默认 id 字段
     *
     * @var string
     */
    protected $selectIdField = '';

    /**
     * 查询时的字段，优化查询速度
     *
     * @var string
     */
    protected $selectFields = '*';

    /**
     * 排序
     *
     * @var string
     */
    protected $selectOrder = '';

    /**
     * 分页大小
     *
     * @var string
     */
    protected $selectPagesize = 20;

    /**
     * 下拉列表关联加载 如 ['level']
     * 设置后`selectTextField`可以为'{id}#{nickname}{level.name}'
     * 若设置了`selectFields`,必须包含关联字段`level_id`，
     * 如：$this->selectFields＝ 'id,nickname,level_id';
     * @var array
     */
    protected $selectWith = [];

    public function selectPage()
    {
        if (!$this->dataModel) {
            return json(
                [
                    'data' => $this->buildDataList(),
                    'has_more' => 0,
                ]
            );
        }

        $selected = input('selected', '');
        $where = [];
        $list = [];

        if (!$this->selectTextField && $this->selectSearch) {
            $this->selectTextField = explode('|', $this->selectSearch)[0];
        }

        $idField = input('idField', '_');
        $textField = input('textField', '_');

        if (empty($idField) || $idField == '_') {
            $idField = $this->selectIdField ?: $this->getPk();
        }
        if (empty($textField) || $textField == '_') {
            $textField = 'text';
        }

        if ($textField == 'text') {
            $textField = $this->selectTextField;
        }

        if ($textField && (strpos($textField, '{') === false || strpos($textField, '}') === false)) {
            $textField = '{' . $textField . '}';
        }

        $sortOrder = $this->selectOrder ?: ($this->sortOrder ?: $idField . ' desc');
        $pagesize = $this->selectPagesize ?: ($this->pagesize ?: 20);

        $hasMore = 1;
        if ($selected != '') { //初始化已选中的
            if (is_numeric($selected)) {
                $where[] = [$idField, 'eq', $selected];
            } else {
                $arr = array_filter(explode(',', $selected));
                if (count($arr)) {
                    $where[] = [$idField, 'in', $selected];
                } else {
                    $where[] = [$idField, 'eq', $selected];
                }
            }
            $list = $this->dataModel->with($this->selectWith)->where($where)->order($sortOrder)->field($this->selectFields)->select();
            $hasMore = 0;
        } else {
            $q = input('q', '');
            $page = input('page/d', 1);

            $page = $page < 1 ? 1 : $page;
            $whereOr = [];

            $where = $this->selectScope;

            if ($q) {
                if ($this->selectSearch) {
                    $where[] = [$this->selectSearch, 'like', '%' . $q . '%'];
                }
                if (is_numeric($q)) {
                    $whereOr[] = [$idField, 'eq', $q];
                }
            }

            $list = $this->dataModel->with($this->selectWith)->where($where)->whereOr($whereOr)->order($sortOrder)->limit(($page - 1) * $pagesize, $pagesize)->field($this->selectFields)->select();

            $hasMore = count($list) == $pagesize ? 1 : 0;
        }

        $data = [];
        if ($textField && $textField != 'text') {
            $keys = [];
            $replace = [];
            $n = 0;
            foreach ($list as $li) {
                $li = $li->toArray();
                $keys = [];
                $replace = [];
                foreach ($li as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k => $v) {
                            $keys[] = '{' . $key . '.' . $k . '}';
                            $replace[] = $v;
                        }
                        continue;
                    }
                    $keys[] = '{' . $key . '}';
                    $replace[] = $val;
                }
                $li['__id__'] = $li[$idField];
                $li['__text__'] = str_replace($keys, $replace, $textField);

                //处理关联不上的情况,如`member`表的`level_id`关联`level`表，但如果某条记录的`level_id`为０,则关联失败，下拉框会出现{level.name}
                $li['__text__'] = preg_replace('/\{\w+\.\w+\}/', '-', $li['__text__']);

                $n += 1;
                $data[] = $li;
            }
        } else {
            foreach ($list as $li) {
                $li = $li->toArray();
                $li['text'] = implode(',', array_slice(array_values($li), 0, 4));
                $li['__id__'] = $li[$idField];
                $li['__text__'] = $li['text'];
                $data[] = $li;
            }
        }
        return json(
            [
                'data' => $data,
                'has_more' => $hasMore,
                'textField' => $textField,
            ]
        );
    }
}
