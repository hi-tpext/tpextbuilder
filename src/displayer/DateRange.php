<?php

namespace tpext\builder\displayer;

class DateRange extends Text
{
    protected $view = 'daterange';

    protected $js = [
        '/assets/tpextbuilder/js/bootstrap-datepicker/bootstrap-datepicker.min.js',
        '/assets/tpextbuilder/js/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/bootstrap-datepicker/bootstrap-datepicker3.min.css',
    ];

    protected $size = [2, 4];

    protected $format = 'yyyy-mm-dd';

    protected $befor = '<span class="input-group-addon"><i class="mdi mdi-calendar-multiple"></i></span>';

    protected $separator = ',';

    protected $timespan = '';

    protected $jsOptions = [
        'weekStart' => 1,
        'autoclose' => true,
        'language' => 'zh-CN'
    ];

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function jsOptions($options)
    {
        $this->jsOptions = array_merge($this->jsOptions, $options);
        return $this;
    }

    protected function dateRangeScript()
    {
        $inputId = $this->getId();

        $this->jsOptions = array_merge(
            $this->jsOptions,
            [
                'format' => $this->format,
                'multidateSeparator' => $this->separator,
            ]
        );

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $script = <<<EOT

        $('#{$inputId}-piker').datepicker({
            {$configs},
            inputs : [$('#{$inputId}-start'), $('#{$inputId}-end')]
        });

        if($('#{$inputId}').val())
        {
            var arr = $('#{$inputId}').val().split('{$this->separator}');
            $('#{$inputId}-start').val(arr[0]);
            $('#{$inputId}-end').val(arr.length > 1 ? arr[1] : '');
        }

        $('#{$inputId}-start,#{$inputId}-end').on('change', function(){
            $('#{$inputId}').val([$('#{$inputId}-start').val().trim(),$('#{$inputId}-end').val().trim()].join('{$this->separator}'));
            if($('#{$inputId}').val()==',')
            {
                $('#{$inputId}').val('');
            }
        });

EOT;
        $this->script[] = $script;

        return $script;
    }

    /**
     * Undocumented function
     * yyyy-mm-dd
     * @param string $val
     * @return $this
     */
    public function format($val)
    {
        $this->format = $val;
        return $this;
    }

    /**
     * Undocumented function
     * ','
     * @param string $val
     * @return $this
     */
    public function separator($val = ',')
    {
        $this->separator = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function timespan($val = 'Y-m-d')
    {
        $this->timespan = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function renderValue()
    {
        $value = parent::renderValue();

        if ($this->timespan && $value) {
            $arr = explode($this->separator, $value);
            if (isset($arr[0]) && is_numeric($arr[0])) {
                $arr[0] = date($this->timespan, $arr[0]);
            }
            if (isset($arr[1]) && is_numeric($arr[1])) {
                $arr[1] = date($this->timespan, $arr[1]);
            }
            $value = implode($this->separator, $arr);
        }

        return $value;
    }

    public function beforRender()
    {
        $this->dateRangeScript();

        return parent::beforRender();
    }
}
