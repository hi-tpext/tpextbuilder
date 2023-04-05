<?php

namespace tpext\builder\displayer;

class DateTimeRange extends Text
{
    protected $js = [
        '/assets/tpextbuilder/js/moment/moment.min.js',
        '/assets/tpextbuilder/js/moment/locale/zh-cn.js',
        '/assets/tpextbuilder/js/bootstrap-daterangepicker/daterangepicker.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/bootstrap-daterangepicker/daterangepicker.min.css',
    ];

    protected $size = [2, 4];

    protected $format = 'YYYY-MM-DD HH:mm:ss';

    protected $befor = '';

    protected $separator = ',';

    protected $timespan = 'Y-m-d H:i:s';

    protected $jsOptions = [
        'opens' => 'right', //(left/right/center) 选择器是否显示为左侧，右侧，或者它所附加的HTML元素下方居中。
        'drops' => 'down', //(down/up) 选择器是出现在下面（默认）还是高于它所附加的HTML元素。
        'showDropdowns' => true, //(true/false) 显示年份和月份选择日历上方的框以跳转到特定的月份和年份
        'timePicker' => true, //(true/false)添加选择框以选择时间
        'timePickerSeconds' => true, //在timePicker中显示秒数
        'linkedCalendars' => false, //(true/false) 启用后，显示的两个日历将始终为两个连续月份（即1月和2月），当点击日历上方的左箭头或右箭头时，两个日历都会提前。禁用时，两个日历可以单独进行，并显示任何月份/年份
        'showCustomRangeLabel' => true,
        'timePicker24Hour' => true, //设置小时为24小时制
        'locale' => [], //(object) 允许您为按钮和标签提供本地化字符串，自定义日期格式，以及更改日历的第一天。locale在配置生成器中检查以查看如何自定义这些选项
        'parentEl' => '', //将添加日期范围选择器的父元素的jQuery选择器，如果没有提供，这将是body
    ];

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function startDate($val)
    {
        $this->jsOptions['startDate'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function endDate($val)
    {
        $this->jsOptions['endDate'] = $val;
        return $this;
    }

    protected function dateTimeRangeScript()
    {
        $inputId = $this->getId();

        $this->jsOptions['locale'] = array_merge(
            $this->jsOptions['locale'],
            [
                'format' => $this->format,
                'separator' => $this->separator,
            ]
        );

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $script = <<<EOT

        $('#{$inputId}').daterangepicker({
            {$configs}
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
    public function timespan($val = 'Y-m-d H:i:s')
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
        $this->dateTimeRangeScript();

        return parent::beforRender();
    }
}
