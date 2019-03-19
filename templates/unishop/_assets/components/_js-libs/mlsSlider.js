;(function($){

	$.mlsSlider = {

		getCols: function(slide){
			console.log();
			var breakpoints = [768,992,1200];
			var cols = slide ? slide.split(',') : false;

			if($.isArray(cols)){
				cols.shift();
				if(cols.length > 0){
					return $.map(cols, function(value, index){
						return {
							breakpoint: breakpoints[index],
							settings: {
								slidesToShow: parseInt(value)
							}
						}
					});
				}else{
					return false;
				}	  
			}else{
				return false;
			}
		},
		getFirstCol: function(slide){
			var cols = slide ? slide.split(',') : false;
			if(cols){
				return parseInt(slide.split(',')[0]);
			}else{
				return 2;
			}
		}
	}
	
})(jQuery);