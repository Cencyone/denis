$(document).on('click', '[data-found-cheaper-link]', function (e) {
  e.preventDefault();
  var link = $(this);

  $.mlsModal({
    src: link.attr('data-found-cheaper-link'),
    data: {
      template: link.data('modal')
    }
  });

});

$(document).on('submit', '[data-found-cheaper]', function (e) {
  e.preventDefault();

  var form = $(this);

  $.ajax({
    url: form.attr('action'),
    type: form.attr('method') ? form.attr('method') : 'get',
    data: form.serialize(),
    dataType: 'json',
    beforeSend: function () {
      /* Loader visible before ajax response */
      $.mlsAjax.preloaderShow({
        type: 'frame',
        frame: form
      });
    },
    success: function (data) {
      var isSuccess = data.success ? true : false;

      if(isSuccess){
        form.find('[data-found-cheaper-errors-frame]').addClass('hidden');
        form.find('[data-found-cheaper-errors-list]').empty();
        form.find('[data-found-cheaper-success-message]').removeClass('hidden');
        form.find('[data-found-cheaper-form-fields], [data-found-cheaper-submit-btn]').addClass('hidden');
      }else{
        form.find('[data-found-cheaper-errors-frame]').removeClass('hidden');
        form.find('[data-found-cheaper-errors-list]').html(data.message);
      }

    }
  });

});