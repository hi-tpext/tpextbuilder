<?php

namespace tpext\builder\form;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Plugin;
use tpext\builder\common\Renderable;

class Step implements Renderable
{
    private $view = '';

    protected $class = '';

    protected $rows = [];

    protected $labels = [];

    protected $descriptions = [];

    protected $active = '';

    protected $id = '';

    protected $mode = 'dots';

    public function getId()
    {
        if (empty($this->id)) {
            $this->id = mt_rand(1000, 9999);
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
            $name = '' . count($this->rows);
        }

        if (count($this->rows) == 0) {
            $this->active = $name;
        }

        if ($isActive) {
            $this->actives[$name] = $name;
        }

        $content = new FieldsContent();

        $this->rows[$name] = $content;
        $this->labels[$name] = $label;
        $this->descriptions[$name] = $description;

        return $content;
    }

    public function anchor()
    {
        $this->mode = 'anchor';
    }

    public function dots()
    {
        $this->mode = 'dots';
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function class ($val)
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
    public function actives($val)
    {
        $this->active = $val;
        return $this;
    }

    protected function stepScript()
    {
        $script = <<<EOT

        $('.guide-box').bootstrapWizard({
            'tabClass': 'nav-step',
            'nextSelector': '[data-wizard="next"]',
            'previousSelector': '[data-wizard="prev"]',
            'finishSelector': '[data-wizard="finish"]',
            'onTabClick': function(e, t, i) {
                if (!$('.guide-box').is('[data-navigateable="true"]')) return ! 1
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

    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row->beforRender();
        }

        Builder::getInstance()->addJs('/assets/tpextbuilder/js/jquery.bootstrap.wizard.min.js');

        $this->stepScript();
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render($partial = false)
    {
        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'form', 'step.html']);

        $vars = [
            'labels' => $this->labels,
            'descriptions' => $this->descriptions,
            'rows' => $this->rows,
            'active' => $this->active,
            'id' => $this->getId(),
            'class' => ($this->mode == 'anchor' ? 'step-anchor' : 'step-dots') . ' ' . $this->class,
            'mode' => $this->mode,
        ];

        $viewshow = new ViewShow($this->view);

        if ($partial) {
            return $viewshow->assign($vars);
        }

        return $viewshow->assign($vars)->getContent();
    }
}
