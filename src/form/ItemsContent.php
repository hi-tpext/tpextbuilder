<?php

namespace tpext\builder\form;

use think\Collection;
use tpext\builder\common\Form;
use tpext\builder\common\Module;
use tpext\builder\displayer\Field;
use tpext\builder\form\FRow;
use tpext\builder\table\Actionbar;
use tpext\builder\traits\HasDom;
use tpext\think\View;

class ItemsContent extends FWrapper
{
    use HasDom;

    protected $view = 'itemscontent';

    protected $headers = [];

    protected $cols = [];

    protected $data = [];

    protected $list = [];

    protected $script = [];

    protected $pk = 'id';

    protected $ids = [];

    protected $emptyText = '';

    protected $isInitData = false;

    protected $actionRowText = '';

    protected $canDelete = true;

    protected $canAdd = true;

    protected $name = '';

    protected $template = [];

    /**
     * Undocumented variable
     *
     * @var Actionbar
     */
    protected $actionbar = null;

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $form;

    /**
     * Undocumented variable
     *
     * @var \Closure
     */
    protected $templateFieldCall = null;

    public function __construct()
    {
        $this->class = 'table-striped table-hover table-bordered table-condensed table-responsive';

        $this->actionRowText = __blang('bilder_action_operation');
        $this->emptyText = '<span>' . __blang('bilder_no_relevant_data') . '</span>';
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return void
     */
    public function name($val)
    {
        $this->name = $val;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function actionRowText($val)
    {
        $this->actionRowText = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param FRow|Field|Fillable $row
     * @return $this
     */
    public function addCol($name, $col)
    {
        $this->headers[$name] = $col->getLabel();
        $this->cols[$name] = $col;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return FRow[]
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * Undocumented function
     *
     * @param Form $val
     * @return $this
     */
    public function setForm($val)
    {
        $this->form = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
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

        $this->form->fieldsEnd();
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
        $this->canDelete = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function hasAction()
    {
        return $this->canDelete || $this->canAdd;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function canAdd($val)
    {
        $this->canAdd = $val;
        return $this;
    }

    /**
     * Undocumented function
     * 主键, 默认 为 'id'
     * @param string $val
     * @return $this
     */
    public function pk($val)
    {
        $this->pk = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection $data
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
     * @return array|Collection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = true)
    {
        foreach ($this->cols as $col) {
            $col->getDisplayer()->readonly($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clearScript()
    {
        foreach ($this->cols as $col) {
            if (!($col instanceof FRow)) {
                continue;
            }

            $col->getDisplayer()->clearScript();
        }
        return $this;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function templateFieldCall($callback)
    {
        $this->templateFieldCall = $callback;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $this->initData();

        return $this;
    }

    protected function initData()
    {
        $this->list = [];

        $pk = $this->pk;

        $cols = array_keys($this->cols);

        foreach ($this->data as $key => $data) {

            if (isset($data[$pk])) {

                $this->ids[$key] = $data[$pk];
            } else {
                $this->ids[$key] = $key;
            }

            foreach ($cols as $col) {

                $colunm = $this->cols[$col];

                if (!($colunm instanceof FRow)) {
                    continue;
                }

                $displayer = $colunm->getDisplayer();

                $displayer->clearScript();

                $displayer
                    ->value('')
                    ->fill($data)
                    ->extKey($key . $this->name)
                    ->arrayName([$this->name . '[' . $this->ids[$key] . '][', ']'])
                    ->showLabel(false)
                    ->size('0', '12 col-lg-12 col-sm-12 col-xs-12')
                    ->addClass('item-field ' . ($displayer->isRequired() ? ' item-field-required' : ''))
                    ->addAttr('data-label="' . $colunm->getLabel() . '"')
                    ->beforRender();

                $this->list[$key][$col] = [
                    'displayer' => $displayer,
                    'value' => $displayer->render(),
                    'attr' => $displayer->getAttrWithStyle(),
                    'wrapper' => $colunm,
                    '__can_delete__' => isset($data['__can_delete__']) ? $data['__can_delete__'] : 1,
                ];
            }
        }

        foreach ($this->cols as $key => $colunm) {
            if (!($colunm instanceof FRow)) {
                continue;
            }
            $displayer = $colunm->getDisplayer();

            $displayer->clearScript();

            $isRequired = $displayer->isRequired();

            if ($isRequired) {
                $this->headers[$key] = $displayer->getLabel() . '<strong title="' . __blang('bilder_this_field_is_required') . '" class="field-required">*</strong>';
            }

            $displayer->required(false);

            if ($this->templateFieldCall) {
                $this->templateFieldCall->call($this, $displayer);
            }

            $displayer
                ->extKey($this->name)
                ->arrayName([$this->name . '[' . '__new__' . '][', ']'])
                ->showLabel(false)
                ->value('')
                ->size(12, 12)
                ->addClass('item-field ' . ($isRequired ? ' item-field-required' : ''))
                ->addAttr('data-label="' . $colunm->getLabel() . '"')
                ->beforRender();

            $displayer->extKey($this->name . '-no-init-script'); //模板的id改了，避免被初始化，添加以后再初始化

            $this->template[] = [
                'value' => $displayer->render(),
                'attr' => $displayer->getAttrWithStyle(),
                'wrapper' => $colunm,
            ];

            $script = $displayer->getScript();

            $this->script = array_merge($this->script, $script);
        }

        $this->isInitData = true;
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

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'form' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = new View($template);

        $vars = [
            'name' => $this->name,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'headers' => $this->headers,
            'cols' => $this->cols,
            'list' => $this->list,
            'data' => $this->data,
            'emptyText' => $this->emptyText,
            'ids' => $this->ids,
            'canDelete' => $this->canDelete,
            'actionRowText' => $this->actionRowText,
            'canAdd' => $this->canAdd,
            'script' => implode('', array_unique($this->script)),
            'template' => $this->template,
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $col = FRow::make($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 1);

            $this->headers[$arguments[0]] = $col->getLabel();
            $this->cols[$arguments[0]] = $col;

            return $col->$name($arguments[0], $col->getLabel());
        }

        throw new \InvalidArgumentException(__blang('bilder_invalid_argument_exception') . ' : ' . $name);
    }
}
