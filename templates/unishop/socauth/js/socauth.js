/* Open social network login page on popup window */
$(document).on('click', '[data-socauth-popup]', function (e) {
  var link = $(this);
  var popupWindow = {
    href: link.attr('href'),
    title: link.attr('title'),
    width: 500,
    height: 600
  };
  var left = (window.innerWidth / 2) - (popupWindow.width / 2);
  var top = (window.innerHeight / 2) - (popupWindow.height / 2);

  e.preventDefault();

  window.open(popupWindow.href, popupWindow.title, 'width=' + popupWindow.width + ',height=' + popupWindow.height + ',left=' + left + ',top=' + top);

});


/* Unlink from social network from profile page */
$(document).on('click', '[data-socauth-unlink]', function (e) {
  var href = $(this).attr('data-socauth-unlink');

  e.preventDefault();

  $.ajax({
    type: 'post',
    url: href,
    dataType: 'json',
    success: function (data) {
      if (data.answer === 'sucesfull') {
        document.location.href = '/shop/profile';
      }
    }
  });
});