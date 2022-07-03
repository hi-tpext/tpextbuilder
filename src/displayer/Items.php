<?php

namespace tpext\builder\displayer;

use think\Model;
use think\Collection;
use tpext\builder\form\ItemsContent;

class Items extends Field
{
    protected $view = 'items';

    protected $isInput = false;

    protected $isFieldsGroup = true;

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

    public function created($fieldType = '')
    {
        parent::created($fieldType);

        $this->form = $this->getWrapper()->getForm();
        $this->__items_content__ = $this->form->createItems();

        if (empty($this->name)) {
            $this->name = 'items' . mt_rand(100, 999);
            $this->getWrapper()->setName($this->name);
        }

        $this->__items_content__->name($this->name);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param mixed ...$fields
     * @return $this
     */
    public function with(...$fields)
    {
        if (count($fields) && $fields[0] instanceof \Closure) {
            $fields[0]($this->form);
        }

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
    public function canDelete($val)
    {
        $this->__items_content__->cnaDelete($val);
        return $this;
    }

    /**
     * canDelete的错误写法，保留兼容
     * @deprecated  1.9.0044
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
     * @return $this
     */
    public function canNotAddOrDelete()
    {
        $this->__items_content__->cnaDelete(false);
        $this->__items_content__->canAdd(false);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model|Collection $data
     * @param boolean $overWrite
     * @return $this
     */
    public function fill($data = [], $overWrite = false)
    {
        if ($data instanceof Collection) {
            return $this->dataWithId($data, 'id', $overWrite);
        }

        if (!$overWrite && !empty($this->data)) {
            return $this;
        }

        if (!empty($this->name) && isset($data[$this->name])) {
            if (is_array($data[$this->name])) {
                $this->data = $data[$this->name];
            } else if ($data[$this->name] instanceof Collection) {
                return $this->dataWithId($data[$this->name], 'id', $overWrite);
            } else {
                //
            }
        } else {
            $this->data = $data;
        }

        $this->__items_content__->fill($this->data);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = true)
    {
        $this->canDelete(false);
        $this->canAdd(false);
        $this->__items_content__->readonly($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Collection $data
     * @param string $idField
     * @param boolean $overWrite
     * @return $this
     */
    public function dataWithId($data, $idField = 'id', $overWrite = false)
    {
        if (!$overWrite && !empty($this->data)) {
            return $this;
        }

        $list = [];
        foreach ($data as $d) {
            if (empty($idField)) {
                $idField = $d->getPk();
            }

            $list[$d[$idField]] = $d;
        }
        $this->data = $list;

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
        $(document).on('click', "#{$id} .row-__action__ span.action-delete", function () {
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
        $("#{$id}-temple .item-field").each(function(i, obj){
            if($(obj).hasClass('checkbox-label') || $(obj).hasClass('radio-label'))
            {
                var boxes = $(obj).find('input');
                boxes.each(function(){
                    $(this).attr('data-name', $(this).attr('name'));
                    $(this).removeAttr('name');
                });
            }
            else if($(obj).hasClass('switch-label'))
            {
                var input = $(obj).prev('input');
                input.attr('data-name', input.attr('name'));
                input.removeAttr('name');
            }
            else
            {
                $(obj).attr('data-name', $(obj).attr('name'));
                $(obj).removeAttr('name');
            }
        });

        var i = 1;
        var script = '';

        if(!window.reset)
        {
            window.reset=function(obj)
            {
                if($(obj).hasClass('checkbox-label') || $(obj).hasClass('radio-label'))
                {
                    var boxes = $(obj).find('input');
                    boxes.each(function(){
                        $(this).attr('data-name', $(this).attr('name'));
                        $(this).removeAttr('name');
                        reset(this);
                    });
                    return;
                }
                else if($(obj).hasClass('switch-label'))
                {
                    var input = $(obj).prev('input');
                    input.attr('data-name', input.attr('name'));
                    input.removeAttr('name');
                    reset(input);
                    return;
                }
                var oldName = $(obj).data('name');
                var oldId = $(obj).attr('id');
                var oldOldId = oldId;
                if(!oldId)
                {
                    return;
                }
                oldId = oldId.replace('-no-init-script', '');
                var newid = oldId + '__new__' + i;
                script = script.replace(new RegExp('#' + oldId, "gm"), '#' + newid);
                $(obj).attr('id', newid);
                var newName = oldName.replace(/(.+?)\[__new__\](.+?)/, '$1' + '[__new__' + i + ']$2');
    
                //console.log('oldId:'+oldId);
                //console.log('oldName:'+oldName);
                //console.log('newid:'+newid);
                //console.log('newName:'+newName);
                //console.log('-------------------------------------------');
                $(obj).attr('name', newName);
                $(obj).removeAttr('data-name');
                if($(obj).hasClass('item-field-required'))
                {
                    $(obj).attr('required', true);
                }
                if($(obj).hasClass('file-url-input'))
                {
                    var prent = $(obj).parent('.input-group');
                    var ulist = prent.prev('ul.lyear-uploads-pic');
                    var picker = prent.find('.upload-picker');
    
                    ulist.attr('id', 'file_list_' + newid);
                    $(obj).siblings('.input-group-addon.choose-file,.input-group-addon.upload-file').data('id', newid).data('name', newid);
                    picker.attr('id', 'picker_'+newid);
    
                    window.uploadConfigs[newid] = window.uploadConfigs[oldOldId];
                }
            };
        }

        $(document).on('click', "#{$id}-add", function () {
            var node = $("#{$id}-temple").find('tr').clone();
            var fields = node.find('.item-field');
            script = $("#{$id}-script").val();

            i += 1;
            fields.each(function(){
                reset(this);
            });
            $(this).parents('tr').before(node);
            $('.items-empty-text').hide();
            script = script.replace(/-no-init-script/g, '');
            //console.log(script);
            if(script)
            {
                if ($('#script-div').size()) {
                    $('#script-div').html('\<script\>' + script + '\</script\>');
                } else {
                    $('body').append('<div class="hidden" id="script-div">' + '\<script\>' + script + '\</script\>' + '</div>');
                }
            }
            //复制出来的，需要对应的初始化脚本
        });

EOT;
        $this->script[] = $script;

        return $this;
    }

    public function beforRender()
    {
        $this->getWrapper()->addClass('items-wrapper');

        if ($this->__items_content__->hasAction()) {
            $this->actionScript();
        }

        $this->__items_content__->beforRender();

        parent::beforRender();

        return $this;
    }

    public function customVars()
    {
        return [
            'items_content' => $this->__items_content__,
        ];
    }

    /**
     * 在每个模板字段上执行
     * 
     * @param \Closure $callback
     * @return $this
     */
    public function templateFieldCall($callback)
    {
        $this->__items_content__->templateFieldCall($callback);
        return $this;
    }
}
