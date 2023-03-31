<?php

namespace tpext\builder\tree;

use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\common\Widget;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\think\View;

class JSTree extends Widget implements Renderable
{
    use HasDom;

    protected $data;

    protected $onClick = 'alert("未绑定`onClick`事件。点击了"+data.instance.get_node(data.selected[0]).text);';

    protected $trigger = '';

    protected $id = 'the-jstree';

    protected $partial = false;

    protected $expandAll = false;

    public function __construct()
    {
        $this->addStyle('float:left;padding-left:5px;');
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
     * @param \think\Collection|array $optionsData
     * @param string $textField
     * @param string $idField
     * @param string $pidField
     * @return $this
     */
    public function fill($treeData, $textField = 'name', $idField = 'id', $pidField = 'parent_id', $rootText = '全部')
    {
        $tree = [];

        if ($rootText) {
            $tree[] = [
                'id' => '__all__',
                'text' => $rootText,
                'state' => [
                    'opened' => false,
                ],
                'children' => [],
            ];
        }

        foreach ($treeData as $k => $d) {

            if ($d[$pidField] !== 0 && $d[$pidField] !== '') {
                continue;
            }

            unset($treeData[$k]);

            $tree[] = [
                'id' => $d[$idField],
                'text' => $d[$textField],
                'state' => [
                    'opened' => true,
                ],
                'children' => isset($d['children']) ? $d['children'] : $this->getChildren($treeData, $d[$idField], $textField, $idField, $pidField),
            ];
        }

        $this->data = $tree;

        return $this;
    }

    protected function getChildren($treeData, $pid, $textField = 'name', $idField = 'id', $pidField = 'parent_id')
    {
        $children = [];

        foreach ($treeData as $k => $d) {

            if ('' . $d[$pidField] === '' . $pid) {

                unset($treeData[$k]);

                $children[] = [
                    'id' => $d[$idField],
                    'text' => $d[$textField],
                    'state' => [
                        'opened' => true,
                    ],
                    'children' => isset($d['children']) ? $d['children'] : $this->getChildren($treeData, $d[$idField], $textField, $idField, $pidField),
                ];
            }
        }

        return $children;
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

                    var treeNode = data.instance.get_selected(true)[0];

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
                        $('form.search-form {$element}').empty().append('<option value="' + selected + '">' + treeNode.text + '</option>');
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

        $expandAll = $this->expandAll ? 'open_all' : 'close_all';

        $script = <<<EOT

        var setting = {
            'core' : {
                'themes' : {
                    'responsive': false
                },
                'data' : {$data}
            },
            "types" : {
                'default' : {
                    'icon' : 'mdi mdi-folder-outline'
                },
                'file' : {
                    'icon' : 'mdi mdi-file-outline'
                }
            },
            'plugins' : ['types']
        };

        $(document).ready(function () {
            $('#{$this->id}').jstree(setting);
            $('#{$this->id}').on('activate_node.jstree', function(event, data) {
                {$this->onClick}
            });
            $('#{$this->id}').on("loaded.jstree", function (event, data) {
                $('#{$this->id}').jstree('{$expandAll}');
            });
        });

        var leftw = $('.tree-div').parent('div').outerWidth();
        var rightw = $('.tree-div').parent('div').next('div').outerWidth();

        $('.left-tree').on('click', '.hide-left',function(){
            var leftTree = $(this).parent('.tree-div').parent('div.left-tree');
            leftTree.addClass('hidden');

            var rightTree = leftTree.next('.right-table');
            rightTree.css('width','100%');

            if(!rightTree.find('.show-left').length)
            {
                rightTree.append('<a href="#" title="打开左侧" class="show-left"><i class="mdi mdi-format-horizontal-align-right"></i></a>');
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

        $builder->customCss('/assets/tpextbuilder/js/jstree/style.min.css');
        $builder->customJs('/assets/tpextbuilder/js/jstree/jstree.min.js');

        $builder->addScript($script);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'tree' . DIRECTORY_SEPARATOR . 'jstree.html';

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
