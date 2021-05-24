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
            $cases = [$cases];
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
     * @return string
     */
    public function whenScript()
    {
        $script = '';

        $watchFor = $this->watchFor->getId();

        $names = [];
        foreach ($this->fields as $field) {
            $names[] = '.row-' . $field->getName() . '-div';
        }

        $triggerNames = implode(',', $names);

        $key = preg_replace('/\W/', '', $watchFor . $triggerNames);

        $cases = json_encode($this->cases);

        $fieldType = class_basename($this->watchFor);

        $box = '';

        if ($fieldType == 'Checkbox') {
            $box = ' input:checkbox';
        } else if ($fieldType == 'Checkbox') {
            $box = ' input:radio';
        }

        $script = <<<EOT

        var cases{$key} =  {$cases};
        var fieldType{$key} =  '{$fieldType}';

        $("#{$watchFor}{$box}").on('change', function(){
            var match = false;

            if(fieldType{$key} == 'Checkbox' || fieldType{$key} == 'DualListbox' || fieldType{$key} == 'MultipleSelect')
            {
                var val = [];
                if(fieldType{$key} == 'Checkbox')
                {
                    var checkboxes = $("#{$watchFor} input:checkbox");
                    checkboxes.each(function (i, e) {
                        if ($(e).is(':checked')) {
                            val.push($(e).val());
                        }
                    });
                }
                else
                {
                    var val = $(this).val() || [];
                }
                var cases = [];
                var m = 0;
                for(var i in cases{$key})
                {
                    cases = cases{$key}[i].split('+');
                    if(val.length != cases.length)
                    {
                        continue;
                    }
                    m = 0;
                    for(var j in val)
                    {
                        for(var k in cases)
                        {
                            if(val[j] == cases[k])
                            {
                                m += 1;
                            }
                        }
                    }

                    if(m > 0 && m == val.length)
                    {
                        match = true;
                        break;
                    }
                }
            }
            else // Radio / Select
            {
                var val = '';
                if(fieldType{$key} == 'Radio')
                {
                    val = $("#{$watchFor} input:checked").val();
                }
                else
                {
                    val = $(this).val();
                }
                for(var i in cases{$key})
                {
                    if(val == cases{$key}[i])
                    {
                        match = true;
                        break;
                    }
                }
            }
            if(match)
            {
                $('{$triggerNames}').removeClass('hidden');
                $('{$triggerNames}').find('input,textadea,selcet').removeClass('ignore');//不验证
            }
            else
            {
                $('{$triggerNames}').addClass('hidden');
                $('{$triggerNames}').find('input,textadea,selcet').addClass('ignore');//不验证
            }
        }).trigger('change');

EOT;
        return $script;
    }
}
