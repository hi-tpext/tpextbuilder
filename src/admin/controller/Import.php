<?php

namespace tpext\builder\admin\controller;

use think\Controller;
use think\facade\Session;
use tpext\builder\common\Module;
use tpext\builder\common\Builder;

/**
 * Undocumented class
 * @title 导入
 */
class Import extends Controller
{
    /**
     * Undocumented function
     *
     * @title 上传文件弹窗
     * @return mixed
     */
    public function page()
    {
        $acceptedExts = input('acceptedExts', '');
        $fileSize = input('fileSize', '');
        $pageToken = input('pageToken', '');
        $successUrl = input('successUrl', '');
        $driver = input('driver', '');

        if ($fileSize == '' || empty($pageToken) || empty($successUrl)) {
            $this->error(__blang('bilder_parameter_error'));
        }

        $importpagetoken = Session::get('importpagetoken');

        $_pageToken = md5($importpagetoken . $acceptedExts . $fileSize);

        if ($_pageToken != $pageToken) {
            $this->error(__blang('bilder_validate_failed'));
        }

        $config = Module::getInstance()->getConfig();

        if ($fileSize == 0 || $fileSize == '' || $fileSize > $config['max_size']) {
            $fileSize = $config['max_size'];
        }

        if ($acceptedExts == '*' || $acceptedExts == '*/*' || empty($acceptedExts)) {

            $acceptedExts = $config['allow_suffix'];
        }

        $acceptedExts = explode(',', $acceptedExts);
        $acceptedExts = '.' . implode(',.', $acceptedExts);

        $successUrl = urldecode($successUrl);

        $builder = Builder::getInstance();

        $token = $builder->getCsrfToken();

        $uploadUrl = url(Module::getInstance()->getUploadUrl(), ['utype' => 'dropzone', 'token' => $token, 'driver' => $driver]);

        $builder->display('<div id="dropzone-elm" style="width: 220px; margin: 0 auto;" class="dropzone"></div>');

        $builder->customCss(['/assets/tpextbuilder/js/dropzone/min/basic.min.css', '/assets/tpextbuilder/js/dropzone/min/dropzone.min.css']);
        $builder->customJs(['/assets/tpextbuilder/js/dropzone/min/dropzone.min.js']);

        $script = <<<EOT

        $("#dropzone-elm").dropzone({
            url: "{$uploadUrl}",
            method: "post",  // 也可用put
            paramName: "file", // 默认为file
            maxFiles: 1,// 一次性上传的文件数量上限
            maxFilesize: '{$fileSize}', // 文件大小，单位：MB
            acceptedFiles: "{$acceptedExts}", // 上传的类型
            addRemoveLinks: false,
            parallelUploads: 1,// 一次上传的文件数量
            dictDefaultMessage: __blang.bilder_dropzone_default_message,
            dictMaxFilesExceeded: __blang.bilder_dropzone_max_files_exceeded.replace('{num}',1),
            dictResponseError: __blang.bilder_file_uploading_failed,
            dictInvalidFileType: __blang.bilder_dropzone_invalid_file_type,
            dictFallbackMessage: __blang.bilder_dropzone_fallback_message,
            dictFileTooBig: __blang.bilder_dropzone_file_too_big,
            dictRemoveLinks: __blang.bilder_dropzone_remove_links,
            dictCancelUpload: __blang.bilder_dropzone_cancel_upload,
            init: function () {
                this.on("addedfile", function (file) {
                    // 上传文件时触发的事件
                });
                this.on("success", function (file, data) {
                    if (data.status == '200') {
                        location.href = '{$successUrl}?fileurl=' + encodeURI(data.picurl.split('?')[0]);
                    }
                    else {
                        parent.lightyear.notify(__blang.bilder_file_uploading_failed + data.info, 'danger');
                    }
                    // 上传成功触发的事件
                });
                this.on("error", function (file, data) {
                    // 上传失败触发的事件
                    parent.lightyear.notify(__blang.bilder_file_uploading_failed + '-' + data, 'danger');
                });
            }
        });
        Dropzone.autoDiscover = false;

EOT;
        $builder->addScript($script);
        return $builder;
    }

    /**
     * Undocumented function
     *
     * @title 上传成功
     * @return mixed
     */
    public function afterSuccess()
    {
        $builder = Builder::getInstance(__blang('bilder_operation_tips'));

        $fileurl = input('fileurl');

        $script = <<<EOT
        <p>文件上传成功，但未做后续处理：{$fileurl}</p>
        <pre>
        //指定你的处理action，如 url('afterSuccess')
        \$table->getToolbar()->btnImport(url('afterSuccess'));

        //请在你的控制器实现导入逻辑
        public function afterSuccess()
        {
            \$fileurl = input('fileurl');
            if (is_file('.' . \$fileurl)) {
                // 导入逻辑...
                //....
                
                //返回成功提示并刷新列表页面
                \$builder = Builder::getInstance();
                return \$builder->layer()->closeRefresh(1, '导入成功：' . \$fileurl);
            }

            \$builder = Builder::getInstance('出错了');
            \$builder->content()->display('&lt;p&gt;' . '未能读取文件:' . \$fileurl . '&lt;/p&gt;');
            return \$builder->render();
        }
        </pre>

EOT;
        $builder->content()->display($script);
        return $builder->render();
    }
}
