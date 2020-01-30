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
});