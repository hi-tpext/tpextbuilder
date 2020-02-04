<?php

namespace tpext\builder\displayer;

class WangEditor extends Field
{
    protected $view = 'wangeditor';

    protected $js = [
        '/assets/tpextbuilder/js/wangEditor/wangEditor.min.js',
    ];

    protected $jsOptions = [
        'uploadImgMaxSize' => 20 * 1024 * 1024,
        'uploadImgMaxLength' => 10,
        'uploadImgTimeout' => 3000,
        'uploadFileName' => 'file',
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

    protected function editorScript()
    {
        $script = '';
        $inputId = $this->getId();

        $token = md5('uploadtoken' . time() . uniqid());

        session('uploadtoken', $token);

        if (!isset($this->jsOptions['uploadImgServer']) || empty($this->jsOptions['uploadImgServer'])) {
            $this->jsOptions['uploadImgServer'] = url('tpextbuilder/admin/upload/upfiles', ['type' => 'wangeditor', 'token' => $token]);
        }

        $this->jsOptions['uploadImgParams'] = [];

        $configs = json_encode($this->jsOptions);

        $script = <<<EOT

        var E = window.wangEditor;
        var editor = new E('#{$inputId}-div');

        editor.customConfig = {$configs};

        editor.customConfig.uploadImgHooks = {
            fail: function (xhr, editor, result) {
                // 图片上传并返回结果，但图片插入错误时触发
                // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象，result 是服务器端返回的结果
                alert("失败");
            },
            error: function (xhr, editor) {
                // 图片上传出错时触发
                // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象
                alert("错误");
            },
            // 如果服务器端返回的不是 {errno:0, data: [...]} 这种格式，可使用该配置
            // （但是，服务器端返回的必须是一个 JSON 格式字符串！！！否则会报错）
            customInsert: function (insertImg, result, editor) {
                // 图片上传并返回结果，自定义插入图片的事件（而不是编辑器自动插入图片！！！）
                // insertImg 是插入图片的函数，editor 是编辑器对象，result 是服务器端返回的结果
                // 举例：假如上传图片成功后，服务器端返回的是 {url:'....'} 这种格式，即可这样插入图片：
                var url = result.url;
                insertImg(url);
                // result 必须是一个 JSON 格式字符串！！！否则报错
            }
        };
        // 设置内容
        editor.customConfig.onchange = function (html) {
            $('#{$inputId}').val(html);
        }
        editor.create();

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        $this->editorScript();

        return parent::beforRender();
    }
}
