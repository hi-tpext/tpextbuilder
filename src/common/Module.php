<?php

namespace tpext\builder\common;

use tpext\common\Module as baseModule;

class Module extends baseModule
{
    protected $version = '1.0.3';

    protected $name = 'tpext.builder';

    protected $title = 'tpext ui生成';

    protected $description = '后台ui生成';

    protected $root = __DIR__ . '/../../';

    protected $assets = 'assets';

    protected $modules = [
        'admin' => ['upload', 'import', 'attachment'],
    ];

    //所有视图的基础路径
    protected $viewsPath = '';

    /**
     * 存储驱动类列表
     *
     * @var array
     */
    protected $storageDrivers = [\tpext\builder\logic\LocalStorage::class => '本地'];

    /**
     * 版本列表，列出所有存在过的版本，即使没有升级脚本也要列出
     * 版本号 => 升级脚本
     *
     * @var array
     */
    protected $versions = [
        '1.0.1' => '',
        '1.0.2' => '',
        '1.0.3' => '1.0.3.sql',
    ];

    public function setViewsPath($newPath)
    {
        $this->viewsPath = $newPath;
    }

    /**
     * 添加存储驱动类
     *
     * @param string $class 驱动类名
     * @param string $title 驱动名称
     * @return $this
     */
    public function addStorageDriver($class, $title)
    {
        $this->storageDrivers[$class] = $title;
        return $this;
    }

    public function getViewsPath()
    {
        //如果未设置新的视图路径,则设置为默认路径 root/src/view/
        if (empty($this->viewsPath)) {
            $this->viewsPath = $this->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', '']);
        }

        return $this->viewsPath;
    }

    /**
     * 获取存储驱动类列表
     *
     * @return array
     */
    public function getStorageDrivers()
    {
        return $this->storageDrivers;
    }
}
