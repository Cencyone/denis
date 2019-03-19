/* Load warehouses on city change event */
;(function ($) {
  $.mlsNovaPoshta = {
    appendWarehouses: function (frameElem, warehouses) {
      var value,
        coordinates,
        item;

      for (var i = 0; i < warehouses.length; i++) {
        item = warehouses[i];
        value = item.Number + '/' + item.CityRef;
        coordinates = item.Latitude + ',' + item.Longitude;

        frameElem.append('<option value="' + value + '" data-nposhta--coords="' + coordinates + '">' + item.Description + '</option>');
      }
    }
  }
})(jQuery);


$(document).on('change', '[data-nposhta--city]', function (e) {
  e.preventDefault();

  var city = $(this);
  var cityId = city.val();
  var warehouseUrl = city.attr('data-nposhta--warehouses-url') + "/" + cityId;
  var delivery = city.closest('[data-cart-delivery--item]');
  var warehouse = delivery.find('[data-nposhta--warehouses]');
  var warehouseList = delivery.find('[data-nposhta--warehouses-select]');
  var clearWarehouseList = function () {
    warehouseList.empty();
    warehouse.addClass('hidden');
  };

  /* Hide warehouses list if no city has been chosen and clear previous city warehouses list */
  if (cityId === '') {
    clearWarehouseList();
    return false;
  }

  /* Get warehouses list of chosen city */
  $.ajax({
    url: warehouseUrl,
    type: 'get',
    dataType: 'json',
    beforeSend: function () {
      $.mlsAjax.preloaderShow({
        type: 'frame',
        frame: delivery
      });
    },
    success: function (data) {
      /* Clear previous city warehouses list and set new */
      warehouseList.empty();
      $.mlsNovaPoshta.appendWarehouses(warehouseList, data);
      warehouse.removeClass('hidden');
    },
    error: function () {
      /* Hide warehouses list if no warehouses in current city */
      clearWarehouseList();
    }
  });

});

/* Show warehouse on google maps */
$(document).on('click', '[data-nposhta--map]', function (e) {
  e.preventDefault();
  var link = $(this);
  var delivery = link.closest('[data-cart-delivery--item]');
  var warehouseList = delivery.find('[data-nposhta--warehouses-select]');
  var coordinates = warehouseList.find('option:selected').attr('data-nposhta--coords');
  var lang = document.documentElement.lang;

  $.magnificPopup.open({
    items: {
      type: 'iframe',
      src: 'https://maps.google.com/maps?q=' + coordinates + '&hl=' + lang
    }
  });

});
