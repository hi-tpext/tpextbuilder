<?php

namespace tpext\builder\traits;

use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\form\When;
use tpext\builder\common\Builder;

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
     * @param string|int|array $cases 如：'1' 或 '1 + 2' 或 ['1 + 2', '2 + 3']
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
                    $this->__when__->toggle($field);
                }
            }

            $form->whenEnd();
            $this->whenEnd();
            //如果此处传入[toggleFields]参数，那么就结束，后面就不要再调用with($toggleFields)方法了。否则，后面可以继续调用with($toggleFields)方法;
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function emptyWhens()
    {
        return empty($this->whens);
    }

    /**
     * Undocumented function
     *
     * @param mixed ...$toggleFields
     * @return $this
     */
    public function with(...$toggleFields)
    {
        if (!$this->__when__) {
            throw new \LogicException('when(\$cases, ...\$toggleFields)第二个参数[toggleFields]已传入，后续不要继续');
        }

        $form = $this->getForm();

        if (count($toggleFields)) {
            if ($toggleFields[0] instanceof \Closure) {
                $toggleFields[0]($form);
            } else {
                if (is_array($toggleFields[0])) {
                    $toggleFields = $toggleFields[0];
                }
                foreach ($toggleFields as $field) {
                    $this->__when__->toggle($field);
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
     * @param boolean $viewModel 是否为view模式
     * @return string
     */
    public function whenScript($viewModel = false)
    {
        if (count($this->whens) == 0) {
            return '';
        }

        $watchFor = $this->getId();

        $key = 'w_' . $this->getName();

        $key = preg_replace('/\W/', '', $key);

        $casesOptions = [];

        $i = 1;
        foreach ($this->whens as $when) {
            $when->setWrapperClass($key . ' ' . $key . '_' . $i);
            $casesOptions[$key . '_' . $i] = $when->getCases();
            $i += 1;
        }

        $viewModel = $viewModel ? 1 : 0;
        $script = '';

        $casesOptions = json_encode($casesOptions);

        $fieldType = class_basename($this);

        $box = '';
        if ($viewModel) {
            $box = '__when__';
        } else {
            if ($fieldType == 'Checkbox') {
                $box = ' input:checkbox';
            } else if ($fieldType == 'Radio') {
                $box = ' input:radio';
            }
        }

        $script = <<<EOT

        var casesOptions{$key} = {$casesOptions};
        var fieldType{$key} = '{$fieldType}';
        var viewModel{$key} = '{$viewModel}' == '1';

        if(viewModel{$key})
        {
            var __field__ = document.createElement("input");
            __field__.type = "hidden";
            __field__.id = '{$watchFor}{$box}';
            __field__.value = $("#{$watchFor}").data('selected');
    
            $('.form-wrapper form').append(__field__);
        }

        $("#{$watchFor}{$box}").on('change', function(){
            $('#help-block .error-label').html('');
            $('.{$key}.match-case').removeClass('match-case');
            if(fieldType{$key} == 'Checkbox' || fieldType{$key} == 'Transfer' || fieldType{$key} == 'MultipleSelect')
            {
                var val = [];
                if(viewModel{$key})
                {
                    val = $(this).val().split(',');
                }
                else
                {
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
                        val = $(this).val() || [];
                    }
                }
                
                var cases = [];
                var m = 0;
                for(var c in casesOptions{$key})
                {
                    for(var i in casesOptions{$key}[c])
                    {
                        cases = ('' + casesOptions{$key}[c][i]).split('+');
                        if(val.length != cases.length)
                        {
                            continue;
                        }
                        m = 0;
                        for(var j in val)
                        {
                            for(var k in cases)
                            {
                                if(val[j] == cases[k].trim())
                                {
                                    m += 1;
                                }
                            }
                        }

                        if(m > 0 && m == val.length)
                        {
                            $('.' + c).addClass('match-case');
                            break;
                        }
                    }
                }
            }
            else // Radio / Select
            {
                var val = '';
                if(viewModel{$key})
                {
                    val = $(this).val();
                }
                else
                {
                    if(fieldType{$key} == 'Radio')
                    {
                        val = $("#{$watchFor} input:checked").val();
                    }
                    else
                    {
                        val = $(this).val();
                    }
                }
                
                for(var c in casesOptions{$key})
                {
                    for(var i in casesOptions{$key}[c])
                    {
                        if(val == ('' + casesOptions{$key}[c][i]).trim())
                        {
                            $('.' + c).addClass('match-case');
                            break;
                        }
                    }
                }
            }
            $('.{$key}.match-case').removeClass('hidden');

            $('.{$key}').not('.match-case').addClass('hidden');
            $('.{$key}').not('.match-case').find('input,textarea,select').each(function(i, e){
                $(e).addClass('ignore');//不验证
                if($(e).attr('name'))
                {
                    if(!$(e).data('name'))
                    {
                        $(e).data('name', $(e).attr('name'));
                    }
                    
                    $(e).removeAttr('name');//移除name，不会被表单提交
                }
            });

            $('.{$key}.match-case').find('input,textarea,select').each(function(i, e){
                $(e).removeClass('ignore');//验证
                if($(e).data('name') && !$(e).hasClass('switch-box'))
                {
                    $(e).attr('name', $(e).data('name'));
                }
            });
        });

        $("#{$watchFor}{$box}").trigger('change');

EOT;
        $this->script[] = $script;

        if ($viewModel) {
            Builder::getInstance()->addScript($script);
        }

        return $script;
    }
}
