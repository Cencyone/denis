;(function($){

	$.mlsWishList = {
		
		moveListValidation: function(scope){

			var radioFieldList = scope.find('input[type="radio"]');
			var radioFieldNew = scope.find('[data-wishlist-new-radio]');
			var textFieldNew = scope.find('[data-wishlist-new-input]');

			var result = false;

			radioFieldList.each(function(){
				
				var radioField = $(this);

				if(radioField.not('[data-wishlist-new-radio]').is(':checked')){
					result = true;
				}else{
					if(radioFieldNew.is(':checked') && $.trim(textFieldNew.val()) != ""){
						result = true;
					}
				}

			});

			return result;
		}		
	}
})(jQuery);