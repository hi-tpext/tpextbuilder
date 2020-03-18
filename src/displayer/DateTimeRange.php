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
        '/assets/tpextbuilder/js/bootstrap-daterangepicker/daterangepicker.css',
    ];

    protected $size = [2, 6];

    protected $format = 'YYYY-MM-DD HH:mm:ss';

    protected $befor = '<span class="input-group-addon"><i class="mdi mdi-calendar-multiple"></i></span>';

    protected $timePicker = true;

    protected $separator = ' ~ ';

    protected $jsOptions = [
        'opens' => 'right',
        'showDropdowns' => true,
        'timePicker24Hour' => true, //设置小时为24小时制
        'locale' => [
        ],
    ];

    /**
     * Undocumented function
     *
     * @param string $val
     * @return void
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
     * @return void
     */
    public function endDate($val)
    {
        $this->jsOptions['endDate'] = $val;
        return $this;
    }

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

    protected function dateTimeRangeScript()
    {
        $inputId = $this->getId();

        $this->jsOptions['timePicker'] = $this->timePicker;

        $this->jsOptions['locale'] = array_merge(
            $this->jsOptions['locale'],
            [
                'format' => $this->format,
                'separator' => $this->separator,
            ]);

        $value = $this->renderValue();

        if ($value) {
            $values = explode($this->separator, $value);

            if (count($values) == 2) {
                $this->jsOptions['startDate'] = $values[0];
                $this->jsOptions['endDate'] = $values[1];
            }
        } else {
            if (!isset($this->jsOptions['startDate'])) {
                $this->jsOptions['startDate'] = date('Y-m-d H:i:s', strtotime('-1 month'));
            }
            if (!isset($this->jsOptions['endDate'])) {
                $this->jsOptions['endDate'] = date('Y-m-d H:i:s');
            }
        }

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
     * YYYY-MM-DD HH:mm:ss
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
     * ' ~ '
     * @param string $val
     * @return $this
     */
    public function separator($val = ' ~ ')
    {
        $this->separator = $val;
        return $this;
    }

    public function beforRender()
    {
        $this->dateTimeRangeScript();

        return parent::beforRender();
    }
}
