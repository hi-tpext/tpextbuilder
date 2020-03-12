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
     * @param boolean $show
     * @return $this
     */
    public function dataUrl($url, $options = ['delay' => 250, 'id' => 'id', 'text' => 'text'], $loadmore = true)
    {
        $this->jsOptions['ajax'] = [
            'url' => $url,
            'options' => $options,
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

            $.get('{$url}',{q : $('#{$prevID}').val(),eleid : '{$prevID}'}, function (data) {
                $('#{$selectId}').find("option").remove();
                //$('#{$selectId}').select2('destroy').empty();
                $('#{$selectId}').select2({
                    {$configs},
                    data: $.map(data, function (d) {
                        d.id = d.{$id};
                        d.text = d.{$text};
                        return d;
                    })
                }).trigger('change');
            });

EOT;
            $this->jsOptions['ajax'] = $ajax;
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

            $script = <<<EOT

            $('#{$selectId}').select2({
              {$configs},
              ajax: {
                url: '{$url}',
                dataType: 'json',
                delay: {$delay},
                data: function (params) {
                  return {
                    q: params.term || '',
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
     * @return $this
     */
    public function withNext($nextSelect)
    {
        $selectId = $this->getId();

        $nextScript = $nextSelect->asNextScript($selectId);

        $script = <<<EOT
        $(document).off('change', '#{$selectId}');
        $(document).on('change', "#{$selectId}", function () {
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

        $vars = array_merge($vars, [
            'checked' => $this->checked,
            'select2' => $this->select2,
            'group' => $this->group,
            'options' => $this->options,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
