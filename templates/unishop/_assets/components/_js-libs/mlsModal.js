(function($){

	$.mlsModal = function(options){
    $.magnificPopup.close();
		$.magnificPopup.open({
			items: {
	        src: options.src
	    },
			type: 'ajax',
			ajax: {
				settings: {
					data: options.data
				}
			},
			callbacks: {
				parseAjax: function(mfpResponse) {
					if(options.transferData) {
						$.mlsAjax.transferData(mfpResponse.data);
					}
				}
			},
			showCloseBtn: false,
			modal: false
		});
	}

})(jQuery);


/* Open modal window */
$(document).on('click', '[data-modal]', function(e){
	e.preventDefault();

	$.mlsModal({
		src: $(this).attr('href'),
		data: {
			template: $(this).data('modal')
		}
	});

});


/* Close modal window */
$(document).on('click', '[data-modal-close]', function (e) {
	e.preventDefault();
	$.magnificPopup.close();
});