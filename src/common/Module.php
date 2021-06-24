<?php

namespace tpext\builder\common;

use tpext\common\Module as baseModule;

class Module extends baseModule
{
    protected $version = '1.0.2';

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

    public function setViewsPath($newPath)
    {
        $this->viewsPath = $newPath;
    }

    /**
     * 添加存储驱动类
     *
     * @param string $class 驱动类名
     * @param string $title 模板名称
     * @return $this
     */
    public function addstorageDriver($class, $title)
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
