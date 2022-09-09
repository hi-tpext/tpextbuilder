<?php

namespace tpext\builder\toolbar;

use think\Model;
use tpext\builder\common\Builder;

class ActionBtn extends Bar
{
    protected $view = 'actionbtn';

    protected $mapClass = [];

    protected $postRowid = '';

    protected $extClass = '';

    protected $data = [];

    protected $dataId = 0;

    protected $confirm = true;

    protected $initPostRowidScript = false;

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function parseUrl($data = [])
    {
        if ($data) {
            $this->data = $data;
        } else {
            $data = $this->data;
        }

        if (empty($this->href) || empty($data)) {
            return $this;
        }

        preg_match_all('/__data\.([\w\.]+)__/', $this->href, $matches);

        $keys = ['__data.pk__'];
        $replace = [$this->dataId];
        $arr = [];

        if (isset($matches[1]) && count($matches[1]) > 0) {
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
        } else {
            $this->__href__ = $this->href;
        }

        return $this;
    }

    protected function parseLabel()
    {
        $data = $this->data;

        $label = $this->label;

        if ($label instanceof \Closure) {
            return $label($data);
        }

        if (empty($label)) {
            return '';
        }
        if (empty($data)) {
            return $label;
        }

        preg_match_all('/\{([\w\.]+)\}/', $label, $matches);

        $keys = [];
        $replace = [];
        $arr = [];

        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $match) {
                $arr = explode('.', $match);

                if (count($arr) == 1) {

                    $keys[] = '{' . $arr[0] . '}';
                    $replace[] = isset($data[$arr[0]]) ? $data[$arr[0]] : '';
                } else if (count($arr) == 2) {

                    $keys[] = '{' . $arr[0] . '.' . $arr[1] . '}';
                    $replace[] = isset($data[$arr[0]]) && isset($data[$arr[0]][$arr[1]]) ? $data[$arr[0]][$arr[1]] : '-';
                } else {
                    //最多支持两层 xx 或 xx.yy
                }
            }

            $val = str_replace($keys, $replace, $label);
        } else {
            $val = $label;
        }

        return $val;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function parseMapClass($data)
    {
        $matchClass = [];

        $values = $class = $field = $logic = $val = $match = null;

        $this->extClass = '';
        foreach ($this->mapClass as $class => $mp) {
            if (is_array($mp)) { // 'enable' => ['hidden' => [1, 'status']],
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
                    $matchClass[] = $class;
                }
            } else if ($mp instanceof \Closure) {
                // 'delete' => ['hidden' => function ($data) {
                //     return $data['pay_status'] >1;
                // }],
                $match = $mp($data);
                if ($match) {
                    $matchClass[] = $class;
                }
            } else { // 'enable' => ['hidden' => '__hi_en__'],
                if (isset($data[$mp]) && $data[$mp]) {
                    $matchClass[] = $class;
                }
            }
        }

        if (count($matchClass)) {
            $this->extClass .= ' ' . implode(' ', array_unique($matchClass));
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
        if (!empty($mapData) && isset($mapData[$this->name])) {
            $this->mapClass = $mapData[$this->name];
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function dataId($val)
    {
        $this->dataId = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean|string $confirm
     * @return $this
     */
    public function postRowid($url, $confirm = true)
    {
        $this->postRowid = $url;
        $this->confirm = $confirm ? $confirm : 0;

        return $this;
    }

    protected function postRowidScript()
    {
        if ($this->initPostRowidScript) {
            return '';
        }
        $script = '';
        $class = 'action-' . $this->name;

        $script = <<<EOT

        tpextbuilder.postRowid('{$class}', '{$this->postRowid}', '{$this->confirm}');

EOT;
        $this->script[] = $script;

        $this->initPostRowidScript = true;

        return $script;
    }

    public function beforRender()
    {
        if ($this->postRowid) {

            if (Builder::checkUrl($this->postRowid)) {
                $this->postRowidScript();
            } else {
                $this->addClass('hidden disabled');
            }
        }

        $this->parseMapClass($this->data);

        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'class' => $vars['class'] . $this->extClass,
            'dataId' => $this->dataId,
            'label' => $this->parseLabel()
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
