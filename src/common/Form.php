<?php

namespace tpext\builder\common;

use think\Model;
use think\Collection;
use tpext\think\View;
use tpext\common\ExtLoader;
use tpext\builder\form\FRow;
use tpext\builder\form\Step;
use tpext\builder\form\When;
use tpext\builder\common\Module;
use tpext\builder\form\Fillable;
use tpext\builder\form\FWrapper;
use tpext\builder\traits\HasDom;
use tpext\builder\common\Builder;
use tpext\builder\displayer\Field;
use tpext\builder\displayer\Items;
use tpext\builder\displayer\Fields;
use tpext\builder\form\ItemsContent;
use tpext\builder\inface\Renderable;
use tpext\builder\form\FieldsContent;
use tpext\builder\displayer\MultipleFile;

/**
 * Form class
 */
class Form extends FWrapper implements Renderable
{
    use HasDom;

    protected $view = '';

    protected $action = '';

    protected $id = 'the-form';

    protected $method = 'post';

    protected $rows = [];

    protected $data = [];

    protected $botttomButtonsCalled = false;

    protected $bottomOffsetCalled = false;

    protected $ajax = true;

    protected $defaultDisplayerSize = null;

    protected $defaultDisplayerColSize = 12;

    protected $validator = [];

    protected $butonsSizeClass = 'btn-sm';

    protected $readonly = false;

    protected $partial = false;

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
    protected $__tabs_content__ = null;

    /**
     * Undocumented variable
     *
     * @var FieldsContent
     */
    protected $__fields__ = null;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $__fields__bag__ = [];

    /**
     * Undocumented variable
     *
     * @var ItemsContent
     */
    protected $__items__ = null;

