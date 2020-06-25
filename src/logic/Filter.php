<?php

namespace tpext\builder\logic;

use \tpext\builder\common\Search;

class Filter
{
    /**
     * Undocumented function
     *
     * @param Search $search
     * @return array
     */
    public function getQuery($search)
    {
        $where = [];
        $data = request()->post();

        $rows = $search->getRows();

        $comumn = '';

        foreach ($rows as $row) {

            $comumn = $row->getName();

            if (isset($data[$comumn]) && $data[$comumn] !== '' && $data[$comumn] !== []) {
                $where[] = [$comumn, $row->getFilter(), $data[$comumn]];
            }
        }
    }
}
