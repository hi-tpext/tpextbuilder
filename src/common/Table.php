<?php

namespace tpext\builder\common;

use tpext\builder\table\Column;

class Table implements Renderable
{
    protected $headers = [];

    protected $cols = [];

    protected $data = [];

    public function column($name, $label = '')
    {
        if (empty($label)) {
            $label = ucfirst($name);
        }
        $col = new Column($name, $label);
        $this->cols[] = $col;
        $this->headers[] = $label;
        return $col;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render()
    {
        return 'xxx';
    }
}
