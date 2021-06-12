<?php

namespace tpext\builder\toolbar;

use think\Model;
use tpext\builder\common\Builder;

class Actions extends DropdownBtns
{
    protected $mapClass = [];

    protected $mapData = [];

    protected $data = [];

    protected $dataId = 0;

    protected $initPostRowidScript = false;

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function dataId($val)
    {
        $this->dataId = $val;
        $this->addGroupAttr('data-id="' . $val . '"');
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function parseUrl($data)
    {
        $this->data = $data;

        preg_match_all('/\{([\w\.]+)\}/', $this->href, $matches);

        $keys = ['__data.pk__'];
        $replace = [$this->dataId];
        $arr = null;

        foreach ($matches[1] as $match) {
            $arr = explode('.', $match);

            if (count($arr) == 1) {

                $keys[] = '__data.' . $arr[0] . '__';
                $replace[] = isset($data[$arr[0]]) ? $data[$arr[0]] : '';
            } else if (count($arr) == 2) {

                $keys[] = '__data.' . $arr[0] . '.' . $arr[1] . '__';
                $replace[] = isset($data[$arr[0]]) && isset($data[$arr[0]][$arr[1]]) ? $data[$arr[0]][$arr[1]] : '';
            } else {
                //最多支持两层 xx 或 xx.yy
            }
        }

        $this->__href__ = str_replace($keys, $replace, $this->href);

        foreach ($this->items as $key => &$item) {
            if (is_string($item)) {
                $item = ['label' => $item];
            }
            if (!isset($item['url']) || empty($item['url'])) {
                if ($key == 'enable') {
                    $item['url'] = url('enable', ['state' => 1]);
                } else if ($key == 'disable') {
                    $item['url'] = url('enable', ['state' => 0]);
                } else {
                    $item['url'] = url($key);
                }
            } else {
                if (stripos($item['url'], '/') === false) {
                    $item['url'] = url($item['url']);
                }
            }
            $item['url'] = str_replace($keys, $replace, $item['url']);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function parseMapClass($data)
    {
        $values = $class = $field = $logic = $val = $match = null;
        $matchClass = [];

        foreach ($this->items as &$item) {
            if (!isset($item['class'])) {
                $item['class'] = '';
            }
            if (!isset($item['mapClass'])) {
                continue;
            }
            $matchClass = [];
            foreach ($item['mapClass'] as $class => $mp) {
                if (is_array($mp)) {
                    $values = $mp[0];

                    if (!is_array($values)) {
                        $values = [$values];
                    }

                    $field = $mp[1];
                    $logic = $mp[2] ?? ''; //in_array|not_in_array|eq|gt|lt|egt|elt|strstr|not_strstr

                    if (strstr($field, '.')) {

                        $arr = explode('.', $field);

                        if (isset($data[$arr[0]]) && isset($data[$arr[0]][$arr[1]])) {

                            $val = $data[$arr[0]][$arr[1]];
                        } else {
                            continue;
                        }
                    } else {

                        if (!isset($data[$field])) {
                            continue;
                        }

                        $val = $data[$field];
                    }

                    $match = false;
                    if ($logic == 'not_in_array') {
                        $match = !in_array($val, $values);
                    } else if ($logic == 'eq' || $logic == '==') {
                        $match = $val == $values[0];
                    } else if ($logic == 'gt' || $logic == '>') {
                        $match = is_numeric($values[0]) && $val > $values[0];
                    } else if ($logic == 'lt' || $logic == '<') {
                        $match = is_numeric($values[0]) && $val < $values[0];
                    } else if ($logic == 'egt' || $logic == '>=') {
                        $match = is_numeric($values[0]) && $val >= $values[0];
                    } else if ($logic == 'elt' || $logic == '<=') {
                        $match = is_numeric($values[0]) && $val <= $values[0];
                    } else if ($logic == 'strpos' || $logic == 'strstr') {
                        $match = strstr($val, $values[0]);
                    } else if ($logic == 'not_strpos' || $logic == 'not_strstr' || $logic == '!strpos' || $logic == '!strstr') {
                        $match = !strstr($val, $values[0]);
                    } else //default in_array
                    {
                        $match = in_array($val, $values);
                    }
                    if ($match) {
                        $mapClass[] = $class;
                    }
                } else if ($mp instanceof \Closure) {
                    // 'delete' => ['hidden' => function ($data) {
                    //     return $data['pay_status'] >1;
                    // }],
                    $match = $mp($data);
                    if ($match) {
                        $matchClass[] = $class;
                    }
                } else {
                    if (isset($data[$mp]) && $data[$mp]) {
                        $matchClass[] = $class;
                    }
                }
            }
            if (count($matchClass)) {
                $item['class'] .= ' ' . implode(' ', array_unique($matchClass));
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $mapData
     * @return $this
     */
    public function mapClass($mapData)
    {
        if (!empty($mapData)) {
            foreach ($this->items as $key => &$item) {
                if (isset($mapData[$key])) {
                    $item['mapClass'] = $mapData[$key];
                }
            }
        }

        return $this;
    }

    protected function postRowidScript()
    {
        if ($this->initPostRowidScript) {
            return '';
        }

        $confirms = [];
        $actions = [];

        foreach ($this->items as $key => $item) {
            if (!Builder::checkUrl($item['url'])) {
                continue;
            }
            $confirms[$item['url']] = isset($item['confirm']) ? $item['confirm'] : '1';
            $actions[$key] = $item;
        }

        $this->items = $actions;

        $script = '';
        $class = 'dropdown-actions';

        $this->addGroupClass($class);

        $confirms = json_encode($confirms, JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

        tpextbuilder.postActionsRowid('{$class}', {$confirms});

EOT;
        $this->initPostRowidScript = true;

        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        $this->postRowidScript();

        $this->parseMapClass($this->data);

        return parent::beforRender();
    }
}
