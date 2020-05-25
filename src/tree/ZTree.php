<?php

namespace tpext\builder\tree;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;

class ZTree implements Renderable
{
    use HasDom;

    protected $data;

    protected $beforeClick = 'alert("未绑定`beforeClick`事件。点击了"+treeNode.id);';

    protected $id = 'the-tree';

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
                'id' => 0,
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
    public function beforeClick($script)
    {
        $this->beforeClick = $script;

        return $this;
    }

    public function beforRender()
    {
        $data = json_encode($this->data);
        $script = <<<EOT

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
                    {$this->beforeClick}
                }
            }
          };
          var zNodes = {$data};

          $(document).ready(function () {
            var treeObj = $.fn.zTree.init($("#{$this->id}"), setting, zNodes);
            treeObj.expandAll(true);
          });

EOT;

        $builder = Builder::getInstance();

        $builder->customCss('/assets/tpextbuilder/js/zTree_v3/css/materialDesignStyle/materialdesign.css');
        $builder->customJs('/assets/tpextbuilder/js/zTree_v3/js/jquery.ztree.all.min.js');

        $builder->addScript($script);

        $builder->addStyleSheet('.ztree li ul{
            padding:0;
        }
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

        $viewshow = new ViewShow($template);

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
