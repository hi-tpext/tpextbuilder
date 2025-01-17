<?php

namespace tpext\builder\form;

use think\Model;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\form\FieldsContent;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\think\View;

class Step implements Renderable
{
    use HasDom;

    protected $view = 'step';

    protected $navigateable = true;

    protected $size = [2, 8];

    protected $rows = [];

    protected $labels = [];

    protected $active = '';

    protected $id = '';

    protected $mode = 'dots';

    protected $readonly = false;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $__fields__ = [];

    public function getId()
    {
        if (empty($this->id)) {
            $this->id = 'step-' . mt_rand(1000, 9999);
        }

        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param boolean $isActive
     * @param string $name
     * @return FieldsContent
     */
    public function addFieldsContent($label, $description = '', $isActive = false, $name = '')
    {
        if (empty($name)) {
            $name = (count($this->labels) + 1);
        }

        if (empty($this->active) && count($this->labels) == 0) {
            $this->active = $name;
        }

        if ($isActive) {
            $this->active = $name;
        }

        $content = new FieldsContent();
        $this->__fields__[] = $content;

        $this->rows[$name] = ['content' => $content, 'description' => $description, 'active' => ''];
        $this->labels[$name] = ['content' => $label, 'active' => ''];

        return $content;
    }

    /**
     * Undocumented function
     *
     * @param array|Model|\ArrayAccess $data
     * @return $this
     */
    public function fill($data = [])
    {
        foreach ($this->__fields__ as $content) {
            $content->fill($data);
        }
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
        foreach ($this->__fields__ as $content) {
            $content->readonly($val);
        }
        $this->readonly = $val;
        return $this;
    }

    public function isFieldsGroup()
    {
        return true;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function navigateable($val)
    {
        $this->navigateable = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $left
     * @param integer $width
     * @return $this
     */
    public function size($left = 2, $width = 8)
    {
        $this->size = [$left, $width];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function anchor()
    {
        $this->mode = 'anchor';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function dots()
    {
        $this->mode = 'dots';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function class($val)
    {
        $this->class = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addClass($val)
    {
        $this->class .= ' ' . $val;
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
        $names = array_keys($this->labels);

        if (in_array($val, $names)) {
            $this->active = $val;
        }

        return $this;
    }

    protected function stepScript()
    {
        $id = $this->getId();
        $navigateable = $this->navigateable ? 'true' : 'false';

        $script = <<<EOT

        $('#{$id}').bootstrapWizard({
            'tabClass': 'nav-step',
            'nextSelector': '[data-wizard="next"]',
            'previousSelector': '[data-wizard="prev"]',
            'finishSelector': '[data-wizard="finish"]',
            'onTabClick': function(e, t, i) {
                return {$navigateable};
            },
            'onTabShow': function(e, t, i) {
                t.children(":gt(" + i + ").complete").removeClass("complete");
                t.children(":lt(" + i + "):not(.complete)").addClass("complete");
            },
            'onFinish': function(e, t, i) {
                // 点击完成后处理提交
            }
        });

EOT;
        Builder::getInstance()->addScript($script);

        return $script;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getClass()
    {
        return empty($this->class) ? '' : ' ' . $this->class;
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
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row['content']->beforRender();
        }

        Builder::getInstance()->customJs('/assets/lightyearadmin/js/jquery.bootstrap.wizard.min.js');

        $this->stepScript();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render($partial = false)
    {
        $template = Module::getInstance()->getViewsPath() . 'form' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $names = array_keys($this->labels);

        foreach ($names as $name) {
            if ($name == $this->active) {
                $this->labels[$name]['active'] = 'active';
                $this->rows[$name]['active'] = 'active';

                break;
            } else {
                $this->labels[$name]['active'] = 'complete';
                $this->rows[$name]['active'] = 'complete';
            }
        }

        $vars = [
            'labels' => $this->labels,
            'rows' => $this->rows,
            'active' => $this->active,
            'id' => $this->getId(),
            'class' => ($this->mode == 'anchor' ? 'step-anchor' : 'step-dots') . ' ' . $this->class,
            'mode' => $this->mode,
            'size' => $this->size,
            'attr' => $this->getAttrWithStyle(),
            'readonly' => $this->readonly,
        ];

        $viewshow = new View($template);

        if ($partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function destroy()
    {
        $this->rows = null;
    }
}
