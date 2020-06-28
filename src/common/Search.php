<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\form\FieldsContent;
use tpext\builder\form\Fillable;
use tpext\builder\inface\Renderable;
use tpext\builder\search\SRow;
use tpext\builder\search\SWrapper;
use tpext\builder\traits\HasDom;

/**
 * Form class
 */
class Search extends SWrapper implements Renderable
{
    use HasDom;

    protected $view = '';

    protected $action = '';

    protected $id = 'search';

    protected $method = 'post';

    protected $rows = [];

    protected $searchButtonsCalled = false;

    protected $ajax = true;

    protected $defaultDisplayerSize = null;

    protected $butonsSizeClass = 'btn-xs';

    protected $open = true;

    protected $tableId = '';

    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields__ = null;

    public function __construct()
    {
        $this->class = 'form-horizontal';
    }

    /**
     * Undocumented function
     *
     * @param SRow|Fillable $row
     * @return $this
     */
    public function addRow($row)
    {
        $this->rows[] = $row;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function createFields()
    {
        $this->__fields__ = new FieldsContent();
        $this->__fields__->setForm($this);
        return $this->__fields__;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function fieldsEnd()
    {
        $this->__fields__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Table $table
     * @return $this
     */
    public function search($table)
    {
        $this->tableId = $table->getTableId();
        $this->id = 'search-' . $this->tableId;
        $this->ajax = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return void
     */
    public function open($val = true)
    {
        $this->open = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getFormId()
    {
        return $this->id;
    }

    /**
     * Undocumented function
     * btn-lg btn-sm btn-xs
     * @param string $val
     * @return $this
     */
    public function butonsSizeClass($val)
    {
        $this->butonsSizeClass = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $label
     * @param integer $element
     * @return $this
     */
    public function defaultDisplayerSize($label = 4, $element = 8)
    {
        $this->defaultDisplayerSize = [$label, $element];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function searchButtons($create = true)
    {
        if ($create) {
            $this->fieldsEnd();
            $this->html('', '', 1)->showLabel(false);
            $this->button('submit', '筛&nbsp;&nbsp;选', 1)->class('btn-info ' . $this->butonsSizeClass);
            $this->button('button', '重&nbsp;&nbsp;置', 1)->class('btn-default ' . $this->butonsSizeClass)->attr('onclick="location.replace(location.href)"');
        }

        $this->searchButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $class
     * @return $this
     */
    public function btnSubmit($label = '提&nbsp;&nbsp;交', $size = 1, $class = 'btn-info')
    {
        $this->fieldsEnd();
        $this->button('submit', $label, $size)->class($class . ' ' . $this->butonsSizeClass);
        $this->searchButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $class
     * @return $this
     */
    public function btnReset($label = '重&nbsp;&nbsp;置', $size = 1, $class = 'btn-warning')
    {
        $this->button('reset', $label, $size)->class($class . ' ' . $this->butonsSizeClass)->attr('onclick="location.replace(location.href)"');
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        if (!$this->open) {
            $this->addClass('hidden');
        }

        $empty = empty($this->rows);

        if (!$empty) {
            if (!$this->searchButtonsCalled) {
                $this->searchButtons();
            }
        } else {
            $this->addClass('form-empty');
        }

        $this->hidden('__page__')->value(1);
        $this->hidden('__pagesize__');
        $this->hidden('__search__')->value($this->id);
        $this->hidden('__table__')->value($this->tableId);
        $this->hidden('__sort__');
        $this->addClass('search-form');
        $this->button('refresh', 'refresh', 1)->addClass('search-refresh hidden');
        $this->searchScript();

        foreach ($this->rows as $row) {
            if (!$row instanceof SRow) {
                $row->beforRender();
                continue;
            }

            $displayer = $row->getDisplayer();

            $displayer->fullSize(4);

            $row->beforRender();
        }

        return $this;
    }

    protected function searchScript()
    {
        $form = $this->getFormId();

        $extKey = '-' . $this->tableId;

        $script = <<<EOT

        $(document).bind('keyup', function(e) {
            if (event.keyCode === 13) {
                window.__forms__['{$form}'].formSubmit();
                return false;
            }
            if (event.keyCode === 0x1B) {
                if($('#{$form} form').hasClass('form-empty'))
                {
                    return true;
                }
                var index = layer.msg('重置筛选条件？', {
                    time: 2000,
                    btn: ['确定', '取消'],
                    yes: function (params) {
                        location.replace(location.href);
                    }
                });
                return false; //阻止系统默认esc事件
            }
        });

        $('body').on('click', '#{$this->tableId} ul.pagination li a:not(.goto-page)', function(){
            var page = $(this).attr('href').replace(/.*\?page=(\d+).*/,'$1');
            $('#{$form} form input[name="__page__"]').val(page);
            window.__forms__['{$form}'].formSubmit();
            return false;
        });

        $('body').on('click', '#{$this->tableId} ul.pagination .goto-page', function(){
            var last = $(this).data('last');
            $.confirm({
                title: '跳转页面',
                content: '<div class="form-group">' +
                '<input id="page-input" type="text" placeholder="请输入页码(1~' + last + ')" class="name form-control"/>' +
                '</div>',
                buttons: {
                    formSubmit: {
                        text: '跳转',
                        btnClass: 'btn-success',
                        action: function () {
                            var page = $('#page-input').val().replace(/\D/,'');
                            if(!page || page <1)
                            {
                                $.alert('输入有误');
                                return false;
                            }
                            else if(page > last)
                            {
                                $.alert('页码不能超过:' + last);
                                return false;
                            }
                            $('#{$form} form input[name="__page__"]').val(page);
                            window.__forms__['{$form}'].formSubmit();
                        }
                    },
                    cancel: {
                        text: '取消'
                    },
                }
            });
        });

        $('body').on('click', '#{$this->tableId} #dropdown-pagesize-div .dropdown-menu li a', function(){
            var pagesize = $(this).data('key');
            var oldsize = $('#{$form} form input[name="__pagesize__"]').val();
            if(pagesize == oldsize)
            {
                return;
            }
            if(pagesize > oldsize)
            {
                $('#{$form} form input[name="__page__"]').val(1);
            }
            $('#{$form} form input[name="__pagesize__"]').val(pagesize);
            $('#{$this->tableId} #dropdown-pagesize-div').find('.pagesize-text').text(pagesize);
            window.__forms__['{$form}'].formSubmit();
        });

        $('body').on('click', '#btn-refresh{$extKey},#form-refresh{$extKey}', function(){
            if($('#{$this->tableId} .table-empty-text').size())//新增第一条数据后，直接刷新整个页面，有些js有数据的时候有效
            {
                location.replace(location.href);
            }
            else
            {
                window.__forms__['{$form}'].formSubmit();
            }
        });

        if(!$('#{$form} form').hasClass('form-empty'))
        {
            $('#btn-search{$extKey}').removeClass('hidden');
        }

        $('body').on('click', '#btn-search{$extKey}', function(){
            if($('#{$form} form').hasClass('hidden'))
            {
                $('#{$form} form').removeClass('hidden');
            }
            else
            {
                $('#{$form} form').slideToggle(300);
            }
        });

        $('body').on('click', '#btn-export{$extKey}', function(){
            var url = $(this).data('export-url');
            window.__forms__['{$form}'].exportPost(url, '', 1);
        });

        $('body').on('click', '#dropdown-exports{$extKey}-div .dropdown-menu li a', function(){
            var url = $('#dropdown-exports{$extKey}').data('export-url');
            var fileType = $(this).data('key');
            window.__forms__['{$form}'].exportPost(url, fileType, 1);
        });

        $('body').on('click', '#form-submit{$extKey}', function(){
            $('#$form form input[name="__page__"]').val(1);
            return window.__forms__['{$form}'].formSubmit();
        });

        $('body').on('click', '.table .sortable', function(){
            var sort = '';
            if($(this).hasClass('mdi-sort-descending'))
            {
                sort = $(this).data('key') + ' asc';
                $(this).removeClass('mdi-sort-descending').addClass('mdi-sort-ascending');
            }
            else
            {
                sort = $(this).data('key') + ' desc';
                $('.sortable.mdi-sort-ascending').removeClass('mdi-sort-ascending').addClass('mdi-sort');
                $('.sortable.mdi-sort-descending').removeClass('mdi-sort-descending').addClass('mdi-sort');
                $(this).removeClass('mdi-sort').addClass('mdi-sort-descending');
            }

            $('#$form form input[name="__sort__"]').val(sort);
            window.__forms__['{$form}'].formSubmit();
        });

EOT;
        Builder::getInstance()->addScript($script);

        return $script;
    }

    /**
     * Undocumented function
     *
     * @return string|ViewShow
     */
    public function render()
    {
        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'form.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'rows' => $this->rows,
            'action' => $this->action,
            'method' => strtoupper($this->method),
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'id' => $this->getFormId(),
            'ajax' => $this->ajax,
            'searchFor' => $this->tableId,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $row = new SRow($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 2, $count > 3 ? $arguments[3] : '');

            if ($this->__fields__) {
                $this->__fields__->addRow($row);
            } else {
                $this->rows[] = $row;
            }

            $row->setForm($this);

            $displayer = $row->$name($arguments[0], $row->getLabel());

            if ($this->defaultDisplayerSize) {
                $displayer->size($this->defaultDisplayerSize[0], $this->defaultDisplayerSize[1]);
            }

            $displayer->extKey('-' . $this->tableId);

            return $displayer;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
