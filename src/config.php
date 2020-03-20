<?php

return [
    'allowSuffix' =>
    //
    "jpg,jpeg,gif,wbmp,webpg,png,bmp," .
    //
    "flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogv,mov,wmv,mp4,webm," .
    //
    "ogg,mp3,wav,mid," .
    //
    "rar,zip,tar,gz,7z,bz2,cab,iso," .
    //
    "doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md"
    ,
    'maxSize' => 20,
    'isRandName' => 1,
    //
    'map' => '',
    'amap_js_key' => '//webapi.amap.com/maps?v=1.4.15&key=您申请的key值',
    'baidu_map_js_key' => '//api.map.baidu.com/api?v=3.0&ak=您的密钥',
    'google_map_js_key' => '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=您申请的key值',
    'tcent_map_js_key' => '//map.qq.com/api/js?v=2.exp&libraries=place&key=您申请的key值',
    'yandex_map_js_key' => '//api-maps.yandex.ru/2.1/?lang=ru_RU',
    //
    //配置描述
    '__config__' => [
        'allowSuffix' => ['type' => 'textarea', 'label' => '允许上传的文件后缀', 'size' => [2, 10], 'help' => '以英文,号分割'],
        'maxSize' => ['type' => 'number', 'label' => '上传文件大小限制(MB)'],
        'isRandName' => ['type' => 'radio', 'label' => '随机文件名', 'options' => [0 => '否', 1 => '是']],
        'map' => ['type' => 'divider', 'label' => '地图api，按需配置'],
        'amap_js_key' => ['type' => 'text', 'label' => '高德地图js'],
        'baidu_map_js_key' => ['type' => 'text', 'label' => '百度地图js'],
        'google_map_js_key' => ['type' => 'text', 'label' => 'google地图js'],
        'tcent_map_js_key' => ['type' => 'text', 'label' => '腾旭地图js'],
        'yandex_map_js_key' => ['type' => 'text', 'label' => 'yandex地图js'],
    ],
];
