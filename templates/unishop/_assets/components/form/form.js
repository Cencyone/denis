/* Ajax form submit */
$(document).on('submit', '[data-form-ajax]' , function(e){
	e.preventDefault();

	var target = $(this);

	//frames in which response data will be inputted
	var responseFrame = $('[data-form-ajax="'+target.attr('data-form-ajax')+'"]');
	
	$.ajax({
		url: target.attr('action'),
		type: target.attr('method') ? target.attr('method') : 'get',
		data: target.serialize(),
		beforeSend: function(){
			/* Loader visible before ajax response */
			$.mlsAjax.preloaderShow({
				type: 'frame',
				frame: target
			});
		},
		success: function(data){	
			/* Insert response into document */
			$.mlsAjax.loadResponseFrame(data, responseFrame);

			/* Grab extra data from response frame and insert it into remote places */
			$.mlsAjax.transferData(data);
		}
	});

});


/* Quantity change */
$(document).on('click', '[data-form-quantity-control]', function(e){
	e.preventDefault();

	var scope = $(this).closest('[data-form-quantity]');
	//text field element and value
	var textField = scope.find('[data-form-quantity-field]');
	var currentValue = Number(textField.val().replace(',', '.'));
	// "+" and "-" controls
	var currentControl = $(this).attr('data-form-quantity-control');
	//amount on which value should increase or decrease
	var step = scope.find('[data-form-quantity-step]').attr('data-form-quantity-step');
	var stepValue = (Boolean(step) !== false) ? Number(step.replace(',', '.')) : 1;
	//submit form after click. Used in cart to recount total price
	var recountPrice = scope.attr('data-form-quantity-submit');

	//Calculating result value depending on "+" or "-" button was clicked
	if(currentControl == 'minus'){
		currentValue = (currentValue > stepValue) ? currentValue - stepValue : stepValue;
	}
	if(currentControl == 'plus'){
		currentValue = currentValue >= stepValue ? currentValue + stepValue : stepValue;
	}

	//insert result value into DOM
	textField.val(currentValue);

	//recount price in cart by submitting form
	if(typeof recountPrice != 'undefined'){
		$(this).trigger('submit');
	}


});