;(function () {

    var sliders = $('[data-slider="widget-primary"]');

    sliders.each(function () {

        var scope = $(this);
        var slides = $('[data-slider-slides]', scope).attr('data-slider-slides');

        $('[data-slider-slides]', scope).find('[data-slider-slide]').css('float', 'left').end().slick({
            dots: false,
            arrows: true,
            infinite: false,
            adaptiveHeight: true,
            slidesToShow: $.mlsSlider.getFirstCol(slides),
            autoplay: false,
            autoplaySpeed: 3000,
            swipeToSlide: true,
            mobileFirst: true,
            rows: 1,
            prevArrow: $('[data-slider-arrow-left]', scope).removeClass('hidden'),
            nextArrow: $('[data-slider-arrow-right]', scope).removeClass('hidden'),
            responsive: $.mlsSlider.getCols(slides)
        });

    });

})();