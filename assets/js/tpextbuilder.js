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
});

$(document).ready(function () {
    $('select').each(function () {
        if ($(this).is('[readonly]')) {
            $(this).parent('div').find('span.select2-selection__choice__remove').first().css('display', 'none');
            $(this).parent('div').find('li.select2-search').first().css('display', 'none');
            $(this).parent('div').find('span.select2-selection__clear').first().css('display', 'none');
            $(this).parent('div').find('span.select2-selection').first().css('background-color', '#eee');
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

    $('.btn-loading').click(function() {
        var l = $('body').lyearloading({
            opacity           : 0.1,              // 遮罩层透明度，为0时不透明
            backgroundColor   : '#ccc',           // 遮罩层背景色
            imgUrl            : '',               // 使用图片时的图片地址
            textColorClass    : 'text-success',   // 文本文字的颜色
            spinnerColorClass : 'text-success',   // 加载动画的颜色(不使用图片时有效)
            spinnerSize       : 'lg',             // 加载动画的大小(不使用图片时有效，示例：sm/nm/md/lg，也可自定义大小，如：25px)
            spinnerText       : '加载中...',       // 文本文字    
            zindex            : 9999,             // 元素的堆叠顺序值
        });
        setTimeout(function() {
            l.hide();
        }, 500000)
    });
});