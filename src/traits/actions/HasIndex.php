<?php

namespace tpext\builder\traits\actions;

use tpext\builder\common\Builder;
/**
 * 列表
 */

trait HasIndex
{
    use HasExport;
    use HasSelectPage;

    public function index()
    {
        $builder = $this->builder($this->pageTitle, $this->indexText, 'index');

        $table = $builder->table();
        $table->pk($this->getPk());
        $this->table = $table;
        $this->search = $table->getSearch();

        $this->buildSearch();
        $this->buildDataList();

        if (request()->isAjax()) {
            return $this->table->partial()->render();
        }

        return $builder->render();
    }

    /**
     * Undocumented function
     *
     * @param Builder $builder
     * @return void
     */
    protected function createIndexTabs($builder)
    {
        $tab = $builder->tab();
        $tabClass = $this->indexTabsKey . '-tab';
        $tab->addClass($tabClass);
        if (!isset($this->indexTabs[''])) {
            $this->indexTabs = ['' => '全部'] + $this->indexTabs;
        }
        foreach ($this->indexTabs as $k => $v) {
            $tab->add($v, $k === $this->indexTabsDefault, $this->indexTabsKey . '_' . $k);
        }

        $element = '.row-' . $this->indexTabsKey;
        $script = <<<EOT

        $('body').on('click', '.{$tabClass} .nav-item a', function(){
            var val = $(this).attr('href').replace(/.+?tabkey_(.*)$/,'$1');
            if($('{$element}').hasClass('select2-use-ajax'))
            {
                $('{$element}').empty().append('<option value="' + val + '">' + $(this).text() + '</option>');
            }
            else
            {
                $('{$element}').val(val);
            }
            $('{$element}').trigger('change');
            $('.row-submit').trigger('click');
        });

EOT;

        $builder->addScript($script);
    }
}
