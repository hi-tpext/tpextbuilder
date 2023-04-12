<?php

namespace tpext\builder\displayer;

class DateTime extends Text
{
    protected $js = [
        '/assets/tpextbuilder/js/moment/moment.min.js',
        '/assets/tpextbuilder/js/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css',
    ];

    protected $size = [2, 3];

    protected $format = 'YYYY-MM-DD HH:mm:ss';

    protected $befor = '<span class="input-group-addon"><i class="mdi mdi-calendar-clock"></i></span>';

    protected $timespan = 'Y-m-d H:i:s';

    protected $jsOptions = [
        'useCurrent' => false,
        'locale' => 'zh-cn',
        'showTodayButton' => false,
        'showClear' => true,
        'showClose' => true,
        'sideBySide' => true,
        'inline' => false,
        'icons' => [
            'time' => 'mdi mdi-clock',
            'date' => 'mdi mdi-calendar',
            'up' => 'mdi mdi-chevron-up',
            'down' => 'mdi mdi-chevron-down',
            'previous' => 'mdi mdi-chevron-left',
            'next' => 'mdi mdi-chevron-right',
            'today' => 'mdi mdi-crosshairs-gps',
            'clear' => 'mdi mdi-delete',
            'close' => 'mdi mdi-close'
        ],
    ];

    protected function dateTimeScript()
    {
        $inputId = $this->getId();

        $str = preg_replace('/\W/', '', $this->name);

        $this->jsOptions['format'] = $this->format;

        $locale = empty($this->jsOptions['locale']) ?  'zh-cn' : $this->jsOptions['locale'];

        unset($this->jsOptions['locale']);

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $script = <<<EOT
        var locale{$str} = moment.locale('{$locale}');

        $('#{$inputId}').datetimepicker({
            "locale" : locale{$str},
            {$configs}
        }).on('dp.show', function(e){
            if($('#{$inputId}').hasClass('item-field'))//在items中，修复定位问题
            {
                var offset = $('#{$inputId}').offset();

                $('#{$inputId}').parent('.input-group')
                    .find('.bootstrap-datetimepicker-widget')
                    .css('position', 'fixed')
                    .css('z-index', 999)
                    .css('left', offset.left + 'px')
                    .css('top', offset.top + $('#{$inputId}').height() + 15  + 'px')
            }
        });

EOT;
        $this->script[] = $script;

        return $script;
    }

    /**
     * Undocumented function
     * @param string $val YYYY-MM-DD HH:mm:ss
     * @return $this
     */
    public function format($val)
    {
        $this->format = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function timespan($val = 'Y-m-d H:i:s')
    {
        $this->timespan = $val;
        return $this;
    }

    public function beforRender()
    {
        $this->customJs('/assets/tpextbuilder/js/moment/locale/' . (empty($this->jsOptions['locale']) ?  'zh-cn' : $this->jsOptions['locale']) . '.js');
        $this->customJs('/assets/tpextbuilder/js/bootstrap-datetimepicker/locale/' . (empty($this->jsOptions['locale']) ?  'zh-cn' : $this->jsOptions['locale']) . '.js');
        $this->dateTimeScript();
        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function renderValue()
    {
        /**
         * 数字格式时间戳自动转为日期格式
         * 但要避免没有`-/`分割的时间格式被转换，如：20200630 => 1970-08-23 03:17:10
         * 解决办法，截取前字符串4位，如果大于2099或小于1900则认为是时间戳，否则认为是`-/`分割的时间
         * 如果值是数字但可以确定值不是时间戳，可主动使用->timespan('')清空格式避免自动转换。
         */
        if ($this->timespan && is_numeric($this->value) && $this->value > 0) {

            $char4 = substr((string)$this->value, 0, 4);

            if ($char4 < 1900 || $char4 > 2099) //1900~2099区间不会误判
            {
                $this->value = date($this->timespan, $this->value);
            }
        }

        return parent::renderValue();
    }
}
