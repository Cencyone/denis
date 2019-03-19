;(function($){

  $.mlsCart = {
    loadSummaryJson: function(target, settings){

      //request url
      var href = target.attr('data-cart-summary--href');

      //response scope
      var scope = target.closest('[data-cart-summary]');      

      // html data resposne url, loading on second request
      var cartUrl = scope.attr('data-cart-summary--url');

      // html data resposne template, loading on second request
      var cartTpl = scope.attr('data-cart-summary--tpl');

      //additional cart page request
      var cartFrame = $('[data-cart-summary]');
      var modalCartFrame = $('[data-cart-summary="modal"]');
      var pageCartFrame = $('[data-cart-summary="page"]');

      $.ajax({
        url: href,
        type: target.attr('method') ? target.attr('method') : 'get',
        data: target.serialize(),
        dataType: 'json',
        beforeSend: function(){
          /* Submit button ico loader */
          $.mlsAjax.preloaderShow({
            type: 'frame',
            frame: cartFrame
          });
        },
        success: function(){
          
          // If modal cart has changed on cart page, reloading cart summary on this page
          if(pageCartFrame.size() > 0 && modalCartFrame.size() > 0){

            cartFrame = modalCartFrame;
            
            $.ajax({
              url: cartUrl,
              success: function(data){
                /* Insert response into document */
                $.mlsAjax.loadResponseFrame(data, pageCartFrame);
              }
            });
          }

          //Target frame html rasponse
          $.ajax({
            url: cartUrl,
            data: {
              template: cartTpl
            },
            success: function(data){

              // Insert response into document
              if(settings.loadFrame){                
                $.mlsAjax.loadResponseFrame(data, cartFrame);
              }

              // Grab extra data from response frame and insert it into remote places
              if(settings.transferData){
                $.mlsAjax.transferData(data);
              }

              // Change "Oder view" button to "Add to cart" button inside product
              if(settings.toggleAddToCartButton){
                $.mshProduct.editCartButtons(settings.variantId, 0);
              }

              // Change kit "add to cart" button
              if(settings.toggleKitButton){
                settings.kitList.toggleClass('hidden');
              }


            }
          });          

        }
      });
    }
  }

})(jQuery);