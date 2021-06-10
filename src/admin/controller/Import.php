<?php

namespace tpext\builder\admin\controller;

use think\Controller;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;

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
        $fileSize = input('fileSize');
        $pageToken = input('pageToken');
        $successUrl = input('successUrl');

        if ($fileSize == '' || empty($pageToken) || empty($successUrl)) {
            $this->error('参数有误');
        }

        $importpagetoken = session('importpagetoken');

        $_pageToken = md5($importpagetoken . $acceptedExts . $fileSize);

        if ($_pageToken != $pageToken) {
            $this->error('验证失败');
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

        $token = Builder::getInstance()->getCsrfToken();

        $uploadUrl = url('/admin/upload/upfiles', ['type' => 'dropzone', 'token' => $token]);

        $this->assign('admin_copyright', '');
        $this->assign('uploadUrl', $uploadUrl);
        $this->assign('acceptedExts', $acceptedExts);
        $this->assign('fileSize', $fileSize);
        $this->assign('successUrl', $successUrl);

        return $this->fetch();
    }

    /**
     * Undocumented function
     *
     * @title 上传成功
     * @return mixed
     */
    public function afterSuccess()
    {
        $builder = Builder::getInstance('提示');

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
            if (is_file(app()->getRootPath() . 'public' . \$fileurl)) {
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
