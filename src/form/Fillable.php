<?php

namespace tpext\builder\form;

use tpext\builder\common\Renderable;

interface Fillable extends Renderable
{
    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function fill($data = []);
}
