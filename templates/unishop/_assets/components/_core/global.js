;(function($){

  /* Remove ajax loader */
  $(document).on('ajaxStop', function(){
    $.mlsAjax.preloaderHide();
  });

  /* Hover to click on touch devices. Double click to link */
  $('[data-global-doubletap]').doubleTapToGo();

})(jQuery);