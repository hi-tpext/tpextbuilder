<?php

use tpext\builder\common\Module;

return [
    'search_open' => 1,
    'layer_size' => '1000px,auto',
    'allow_suffix' =>
    //
    "jpg,jpeg,gif,wbmp,webpg,png,bmp,ico," .
        //
        "flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogv,mov,wmv,mp4,webm," .
        //
        "ogg,mp3,wav,mid," .
        //
        "rar,zip,tar,gz,7z,bz2,cab,iso," .
        //
        "doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md",
    'max_size' => 20,
    'is_rand_name' => 1,
    'file_by_date' => 5,
    'storage_driver' => \tpext\builder\logic\LocalStorage::class,
    'image_water' => '',
    'image_water_position' => 'bottom-right',
    'image_size_limit' => '1024,1024',
    'table_empty_text' => '<div class="text-center"><img src="/assets/tpextbuilder/images/empty.png" /><p>暂无相关数据~</p></div>',
    //
    '__hr__' => '地图api，按需配置',
    'amap_js_key' => '//webapi.amap.com/maps?v=1.4.15&key=您申请的key&jscode=你的jscode',
    'baidu_map_js_key' => '//api.map.baidu.com/api?v=3.0&ak=您的密钥',
    'google_map_js_key' => '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=您申请的key值',
    'tcent_map_js_key' => '//map.qq.com/api/js?v=2.exp&libraries=place&key=您申请的key值',
    'yandex_map_js_key' => '//api-maps.yandex.ru/2.1/?lang=ru_RU',
    //
    //配置描述
    '__config__' => [
        'search_open' => ['type' => 'radio', 'label' => '列表页搜索默认展开', 'options' => [0 => '否', 1 => '是']],
        'layer_size' => ['type' => 'text', 'label' => 'layer弹窗大小', 'size' => [2, 4], 'help' => '宽高以英文,号分割，如1000px,auto、1000px,98%，若高为`auto`，则自适应'],
        'allow_suffix' => ['type' => 'textarea', 'label' => '允许上传的文件后缀', 'size' => [2, 10], 'help' => '以英文,号分割'],
        'max_size' => ['type' => 'number', 'label' => '上传文件大小限制(MB)'],
        'is_rand_name' => ['type' => 'radio', 'label' => '随机文件名', 'options' => [0 => '否', 1 => '是']],
        'file_by_date' => ['type' => 'radio', 'label' => '文件目录归档', 'options' => [1 => 'Ym(年月)', 2 => 'Ymd(年月日)', 3 => 'Y/m(年/月)', 4 => 'Y/md(年/月日)', 5 => 'Ym/d(年月/日)', 6 => 'Y/m/d(年/月/日)']],
        'storage_driver' => ['type' => 'select', 'label' => '存储驱动', 'size' => [2, 4], 'options' => Module::getInstance()->getStorageDrivers()],
        'image_water' => ['type' => 'image', 'label' => '图片水印', 'size' => [2, 4], 'help' => '若设置则所有上传图片(jpg/jpeg/png/webp/gif)都加此水印'],
        'image_water_position' => ['type' => 'radio', 'label' => '图片水印位置', 'size' => [2, 10], 'help' => '水印在原图上的位置', 'options' => ['top-left' => '左上', 'top' => '中上', 'top-right' => '右上', 'left' => '左中', 'center' => '居中', 'right' => '右中', 'bottom-left' => '左下', 'bottom' => '中下', 'bottom-right' => '右下']],
        'image_size_limit' => ['type' => 'text', 'label' => '上传图片大小限制', 'size' => [2, 4], 'help' => '宽高以英文,号分割，如1024,1024，0值不限制，如果上传图片宽或高超过限制，则缩放此边到限制大小，另一边等比例缩放'],
        'table_empty_text' => ['type' => 'textarea', 'label' => '列表无数据显示', 'size' => [2, 10], 'help' => '支持html'],
        'amap_js_key' => ['type' => 'text', 'label' => '高德地图js', 'help' => '[高德]自2021年12月02日升级之后所申请的`key`必须配备安全密钥`jscode`一起使用。两个参数按`&key=你的key&jscode=你的jscode`'],
        'baidu_map_js_key' => ['type' => 'text', 'label' => '百度地图js'],
        'google_map_js_key' => ['type' => 'text', 'label' => 'google地图js'],
        'tcent_map_js_key' => ['type' => 'text', 'label' => '腾讯地图js'],
        'yandex_map_js_key' => ['type' => 'text', 'label' => 'yandex地图js'],
    ],
];
