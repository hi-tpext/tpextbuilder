<?php

namespace tpext\builder\traits;

use think\Collection;

trait HasOptions
{
    protected $options = [];

    /**
     * Undocumented function
     *
     * @param array|Collection $options
     * @return $this
     */
    public function options($options)
    {
        if ($options instanceof Collection) {
            return $this->optionsData($options);
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Collection $optionsData
     * @param string $textField
     * @param string $keyField
     * @return $this
     */
    public function optionsData($optionsData, $textField = '', $keyField = '')
    {
        $options = [];
        foreach ($optionsData as $data) {
            if (empty($keyField)) {
                //模型需要实现[getOptKeyAttr]，没有的话默认为主键
                $keyField = isset($data['opt_key']) ? 'opt_key' : $data->getPk();
            }
            if (empty($textField)) {
                $textField = isset($data['opt_text']) ? 'opt_text' : 'name'; //模型需要实现[getOptTextAttr]，否则看是否刚好有name这个字段;
            }

            $options[$keyField] = $data[$textField];
        }

        $this->options = $options;

        return $this;
    }
}
