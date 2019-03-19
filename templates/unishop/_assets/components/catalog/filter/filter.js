$(document).ready(function () {


  /*
   * Toggle filter visibility on mobile devices
   */
  $(document).on('click', '[data-filter-toggle--btn]', function (e) {
    e.preventDefault();
    $('[data-filter-toggle--filter]').toggleClass('hidden-xs');
    $(this).find('[data-filter-toggle--btn-text]').toggleClass('hidden');
  });


  /*
   * Toggle property valuse visibility if dropDown option in filter setting is true
   */
  $(document).on('click', '[data-filter-drop-handle]', function (e) {
    e.preventDefault();
    $(this).closest('[data-filter-drop-scope]').find('[data-filter-drop-inner]').slideToggle(300);
    $(this).closest('[data-filter-drop-scope]').find('[data-filter-drop-ico]').toggleClass('hidden', 300);
  });


  /*
   * Positioning scroll into the middle of checked value
   * Working only if scroll option in filter setting is true
   */
  $('[data-filter-scroll]').each(function () {
    var frame = $(this);
    var fieldActive = frame.find('[data-filter-control]:checked').first();

    if (fieldActive.size() > 0) {
      var fieldActivePos = fieldActive.offset().top - frame.offset().top;
      frame.scrollTop(fieldActivePos - (frame.height() / 2 - fieldActive.height()));
    }
  });


  /*
   * Submit Form on Change event
   */
  $(document).on('change', '[data-filter-control]', function () {
    $('#catalog-form').submit();
    $('[form="catalog-form"]').attr('disabled', true);
  });

  /*
   * Prevent reference via link and continue to change checkbox. Link should be for SEO reasons
   */
  $('[data-filter-link]').on('click', function (e) {
    e.preventDefault();
    /* Trigger change event on filter option */
    $(this).closest('[data-filter-label]').trigger('click');
  });

  /*
   * Filter form submit handler
   */
  $(document).on('submit', '#catalog-form', function () {
    var filter = $('[data-filter]');
    var defaultFields = $('[data-catalog-default]');
    var form = $(this);
    var minPrice = filter.find('[data-filter-price-min]');
    var maxPrice = filter.find('[data-filter-price-max]');

    defaultFields.attr('disabled', true);

    if (minPrice.attr('data-filter-price-min') == minPrice.val()) {
      minPrice.attr('disabled', true);
    }

    if (maxPrice.attr('data-filter-price-max') == maxPrice.val()) {
      maxPrice.attr('disabled', true);
    }

    /* Make Seo-friendly url for catalog filter when SEO-expert module is used */
    if ($.imcSeoUrl) {
      $.imcSeoUrl.add({
        fields: filter.find('[data-filter-control]:checked'),
        catUrl: filter.attr('data-filter-category'),
        form: form
      });
    }

    /* If url doesn't contain query string use direct url instead of form submit
     * The reason is to prevent question mark output in the end of url
     */
    if (form.serialize() == '') {
      location.assign(form.attr('action'));
      return false;
    }

  });


  /*
   * Remove checked filters
   */
  $(document).on('click', '[data-filter-result]', function (e) {
    e.preventDefault();

    var removeBtn = $(this);
    var filter = $('[data-filter]');
    var fields = '[data-filter-control="brand-' + removeBtn.attr('data-filter-result-value') + '"], [data-filter-control="property-' + removeBtn.attr('data-filter-result-value') + '"]';
    var minPrice, maxPrice;

    //Remove Checkbox Brand and Properties filters
    if (removeBtn.attr('data-filter-result') == 'checkbox') {
      //Trigger submit form on filter via unchecking target element
      filter.find(fields).prop('checked', false).trigger('change');
    }

    //Remove Price filter
    if (removeBtn.attr('data-filter-result') == 'price') {
      minPrice = filter.find('[data-filter-price-min]').attr('data-filter-price-min');
      maxPrice = filter.find('[data-filter-price-max]').attr('data-filter-price-max');
      filter.find('[data-filter-price-min]').val(minPrice).end().find('[data-filter-price-max]').val(maxPrice);
      $('#catalog-form').submit();
    }

  });

});