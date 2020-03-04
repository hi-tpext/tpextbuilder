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
        $builder = Builder::getInstance();

        $fileurl = input('fileurl');

        return $builder->layer()->closeRefresh(1, '导入成功：' . $fileurl);
    }
}
