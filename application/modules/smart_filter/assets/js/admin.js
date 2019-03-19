$(document).on('ready pjax:end', function () {

    $('[data-delete]').on('click', function () {
        var href = $(this).data('delete');
        var ids = new Array();
        $('input[name=ids]:checked').each(function () {
            ids.push($(this).val());
        });
        $.post(href, {
            ids: ids
        }, function (data) {
            $('.modal_del').modal('hide');
            $('#mainContent').after(data);
            window.location.reload();
        });
    });

    var popover;

    $('.popover_ref').on('click', function () {

        popover = $(this);

    });

    $(document).on('click', '[data-var]', function (e) {

        var val = $(this).text().trim();
        var area = popover.closest('.control-group').find('[data-insert-var]');

        if (area.length !== 1) {
            return;
        }

        if (area.hasClass('elRTE')) {
            console.log(area.attr('id'));
            tinymce.EditorManager.get(area.attr('id')).insertContent(val);
        } else {
            var caretPos = area.get(0).selectionStart;
            var textAreaTxt = area.val();
            area.val(textAreaTxt.substring(0, caretPos) + val + textAreaTxt.substring(caretPos));

        }
    });

    $('input[data-submit]').keydown(function (e) {
        if (e.which == 13) {
            submitFilter();
        }
    })

    $('select[data-submit]').change(function () {
        submitFilter();
    })

    function submitFilter() {

        $('[data-submit]').filter(function () {
            return $(this).val() == ''
        }).attr('disabled', '');
        $('[data-filter]').submit();

    }

    $('[data-change-active]').click(function () {
        var id = $(this).attr('data-id');
        $.ajax({
            type: "post",
            url: "/admin/components/cp/smart_filter/changeActive",
            data: "id=" + id,
            success: function (data) {
                $('.notifications').append(data);
            }
        });
    })


    $(document).on('change', '[data-change]', function () {

        var id = $(this).find('option:selected').val();
        var dataChanges = $(this).data('change');
        var dataChangesArray = dataChanges.split(',')

        for (var key in dataChangesArray) {
            var dataChange = dataChangesArray[key];
            var changeSelect = $('[' + dataChange + ']');
            var url = changeSelect.attr(dataChange) + '/' + id;

            loadValues(url, changeSelect);
        }
    });

    function loadValues(url, changeSelect) {
        var locale = $('[data-locale]').val()
        changeSelect.empty();
        $.get('/admin/components/cp/smart_filter/' + url + '/' + locale, function (data) {
            var hiddenBlock = changeSelect.parents('[data-hidden]');

            if (null !== data && data.length > 0) {

                for (var key in data) {
                    $(changeSelect).append($('<option/>').attr('value', data[key].id).html(data[key].value));
                }

                hiddenBlock.removeClass('d_n');
                changeSelect.chosen();
            } else {
                hiddenBlock.addClass('d_n')

            }

            changeSelect.trigger('change')
            changeSelect.trigger('chosen:updated')
        }, "JSON");
    }
});