$(document).on('submit', '[data-profile-ajax]' , function(e){
	e.preventDefault();

	var target = $(this);
	var responseFrame = $('[data-profile-ajax~="'+target.attr('data-profile-ajax')+'"]');
	
	$.ajax({
		url: target.attr('action'),
		type: target.attr('method') ? target.attr('method') : 'get',
		data: target.serialize(),
		beforeSend: function(){
			/* Submit button ico loader */
			$.mlsAjax.preloaderShow({
				type: 'frame',
				frame: responseFrame
			});
		},
		success: function(data){

			/* Insert response into document */
			$.mlsAjax.loadResponseFrame(data, responseFrame);

			/* Grab extra data from response frame and insert it into remote places */
			$.mlsAjax.transferData(data);

			var loginingInButton = responseFrame.find('[data-profile-button]');

			/* If authentification was successful show loading button and redierct to current page */
			if(loginingInButton.size() > 0){
				$.mshButtons.addLoader(loginingInButton);
				location.assign(location.href);
			}

		}
	});

});