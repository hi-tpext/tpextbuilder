<?php

namespace tpext\builder\displayer;

use tpext\builder\common\Module;

class Map extends Text
{
    protected $view = 'map';

    protected $type = 'amap';

    protected $minify = false;

    protected $jsOptions = [];

    protected $height = '450px';

    protected $width = '100%';

    /**
     * Undocumented function
     *
     * @param string|int $val
     * @return $this
     */
    public function height($val)
    {
        if (is_numeric($val)) {
            $val .= 'px';
        }
        $this->height = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|int $val
     * @return $this
     */
    public function width($val)
    {
        if (is_numeric($val)) {
            $val .= 'px';
        }
        $this->width = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function jsOptions($options)
    {
        $this->jsOptions = array_merge($this->jsOptions, $options);
        return $this;
    }

    public function amap()
    {
        $this->type = 'amap';
    }

    public function baidu()
    {

    }

    public function google()
    {

    }

    public function tcent()
    {

    }

    public function yandex()
    {

    }

    public function beforRender()
    {
        $config = Module::getInstance()->getConfig();

        if ($this->type == 'amap') {
            $this->amapScript($config['amap_js_key']);
        }

        $this->beforSymbol('<i class="mdi mdi-map"></i>');

        return parent::beforRender();
    }

    public function commonVars()
    {
        $vars = parent::commonVars();

        $vars = array_merge($vars, [
            'maptype' => $this->type,
            'textTempl' => Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'displayer', 'text.html']),
            'mapStyle' => 'style="width: ' . $this->width . ';height: ' . $this->height . ';max-width: 100%;"',
        ]);

        return $vars;
    }

    public function amapScript($jsKey)
    {
        $script = '';
        $inputId = $this->getId();

        if (is_array($this->default)) {
            $this->default = implode(',', $this->default);
        }

        $value = !($this->value === '' || $this->value === null) ? $this->value : $this->default;

        $position = explode(',', $value);
        if (count($position) != 2) {
            $position = [102.709629, 24.847463];
            $value = '102.709629,24.847463';
        }

        $this->jsOptions = array_merge([
            'center' => $position,
            'zoom' => 11,
        ], $this->jsOptions);

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $script = <<<EOT

        window.amapInit = function(){
            var input = $('#{$inputId}');
            var map = new AMap.Map('map-{$inputId}', {
                {$configs}
            });

            var marker = new AMap.Marker({
                draggable: true,
                position: new AMap.LngLat($value),   // 经纬度对象，也可以是经纬度构成的一维数组
            });

            // 将创建的点标记添加到已有的地图实例：
            map.add(marker);

            map.on('click', function(e) {
                marker.setPosition(e.lnglat);
                input.val(e.lnglat.getLng() + ',' + e.lnglat.getLat());
            });

            marker.on('dragend', function (e) {
                input.val(e.lnglat.getLng() + ',' + e.lnglat.getLat());
            });

            if(!input.val()) {
                map.plugin('AMap.Geolocation', function () {
                    geolocation = new AMap.Geolocation();
                    map.addControl(geolocation);
                    geolocation.getCurrentPosition();
                    AMap.event.addListener(geolocation, 'complete', function (data) {
                        marker.setPosition(data.position);
                        input.val(data.position.getLng() + ',' + data.position.getLat());
                    });
                });
            }

            AMap.plugin('AMap.Autocomplete',function(){
                var autoOptions = {
                    input : "search-{$inputId}"
                };

                var autocomplete= new AMap.Autocomplete(autoOptions);
                AMap.event.addListener(autocomplete, "select", function(data){
                    map.setZoomAndCenter(18, data.poi.location);
                    marker.setPosition(data.poi.location);
                    input.val(data.poi.location.lng + ',' + data.poi.location.lat);
                });
            });
        }

        var url = '$jsKey&callback=amapInit';

        var jsapi = document.createElement('script');
        jsapi.charset = 'utf-8';
        jsapi.src = url;
        document.body.appendChild(jsapi);
EOT;
        $this->script[] = $script;
    }
}
