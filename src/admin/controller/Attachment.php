<?php

namespace tpext\builder\admin\controller;

use think\Controller;
use think\facade\Session;
use tpext\builder\common\Module;
use tpext\builder\traits\actions\HasBase;
use tpext\builder\traits\actions\HasIndex;
use tpext\builder\traits\actions\HasAutopost;
use tpext\builder\common\model\Attachment as AttachmentModel;

/**
 * Undocumented class
 * @title 文件管理
 */
class Attachment extends Controller
{
    use HasBase;
    use HasIndex;
    use HasAutopost;

    /**
     * Undocumented variable
     *
     * @var AttachmentModel
     */
    protected $dataModel;

    protected function initialize()
    {
        $this->dataModel = new AttachmentModel;

        $this->pageTitle = __blang('bilder_attachment_manage');
        $this->postAllowFields = ['name'];
        $this->pagesize = 8;
    }

    protected function filterWhere()
    {
        $searchData = request()->get();

        $where = [];

        $admin = Session::get('admin_user');

        if ($admin['role_id'] != 1) {
            $where[] = ['admin_id', '=', $admin['id']];
        }

        if (!empty($searchData['name'])) {
            $where[] = ['name', 'like', '%' . $searchData['name'] . '%'];
        }

        if (!empty($searchData['url'])) {
            $where[] = ['url', 'like', '%' . $searchData['url'] . '%'];
        }

        $ext = input('ext');

        if ($ext) {
            $where[] = ['suffix', 'in', explode(',', $ext)];
        }

        if (!empty($searchData['suffix'])) {
            $where[] = ['suffix', 'in', $searchData['suffix']];
        }

        return $where;
    }

    /**
     * 构建搜索
     *
     * @return void
     */
    protected function buildSearch()
    {
        $search = $this->search;

        $search->text('name', __blang('bilder_attachment_name'), '6 col-xs-6')->size('4 col-xs-4', '8 col-xs-8')->maxlength(55);
        $search->text('url', __blang('bilder_attachment_url'), '6 col-xs-6')->size('4 col-xs-4', '8 col-xs-8')->maxlength(200);

        $exts = [];
        $arr = [];

        $ext = input('ext');
        if ($ext) {
            $arr = explode(',', $ext);
        } else {
            $config = Module::getInstance()->getConfig();
            $arr = explode(',', $config['allow_suffix']);
        }

        foreach ($arr as $a) {
            $exts[$a] = $a;
        }

        $search->multipleSelect('suffix', __blang('bilder_attachment_suffix'), '6 col-xs-6')->size('4 col-xs-4', '8 col-xs-8')->options($exts);
    }
    /**
     * 构建表格
     *
     * @return void
     */
    protected function buildTable(&$data = [], $isExporting = false)
    {
        $table = $this->table;

        $choose = input('choose', 0);
        $limit = input('limit', 1);

        $table->show('id', 'ID');
        $table->text('name', __blang('bilder_attachment_name'))->autoPost();
        $table->file('file',  __blang('bilder_attachment_file'))->thumbSize(50, 50);
        if (!$choose) {
            $table->show('mime', __blang('bilder_attachment_mime'));
            $table->show('size', __blang('bilder_attachment_size'))->to('{val}MB');
            $table->show('suffix', __blang('bilder_attachment_suffix'))->getWrapper()->addStyle('width:80px');
            $table->show('storage', __blang('bilder_attachment_storage'));
        }

        $table->raw('url', __blang('bilder_attachment_url'))->to('<a href="{val}" target="_blank">{val}</a>');
        $table->show('create_time', __blang('bilder_attachment_create_time'))->getWrapper()->addStyle('width:160px');

        $table->getToolbar()
            ->btnRefresh()
            ->btnImport(url('uploadSuccess'), '', ['280px', '235px'], 0, __blang('bilder_upload_file_button'), 'btn-pink', 'mdi-cloud-upload', 'title="' . __blang('bilder_upload_nwe_file') . '"', '')
            ->btnToggleSearch();

        foreach ($data as &$d) {
            $d['file'] = $d['url'];
        }

        unset($d);

        if ($choose) {
            $id = input('id');
            if ($limit > 1) {
                $table->getToolbar()
                    ->btnOpenChecked('#', __blang('bilder_choose_multiple_files_button'), 'btn-success', 'mdi-note-plus-outline', 'onclick="chooseMultipleUrlsAndClose()"');
            } else {
                $table->useCheckbox(false);
            }
            $table->getActionbar()
                ->btnLink('choose', '#', __blang('bilder_choose_file_button'), 'btn-success', 'mdi-note-plus-outline', 'onclick="chooseUrlAndClose(this)"');

            $script = <<<EOT

            var chooseUrlLimit = {$limit};

            window.chooseUrlAndClose = function(e) {
                var url = $(e).parents('tr').find('.row-url-td').find('a').attr('href');
                if(chooseUrlLimit > 1) {
                    parent.$('#{$id}').val(parent.$('#{$id}').val().trim(',')+(parent.$('#{$id}').val()?',':'') + url).trigger('change');
                }
                else {
                    parent.$('#{$id}').val(url).trigger('change');
                }
                
                window.closeChoose();
            }

            window.chooseMultipleUrlsAndClose = function() {
                var chosen = parent.$('#{$id}').val().trim(',');
                var urls = [];
                if(chosen) {
                    urls = chosen.split(',');
                }
                $("input.table-row:checked").each(function (i, e) {
                    if(urls.length >= chooseUrlLimit) {
                        parent.lightyear.notify(__blang.bilder_maximum_upload_files_num_is + chooseUrlLimit, 'danger');
                        return false;
                    }
                    urls.push($(e).parents('tr').find('.row-url-td').find('a').attr('href'));
                });

                if(!urls.length){
                    lightyear.notify(__blang.bilder_data_not_found);
                    return;
                }

                parent.$('#{$id}').val(urls.join(',')).trigger('change');
                window.closeChoose();
            }

            window.closeChoose = function() {
                parent.layer.close(parent.layer.getFrameIndex(window.name));
            }
EOT;

            $this->builder()->addScript($script);
        } else {
            $table->useCheckbox(false);
            $table->useActionbar(false);
        }
    }

    /**
     * Undocumented function
     *
     * @title 上传成功
     * @return mixed
     */
    public function uploadSuccess()
    {
        $builder = $this->builder(__blang('bilder_file_uploading_succeeded'));

        $builder->addScript('parent.lightyear.notify("' . __blang('bilder_file_uploading_succeeded') . '","success");parent.$(".search-refresh").trigger("click");parent.layer.close(parent.layer.getFrameIndex(window.name));'); //刷新列表页

        return $builder->render();
    }
}
