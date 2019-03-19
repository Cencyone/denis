//mlsAjax Linrary
;(function($){

	//register globals
	var loaderClass = 'spinner-circle';

	$.mlsAjax = {
		preloaderShow: function(options){			
			if(options.type == 'frame'){
				options.frame.attr('data-loader-frame', "1").append('<i class="'+loaderClass+'"></i>');
			}
			if(options.type == 'text'){
				options.frame.html(options.frame.data('loader'));
			}
		},

		preloaderHide: function(){
			$('[data-loader-frame]').removeAttr('data-loader-frame').find('.'+loaderClass).remove();
		},

		loadResponseFrame: function(data, frame){
			var filterData = $(data).find(frame.selector).children();
			$(frame).html(filterData);
		},

		transferData: function(data){
			$(data).find('[data-ajax-grab]').each(function(){
				var injectElement = $(this).data('ajax-grab');
				var injectData = $(this).html();
				$('[data-ajax-inject="'+injectElement+'"]').html(injectData);
			})
		}
	}

})(jQuery);