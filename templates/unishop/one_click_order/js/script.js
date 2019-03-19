/* Add dynamic variantId into button href when variant has changed */
$(document).on('click', '[data-one-click-btn]', function (e) {
  e.preventDefault();

  var button = $(this);
  var variantId = button.attr('data-one-click-variant');
  var moduleHref = button.attr('data-one-click-href');
  var finalHref = moduleHref + '/' + variantId;

  button.attr('href', finalHref);

  $.mlsModal({
    src: $(this).attr('href')
  });

});

/* Submit one click order form in modal window */
$(document).on('submit', '[data-one-click-form]', function (e) {
  e.preventDefault();

  var form = $(this);

  $.ajax({
    url: form.attr('action'),
    type: form.attr('method') ? form.attr('method') : 'post',
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
      var errorListElem = form.find('[data-one-click-validation-list]');
      var sucessMessageElem = form.find('[data-one-click-success]');
      var formFields = form.find('[data-one-click-fields]');
      var submitBtn = form.find('[data-one-click-submit]');

      /* Clear validation error message on next submit */
      errorListElem.addClass('hidden').html('');

      if (data.status == false) {
        /* Errors */
        $.mlsOneClick.validate(errorListElem, data.validations);
      } else {
        /* Success */
        sucessMessageElem.removeClass('hidden');
        $.merge(formFields, submitBtn).addClass('hidden');

      }
    }
  });

});

$.mlsOneClick = {

  validate: function (list, errorList) {

    /* Clear old data in case of second request */
    list.html('');

    /* Paste error element into list */
    for (var error in errorList) {
      if (errorList.hasOwnProperty(error) && typeof errorList[error] == 'string') {
        list.append('<p>' + errorList[error] + '</p>');
      }
    }

    /* Make errors list visible to users */
    list.removeClass('hidden');

  }
};