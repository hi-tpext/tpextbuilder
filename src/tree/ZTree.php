<?php

namespace tpext\builder\tree;

use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;

class ZTree implements Renderable
{
    use HasDom;

    protected $data;

    protected $onClick = 'alert("未绑定`onClick`事件。点击了"+treeNode.id);';

    protected $trigger = '';

    protected $id = 'the-ztree';

    protected $partial = false;

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
     * @param Collection $optionsData
     * @param string $textField
     * @param string $idField
     * @param string $pidField
     * @return $this
     */
    public function fill($treeData, $textField = 'name', $idField = 'id', $pidField = 'parent_id')
    {
        $tree = [
            [
                'id' => '__all__',
                'pId' => '',
                'name' => '全部',
            ],
        ];

        foreach ($treeData as $dep) {
            $tree[] = [
                'id' => $dep[$idField],
                'pId' => $dep[$pidField],
                'name' => $dep[$textField],
            ];
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

                    if(!$('{$element}').length)
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

                    if($('{$element}').hasClass('select2-use-ajax'))
                    {
                        $('{$element}').empty().append('<option value="' + selected + '">' + treeNode.name + '</option>');
                    }
                    else
                    {
                        $('{$element}').val(selected);
                    }
                    $('{$element}').trigger('change');
                    $('.row-refresh').trigger('click');

EOT;
        return $this;
    }

    public function beforRender()
    {
        $data = json_encode($this->data);
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
            treeObj.expandAll(true);
        });

        var leftw = $('.tree-div').parent('div').outerWidth();
        var rightw = $('.tree-div').parent('div').next('div').outerWidth();

        $('.tree-div .hide-left').click(function(){
            var parent = $('.tree-div').parent('div');
            if($(this).children('i').hasClass('mdi-format-horizontal-align-left'))
            {
                parent.next('div').css('width' ,(rightw + leftw - 15) + 'px');
                parent.css({'width':'15px' ,'padding':0 ,'margin':0});
                $(this).next('.ztree').addClass('hidden');
                $(this).children('i').removeClass('mdi-format-horizontal-align-left').addClass('mdi mdi-format-horizontal-align-right');
            }
            else
            {
                parent.next('div').removeAttr('style');
                parent.removeAttr('style');
                $(this).next('.ztree').removeClass('hidden');
                $(this).children('i').removeClass('mdi-format-horizontal-align-right').addClass('mdi mdi-format-horizontal-align-left');
            }
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
        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'tree', 'ztree.html']);

        $viewshow = view($template);

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
}
