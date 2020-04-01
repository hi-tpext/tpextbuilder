<?php

namespace tpext\builder\displayer;

use think\Collection;
use tpext\builder\form\ItemsContent;

class Items extends Field
{
    protected $view = 'items';

    protected $data = [];

    /**
     * Undocumented variable
     *
     * @var Form|Search
     */
    protected $form;

    /**
     * Undocumented variable
     *
     * @var ItemsContent
     */
    protected $__items_content__;

    public function created()
    {
        parent::created();

        $this->form = $this->getWapper()->getForm();
        $this->__items_content__ = $this->form->createItems();
        $this->__items_content__->name($this->name);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Field ...$fields
     * @return $this
     */
    public function with(...$fields)
    {
        $this->form->itemsEnd();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection $data
     * @return $this
     */
    public function value($val)
    {
        return $this->fill($val);
    }

    /**
     * Undocumented function
     *
     * @return ItemsContent
     */
    public function getContent()
    {
        return $this->__items_content__;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function actionRowText($val)
    {
        $this->__items_content__->actionRowText($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function cnaDelete($val)
    {
        $this->__items_content__->cnaDelete($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function canAdd($val)
    {
        $this->__items_content__->canAdd($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection $data
     * @param boolean $overWrite
     * @return $this
     */
    public function fill($data = [], $overWrite = false)
    {
        if (!$overWrite && !empty($this->data)) {
            return $this;
        }
        if (!empty($this->name) && isset($data[$this->name]) &&
            (is_array($data[$this->name]) || $data[$this->name] instanceof Collection)) {
            $this->data = $data[$this->name];
        } else {
            $this->data = $data;
        }

        $this->__items_content__->fill($this->data);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array|Collection
     */
    public function getData()
    {
        return $this->data;
    }

    protected function actionScript()
    {
        $id = 'items-' . $this->name;

        $script = <<<EOT
        $(document).on('click', "#{$id} .row-__action__ a.action-delete", function () {
           var del = $(this).prev('input').val();
           if(del === '0') {
                $(this).prev('input').val(1);
                $(this).removeClass('btn-danger').addClass('btn-success').attr('title', '恢复');
                $(this).children('i').removeClass('mdi-delete').addClass('mdi-restart');
                $(this).parents('td').prevAll('td').find('.item-field-required').addClass('ignore').removeClass('has-error');
           }
           else if(del === '1') {
                $(this).prev('input').val(0);
                $(this).removeClass('btn-success').addClass('btn-danger').attr('title', '删除');
                $(this).children('i').removeClass('mdi-restart').addClass('mdi-delete');
                $(this).parents('td').prevAll('td').find('.item-field-required').removeClass('ignore');
           }
           else {
                $(this).parents('tr').remove();
           }
        });
        //var template = jQuery.validator.format($.trim($("#{$id}-temple").clone(true).find('tbody').html()));
        var i = 1;
        $(document).on('click', "#{$id}-add", function () {
            var node = $("#{$id}-temple").find('tr').clone();
            var fields = node.find('.item-field');
            var script = $("#{$id}-script").val();
            i += 1;
            fields.each(function(){
                var name = $(this).attr('name');
                var id = $(this).attr('id');
                id = id.replace(/-$/, '');
                var newid = id + 'new' + i;
                script = script.replace('#' + id, '#' + newid);
                $(this).attr('id', newid);
                name = name.replace(/(.+?)\[new\](.+?)/, '$1' + 'new' + i + '$2');
                $(this).attr('name', name);
            });
            $(this).parents('tr').before(node);
            if(script)
            {
                if ($('#script-div').size()) {
                    $('#script-div').html('\<script\>' + script + '\</script\>');
                } else {
                    $('body').append('<div class="hidden" id="script-div">' + '\<script\>' + script + '\</script\>' + '</div>');
                }
            }
            //console.log(script);
            //复制出来的，需要对应的初始化脚本
        });

EOT;
        $this->script[] = $script;

        return $this;
    }

    public function beforRender()
    {
        if ($this->__items_content__->hasAction()) {
            $this->actionScript();
        }

        $this->__items_content__->beforRender();

        parent::beforRender();

        return $this;
    }

    public function render()
    {
        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'items_content' => $this->__items_content__,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
