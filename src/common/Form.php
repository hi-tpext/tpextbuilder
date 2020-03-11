<?php

namespace tpext\builder\common;

use think\Model;
use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\form\FieldsContent;
use tpext\builder\form\FWapper;
use tpext\builder\form\Row;
use tpext\builder\form\Step;
use tpext\builder\traits\HasDom;

/**
 * Form class
 */
class Form extends FWapper implements Renderable
{
    use HasDom;

    protected $view = '';

    protected $action = '';

    protected $id = 'the-form';

    protected $method = 'post';

    protected $rows = [];

    protected $botttomButtonsCalled = false;

    protected $ajax = true;

    protected $defaultDisplayerSize = null;

    protected $validator = [];

    protected $butonsSizeClass = 'btn-sm';

    /**
     * Undocumented variable
     *
     * @var Tab
     */
    protected $tab = null;

    /**
     * Undocumented variable
     *
     * @var Step
     */
    protected $step = null;

    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields_content__ = null;

    public function __construct()
    {
        $this->class = 'form-horizontal';
        $this->rand = mt_rand(1000, 9999);
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
     * @param boolean $val
     * @return $this
     */
    public function ajax($val)
    {
        $this->ajax = $val;
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
     *
     * @param string $val
     * @return $this
     */
    public function action($val)
    {
        $this->action = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function method($val)
    {
        $this->method = $val;
        return $this;
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
     * @return Tab
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param boolean $active
     * @param string $name
     * @return $this
     */
    public function tab($label, $active = false, $name = '')
    {
        if (empty($this->tab)) {
            $this->tab = new Tab();
            $this->rows[] = $this->tab;
        }

        $this->__fields_content__ = $this->tab->addFieldsContent($label, $active, $name);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Step
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $description
     * @param boolean $active
     * @param string $name
     * @return $this
     */
    public function step($label, $description = '', $active = false, $name = '')
    {
        if (empty($this->step)) {
            $this->step = new Step();
            $this->rows[] = $this->step;
        }

        $this->__fields_content__ = $this->step->addFieldsContent($label, $description, $active, $name);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function fieldsContentEnd()
    {
        $this->__fields_content__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function getFieldsContent()
    {
        return $this->__fields_content__;
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
     * @param array|Model $data
     * @return $this
     */
    public function fill($data = [])
    {
        foreach ($this->rows as $row) {
            $row->fill($data);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $create
     * @return $this
     */
    public function bottomButtons($create = true)
    {
        if ($create) {
            $this->fieldsContentEnd();
            $this->divider('', '', 12)->size(0, 12)->showLabel(false);
            $this->html('', '', 4)->showLabel(false);
            $this->btnSubmit();
            $this->html('', '', 3)->showLabel(false);
            $this->btnReset();
        }

        $this->botttomButtonsCalled = true;
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
    public function btnSubmit($label = '提&nbsp;&nbsp;交', $size = 1, $class = 'btn-success')
    {
        $this->button('submit', $label, $size)->class($class . ' ' . $this->butonsSizeClass);
        $this->botttomButtonsCalled = true;
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
        $this->button('reset', $label, $size)->class($class . ' ' . $this->butonsSizeClass);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer $size
     * @param string $class
     * @param string $attr
     * @return $this
     */
    public function btnBack($label = '返&nbsp;&nbsp;回', $size = 1, $class = 'btn-default btn-go-back', $attr = 'onclick="history.go(-1);')
    {
        $this->button('button', $label, $size)->class($class . ' ' . $this->butonsSizeClass)->attr($attr);
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
    public function btnLayerClose($label = '返&nbsp;&nbsp;回', $size = 1, $class = 'btn-default')
    {
        $this->button('button', $label, $size)->class($class . ' btn-close-layer' . ' ' . $this->butonsSizeClass);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        if (!$this->botttomButtonsCalled && empty($this->step)) {
            $this->bottomButtons(true);
        }

        foreach ($this->rows as $row) {
            if (!$row instanceof Row) {
                $row->beforRender();
                continue;
            }

            $displayer = $row->getDisplayer();

            if ($displayer->isRequired()) {
                $this->validator[$displayer->getName()]['required'] = true;
            }

            $row->beforRender();
        }

        $this->validatorScript();

        return $this;
    }

    protected function validatorScript()
    {
        $form = $this->getFormId();

        $rules = json_encode($this->validator);

        $script = <<<EOT
        $('#{$form} form').validate({
            ignore: ".ignore",    // 插件默认不验证隐藏元素,这里可以自定义一个不验证的class,即验证隐藏元素,不验证class为.ignore的元素
            focusInvalid: false,  // 禁用无效元素的聚焦
            rules: {$rules},
            errorPlacement: function errorPlacement(error, element) {
                var parent = $(element).parents('.form-group');
                parent.addClass('has-error');
                $('#help-block').removeClass('hidden');
                $('#help-block .error-label').html(parent.find('.control-label').text() + error.text());
            },
            highlight: function(element) {
                var el = $(element);
                if (el.hasClass('js-tags-input')) {
                    el.next('.tagsinput').addClass('is-invalid');  // tags插件所隐藏的输入框没法实时验证，比较尴尬
                }
            },
            unhighlight: function(element) {
                $(element).next('.tagsinput').removeClass('is-invalid');
                $(element).parents('.form-group').removeClass('has-error');
            },
            submitHandler: function(form) {
                return window.forms['{$form}'].formSubmit();
            }
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
            'ajax' => $this->ajax ? 1 : 0,
            'search' => '',
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $row = new Row($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 12, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            if ($this->__fields_content__) {
                $this->__fields_content__->addRow($row);
            } else {
                $this->rows[] = $row;
            }

            $displayer = $row->$name($arguments[0], $row->getLabel());

            if ($this->defaultDisplayerSize) {
                $displayer->size($this->defaultDisplayerSize[0], $this->defaultDisplayerSize[1]);
            }

            if ($name == 'button') {
                $displayer->tableRowKey($this->rand);
            }

            return $displayer;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
