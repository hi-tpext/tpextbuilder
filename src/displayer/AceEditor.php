<?php

namespace tpext\builder\displayer;

class AceEditor extends Field
{
    protected $view = 'aceeditor';

    protected $minify = false;

    protected $js = [
        //core
        '/assets/tpextbuilder/js/ace/ace.js',
        //ext
        '/assets/tpextbuilder/js/ace/ext-beautify.js',
        '/assets/tpextbuilder/js/ace/ext-error_marker.js',
        '/assets/tpextbuilder/js/ace/ext-language_tools.js',
        '/assets/tpextbuilder/js/ace/ext-keybinding_menu.js',
        '/assets/tpextbuilder/js/ace/ext-searchbox.js',
        '/assets/tpextbuilder/js/ace/ext-spellcheck.js',
        '/assets/tpextbuilder/js/ace/ext-static_highlight.js',
        '/assets/tpextbuilder/js/ace/ext-statusbar.js',
        //mode
        '/assets/tpextbuilder/js/ace/mode-css.js',
        '/assets/tpextbuilder/js/ace/mode-text.js',
        '/assets/tpextbuilder/js/ace/mode-html.js',
        '/assets/tpextbuilder/js/ace/mode-javascript.js',
        //theme
        '/assets/tpextbuilder/js/ace/theme-vibrant_ink.js', //dark
        '/assets/tpextbuilder/js/ace/theme-textmate.js', //bright
    ];

    protected $jsOptions = [
        'mode' => 'text',
        'dark' => false,
        'fontSize' => 14,
        'height' => '1000px',
        'width' => '100%',
        //
        'enableBasicAutocompletion' => true,
        'enableSnippets' => true,
        'enableLiveAutocompletion' => true,
    ];

    protected function editorScript()
    {
        $configs = json_encode($this->jsOptions);

        $inputId = $this->getId();

        $readonly = $this->readonly || $this->disabled ? 1 : 0;

        $script = <<<EOT

        var configs = {$configs};
        var readonly = {$readonly} == 1;

        $('#{$inputId}-editor').css({
            position: 'relative',
            width: configs.width,
            height: configs.height,
        });

        var aceeditor = ace.edit("{$inputId}-editor");
        aceeditor.setTheme("ace/theme/"+ (configs.dark ? 'vibrant_ink' : 'textmate'));
        aceeditor.session.setMode("ace/mode/" + configs.mode);
        aceeditor.setFontSize(configs.fontSize);
        
        aceeditor.setOptions({
            enableBasicAutocompletion: configs.enableBasicAutocompletion,
            enableSnippets: configs.enableSnippets,
            enableLiveAutocompletion: configs.enableLiveAutocompletion
        });

        aceeditor.resize();
        aceeditor.setReadOnly(readonly);
        aceeditor.getSession().setUseWrapMode(true);
        aceeditor.setShowPrintMargin(false);

        aceeditor.setValue($('#{$inputId}').val());
        aceeditor.session.on('change', function(e) {
            $('#{$inputId}').val(aceeditor.getValue());
        });
EOT;
        $this->script[] = $script;

        return $script;
    }

    /**
     * 设置代码语言模式
     *
     * @param string $val css/html/javascript/text
     * @return $this
     */
    public function setMode($val = 'text')
    {
        $this->jsOptions['mode'] = $val;

        return $this;
    }

    /**
     * 设置是否为黑色模式
     *
     * @param boolean $val
     * @return $this
     */
    public function setDark($val = true)
    {
        $this->jsOptions['dark'] = $val;

        return $this;
    }

    public function beforRender()
    {
        if (!$this->readonly) {
            $this->editorScript();
        }

        return parent::beforRender();
    }
}
