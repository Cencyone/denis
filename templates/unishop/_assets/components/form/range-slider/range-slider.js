$(document).on('ready', function () {

    //Selectors init
    var form = $('#catalog-form');
    var fieldScope = $('[data-filter]');
    var fieldMin = 'data-filter-price-min';
    var fieldMax = 'data-filter-price-max';
    var sliderScope = 'data-range-slider';

    //Default valued at start
    var initialMinVal = parseFloat(fieldScope.find('[' + fieldMin + ']').attr(fieldMin));
    var initialMaxVal = parseFloat(fieldScope.find('[' + fieldMax + ']').attr(fieldMax));

    //Values after applying filter
    var curMinVal = parseFloat(fieldScope.find('[' + fieldMin + ']').attr('value'));
    var curMaxVal = parseFloat(fieldScope.find('[' + fieldMax + ']').attr('value'));


    //Setting value into form inputs when slider is moving
    $('[' + sliderScope + ']').slider({
        min: initialMinVal,
        max: initialMaxVal,
        values: [curMinVal, curMaxVal],
        range: true,
        slide: function (event, elem) {
            var instantMinVal = elem.values[0];
            var instantMaxVal = elem.values[1];

            fieldScope.find('[' + fieldMin + ']').val(instantMinVal);
            fieldScope.find('[' + fieldMax + ']').val(instantMaxVal);

        },
        change: function () {
            form.trigger('submit');
            fieldScope.find('[' + fieldMin + '], [' + fieldMax + ']').attr('readOnly', true);
        }
    });


    //Setting value slider ranges when form inputs are changing
    fieldScope.find('[' + fieldMin + '], [' + fieldMax + ']').on('change', function () {
        $('[' + sliderScope + ']').slider('values', [
            fieldScope.find('[' + fieldMin + ']').val(),
            fieldScope.find('[' + fieldMax + ']').val()
        ]);
    });

});