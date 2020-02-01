<?php

namespace tpext\builder\displayer;

class Select extends Radio
{
    protected $view = 'select';

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

    protected $select2Options = [
        'placeholder' => '请选择',
        'allowClear' => true,
        'minimumInputLength' => 0,
    ];

    /**
     * Undocumented function
     *
     * @param boolean $show
     * @return void
     */
    public function select2($use)
    {
        $this->select2 = $use;
    }

    public function dataUrl($url, $options = ['delay' => 250, 'id' => 'id', 'text' => 'text'], $loadmore = true)
    {
        $this->select2Options['ajax'] = [
            'url' => $url,
            'options' => $options,
            'loadmore' => $loadmore,
        ];
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return void
     */
    public function select2Options($options)
    {
        $this->select2Options = array_merge($this->select2Options, $options);
    }

    protected function select2Script()
    {
        $script = '';
        $id = $this->getId();

        if (isset($this->select2Options['ajax'])) {
            $ajax = $this->select2Options['ajax'];
            unset($this->select2Options['ajax']);
            $url = $ajax['url'];
            $id = isset($ajax['id']) ? $ajax['id'] : 'id';
            $text = isset($ajax['text']) ? $ajax['text'] : 'text';
            $delay = isset($ajax['delay']) ? $ajax['delay'] : 250;
            $loadmore = $ajax['loadmore'];

            $configs = json_encode($this->select2Options);

            $configs = substr($configs, 1, strlen($configs) - 2);

            $script = <<<EOT
            $("#{$id}").select2({
              {$configs},
              ajax: {
                url: '{$url}',
                dataType: 'json',
                delay: {$delay},
                data: function (params) {
                  return {
                    q: params.term,
                    page: params.page,
                    eleid : '{$id}'
                  };
                },
                processResults: function (data, params) {
                  params.page = params.page || 1;
                  return {
                    results: $.map(data.data, function (d) {
                               d.id = d.{$id};
                               d.text = d.{$text};
                               return d;
                            }),
                    pagination: {
                      more: {$loadmore} ? data.next_page_url : ''
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
            $configs = json_encode($this->select2Options);

            $configs = substr($configs, 1, strlen($configs) - 2);

            $script = <<<EOT
            $('#{$id}').select2({
                {$configs}
            });

EOT;
        }

        $this->script[] = $script;

        return $script;
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

        $this->style[] = 'body{widht:100%}';

        return parent::beforRender();
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!empty($this->value)) {
            $this->checked = $this->value;
        } else {
            $this->checked = $this->default;
        }

        $this->isGroup();

        $vars = array_merge($vars, [
            'checked' => $this->checked,
            'select2' => $this->select2,
            'group' => $this->group,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
