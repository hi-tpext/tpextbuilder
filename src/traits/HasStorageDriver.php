<?php

namespace tpext\builder\traits;

use tpext\builder\inface\Storage;

trait HasStorageDriver
{
    protected $storageDriver;

    /**
     * 设置文件上传驱动
     *
     * @param string $driverClass 驱动的类名，如：\tpext\builder\logic\LocalStorage::class
     * @return $this
     */
    public function storageDriver($driverClass = '')
    {
        if (!is_string($driverClass) && ($driverClass instanceof Storage)) {
            $driverClass = get_class($driverClass);
        }
        $this->storageDriver = $driverClass;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getStorageDriver()
    {
        if (empty($this->storageDriver)) {
            return '';
        }
        
        return str_replace('\\', '-', $this->storageDriver);
    }
}
