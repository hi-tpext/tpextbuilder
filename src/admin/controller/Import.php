<?php
namespace tpext\builder\admin\controller;

use think\Controller;
use tpext\builder\common\Builder;

class Import extends Controller
{
    public function page()
    {
        $acceptedExts = input('acceptedExts');
        $fileSize = input('fileSize');
        $pageToken = input('pageToken');
        $successUrl = input('successUrl');

        if (empty($acceptedExts) || empty($fileSize) || empty($pageToken) || empty($successUrl)) {
            $this->error('参数有误');
        }

        $importpagetoken = session('importpagetoken');

        $_pageToken = md5($importpagetoken . $acceptedExts . $fileSize);

        if ($_pageToken != $pageToken) {
            $this->error('验证失败');
        }

        $acceptedExts = explode(',', $acceptedExts);

        $acceptedExts = '.' . implode(',.', $acceptedExts);

        $successUrl = urldecode($successUrl);

        $token = session('uploadtoken') ? session('uploadtoken') : md5('uploadtoken' . time() . uniqid());

        session('uploadtoken', $token);

        $uploadUrl = url('/tpextbuilder/admin/upload/upfiles', ['type' => 'dropzone', 'token' => $token]);

        $this->assign('admin_copyright', '');
        $this->assign('uploadUrl', $uploadUrl);
        $this->assign('acceptedExts', $acceptedExts);
        $this->assign('fileSize', $fileSize);
        $this->assign('successUrl', $successUrl);

        return $this->fetch();
    }

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
                return Builder::getInstance()->layer()->closeRefresh(1, '导入成功：' . \$fileurl);
            }

            \$builder = Builder::getInstance('出错了');
            \$builder->content()->display('&lt;p&gt;' . '未能读取文件:' . \$fileurl . '&lt;/p&gt;');
        }
        </pre>

EOT;
        $builder->content()->display($script);
        return $builder->render();
    }
}
