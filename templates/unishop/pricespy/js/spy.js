/* Open message in modal before adding to spy */
$('[data-pricespy-link]').magnificPopup({
  type: 'inline',
  midClick: true,
  showCloseBtn: false,
  modal: false
});

/* Add to follow after click button in modal window */
$(document).on('click', '[data-pricespy-href]', function (e) {
  e.preventDefault();

  var link = $(this);
  var modal = link.closest('[data-pricespy-modal]');

  $.ajax({
    url: link.attr('data-pricespy-href') +'/'+ link.attr('data-pricespy-variant'),
    type: 'post',
    dataType: 'json',
    beforeSend: function () {
      /* Frame loader */
      $.mlsAjax.preloaderShow({
        type: 'frame',
        frame: modal
      });
    },
    success: function (data) {
      modal.find('[data-pricespy-is-following-show]').removeClass('hidden');
      modal.find('[data-pricespy-is-following-hide]').addClass('hidden');
    }
  });

});

/* Remove item from follow list */
$(document).on('click', '[data-pricespy-unsubscribe]', function (e) {
  e.preventDefault();

  var unsubscribeBtn = $(this);

  $.ajax({
    type: 'post',
    url: unsubscribeBtn.attr('data-pricespy-unsubscribe'),
    dataType: 'json',
    success: function (data) {
      if(data.answer === 'sucesfull'){
        window.location.assign(unsubscribeBtn.attr('data-pricespy-unsubscribe-redirect'));
      }
    }
  });
});
