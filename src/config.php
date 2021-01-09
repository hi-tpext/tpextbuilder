<?php

return [
    'search_open' => 1,
    'layer_size' => '1100px,98%',
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
    "doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md"
    ,
    'max_size' => 20,
    'is_rand_name' => 1,
    'file_by_date' => 5,
    'table_empty_text' => '<div class="text-center"><img src="/assets/tpextbuilder/images/empty.png" /><p>暂无相关数据~</p></div>',
    //
    '__hr__' => '地图api，按需配置',
    'amap_js_key' => '//webapi.amap.com/maps?v=1.4.15&key=您申请的key值',
    'baidu_map_js_key' => '//api.map.baidu.com/api?v=3.0&ak=您的密钥',
    'google_map_js_key' => '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=您申请的key值',
    'tcent_map_js_key' => '//map.qq.com/api/js?v=2.exp&libraries=place&key=您申请的key值',
    'yandex_map_js_key' => '//api-maps.yandex.ru/2.1/?lang=ru_RU',
    //
    //配置描述
    '__config__' => [
        'search_open' => ['type' => 'radio', 'label' => '列表页搜索默认展开', 'options' => [0 => '否', 1 => '是']],
        'layer_size' => ['type' => 'text', 'label' => 'layer弹窗大小', 'size' => [2, 2]],
        'allow_suffix' => ['type' => 'textarea', 'label' => '允许上传的文件后缀', 'size' => [2, 10], 'help' => '以英文,号分割'],
        'max_size' => ['type' => 'number', 'label' => '上传文件大小限制(MB)'],
        'is_rand_name' => ['type' => 'radio', 'label' => '随机文件名', 'options' => [0 => '否', 1 => '是']],
        'file_by_date' => ['type' => 'radio', 'label' => '文件目录归档', 'options' => [1 => 'Ym(年月)', 2 => 'Ymd(年月日)', 3 => 'Y/m(年/月)', 4 => 'Y/md(年/月日)', 5 => 'Ym/d(年月/日)', 6 => 'Y/m/d(年/月/日)']],
        'table_empty_text' => ['type' => 'textarea', 'label' => '列表无数据显示', 'size' => [2, 10], 'help' => '支持html'],
        'amap_js_key' => ['type' => 'text', 'label' => '高德地图js'],
        'baidu_map_js_key' => ['type' => 'text', 'label' => '百度地图js'],
        'google_map_js_key' => ['type' => 'text', 'label' => 'google地图js'],
        'tcent_map_js_key' => ['type' => 'text', 'label' => '腾讯地图js'],
        'yandex_map_js_key' => ['type' => 'text', 'label' => 'yandex地图js'],
    ],
];
