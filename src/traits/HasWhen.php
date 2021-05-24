<?php

namespace tpext\builder\traits;

use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\form\When;

trait HasWhen
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $whens = [];

    /**
     * Undocumented variable
     *
     * @var When
     */
    protected $__when__ = null;

    /**
     * Undocumented function
     *
     * @param string|int|array $cases 如：'1' 或 '1+2' 或 ['1+2', '2+3']
     * @param mixed ...$toggleFields
     * @return $this
     */
    public function when($cases, ...$toggleFields)
    {
        $form = $this->getForm();
        $when = $form->createWhen($this, $cases);

        $this->__when__ = $when;
        $this->whens[] = $when;

        if (count($toggleFields)) {

            if ($toggleFields[0] instanceof \Closure) { //如果是匿名回调
                $toggleFields[0]($when);
            } else {
                if (is_array($toggleFields[0])) {
                    $toggleFields = $toggleFields[0];
                }
                foreach ($toggleFields as $field) {
                    $this->__when__->trigger($field);
                }
            }

            $form->whenEnd();
            $this->whenEnd();
            //如果此处传入[toggleFields]参数，那么就结束，后面就不要再调用toggleFields方法了。否则，后面可以继续调用toggleFields方法;
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param mixed ...$fields
     * @return $this
     */
    public function toggleFields(...$fields)
    {
        if (!$this->__when__) {
            throw new \LogicException('when(\$cases, ...\$toggleFields)第二个参数[toggleFields]已传入，后续不要继续');
        }

        $form = $this->getForm();

        if (count($fields)) {
            if ($fields[0] instanceof \Closure) {
                $fields[0]($form);
            } else {
                if (is_array($fields[0])) {
                    $fields = $fields[0];
                }
                foreach ($fields as $field) {
                    $this->__when__->trigger($field);
                }
            }
        }

        $form->whenEnd();

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
     * @return Form|Search
     */
    protected function getForm()
    {
        return $this->getWrapper()->getForm();
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    protected function parseWhens()
    {
        foreach ($this->whens as $when) {
            $this->script[] = $when->whenScript();
        }

        return $this;
    }
}
