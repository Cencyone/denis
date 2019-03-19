/* Component functions */
(function ($) {

  $.mshButtons = {
    addLoader: function (button) {
      /* Use timeout to prevent bug in IE-11
       * User couldn't go to order page from modal cart in
       * */
      setTimeout(function () {
        button.attr('disabled', 'disabled').find('[data-button-loader="loader"]').removeClass('hidden');
      }, 0);
    },
    removeLoader: function (button) {
      button.removeAttr('disabled').find('[data-button-loader="loader"]').addClass('hidden');
    }
  }

})(jQuery);


/* Event listeners */

/* Button icon loader */
$(document).on('click', '[data-button-loader="button"]', function () {
  $.mshButtons.addLoader($(this));
});