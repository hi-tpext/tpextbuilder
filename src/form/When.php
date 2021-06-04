<?php

namespace tpext\builder\form;

use tpext\builder\displayer\Field;
use tpext\builder\common\Form;
use tpext\builder\common\Search;

class When
{
    /**
     * Undocumented variable
     *
     * @var Field
     */
    protected $watchFor = null;

    /**
     * Undocumented variable
     *
     * @var string|int|array
     */
    protected $cases = '';

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Undocumented variable
     *
     * @var Form|Search
     */
    protected $form;

    /**
     * Undocumented function
     *
     * @param $this $watchFor
     * @param string|int|array $cases
     * @return $this
     */
    public function watch($watchFor, $cases)
    {
        $this->watchFor = $watchFor;
        if (!is_array($cases)) {
            $cases = ['' . $cases];
        }
        $this->cases = $cases;
        //
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Field $field
     * @return $this
     */
    public function toggle($field)
    {
        $this->fields[] = $field;
        //
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Form|Search $val
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
     * @return Form|Search
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Undocumented function
     *
     * @return string|int|array
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @return $this
     */
    public function setWrapperClass($key)
    {
        foreach ($this->fields as $field) {

            $field->getWrapper()->addClass($key);
        }

        return $this;
    }
}
