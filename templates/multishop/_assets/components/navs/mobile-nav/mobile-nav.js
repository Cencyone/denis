;(function($){
	
	//Move mobile menu to the left
	$('[data-mobile-nav-link]', '[data-mobile-nav]').on('click', function(e){
		e.preventDefault();

		var targetLink = $(this);
		var childList = targetLink.closest('[data-mobile-nav-item]').find(' > [data-mobile-nav-list]');
		var parentLists = targetLink.parents('[data-mobile-nav-list]');
		var currentList = targetLink.closest('[data-mobile-nav-list]')

		//make fitst child list visible
		childList.removeClass('hidden');

		//move all parent lists to left
		parentLists.addClass('mobile-nav__list--is-moving');

		$('.page__mobile').scrollTop(0);

	});


	//Move mobile menu to the right
	$('[data-mobile-nav-go-back]', '[data-mobile-nav]').on('click', function(e){
		e.preventDefault();
		
		var targetLink = $(this);
		var parentList = targetLink.closest('[data-mobile-nav-list]').parent().closest('[data-mobile-nav-list]');
		var childLists = parentList.find('[data-mobile-nav-list]');

		//move first parent list to right
		parentList.removeClass('mobile-nav__list--is-moving');

		//hide all children lists
		setTimeout(function(){
			childLists.addClass('hidden');
		}, 300);

	});

})(jQuery);