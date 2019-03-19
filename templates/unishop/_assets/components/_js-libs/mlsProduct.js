(function ($) {
  /**
   *
   * @type {{loadCartButton: jQuery.mshProduct.loadCartButton, editCartButtons: jQuery.mshProduct.editCartButtons}}
   */
  $.mshProduct = {
    loadCartButton: function (variant) {
      var product = variant.closest('[data-product-scope]');
      var variantId = variant.attr('data-product-variant--id');
      var buttonsForm = product.find('[data-product-button--form]');
      var isVariantInCart = Number(variant.attr('data-product-variant--in-cart'));
      var addToCartButton = product.find('[data-product-button--add]');
      var quantityField = product.find('[data-product-button--quantity]');
      var viewOrderButton = product.find('[data-product-button--view]');

      //Change variant_id for "Add to cart" button
      buttonsForm.attr('data-product-button--variant', variantId);

      //"Add to cart" and "Oder view" buttons toggle
      if (isVariantInCart) {
        addToCartButton.add(quantityField).addClass('hidden');
        viewOrderButton.removeClass('hidden');
      } else {
        addToCartButton.add(quantityField).removeClass('hidden');
        viewOrderButton.addClass('hidden');
      }
    },
    editCartButtons: function (variantId, mod) {
      var productVariant = $('[data-product-variant--id="' + variantId + '"]');
      var buttonVariantId = $('[data-product-button--variant="' + variantId + '"]').attr('data-product-button--variant');

      if (productVariant.size() > 0) {
        // Product has variants
        productVariant.each(function () {
          var productVariantId = $(this).attr('data-product-variant--id');
          //change state in cart for variant
          $(this).attr('data-product-variant--in-cart', mod);
          //change "Add to car" button in all same products on page with same active variant
          if ($(this).is(':checked') && (productVariantId === buttonVariantId)) {
            $.mshProduct.loadCartButton($(this));
            //$('[data-product-button--variant='+productVariantId+']').each(function(){
            //  $(this).find('[data-product-button-item]').toggleClass('hidden');
            //});
          }
        });
      } else {
        // Product has no variants
        $('[data-product-button--variant=' + variantId + ']').find('[data-product-button-item]').toggleClass('hidden');
      }

    },
    getVariant: function (selector) {
      var variant;

      //Linking to product variant depending on element type
      switch (selector.attr('data-product-variant')) {
        case 'select' :
          variant = selector.find('option:checked');
          break;
        default :
          variant = selector;
      }

      return variant;
    }
  }
})(jQuery);