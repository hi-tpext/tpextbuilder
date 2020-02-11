$(function () {
    //动态选择框，上下级选中状态变化
    $('input.checkall').each(function (i, e) {
        var checkall = $(e);
        var checkboxes = $('.' + checkall.data('check'));
        var count = checkboxes.size();

        checkall.on('change', function () {
            checkboxes.prop('checked', checkall.is(':checked'));
        });

        checkboxes.on('change', function () {
            var ss = 0;
            checkboxes.each(function (ii, ee) {
                if ($(ee).is(':checked')) {
                    ss += 1;
                }
            });
            checkall.prop('checked', ss == count);
        });
    });

    $("form select").on("select2:opening", function (e) {
        if ($(this).attr('readonly') || $(this).is(':hidden')) {
            e.preventDefault();
        }
    });

    $('input[type="text"],textarea').each(function () {
        if ($(this).attr('maxlength')) {
            $(this).maxlength({
                warningClass: "label label-info",
                limitReachedClass: "label label-warning",
                placement: "centered-right",
            });
        }
    });

    $('.btn-loading').click(function () {
        var l = $('body').lyearloading({
            opacity: 0.1, // 遮罩层透明度，为0时不透明
            backgroundColor: '#ccc', // 遮罩层背景色
            imgUrl: '', // 使用图片时的图片地址
            textColorClass: 'text-success', // 文本文字的颜色
            spinnerColorClass: 'text-success', // 加载动画的颜色(不使用图片时有效)
            spinnerSize: 'lg', // 加载动画的大小(不使用图片时有效，示例：sm/nm/md/lg，也可自定义大小，如：25px)
            spinnerText: '加载中...', // 文本文字    
            zindex: 9999, // 元素的堆叠顺序值
        });
        setTimeout(function () {
            l.hide();
        }, 500000)
    });

    $('select').each(function (i, e) {
        if ($(e).attr('readonly')) {
            setTimeout(function () {
                $(e).parent('div').find('span.select2-selection__choice__remove').first().css('display', 'none');
                $(e).parent('div').find('li.select2-search').first().css('display', 'none');
                $(e).parent('div').find('span.select2-selection__clear').first().css('display', 'none');
                $(e).parent('div').find('span.select2-selection').first().css('background-color', '#eee');
            }, 1000);
        }
    });

    /*
     * 示例上传成功采用返回ID的形式，即上传成功以附件表形式存储，返回给前端ID值。
     * 成功返回示例：{"status":200,"info":"成功","class":"success","id":1,"picurl":".\/upload\/images\/lyear_5ddfc00174bbb.jpg"}
     * 这里设定单图上传为js-upload-image，多图上传为js-upload-images
     * 存放预览图的div元素，命名：file_list_*；后面的上传按钮的命名：filePicker_*（这里的*跟隐藏的input的name对应）。方便单页面中存在有多个上传时区分以及使用。
     * input上保存上传后的图片ID以及设置上传时的一些参数，
     */

    // 通用绑定，
    $('.js-upload-files').each(function () {
        var $input_file = $(this).find('input'),
            $input_file_name = $input_file.attr('name');

        var jsOptions = window.uploadConfigs[$input_file_name];

        var $multiple = jsOptions.multiple, // 是否选择多个文件
            $ext = jsOptions.ext.join(','), // 支持的文件后缀
            $size = jsOptions.fileSingleSizeLimit; // 支持最大的文件大小

        var $file_list = $('#file_list_' + $input_file_name);
        var ratio = window.devicePixelRatio || 1;
        var thumbnailWidth = (jsOptions.thumbnailWidth || 165) * ratio;
        var thumbnailHeight = (jsOptions.thumbnailHeight || 110) * ratio;

        $file_list.find('li.pic-item').each(function (ii, ee) {
            var $li = $(ee);
            var $btn = $li.find('a.btn-link-pic');
            if ($btn && $btn.attr('href')) {
                var href = $btn.attr('href');
                $img = $li.find('img.preview-img');
                if (!/.+\.(png|jpg|jpeg|gif|bmp|wbmp|webpg)$/i.test(href)) {
                    $btn.removeClass('btn-link-pic');
                    $img.replaceWith('<div class="cantpreview" style="position:relative;width:' + thumbnailWidth + 'px;height:' +
                        thumbnailHeight + 'px"><div  class="filename" style="width:100%;font-size:12px;text-align:center;position:absolute;top:' + (thumbnailHeight / 2 - 10) + 'px;">' + href + '</div></div>');
                } else {
                    $img.css({
                        'display': 'block',
                        'max-height': 'auto',
                        'max-width': thumbnailWidth + 'px',
                        'margin': '0 auto'
                    }).parent('div').css({
                        'height': thumbnailHeight + 'px',
                        'width': thumbnailWidth + 'px',
                    });
                }
            }
        });

        if (jsOptions.canUpload) {
            var uploader = WebUploader.create({
                auto: true,
                chunked: true,
                prepareNextFile: true,
                duplicate: jsOptions.duplicate ? true : false,
                resize: jsOptions.resize ? true : false,
                swf: jsOptions.swf_url,
                server: jsOptions.upload_url,
                pick: {
                    id: '#picker_' + $input_file_name,
                    multiple: $multiple
                },
                fileSingleSizeLimit: $size,
                fileNumLimit: jsOptions.fileNumLimit,
                fileSizeLimit: jsOptions.fileSizeLimit,
                accept: {
                    title: '文件',
                    extensions: $ext,
                    mimeTypes: jsOptions.mimeTypes || '*/*'
                },
                thumb: {
                    // 图片质量，只有type为`image/jpeg`的时候才有效。
                    quality: 70,
                    // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                    allowMagnify: false,
                    // 是否允许裁剪。
                    crop: true,
                    // 为空的话则保留原有图片格式。
                    // 否则强制转换成指定的类型。
                    type: 'image/jpeg'
                }
            });

            uploader.on('beforeFileQueued', function (file) {
                if ($multiple && $file_list.find('li.pic-item').size() >= jsOptions.fileNumLimit) {
                    lightyear.notify('最多允许上传' + jsOptions.fileNumLimit + '个文件', 'danger');
                    return false;
                }
            });
            uploader.on('fileQueued', function (file) {
                var $li = $('<li class="pic-item" id="' + file.id + '">' +
                        '  <figure>' +
                        '<div style="width:' + thumbnailWidth + 'px;height:' + thumbnailHeight + 'px">' +
                        '    <img>' +
                        '</div>' +
                        '    <figcaption>' +
                        '      <a class="btn btn-round btn-square btn-primary btn-link-pic" href="javascript:;"><i class="mdi mdi-eye"></i></a>' +
                        '      <a class="btn btn-round btn-square btn-danger btn-remove-pic" href="javascript:;"><i class="mdi mdi-delete"></i></a>' +
                        '    </figcaption>' +
                        '  </figure>' +
                        '</li>'),
                    $img = $li.find('img');

                if ($multiple) {
                    $file_list.append($li);
                } else {
                    $file_list.html($li);
                    $input_file.val('');
                }
                uploader.makeThumb(file, function (error, src) {
                    if (error) {
                        $img.replaceWith('<div class="cantpreview" style="position:relative;width:' + thumbnailWidth + 'px;height:' +
                            thumbnailHeight + 'px"><div class="filename" style="width:100%;font-size:12px;text-align:center;position:absolute;top:' + (thumbnailHeight / 2 - 10) + 'px;">不能预览</div></div>');
                        return;
                    }
                    $img.attr('src', src);
                    $img.css({
                        'display': 'block',
                        'min-height': 'auto',
                        'max-width': thumbnailWidth + 'px',
                        'margin': '0 auto'
                    });
                }, thumbnailWidth, thumbnailHeight);
                $('<div class="progress progress-sm"><div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div></div>').appendTo($li);
            });
            uploader.on('uploadProgress', function (file, percentage) {
                var $percent = $('#' + file.id).find('.progress-bar');
                $percent.css('width', percentage * 100 + '%');
            });
            uploader.on('uploadSuccess', function (file, response) {
                var $li = $('#' + file.id);
                if (response.status == 200) { // 返回200成功
                    if ($multiple) {
                        if ($input_file.val()) {
                            $input_file.val($input_file.val() + ',' + response.id);
                        } else {
                            $input_file.val(response.id);
                        }
                        $li.find('.btn-remove-pic').attr('data-id', response.id);
                    } else {
                        $input_file.val(response.id);
                    }
                }
                $('<div class="' + response.class + '"></div>').text(response.info + '(' + $file_list.find('li.pic-item').size() + '/' + jsOptions.fileNumLimit + ')').appendTo($li.find('figure'));
                if ($li.find('.cantpreview').size() > 0) {
                    $li.find('a.btn-link-pic').attr('href', response.picurl).removeClass('btn-link-pic').attr('target', '_blank');
                    $li.find('.filename').text(response.picurl);
                } else {
                    $li.find('a.btn-link-pic').attr('href', response.picurl);
                }
            });
            uploader.on('uploadError', function (file) {
                var $li = $('#' + file.id);
                $('<div class="error">上传失败</div>').appendTo($li).find('figure');
            });
            uploader.on('error', function (type) {
                switch (type) {
                    case 'Q_TYPE_DENIED':
                        lightyear.notify('文件类型不正确，只允许上传后缀名为：' + $ext + '，请重新上传！', 'danger');
                        break;
                    case 'F_EXCEED_SIZE':
                        lightyear.notify('文件不得超过' + ($size / 1024) + 'kb，请重新上传！', 'danger');
                        break;
                }
            });
            uploader.on('uploadComplete', function (file) {
                setTimeout(function () {
                    $('#' + file.id).find('.progress').remove();
                }, 500);
            });
            // 删除操作
            $file_list.delegate('.btn-remove-pic', 'click', function () {
                var id = $(this).data('id');
                var that = $(this);
                $.alert({
                    title: '提示',
                    content: '确认要删除此文件吗？',
                    buttons: {
                        confirm: {
                            text: '确认',
                            btnClass: 'btn-primary',
                            action: function () {
                                if ($multiple) {
                                    var ids = $input_file.val().split(',');
                                    if (id) {
                                        for (var i = 0; i < ids.length; i++) {
                                            if (ids[i] == id) {
                                                ids.splice(i, 1);
                                                break;
                                            }
                                        }
                                        $input_file.val(ids.join(','));
                                    }
                                } else {
                                    $input_file.val('');
                                }

                                that.closest('.pic-item').remove();
                            }
                        },
                        cancel: {
                            text: '取消',
                            action: function () {

                            }
                        }
                    }
                });
            });
        }

        // 接入图片查看插件
        $(this).magnificPopup({
            delegate: 'a.btn-link-pic',
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    });

});

(function (w) {

    var tpextbuilder = function () {};
    tpextbuilder.autoPost = function (id, url) {
        var obj = $('#' + id);
        if (!obj.size()) {
            return;
        }
        if (obj.is('div')) {
            $('body').on('change', '#' + id + ' :radio,#' + id + ' :checkbox', function () {
                var name = $(this).attr('name');
                var val = '';
                if ($(this).is(':radio')) {
                    val = $("input[name='" + name + "']:checked").val();
                } else {
                    var values = [];
                    $("input[name='" + name + "']:checked").each(function (i, e) {
                        values.push($(e).val());
                    });
                    val = values.join(',');
                }

                tpextbuilder.autoSendData(name, val, url);
            });
        } else {
            if (/^(input|textarea|select)$/i.test(obj.get(0).tagName)) {
                $('body').on('change', '#' + id, function () {

                    $('#' + id).toolbar({
                        content: '#edit-options',
                        position: 'left',
                        style: 'success',
                    });

                    var name = $(this).attr('name');
                    var val = '';
                    if (/^(input|textarea|select)$/i.test(this.tagName)) {
                        val = $(this).val();
                    }

                    tpextbuilder.autoSendData(name, val, url);
                });
            }
        }
    };

    tpextbuilder.postChecked = function (id, url, confirm) {
        var obj = $('#' + id);
        if (!obj.size()) {
            return;
        }
        $('body').on('click', '#' + id, function () {
            var name = 'rows';
            var val = '';

            var values = [];

            $("input.table-row:checked").each(function (i, e) {
                values.push($(e).val());
            });

            if (values.length == 0) {

                lightyear.notify('未选中任何数据', 'warning');

                return false;
            }

            val = values.join(',');

            if (confirm) {
                var text = $('#' + id).text();
                $.alert({
                    title: '操作提示',
                    content: '确定要执行批量<strong>' + text + '</strong>操作吗？',
                    buttons: {
                        confirm: {
                            text: '确认',
                            btnClass: 'btn-primary',
                            action: function () {
                                tpextbuilder.autoSendData(name, val, url);
                            }
                        },
                        cancel: {
                            text: '取消',
                            action: function () {

                            }
                        }
                    }
                });
            } else {
                tpextbuilder.autoSendData(name, val, url);
            }
        });

        if ($("input.table-row:checked").size() == 0) {
            $('#' + id).addClass('disabled');
        } else {
            $('#' + id).removeClass('disabled');
        }

        $('body').on('change', 'input.table-row', function () {
            if ($("input.table-row:checked").size() == 0) {
                $('#' + id).addClass('disabled');
            } else {
                $('#' + id).removeClass('disabled');
            }
        });

        $('body').on('change', 'input.table-row-checkall', function () {
            if ($("input.table-row:checked").is(':checked')) {
                $('#' + id).removeClass('disabled');
            } else {
                $('#' + id).addClass('disabled');
            }
        });
    }

    tpextbuilder.autoSendData = function (name, value, url) {
        name = name.split('-')[0];
        $.ajax({
            url: url,
            data: {
                name: name,
                value: value,
                __token__: w.__token__,
            },
            type: "POST",
            dataType: "json",
            success: function (data) {
                if (data.status) {
                    lightyear.notify('操作成功！', 'success');
                } else {
                    lightyear.notify('操作失败', 'warning');
                }
            },
            error: function () {
                lightyear.notify('网络错误', 'danger');
            }
        });
    };

    tpextbuilder.refreshTable = function () {

    }

    w.tpextbuilder = tpextbuilder;

})(window);