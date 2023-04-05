<?php

namespace tpext\builder\displayer;

class TimeRange extends Time
{
    protected $view = 'timerange';

    protected $befor = '';

    protected $separator = ',';

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

    public function beforRender()
    {
        $this->timeRangeScript();

        return parent::beforRender();
    }

    protected function timeRangeScript()
    {
        $inputId = $this->getId();

        $str = preg_replace('/\W/', '', $this->name);

        $this->jsOptions['format'] = $this->format;

        $locale = isset($this->jsOptions['locale']) ? $this->jsOptions['locale'] : 'zh-cn';

        unset($this->jsOptions['locale']);

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $script = <<<EOT
        var locale{$str} = moment.locale('{$locale}');

        $('#{$inputId}-start').datetimepicker({
            "locale" : locale{$str},
            {$configs}
        }).on('dp.change', function(e){
            $('#{$inputId}-end').data("DateTimePicker").minDate(e.date);
            $('#{$inputId}').val([$('#{$inputId}-start').val().trim(),$('#{$inputId}-end').val().trim()].join('{$this->separator}'));
            if($('#{$inputId}').val()==',' || $('#{$inputId}').val()=='{$this->separator}')
            {
                $('#{$inputId}').val('');
            }
        }).on('dp.show', function(e){
            if($('#{$inputId}').hasClass('item-field'))//在items中，修复定位问题
            {
                var offset = $('#{$inputId}-start').offset();

                $('#{$inputId}').parent('.input-group')
                    .find('.bootstrap-datetimepicker-widget')
                    .css('position', 'fixed')
                    .css('z-index', 999)
                    .css('left', offset.left + 'px')
                    .css('top', offset.top + $('#{$inputId}-start').height() + 15  + 'px')
            }
        });

        $('#{$inputId}-end').datetimepicker({
            "locale" : locale{$str},
            {$configs}
        }).on('dp.change', function(e){
            $('#{$inputId}-start').data("DateTimePicker").maxDate(e.date);

            $('#{$inputId}').val([$('#{$inputId}-start').val().trim(),$('#{$inputId}-end').val().trim()].join('{$this->separator}'));
            if($('#{$inputId}').val()==',' || $('#{$inputId}').val()=='{$this->separator}')
            {
                $('#{$inputId}').val('');
            }
        }).on('dp.show', function(e){
            if($('#{$inputId}').hasClass('item-field'))//在items中，修复定位问题
            {
                var offset = $('#{$inputId}-end').offset();

                $('#{$inputId}').parent('.input-group')
                    .find('.bootstrap-datetimepicker-widget')
                    .css('position', 'fixed')
                    .css('z-index', 999)
                    .css('left', offset.left + 'px')
                    .css('top', offset.top + $('#{$inputId}-end').height() + 15  + 'px')
            }
        });

        if($('#{$inputId}').val())
        {
            var arr = $('#{$inputId}').val().split('{$this->separator}');
            $('#{$inputId}-start').val(arr[0]);
            $('#{$inputId}-end').val(arr.length > 1 ? arr[1] : '');
        }

EOT;
        $this->script[] = $script;

        return $script;
    }
}
