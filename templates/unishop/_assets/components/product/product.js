//Add product to cart via Ajax
$(document).on('submit', '[data-product-button--form]', function (e) {
  e.preventDefault();

  var addToCartForm = $(this);
  var loaderButton = addToCartForm.find('[data-product-button--loader]');
  var variantId = addToCartForm.attr('data-product-button--variant');
  var requestUrl = addToCartForm.attr('data-product-button--path') + '/' + variantId;
  var modalUrl = addToCartForm.attr('data-product-button--modal-url');
  var modalTpl = addToCartForm.attr('data-product-button--modal-template');

  $.ajax({
    url: requestUrl,
    data: addToCartForm.serialize(),
    type: addToCartForm.attr('method'),
    dataType: 'json',
    beforeSend: function () {
      $.mshButtons.addLoader(loaderButton);
    },
    complete: function () {
      $.mshButtons.removeLoader(loaderButton);
    },
    success: function () {

      $.mlsModal({
        src: modalUrl,
        data: {
          template: modalTpl
        },
        transferData: true
      });

      //Change "Add to cart" button to "Oder view" button inside product
      $.mshProduct.editCartButtons(variantId, 1);
    }
  });
});


//Add kit to cart via Ajax
$(document).on('click', '[data-product-kit]', function (e) {
  e.preventDefault();

  var addToCartButton = $(this);
  var kitList = $('[data-product-kit--id="' + addToCartButton.attr('data-product-kit') + '"]');
  var requestUrl = addToCartButton.attr('href');
  var modalUrl = addToCartButton.attr('data-product-kit--modal-url');
  var modalTpl = addToCartButton.attr('data-product-kit--modal-template');

  $.ajax({
    url: requestUrl,
    data: addToCartButton.serialize(),
    type: 'get',
    beforeSend: function () {
      $.mshButtons.addLoader(addToCartButton);
    },
    complete: function () {
      $.mshButtons.removeLoader(addToCartButton);
    },
    success: function () {

      // open cart in modal window
      $.mlsModal({
        src: modalUrl,
        data: {
          template: modalTpl
        },
        transferData: true
      });

      // change "Add to Cart" button
      kitList.toggleClass('hidden');
    }
  });
});

//Change product variants
$(document).on('change', '[data-product-variant]', function (e) {
  e.preventDefault();

  var variant = $.mshProduct.getVariant($(this));
  var product = variant.closest('[data-product-scope]');
  var variantId = variant.attr('data-product-variant--id');
  var mainThumb = product.find('[data-product-photo-main-thumb]').first().closest('[data-product-photo-thumb]');
  var allThumbs = mainThumb.closest('[data-product-photo-scope]').find('[data-product-photo-thumb]');
  var originPrice = variant.attr("data-product-variant--origin-price");
  var additionalPriceList = variant.attr("data-additional-prices");
  var oneClickBtn = product.find('[data-one-click-btn]').first();
  var oneClickScope = product.find('[data-one-click-scope]').first();
  var bonusPoints = variant.attr('data-product-variant-bonus-points');
  var bonusLabel = variant.attr('data-product-variant-bonus-label');

  //Change "Add to cart" button
  $.mshProduct.loadCartButton(variant);

  //Change variant main photo
  product.find('[data-product-photo]').first().attr('src', variant.attr('data-product-variant--photo'));
  //Change variant thumb photo
  product.find('[data-product-photo-main-thumb]').first().attr('src', variant.attr('data-product-variant--thumb'));
  mainThumb.attr('href', variant.attr('data-product-variant--photo-link'));
  allThumbs.removeAttr('data-product-photo-thumb-active');
  mainThumb.attr('data-product-photo-thumb-active', '');
  $.mlsMedia.magnificGalley();
  //add link to large photo
  product.find('[data-product-photo-link]').first().attr('href', variant.attr('data-product-variant--photo-link'));
  //reinit zoom image plugin
  product.find('[data-zoom-image]').first().attr('data-zoom-image', variant.attr('data-product-variant--photo-link'));
  $.mlsMedia.zoomImage();

  //Change variant number
  product.find('[data-product-number]').first().html(variant.attr('data-product-variant--number'));

  //Check variant stock availability, and change button if necessary
  if (variant.attr('data-product-variant--stock') > 0) {
    product.find('[data-product-available]').first().removeClass('hidden');
    product.find('[data-product-unavailable]').first().addClass('hidden');
  } else {
    product.find('[data-product-available]').first().addClass('hidden');
    product.find('[data-product-unavailable]').first().removeClass('hidden');
    //Add variant id to "notify when avaliable" button
    product.find('[data-product-notify]').first().attr('data-product-notify-variant', variantId);
  }

  //Change variant price
  product.find('[data-product-price--main]').first().html(variant.attr('data-product-variant--price'));
  product.find('[data-product-price--coins]').first().html(variant.attr('data-product-variant--coins'));

  //Change discount price
  if (originPrice > 0) product.find('[data-product-price--origin]').first().html(originPrice);

  //Change additional prices
  if (additionalPriceList) {
    var additionalPriceArr = additionalPriceList.split('|');
    var additionalPriceFrameList = product.find('[data-product-price--addition-list]').first();
    additionalPriceFrameList = additionalPriceFrameList.siblings('[data-product-price--addition-list]').andSelf();

    additionalPriceFrameList.each(function (index) {
      var priceFrame = $(this);
      var fullPrice = additionalPriceArr[index].split('^');
      var priceValue = fullPrice[0];
      var priceCoins = fullPrice[1];
      priceFrame.find('[data-product-price--addition-value]').first().html(priceValue);
      priceFrame.find('[data-product-price--addition-coins]').first().html(priceCoins);
    });
  }

  //Set relative variantId into "Buy One click" button
  oneClickBtn.attr('data-one-click-variant', variantId);

  if (variant.attr('data-product-variant--stock') == 0) {
    oneClickScope.addClass('hidden');
  } else {
    oneClickScope.removeClass('hidden');
  }

  //System bonus module points
  if (bonusPoints > 0) {
    product.find('[data-bonus]').first().removeClass('hidden');
    product.find('[data-bonus-points]').first().html(bonusPoints);
    product.find('[data-bonus-label]').first().html(bonusLabel);
  } else {
    product.find('[data-bonus]').first().addClass('hidden');
  }

});


/* Open modal window for in stock reminder page */
$(document).on('click', '[data-product-notify]', function (e) {
  e.preventDefault();

  var notifyLink = $(this);

  $.mlsModal({
    src: $(this).attr('href'),
    data: {
      ProductId: notifyLink.attr('data-product-notify'),
      VariantId: notifyLink.attr('data-product-notify-variant')
    }
  });
});