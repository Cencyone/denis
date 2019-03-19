;(function($){

  $('[data-slider="banner-simple"]').each(function(){

    var el = $(this);
    
    el.find('[data-slider-slides]').removeAttr('data-slider-nojs').slick({
      adaptiveHeight: false,
      slidesToShow: 1,
      dots: el.data('dots'),
      arrows: el.data('arrows'),
      speed: el.data('speed'),
      autoplay: el.data('autoplay'),
      autoplaySpeed: el.data('autoplayspeed'),
      fade: el.data('fade'),
      infinite: el.data('infinite'),
      prevArrow: el.find('[data-slider-arrow-left]').removeClass('hidden'),
      nextArrow: el.find('[data-slider-arrow-right]').removeClass('hidden'),
      responsive: [
        {
          breakpoint: 992,
          settings: {
            dots: false
          }
        }
      ]
    });

  });  
  
})(jQuery);