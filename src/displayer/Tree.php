<?php

namespace tpext\builder\displayer;

use think\Collection;

class Tree extends Field
{
    protected $view = 'tree';

    protected $minify = false;

    protected $options = [];

    protected $js = [
        '/assets/tpextbuilder/js/zTree_v3/js/jquery.ztree.all.min.js',
        '/assets/tpextbuilder/js/zTree_v3/js/jquery.ztree.exhide.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/zTree_v3/css/lyearStyle/lyearStyle.css'
    ];

    protected $expandAll = true;

    protected $multiple = true;

    protected $maxHeight = 400;

    protected $enableCheck = true;

    /**
     * Undocumented variable
     *
     * @var array|string
     */
    protected $checked = [];

    protected $disabledOptions = [];

    /**
     * 点击父节点自动展开/收起子节点
     *
     * @var bool 
     */
    protected $expandNodeOnclick = false;

    protected $jsOptions =  [
        'view' => [
            'dblClickExpand' => false,
            'addHoverDom' => false,
            'removeHoverDom' => false,
            'selectedMulti' => true,
        ]
    ];

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function default($val = [])
    {
        $this->default = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function enableCheck($val = true)
    {
        $this->enableCheck = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function expandNodeOnclick($val = true)
    {
        $this->expandNodeOnclick = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function data($val)
    {
        $this->data = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param bool $val
     * @return $this
     */
    public function multiple($val = true)
    {
        $this->multiple = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $val
     * @return $this
     */
    public function maxHeight($val = 800)
    {
        $this->maxHeight = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function expandAll($val = true)
    {
        $this->expandAll = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|array $val
     * @return $this
     */
    public function disabledOptions($val)
    {
        $this->disabledOptions = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function placeholder($val)
    {
        $this->jsOptions['placeholder'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param array|Collection|\IteratorAggregate $options [[id,name,pId],...]
     * @return $this
     */
    public function options($options)
    {
        if ($options instanceof Collection || $options instanceof \IteratorAggregate) {
            return $this->optionsData($options);
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection|\IteratorAggregate $treeData
     * @param string $textField
     * @param string $idField
     * @param string $pidField
     * @param string $rootText
     * @param int $rootId
     * 
     * @return $this
     */
    public function optionsData($treeData, $textField = '', $idField = 'id', $pidField = 'parent_id', $rootText = '全部', $rootId = 0)
    {
        $tree = [];

        if ($rootText == '全部') {
            $rootText = __blang('bilder_left_tree_text_all') . $this->getlabel();
        }

        if ($rootText) {
            $tree[] = [
                'id' => $rootId,
                'pId' => -1,
                'name' => $rootText,
            ];
        }

        preg_match_all('/\{([\w\.]+)\}/', $textField, $matches);

        $needReplace = isset($matches[1]) && count($matches[1]) > 0;

        foreach ($treeData as $li) {

            if (empty($idField)) {
                $idField = $li->getPk();
            }
            if (empty($textField)) {
                $textField = isset($li['name']) ? 'name' : 'title';
            }

            if ($needReplace) {

                $keys = [];
                $replace = [];

                foreach ($matches[1] as $match) {
                    $arr = explode('.', $match);
                    if (count($arr) == 1) {

                        $keys[] = '{' . $arr[0] . '}';
                        $replace[] = isset($li[$arr[0]]) ? $li[$arr[0]] : '-';
                    } else if (count($arr) == 2) {

                        $keys[] = '{' . $arr[0] . '.' . $arr[1] . '}';
                        $replace[] = isset($li[$arr[0]]) && isset($li[$arr[0]][$arr[1]]) ? $li[$arr[0]][$arr[1]] : '-';
                    } else {
                        //最多支持两层 xx 或 xx.yy
                    }
                }

                $tree[] = [
                    'id' => $li[$idField],
                    'pId' => $li[$pidField] ?? $li['pid'],
                    'name' => str_replace($keys, $replace, $textField),
                ];
            } else {
                $tree[] = [
                    'id' => $li[$idField],
                    'pId' => $li[$pidField] ?? $li['pid'],
                    'name' => $li[$textField] ?? '-',
                ];
            }
        }

        $this->options = $tree;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function ztreeScript()
    {
        if (!($this->value === '' || $this->value === null || $this->value === [])) {
            $this->checked = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!($this->default === '' || $this->default === null || $this->default === [])) {
            $this->checked = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        if ($this->disabledOptions && !is_array($this->disabledOptions)) {
            $this->disabledOptions = explode(',', $this->disabledOptions);
        }

        foreach ($this->options as &$d) {
            $d['chkDisabled'] = in_array($d['id'], $this->disabledOptions) || $this->isReadonly() || $this->isDisabled();
            $d['checked'] = in_array($d['id'], $this->checked);
            $d['open'] = $this->expandAll;
        }

        $selectId = $this->getId();

        if (empty($this->jsOptions['placeholder'])) {
            $this->jsOptions['placeholder'] = '请选择' . $this->getlabel();
        }

        $key = preg_replace('/\W/', '', $selectId);

        $configs = json_encode($this->jsOptions);
        $configs = substr($configs, 1, strlen($configs) - 2);

        $zNodes = json_encode($this->options);
        $multiple = $this->multiple ? 1 : 0;
        $enableCheck = $this->enableCheck ? 1 : 0;

        $expandNodeOnclick = $this->expandNodeOnclick ? 'true' : 'false';

        $script = <<<EOT

        var treeObj{$key} = null;

        var setting{$key} = {
            {$configs},
            check: {
              enable: '{$enableCheck}' == '1',
              autoCheckTrigger: true,
              chkStyle: '{$multiple}' == '1' ? 'checkbox' : 'radio',
              radioType: 'all',
            },
            data: {
              simpleData: {
                enable: true
              }
            },
            edit: {
              enable: false
            },
            callback: {
                beforeClick: function(treeId, treeNode, clickFlag){
                    if ({$expandNodeOnclick} && treeNode.isParent) {
                        treeObj{$key}.expandNode(treeNode);
                        return true;
                    }
                },
                onCheck: function(event, treeId, treeNode) {
                    var optionId = (treeId + '-' + treeNode.id).replace(/\W/g, '-');
                    if(treeNode.checked)
                    {
                        if(!$('#' + optionId).size())
                        {
                            $('<option id="' + optionId + '" value="' + treeNode.id + '" selected="selected">' + treeNode.name + '</option>').appendTo("#{$selectId}");
                        }
                    }
                    else
                    {
                        $('#' + optionId).remove();
                    }
                }
            }
        };

        treeObj{$key} = $.fn.zTree.init($("#{$selectId}-tree"), setting{$key}, {$zNodes});

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        $this->ztreeScript();

        if (!$this->readonly && $this->maxHeight > 0) {
            $this->addStyle('max-height:' . $this->maxHeight . 'px;overflow:scroll;');
        }

        return parent::beforRender();
    }

    public function render()
    {
        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'checked' => $this->checked,
            'options' => $this->options,
            'disabledOptions' => $this->disabledOptions,
            'multiple' => $this->multiple,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
