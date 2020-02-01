<?php

namespace tpext\builder\displayer;

class Tags extends Field
{
    protected $view = 'tags';

    protected $js = [
        '/assets/tpextbuilder/js/jquery-tags-input/jquery.tagsinput.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/jquery-tags-input/jquery.tagsinput.min.css',
    ];
}
