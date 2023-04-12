<?php

namespace tpext\builder\displayer;

class Load extends Field
{
    protected $view = 'load';

    protected $isInput = false;

    public $loadingText = '加载中...';

    protected $jsOptions = [
        'ajax' => [
            'url' => '',
            'text' => '',
            'separator' => '、'
        ]
    ];

    public function created($fieldType = '')
    {
        $this->loadingText = __blang('bilder_loading');
        parent::created($fieldType);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val 加载中...|&nbsp;
     * @return $this
     */
    public function loadingText($val = '&nbsp;')
    {
        $this->loadingText = $val;

        return $this;
    }

    public function beforRender()
    {
        $this->loadTextScript();

        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $textField text|name
     * @return $this
     */
    public function dataUrl($url, $textField = '')
    {
        $this->jsOptions['ajax'] = array_merge($this->jsOptions['ajax'], [
            'url' => $url,
            'text' => $textField,
        ]);

        return $this;
    }

    protected function loadTextScript()
    {
        $script = '';
        $selectId = $this->getId();

        $ajax = $this->jsOptions['ajax'];
        $url = $ajax['url'];
        $text = $ajax['text'] ?: '_';
        $separator = $ajax['separator'] ?: '、';

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $key = preg_replace('/\W/', '', $selectId);

        $script = <<<EOT

        var selected{$key} = $('#{$selectId}').data('selected');

        if(selected{$key} !== '')
        {
            var params = {
                q: '',
                page: 1,
                selected : selected{$key},
                ele_id : '{$selectId}',
                prev_ele_id : '',
                idField : '',
                textField : '{$text}' == '_' ? null : '{$text}',
                load : 1,
            };

            $.ajax({
                url: '{$url}',
                data: params,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    var list = (data.data ? data.data : data) || [];
                    var d = null;
                    var texts = [];
                    for(var i in list)
                    {
                        d = list[i];
                        texts.push(d.__text__ || d['{$text}'] || d.text);
                    }
                    $('#{$selectId}').text(texts.length ? texts.join('{$separator}') : __blang.bilder_value_is_empty);
                },
                error:function(){
                    $('#{$selectId}').data('selected', '');
                    $('#{$selectId}').text(__blang.bilder_loading_error);
                }
            });
        }

EOT;

        $this->script[] = $script;

        return $script;
    }

    public function customVars()
    {
        $checked = '';

        if (!($this->value === '' || $this->value === null)) {
            $checked = $this->value;
        } else {
            $checked = $this->default;
        }

        return [
            'checked' => $checked,
            'loadingText' => $this->loadingText
        ];
    }
}
