/* Focusing text field if relative radio is checked */
$(document).on('click', '[data-wishlist-new-input]', function(){
  var radioNew = $(this).closest('[data-wishlist-new-scope]').find('[data-wishlist-new-radio]');
  $(radioNew).trigger('click');
});

$(document).on('click', '[data-wishlist-new-radio]', function(){
  var inputNew = $(this).closest('[data-wishlist-new-scope]').find('[data-wishlist-new-input]');
  $(inputNew).trigger('focus');
});


/* Ajax Add to wishlist and moving between lists */
$(document).on('submit', '[data-wishlist-ajax]' , function(e){
  e.preventDefault();

  var target = $(this);
  var responseFrame = $('[data-wishlist-ajax]');
  var moveButtonLoader = target.find('[data-button-loader="loader"]').closest('[data-wishlist-move-loader]');
  
  $.ajax({
    url: target.attr('action'),
    type: target.attr('method') ? target.attr('method') : 'get',
    data: target.serialize(),
    beforeSend: function(){
    
      if(target.data('wishlist-ajax') == 'move' && $.mlsWishList.moveListValidation(target)){       
        $.mshButtons.addLoader(moveButtonLoader);
      }else{
        /* Submit Loader */
        $.mlsAjax.preloaderShow({
          type: 'frame',
          frame: responseFrame
        });
      }
      
    },
    success: function(data){
      if(target.data('wishlist-ajax') == 'move' && $.mlsWishList.moveListValidation(target)){
        location.assign(location.href);
      }else{
        /* Insert response into document */
        $.mlsAjax.loadResponseFrame(data, responseFrame);

        /* Grab extra data from response frame and insert it into remote places */
        $.mlsAjax.transferData(data);
      }     
    }
  });

});



/* Ajax Edit */
$(document).on('submit', '[data-wishlist-edit]' , function(e){
  e.preventDefault();

  var target = $(this);
  var scope = $('[data-wishlist-edit]');
  var loaderFrame = $('[data-wishlist-edit]');
  var editButtonLoader = scope.find('[data-wishlist-edit--button]');
  var errorFrame = scope.find('[data-wishlist-edit--error]');

  $.ajax({
    url: target.attr('action'),
    type: target.attr('method') ? target.attr('method') : 'get',
    data: target.serialize(),
    dataType: 'json',
    beforeSend: function(){
    
      $.mlsAjax.preloaderShow({
        type: 'frame',
        frame: loaderFrame
      });
      
    },
    success: function(data){
      if(data.answer == 'error'){
        errorFrame.removeClass('hidden').find('[data-wishlist-edit--error-message]').html(data.data[0]);
      }else{
        errorFrame.addClass('hidden');
        $.mshButtons.addLoader(editButtonLoader);
        location.assign(location.href);
      }
    }
  });

});