<?php

namespace tpext\builder\common;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\form\FieldsContent;
use tpext\builder\form\Fillable;
use tpext\builder\search\SRow;
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

    protected $open = true;

    protected $rand = 0;

    protected $tableRand = 0;

    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields__ = null;

    public function __construct()
    {
        $this->class = 'form-horizontal';
        $this->rand = input('__search__', mt_rand(1000, 9999));
        $this->id .= $this->rand;
    }

    /**
     * Undocumented function
     *
     * @return int
     */
    public function getRand()
    {
        return $this->rand;
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
        return $this->rows();
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
     * @param Table $val
     * @return $this
     */
    public function search($val)
    {
        $this->search = $val->getTableId();
        $this->tableRand = $val->getRand();
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
    public function searchButtons($create = true)
    {
        if ($create) {
            $this->fieldsEnd();
            $this->html('', '', 1)->showLabel(false);
            $this->button('submit', '筛&nbsp;&nbsp;选', 1)->class('btn-success btn-xs');
            $this->button('button', '重&nbsp;&nbsp;置', 1)->class('btn-default btn-xs')->attr('onclick="location.replace(location.href)"');
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
    public function btnSubmit($label = '提&nbsp;&nbsp;交', $size = 1, $class = 'btn-success btn-xs')
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
        $this->hidden('__search__')->value($this->rand);
        $this->hidden('__table__')->value($this->tableRand);
        $this->hidden('__sort__');
        $this->addClass('search-form');
        $this->button('refresh', 'refresh', 1)->addClass('search-refresh')->getWapper()->class('hidden');
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

        $script = <<<EOT
        $('body').on('click', '#{$this->search} ul.pagination li a', function(){
            var page = $(this).attr('href').replace(/.*\?page=(\d+).*/,'$1');
            $('#{$form} form input[name="__page__"]').val(page);
            window.forms['{$form}'].formSubmit();
            return false;
        });

        $('body').on('click', '#btn-refresh{$this->tableRand},#form-refresh{$this->rand}', function(){
            window.forms['{$form}'].formSubmit();
        });

        if(!$('#{$form} form').hasClass('form-empty'))
        {
            $('#btn-search{$this->tableRand}').removeClass('hidden');
        }

        $('body').on('click', '#btn-search{$this->tableRand}', function(){
            if($('#{$form} form').hasClass('hidden'))
            {
                $('#{$form} form').removeClass('hidden');
            }
            else
            {
                $('#{$form} form').slideToggle(300);
            }
        });

        $('body').on('click', '#btn-export{$this->tableRand}', function(){
            var url = $(this).data('export-url');
            window.forms['{$form}'].exportPost(url, '');
        });

        $('body').on('click', '#dropdown-exports{$this->tableRand}-div .dropdown-menu li a', function(){
            var url = $('#dropdown-exports{$this->tableRand}').data('export-url');
            var fileType = $(this).data('key');
            window.forms['{$form}'].exportPost(url, fileType);
        });

        $('body').on('click', '#form-submit{$this->rand}', function(){
            $('#$form form input[name="__page__"]').val(1);
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

            $('#$form form input[name="__sort__"]').val(sort);
            window.forms['{$form}'].formSubmit();
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
            'search' => $this->search,
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

            $row = new SRow($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 3, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

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

            $displayer->extKey($this->rand);

            return $displayer;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
