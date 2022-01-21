<?php

namespace tpext\builder\search;

use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasOptions;

class TabLink implements Renderable
{
    use HasDom;
    use HasOptions;

    private $view = 'tab';

    protected $active = '';
    protected $id = '';
    protected $key = '';
    protected $searchId = '';

    public function getId()
    {
        if (empty($this->id)) {
            $this->id = 'tab-' . mt_rand(1000, 9999);
        }

        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param string $id
     * @return $this
     */
    public function searchId($id)
    {
        $this->searchId = $id;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function key($val)
    {
        $this->key = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function active($val)
    {
        $this->active = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $id = $this->getId();
        $element = 'row-' . $this->key;
        $script = <<<EOT

    if(!$('#{$this->searchId} .{$element}').length)
    {
        var __field__ = document.createElement("input");
        __field__.type = "hidden";
        __field__.name = '{$this->key}';
        __field__.className = '{$element}';

        $('#{$this->searchId} form.search-form').append(__field__);
    }

    $('body').on('click', '#{$id} .nav-item a', function(){
        var val = $(this).data('val');
        if($('#{$this->searchId} .{$element}').hasClass('select2-use-ajax'))
        {
            $('#{$this->searchId} .{$element}').empty().append('<option value="' + val + '">' + $(this).text() + '</option>');
        }
        else
        {
            $('#{$this->searchId} .{$element}').val(val);
        }
        $('#{$this->searchId} .{$element}').trigger('change');
        $('#{$this->searchId} .row-submit').trigger('click');
        $('#{$id} .nav-item').removeClass('in active');
        $(this).parent('.nav-item').addClass('in active');
        return false;
    });

    if($('#{$id} .nav-item.tab-{$this->active}').length)
    {
        $('#{$id} .nav-item.tab-{$this->active}').addClass('in active');
    }
    else
    {
        $('#{$id} .nav-item').eq(0).addClass('in active');
    }

EOT;

        Builder::getInstance()->addScript($script);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|\think\response\View
     */
    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'table' . DIRECTORY_SEPARATOR . $this->view . '.html';
        $vars = [
            'options' => $this->options,
            'active' => $this->active,
            'id' => $this->getId(),
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
        ];

        $viewshow = view($template);
        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        return $this->render();
    }
}
