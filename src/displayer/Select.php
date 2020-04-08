<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Select extends Radio
{
    use HasOptions;

    protected $view = 'select';

    protected $class = '';

    protected $js = [
        '/assets/tpextbuilder/js/select2/select2.min.js',
        '/assets/tpextbuilder/js/select2/i18n/zh-CN.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/select2/select2.min.css',
    ];

    protected $attr = 'size="1"';

    protected $group = false;

    protected $select2 = true;

    protected $jsOptions = [
        'placeholder' => '',
        'allowClear' => true,
        'minimumInputLength' => 0,
        'language' => 'zh-CN',
    ];

    /**
     * Undocumented function
     *
     * @param boolean $show
     * @return $this
     */
    public function select2($use)
    {
        $this->select2 = $use;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $textField
     * @param string $IdField
     * @param integer $delay
     * @param boolean $loadmore
     * @return $this
     */
    public function dataUrl($url, $textField = 'text', $IdField = 'id', $delay = 250, $loadmore = true)
    {
        $this->jsOptions['ajax'] = [
            'url' => $url,
            'id' => $IdField,
            'text' => $textField,
            'delay' => $delay,
            'loadmore' => $loadmore,
        ];

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param [type] $val
     * @return $this
     */
    public function placeholder($val)
    {
        $this->jsOptions['placeholder'] = $val;
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

    public function asNextScript($prevID)
    {
        $script = '';
        $selectId = $this->getId();

        if (empty($this->jsOptions['placeholder'])) {
            $this->jsOptions['placeholder'] = '请选择' . $this->getlabel();
        }

        if (isset($this->jsOptions['ajax'])) {
            $ajax = $this->jsOptions['ajax'];
            unset($this->jsOptions['ajax']);
            $url = $ajax['url'];
            $id = isset($ajax['id']) ? $ajax['id'] : 'id';
            $text = isset($ajax['text']) ? $ajax['text'] : 'text';

            $configs = json_encode($this->jsOptions);

            $configs = substr($configs, 1, strlen($configs) - 2);
            $script = <<<EOT

            //是否自动加载下一级。比如省市区三级联动时，开启了的话，选择了云南省，市和区会自动选择：云南省-昆明市-五华区。
            //某些时候这样未必是合理的。
            if(autoLoad)
            {
                $('#{$selectId}').next('.select2').lyearloading({
                    opacity: 0.05,
                    spinnerSize: 'nm'
                });
                $.get('{$url}',{q : $('#{$prevID}').val(), eleid : '{$prevID}'}, function (data) {
                    $('#{$selectId}').select2('destroy').empty();
                    var list = data.data ? data.data : data;
                    $('#{$selectId}').select2({
                        {$configs},
                        data: $.map(list, function (d) {
                            d.id = d.{$id};
                            d.text = d.{$text};
                            return d;
                        })
                    }).trigger('change');
                });
            }
            else
            {
                //关闭后，省变化了，市和区不会自动加载，比如选择了云南省：云南省-请选择市-请选择区，要一级一级去选
                $('#{$selectId}').empty().trigger('change');
            }

EOT;
            $this->jsOptions['ajax'] = $ajax;
            $this->jsOptions['prev_id'] = $prevID;
            return $script;
        }
    }
    /**
     * Undocumented function
     *
     * @param string $prev
     * @return string
     */
    protected function select2Script()
    {
        $script = '';
        $selectId = $this->getId();

        if (empty($this->jsOptions['placeholder'])) {
            $this->jsOptions['placeholder'] = '请选择' . $this->getlabel();
        }

        if (isset($this->jsOptions['ajax'])) {
            $ajax = $this->jsOptions['ajax'];
            unset($this->jsOptions['ajax']);
            $url = $ajax['url'];
            $id = isset($ajax['id']) ? $ajax['id'] : 'id';
            $text = isset($ajax['text']) ? $ajax['text'] : 'text';
            $delay = isset($ajax['delay']) ? $ajax['delay'] : 250;
            $loadmore = $ajax['loadmore'];

            $configs = json_encode($this->jsOptions);

            $configs = substr($configs, 1, strlen($configs) - 2);

            $prev_id = isset($this->jsOptions['prev_id']) ? $this->jsOptions['prev_id'] : '';

            $script = <<<EOT

            $('#{$selectId}').select2({
              {$configs},
              ajax: {
                url: '{$url}',
                dataType: 'json',
                delay: {$delay},
                data: function (params) {
                  return {
                    q: params.term || ('{$prev_id}' ? $('#{$prev_id}').val() : ''),
                    page: params.page || 1,
                    eleid : '{$selectId}'
                  };
                },
                processResults: function (data, params) {
                  params.page = params.page || 1;
                  var list = data.data ? data.data : data;
                  return {
                    results: $.map(list, function (d) {
                               d.id = d.{$id};
                               d.text = d.{$text};
                               return d;
                            }),
                    pagination: {
                      more: {$loadmore} ? data.more_url : ''
                    }
                  };
                },
                cache: true
              },
              escapeMarkup: function (markup) {
                  return markup;
              }
            });

EOT;
            $this->jsOptions['ajax'] = $ajax;
        } else {
            $configs = json_encode($this->jsOptions);

            $configs = substr($configs, 1, strlen($configs) - 2);

            $script = <<<EOT
            $('#{$selectId}').select2({
                {$configs}
            });

EOT;
        }

        if (empty($prev)) {
            $this->script[] = $script;
        }

        return $script;
    }

    /*
    $form->select('province', '省份', 4)->dataUrl(url('province'))->withNext(
    $form->select('city', '城市', 4)->dataUrl(url('city'))->withNext(
    $form->select('area', '区域', 4)->dataUrl(url('area'))
    )
    );
     */

    /**
     * Undocumented function
     *
     * @param Select $nextSelect
     * @param boolean $autoLoad 此select变化时，是否自动加载下一级的选项
     * @return $this
     */
    public function withNext($nextSelect, $autoLoad = false)
    {
        $selectId = $this->getId();

        $nextScript = $nextSelect->asNextScript($selectId);

        $autoLoad = $autoLoad ? 1 : 0;

        $script = <<<EOT
        $(document).off('change', '#{$selectId}');
        $(document).on('change', "#{$selectId}", function () {
            var autoLoad = {$autoLoad};
            {$nextScript};
        });

EOT;
        $this->script[] = $script;

        return $this;
    }

    protected function isGroup()
    {
        foreach ($this->options as $option) {

            if (isset($option['options']) && isset($option['label'])) {
                $this->group = true;
                break;
            }
        }

        return $this->group;
    }

    public function beforRender()
    {
        if ($this->select2) {
            $this->select2Script();
        }

        return parent::beforRender();
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!($this->value === '' || $this->value === null)) {
            $this->checked = $this->value;
        } else {
            $this->checked = $this->default;
        }

        $this->isGroup();

        if (!$this->group && !isset($this->options[''])) {
            $this->options = ['' => $this->jsOptions['placeholder']] + $this->options;
        }

        $vars = array_merge($vars, [
            'checked' => '-' . $this->checked,
            'select2' => $this->select2,
            'group' => $this->group,
            'options' => $this->options,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
