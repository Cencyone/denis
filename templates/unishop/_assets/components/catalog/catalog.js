/*
 * Change Catalog View
 */
$(document).on('click', '[data-catalog-view-item]', function (e) {
    var cookieValue = $(this).attr('data-catalog-view-item');

    e.preventDefault();
    document.cookie = 'catalog_view=' + cookieValue + ';path=/';
    window.location.reload();
});


/*
 * Order form onchange
 */
$(document).on('change', '[data-catalog-order-select]', function(){
    $('#catalog-form').submit();
    $('[form="catalog-form"]').attr('disabled', true);
});

/*
 * Per page form onchange
 */
$(document).on('change', '[data-catalog-perpage-select]', function(){
    $('#catalog-form').submit();
    $('[form="catalog-form"]').attr('disabled', true);
});