    /**
     * Undocumented variable
     *
     * @var When
     */
    protected $__when__ = null;

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function created()
    {
        $this->class = 'form-horizontal';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param FRow|Fillable $row
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
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = true)
    {
        if ($val) {
            foreach ($this->rows as $row) {

                if ($row instanceof Tab || $row instanceof Step) {
                    $row->readonly($val);
                    continue;
                }

                if (!($row instanceof FRow)) {
                    continue;
                }

                $row->getDisplayer()->readonly($val);
            }
        }

        $this->readonly = $val;

        return $this;
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
        $this->action = (string)$val;
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
     * @return string
     */
    public function getButonsSizeClass()
    {
        return $this->butonsSizeClass;
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
     * @return FieldsContent
     */
    public function tab($label, $active = false, $name = '')
    {
        $this->__fields__ = null;
        $this->__items__ = null;

        if (empty($this->tab)) {
            $this->tab = new Tab();
            $this->rows[] = $this->tab;
        }

        $this->__tabs_content__ = $this->tab->addFieldsContent($label, $active, $name);
        $this->__tabs_content__->setForm($this);
        return $this->__tabs_content__;
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
     * @return FieldsContent
     */
    public function step($label, $description = '', $active = false, $name = '')
    {
        $this->__fields__ = null;
        $this->__items__ = null;

        if (empty($this->step)) {
            $this->step = new Step();
            $this->rows[] = $this->step;
        }

        $this->__tabs_content__ = $this->step->addFieldsContent($label, $description, $active, $name);
        $this->__tabs_content__->setForm($this);
        return $this->__tabs_content__;
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function createFields()
    {
        if ($this->__fields__) {
            $this->__fields__bag__[] = $this->__fields__;
        }
        $this->__fields__ = new FieldsContent();
        $this->__fields__->setForm($this);
        return $this->__fields__;
    }

    /**
     * Undocumented function
     *
     * @return ItemsContent
     */
    public function createItems()
    {
        $this->__items__ = new ItemsContent();
        $this->__items__->setForm($this);
        return $this->__items__;
    }

    /**
     * Undocumented function
     * @param Field $watchFor
     * @param string|int|array $cases
     * @return When
     */
    public function createWhen($watchFor, $cases)
    {
        $this->__when__ = new When();
        $this->__when__->watch($watchFor, $cases);
        $this->__when__->setForm($this);
        return $this->__when__;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function fieldsEnd()
    {
        $this->__fields__ = array_pop($this->__fields__bag__);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function itemsEnd()
    {
        $this->__items__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function whenEnd()
    {
        $this->__when__ = null;
        return $this;
    }


    /**
     * Undocumented function
     *
     * @return $this
     */
    public function tabEnd()
    {
        $this->__tabs_content__ = null;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function stepEnd()
    {
        $this->__tabs_content__ = null;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function allContentsEnd()
    {
        $this->__fields__ = null;
        $this->__tabs_content__ = null;
        $this->__items__content = null;
        $this->__when__ = null;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FieldsContent
     */
    public function getTabsContent()
    {
        return $this->__tabs_content__;
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
     * @param integer $size
     * @return $this
     */
    public function defaultDisplayerColSize($size = 12)
    {
        $this->defaultDisplayerColSize = $size;
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
        $this->data = $data;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param string $rule
     * @param boolean $val
     * @return $this
     */
    public function addJqValidatorRule($name, $rule, $val = true)
    {
        $this->validator[$name][$rule] = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array|Model
     */
    public function getData()
    {
        return $this->data;
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
            if ($this->readonly) {
                $this->btnLayerClose();
            } else {
                $this->btnSubmit();
                $this->btnReset();
            }
        }

        $this->fieldsEnd();

        $this->botttomButtonsCalled = true;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function bottomOffset()
    {
        if ($this->bottomOffsetCalled) {
            return $this;
        }
        $this->allContentsEnd();
        $this->html('', '', '12 col-lg-12 col-sm-12 col-xs-12')->value(''); //这里时一个空行
        //此处开启了一个fields装载后面的操作按钮，不会调用fieldsEnd了，正常情况下，底部按钮后面不会有其他元素了。如果有，需要调用fieldsEnd结束按钮区域
        //col-lg 比例:      左(4) | 中部按钮组(4) | 右(4)
        //clo-md 比例:      左(4) | 中部按钮组(4) | 右(4)
        //col-sm 比例:      左(3) | 中部按钮组(6) | 右(3)
        //col-xs 比例:      左(2) | 中部按钮组(8) | 右(2)
        $this->html('', '', '4 col-lg-4 col-sm-3 col-xs-2')->showLabel(false); //左侧offset 4,4,3,2
        $this->fields('bottom_buttons', '', '4 col-lg-4 col-sm-6 col-xs-8 bottom-buttons') //中间按钮组 4,4,6,8
            ->size(0, '12 col-lg-12 col-sm-12 col-xs-12')->showLabel(false);

        $this->bottomOffsetCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer|string $size
     * @param string $class
     * @return $this
     */
    public function btnSubmit($label = '提&nbsp;&nbsp;交', $size = '6 col-lg-6 col-sm-6 col-xs-6', $class = 'btn-info')
    {
        $this->bottomOffset();
        $this->button('submit', $label, $size)->class($class . ' ' . $this->butonsSizeClass);
        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer|string $size
     * @param string $class
     * @return $this
     */
    public function btnReset($label = '重&nbsp;&nbsp;置', $size = '6 col-lg-6 col-sm-6 col-xs-6', $class = 'btn-warning')
    {
        $this->bottomOffset();
        $this->button('reset', $label, $size)->class($class . ' ' . $this->butonsSizeClass);
        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer|string $size
     * @param string $class
     * @param string $attr
     * @return $this
     */
    public function btnBack($label = '返&nbsp;&nbsp;回', $size = '6 col-lg-6 col-sm-6 col-xs-6', $class = 'btn-default btn-go-back', $attr = 'onclick="history.go(-1);')
    {
        $this->bottomOffset();
        $this->button('button', $label, $size)->class($class . ' ' . $this->butonsSizeClass)->addAttr($attr);
        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param integer|string $size
     * @param string $class
     * @return $this
     */
    public function btnLayerClose($label = '返&nbsp;&nbsp;回', $size = '12 col-lg-12 col-sm-12 col-xs-12', $class = 'btn-default')
    {
        $this->bottomOffset();
        $this->button('button', $label, $size)->class($class . ' btn-close-layer' . ' ' . $this->butonsSizeClass);
        $this->botttomButtonsCalled = true;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $colSize col大小
     * @param Closure|null $fieldsCall
     * @return Fields
     */
    public function left($colSize = 6, $fieldsCall = null)
    {
        $this->fieldsEnd(); //清理，避免被包含到其他fields中。因为fields可以包含fields的
        $displayer =  $this->fields('left' . mt_rand(10, 99), '', $colSize)->size(0, 12)->showLabel(false);

        if ($fieldsCall) {
            if (!($fieldsCall instanceof \Closure)) {
                throw new \UnexpectedValueException('fieldsCall参数只能是\\Closure或null(若为null，请后续再使用->with(...$fields))');
            }
            $fieldsCall($this);
            $this->fieldsEnd();
            //如果传入了fields，这里结束掉。如果未传，后面可以再使用->with(...$fields)
            // $form->left(6, function(){//...$fields}); 或者 $form->left(6)->with(...$fields);
        }

        return $displayer;
    }

    /**
     * Undocumented function
     *
     * @param integer $colSize col大小
     * @param Closure|null $fieldsCall
     * @return Fields
     */
    public function middle($colSize = 6, $fieldsCall = null)
    {
        $this->fieldsEnd(); //同上
        $displayer =  $this->fields('middle' . mt_rand(10, 99), '', $colSize)->size(0, 12)->showLabel(false);

        if ($fieldsCall) {
            if (!($fieldsCall instanceof \Closure)) {
                throw new \UnexpectedValueException('fieldsCall参数只能是\\Closure或null(若为null，请后续再使用->with(...$fields))');
            }
            $fieldsCall($this);
            $this->fieldsEnd();
        }

        return $displayer;
    }

    /**
     * Undocumented function
     *
     * @param integer $colSize col大小
     * @param Closure|null $fieldsCall
     * @return Fields
     */
    public function right($colSize = 6, $fieldsCall = null)
    {
        $this->fieldsEnd(); //同上
        $displayer =  $this->fields('right' . mt_rand(10, 99), '', $colSize)->size(0, 12)->showLabel(false);

        if ($fieldsCall) {
            if (!($fieldsCall instanceof \Closure)) {
                throw new \UnexpectedValueException('fieldsCall参数只能是\\Closure或null(若为null，请后续再使用->with(...$fields))');
            }
            $fieldsCall($this);
            $this->fieldsEnd();
        }

        return $displayer;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param array|Collection dataList
     * @param Closure|null $itemsCall
     * @param array displayerSize 大小 [12, 12] 为上下结构，[2, 10]为左右结构
     * @return Items
     */
    public function logs($label, $dataList, $itemsCall = null, $displayerSize = [12, 12])
    {
        $this->itemsEnd();
        $displayer =  $this->items('logs' . mt_rand(10, 99), $label, 12)->size($displayerSize[0], $displayerSize[1])->readonly();

        if (is_array($dataList)) {
            $displayer->fill($dataList);
        } else if ($dataList instanceof Collection) {
            $displayer->dataWithId($dataList);
        }
        if ($itemsCall) {
            if (!($itemsCall instanceof \Closure)) {
                throw new \UnexpectedValueException('itemsCall参数只能是\\Closure或null(后续再使用->with(...$fields))');
            }
            $itemsCall($this);
            $this->fieldsEnd();
        }
        return $displayer;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        ExtLoader::trigger('tpext_form_befor_render', $this);

        if (!$this->botttomButtonsCalled && empty($this->step)) {
            $this->bottomButtons(true);
        }
        foreach ($this->rows as $row) {
            $row->fill($this->data);

            if (!($row instanceof FRow)) {
                $row->beforRender();
                continue;
            }

            $displayer = $row->getDisplayer();

            if ($displayer->isRequired()) {
                $this->validator[$displayer->getName()]['required'] = true;
            }

            $row->beforRender();
        }

        if (!in_array(strtolower($this->method), ['get', 'post'])) {
            $this->hidden('_method')->value($this->method);
            $this->method = 'post';
        }

        $this->validatorScript();

        return $this;
    }

    protected function validatorScript()
    {
        $form = $this->getFormId();

        $rules = json_encode($this->validator);

        $script = <<<EOT

        window.focus();

        $(document).bind('keyup', function(event) {
            if (event.keyCode === 0x1B) {
                var index = layer.msg('关闭当前弹窗？', {
                    time: 2000,
                    btn: ['确定', '取消'],
                    yes: function (params) {
                        layer.close(index);
                        var index2 = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index2);
                    }
                });
                return false; //阻止系统默认esc事件
            }
        });

        $('#{$form} form').validate({
            ignore: ".ignore",    // 插件默认不验证隐藏元素,这里可以自定义一个不验证的class,即验证隐藏元素,不验证class为.ignore的元素
            focusInvalid: false,  // 禁用无效元素的聚焦
            rules: {$rules},
            errorPlacement: function errorPlacement(error, element) {
                var parent = $(element).closest('div.form-group');
                if($(element).hasClass('item-field'))
                {
                    $('#help-block .error-label').html(parent.find('.control-label,.full-label').text() + $(element).data('label') + '* 这是必填字段');
                    $(element).closest('td').addClass('has-error');
                    return;
                }
                parent.addClass('has-error');
                $('#help-block .error-label').html(parent.find('.control-label,.full-label').text() + '* ' + error.text());
            },
            highlight: function(element) {
                var el = $(element);
                if (el.hasClass('js-tags-input')) {
                    el.next('.tagsinput').addClass('is-invalid');  // tags插件所隐藏的输入框没法实时验证，比较尴尬
                }
            },
            unhighlight: function(element) {
                $(element).next('.tagsinput').removeClass('is-invalid');
                if($(element).hasClass('item-field'))
                {
                    $(element).closest('td').removeClass('has-error');
                }
                $(element).closest('div.form-group').removeClass('has-error');
                if($('.form-group.has-error').size() == 0 && $('.item-field.has-error').size() == 0)
                {
                    $('#help-block .error-label').html('&nbsp;');
                }
            },
            submitHandler: function(form) {
                return window.__forms__['{$form}'].formSubmit();
            }
        });

EOT;
        Builder::getInstance()->addScript($script);

        return $script;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [];
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getViewemplate()
    {
        $template = Module::getInstance()->getViewsPath() . 'form.html';

        return $template;
    }

    /**
     * Undocumented function
     *
     * @return string|View
     */
    public function render()
    {
        $viewshow = new View($this->getViewemplate());

        $vars = [
            'rows' => $this->rows,
            'action' => $this->action,
            'method' => strtoupper($this->method),
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'id' => $this->getFormId(),
            'ajax' => $this->ajax ? 1 : 0,
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        if ($this->partial) {
            return $viewshow->assign($vars);
        }

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

            $row = FRow::make($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : ($name == 'button' ? 1 : $this->defaultDisplayerColSize));

            if ($this->__fields__) {
                $this->__fields__->addRow($row);
            } else if ($this->__items__) {

                $row->class('text-center');
                $this->__items__->addCol($arguments[0], $row);
            } else if ($this->__tabs_content__) {

                $this->__tabs_content__->addRow($row);
            } else {

                $this->rows[] = $row;
            }

            $row->setForm($this);

            $displayer = $row->$name($arguments[0], $row->getLabel());

            if ($this->__when__) {
                $this->__when__->toggle($displayer);
            }

            if ($this->defaultDisplayerSize) {
                $displayer->size($this->defaultDisplayerSize[0], $this->defaultDisplayerSize[1]);
            }

            if ($name == 'button') {
                $displayer->extKey('-' . $this->id . mt_rand(10, 99));
            }

            if ($this->__items__ && $displayer instanceof MultipleFile) { //表格中默认禁止直接上传图片
                $displayer->setIsInTable();
            }

            return $displayer;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }

    /**
     * 创建自身
     *
     * @param mixed $arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return Widget::makeWidget('Form', $arguments);
    }
}
