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

    protected $befor = '';

    protected $separator = ',';

    protected $timespan = 'Y-m-d';

    protected $jsOptions = [
        'weekStart' => 1,
        'autoclose' => false,
        'language' => 'zh-CN'
    ];

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

        $('#{$inputId}').parent('.input-group').datepicker({
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
            if($('#{$inputId}').val()==',' || $('#{$inputId}').val()=='{$this->separator}')
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
     * @param string $val yyyy-mm-dd
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
    public function renderValue()
    {
        $arr = explode($this->separator, $this->value);

        /**
         * 数字格式时间戳自动转为日期格式
         * 但要避免没有`-/`分割的时间格式被转换，如：20200630 => 1970-08-23 03:17:10
         * 解决办法，截取前字符串4位，如果大于2099或小于1900则认为是时间戳，否则认为是`-/`分割的时间
         * 如果值是数字但可以确定值不是时间戳，可主动使用->timespan('')清空格式避免自动转换。
         */
        if ($this->timespan && isset($arr[0]) && is_numeric($arr[0]) && $arr[0] > 0) {
            $char4 = substr((string)$arr[0], 0, 4);

            if ($char4 < 1900 || $char4 > 2099) //1900~2099区间不会误判
            {
                $arr[0] = date($this->timespan, $arr[0]);
            }
        }

        if ($this->timespan && isset($arr[1]) && is_numeric($arr[1]) && $arr[1] > 0) {
            $char4 = substr((string)$arr[1], 0, 4);

            if ($char4 < 1900 || $char4 > 2099) //1900~2099区间不会误判
            {
                $arr[1] = date($this->timespan, $arr[1]);
            }
        }

        $this->value = implode($this->separator, $arr);

        return parent::renderValue();
    }

    public function beforRender()
    {
        $this->dateRangeScript();

        return parent::beforRender();
    }
}
