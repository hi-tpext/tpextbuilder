<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\search\Row;
use tpext\builder\search\SWapper;
use tpext\builder\traits\HasDom;

/**
 * Form class
 */
class Search extends SWapper implements Renderable
{
    use HasDom;

    protected $view = '';

    protected $action = '';

    protected $id = 'the-form-search';

    protected $method = 'post';

    protected $rows = [];

    protected $searchButtonsCalled = false;

    protected $ajax = true;

    protected $defaultDisplayerSize = null;

    protected $butonsSizeClass = 'btn-sm';

    public function __construct()
    {
        $this->class = 'form-horizontal';
    }

    /**
     * Undocumented function
     *
     * @param \tpext\builder\form\Row $row
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
        return $this->rows();
    }

    /**
     * Undocumented function
     *
     * @param Table $val
     * @return $this
     */
    public function search($val)
    {
        $this->search = $val->getTableId();
        $this->ajax = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function formId($val)
    {
        $this->id = $val;
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
    public function defaultDisplayerSize($label = 2, $element = 8)
    {
        $this->defaultDisplayerSize = [$label, $element];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function searchButtons()
    {
        $this->html('', '', 1)->showLabel(false);
        $this->button('submit', '筛&nbsp;&nbsp;选', 1)->class('btn-success btn-xs');
        $this->button('button', '重&nbsp;&nbsp;置', 1)->class('btn-default btn-xs')->attr('onclick="location.replace(location.href)"');

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
    public function btnSubmit($label = '提&nbsp;&nbsp;交', $size = 1, $class = 'btn-success btn-xs')
    {
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
    public function btnReset($label = '重&nbsp;&nbsp;置', $size = 1, $class = 'btn-warning btn-xs')
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
        $this->addClass('hidden');
        $empty = empty($this->rows);

        $this->hidden('__page__')->value(1);
        $this->hidden('__search__')->value(1);
        $this->hidden('__sort__');
        $this->addClass('search-form');
        $this->button('refresh', 'refresh', 1)->addClass('search-refresh')->getWapper()->class('hidden');
        $this->searchScript();

        if (!$empty) {
            if (!$this->searchButtonsCalled) {
                $this->searchButtons();
            }
        } else {
            $this->addClass('form-empty');
        }

        foreach ($this->rows as $row) {
            if (!$row instanceof Row) {
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

        $script = <<<EOT
        $('body').on('click', '#{$this->search} ul.pagination li a', function(){
            var page = $(this).attr('href').replace(/.*\?page=(\d+).*/,'$1');
            $('#form-__page__').val(page);
            window.forms['{$form}'].formSubmit();
            return false;
        });

        $('body').on('click', '#btn-refresh,#form-refresh', function(){
            window.forms['{$form}'].formSubmit();
        });

        if($('#{$form} form').hasClass('form-empty'))
        {
            $('#btn-search').remove();
        }

        $('body').on('click', '#btn-search', function(){
            if($('#{$form} form').hasClass('hidden'))
            {
                $('#{$form} form').removeClass('hidden');
            }
            else
            {
                $('#{$form} form').slideToggle(300);
            }
        });

        $('body').on('click', '#btn-export', function(){
            var url = $(this).data('export-url');
            window.forms['{$form}'].exportPost(url, '');
        });

        $('body').on('click', '#dropdown-exports-div .dropdown-menu li a', function(){
            var url = $('#dropdown-exports').data('export-url');
            var fileType = $(this).data('key');
            window.forms['{$form}'].exportPost(url, fileType);
        });

        $('body').on('click', '#form-submit', function(){
            $('#form-__page__').val(1);
            return window.forms['{$form}'].formSubmit();
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

            $('#form-__sort__').val(sort);
            window.forms['{$form}'].formSubmit();
        });

EOT;
        Builder::getInstance()->addScript($script);

        return $script;
    }

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
            'search' => $this->search,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $row = new Row($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 12, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            $this->rows[] = $row;

            $displayer = $row->$name($arguments[0], $row->getLabel());

            if ($this->defaultDisplayerSize) {
                $displayer->size($this->defaultDisplayerSize[0], $this->defaultDisplayerSize[1]);
            }

            return $displayer;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
