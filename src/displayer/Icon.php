<?php

namespace tpext\builder\displayer;

class Icon extends Text
{
    protected $js = [
        '/assets/tpextbuilder/js/fontIconPicker/jquery.fonticonpicker.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/fontIconPicker/css/jquery.fonticonpicker.min.css',
        '/assets/tpextbuilder/js/fontIconPicker/themes/bootstrap-theme/jquery.fonticonpicker.bootstrap.min.css',
    ];

    protected $jsOptions = [
        'theme' => 'fip-bootstrap',
    ];

    /**
     * Undocumented function
     *
     * @param array $options
     * @return void
     */
    public function jsOptions($options)
    {
        $this->jsOptions = array_merge($this->jsOptions, $options);
    }

    protected function iconScript()
    {
        $script = '';
        $inputId = $this->getId();

        $str = preg_replace('/\W/', '', $this->name);

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $script = <<<EOT

        var icon{$str} = $('#{$inputId}').fontIconPicker({
            {$configs}
        });

        $.ajax({
            url: '/assets/tpextbuilder/js/fontIconPicker/fontjson/materialdesignicons.json',
            type: 'GET',
            dataType: 'json'
        }).done(function(response) {
            var fontello_json_icons = [];
            $.each(response.glyphs, function(i, v) {
                fontello_json_icons.push( v.css );
            });

            icon{$str}.setIcons(fontello_json_icons);
        }).fail(function() {
            console.error('字体图标配置加载失败');
        });


EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        $this->iconScript();

        return parent::beforRender();
    }
}
