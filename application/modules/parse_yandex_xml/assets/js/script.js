var mysteep = 50;
function url_oasis(count) {
    $('.action_name').text('Загрузка файла').show();
    $(".parse_yandex_xml__frame").addClass("loading");
    $.post('/parse_yandex_xml/start_parse', {
            // 'cats': 1
        },
        function (datas) {
            $('.action_name').text('Загрузка файла').show();
            showMessage(lang('Файл загружен'), 'g');
            $(".parse_yandex_xml__frame").addClass("loading");
            $.post('/parse_yandex_xml/start_parse', {
                    'cats': 1
                },
                function (data1) {
                    $('.action_name').text('Обновление Категорий').show();
                    answers = JSON.parse(data1);

                    a = answers;

                    if (a[1] == 'r') {
                        showMessage(lang('Message'), a[0], a[1]);
                    }
                    if (a[1] == 'g') {
                        alert(a[0]);
                        showMessage(lang('Message'), a[0], a[1]);
                    } else {
                        alert('Не все товары созданы, запустите скрипт еще раз');
                    }
                    // $.pjax({
                    //     url: window.location.pathname,
                    //     container: '#mainContent'
                    // });
                    $(".parse_yandex_xml__frame").addClass("loading");
                    run_prods(Number(a[2]), 0);
                });
        });
};


function run_prods(all_count, data0) {

    $(".parse_yandex_xml__frame").addClass("loading");
    //console.log('count ' + count);    
    $.post('/parse_yandex_xml/start_parse', {
            'products': Number(data0) + mysteep
        },
        function (data) {
            console.log(data0);
            console.log(data);
            if (Number(data0) / Number(all_count) < 1) {

                showProcess(Number(data0) + ' / ' + Number(all_count), Number(data0) / Number(all_count));
            }
            $('.action_name').text('Обновление товаров %').show();
            if (Number(data0) >= Number(all_count)) {
                $.pjax({
                    url: window.location.pathname,
                    container: '#mainContent'
                });
            } else {
                run_prods(Number(all_count), Number(data0) + mysteep);
            }
        });
};


function showProcess(sucsess, sucsess2) {

    // $('#url_n, #refreshScript').hide();
    $('.progress').show();
    // $('#runScript').text('Загрузка!');
    $('.bar').text(sucsess);
    $('.bar').css('width', sucsess2 * 100 + '%');

}


function addCategoryModalOasis() {
    if ($('#fast_add_formOasis').valid())
        $('#fast_add_formOasis').ajaxSubmit({
            success: function (responseText) {
                responseObj = JSON.parse(responseText);
                $('.modal').modal('hide');
                if (responseObj.success) {
                    // $('#iddCategory').html(responseObj.categories);
                    //$('#iddCategory').find('option:selected').removeAttr('selected');
                    //$('#iddCategory').trigger("chosen:updated");
                    $('select[name="main_set[CategoryIdOasis]"]').html(responseObj.categories)
                    $('select[name="main_set[CategoryIdOasis]"]').trigger("chosen:updated");
                    showMessage(lang('Message'), responseObj.message);
                }
                else
                    showMessage(lang('Error'), responseObj.message, 'r');
            }
        });
    return false;
}



