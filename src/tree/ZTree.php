<?php

namespace tpext\builder\tree;

use think\Collection;
use tpext\think\View;
use tpext\builder\common\Module;
use tpext\builder\common\Widget;
use tpext\builder\traits\HasDom;
use tpext\builder\common\Builder;
use tpext\builder\inface\Renderable;

class ZTree extends Widget implements Renderable
{
    use HasDom;

    protected $data;

    protected $onClick = 'alert("`onClick` event was not binded, clicked : "+treeNode.id);';

    protected $trigger = '';

    protected $id = 'the-ztree';

    protected $partial = false;

    protected $expandAll = false;

    public function __construct()
    {
        $this->addStyle('float:left;');
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function partial($val = true)
    {
        $this->partial = $val;
        return $this;
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
     * @param string $val
     * @return $this
     */
    public function setId($val)
    {
        $this->id = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $val
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
     * @param array|Collection|\IteratorAggregate $optionsData
     * @param string $textField
     * @param string $idField
     * @param string $pidField
     * @return $this
     */
    public function fill($treeData, $textField = 'name', $idField = 'id', $pidField = 'parent_id', $rootText = '全部')
    {
        $tree = [];

        if ($rootText == '全部') {
            $rootText = __blang('bilder_left_tree_text_all');
        }

        if ($rootText) {
            $tree[] = [
                'id' => '__all__',
                'pId' => '',
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
                    'pId' => $li[$pidField],
                    'name' => $li[$textField],
                ];
            }
        }

        $this->data = $tree;

        return $this;
    }

    /**
     * Undocumented function
     * $('input[name="category_id"]').val(treeNode.id);$('.row-submit').trigger('click');
     *
     * @param string $script
     * @return $this
     */
    public function onClick($script)
    {
        $this->onClick = $script;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $element
     * @return $this;
     */
    public function trigger($element)
    {
        $this->trigger = $element;
        $this->onClick = <<<EOT

                    if(!$('form.search-form {$element}').length)
                    {
                        var __field__ = document.createElement("input");
                        __field__.type = "hidden";
                        __field__.name = '{$element}'.replace(/^\.row\-/, '');
                        __field__.className = '{$element}'.replace(/^\./, '');

                        $('form.search-form').append(__field__);
                    }

                    var selected = treeNode.id;
                    if(selected == '__all__')
                    {
                        selected = '';
                    }

                    if($('form.search-form {$element}').hasClass('select2-use-ajax'))
                    {
                        $('form.search-form {$element}').empty().append('<option value="' + selected + '">' + treeNode.name + '</option>');
                    }
                    else
                    {
                        $('form.search-form {$element}').val(selected);
                    }
                    $('form.search-form {$element}').trigger('change');
                    $('form.search-form .row-refresh').trigger('click');

EOT;
        return $this;
    }

    public function beforRender()
    {
        $data = json_encode($this->data);

        $expandAll = $this->expandAll ? 'true' : 'false';

        $script = <<<EOT

        var treeObj = null;

        var setting = {
            view: {
              addHoverDom: false,
              removeHoverDom: false,
              selectedMulti: false
            },
            check: {
              enable: false
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
                    if (treeNode.isParent) {
                        treeObj.expandNode(treeNode);
                        return true;
                    }
                },
                onClick: function(event, treeId, treeNode) {
                    {$this->onClick}
                }
            }
        };
        var zNodes = {$data};

        $(document).ready(function () {
            treeObj = $.fn.zTree.init($("#{$this->id}"), setting, zNodes);
            treeObj.expandAll({$expandAll});
        });

        $('.left-tree').on('click', '.hide-left',function(){
            var leftTree = $(this).parent('.tree-div').parent('div.left-tree');
            leftTree.addClass('hidden');

            var rightTree = leftTree.next('.right-table');
            rightTree.css('width','100%');

            if(!rightTree.find('.show-left').length)
            {
                rightTree.append('<a href="#" title="' + __blang.bilder_action_open_left_tree + '" class="show-left"><i class="mdi mdi-format-horizontal-align-right"></i></a>');
            }
            else
            {
                rightTree.find('.show-left').removeClass('hidden');
            }
        });

        $('.right-table').on('click', '.show-left', function(){
            var rightTree = $(this).parent('.right-table');
            rightTree.removeAttr('style');

            var leftTree = rightTree.prev('div.left-tree');
            leftTree.removeClass('hidden');
            rightTree.find('.show-left').addClass('hidden');
        });

EOT;

        $builder = Builder::getInstance();

        $builder->customCss('/assets/tpextbuilder/js/zTree_v3/css/lyearStyle/lyearStyle.css');
        $builder->customJs('/assets/tpextbuilder/js/zTree_v3/js/jquery.ztree.all.min.js');

        $builder->addScript($script);

        $builder->addStyleSheet('
        .ztree li a.curSelectedNode
        {
            color : green;
        }
');
        return $this;
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'tree' . DIRECTORY_SEPARATOR . 'ztree.html';

        $viewshow = new View($template);

        $vars = [
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'id' => $this->id,
        ];

        if ($this->partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function destroy()
    {
        $this->data = null;
    }
}
