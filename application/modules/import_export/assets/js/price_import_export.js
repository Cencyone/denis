
$(document).ready(function () {

    $(".runPriceExport").on("click", function () {

        var form_data = $('#makePriceExportForm').serialize();

        $.ajax({
            url: "/admin/components/init_window/import_export/getPriceExport",
            type: "post",
            data: form_data ,
            success: function (data) {
                switch (data) {
                    case "xls":
                    case "xlsx":
                        $("#makePriceExportForm input[name='formed_file_type']").val(data);
                        $('#makePriceExportForm').submit();
                        break;
                    default:
                        showMessage("", data , 'r');
                }
            }
        });
    });

    var files;

    $('#import_file').change(function(){
        files = this.files;
    });


    $("#makePriceImport").on("click", function () {

        var data = new FormData();
        $.each( files , function ( key, value ){
            data.append( key, value );
        });

        $.ajax({
            url: "/admin/components/init_window/import_export/getPriceImport",
            type: "post",
            data: data ,
            processData: false, // Не обрабатываем файлы (Don't process the files)
            contentType: false, // Так jQuery скажет серверу что это строковой запрос
            success: function (data) {





                $('body').append(data);







                //console.log(data);


            }
        });

        return false;
    });

});
