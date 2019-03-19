$(function() {
	var pushy = $('[ data-page-pushy-mobile]'), //menu css class
		container = $('[ data-page-pushy-container]'), //container css class
		siteOverlay = $('[data-page-pushy-overlay]'), //site overlay
		pushyClass = "page__mobile--js-open", //menu position & menu open class
		containerClass = "page__body--js-pushed", //container open class
		menuBtn = $('[data-page-mobile-btn]'), //css classes to toggle the menu
		menuBtnOpen = 'ico-mobile--open', //css classes to toggle the menu
		menuBtnClose = 'ico-mobile--close'; //css classes to toggle the menu

	function togglePushy(){
		siteOverlay.toggleClass('hidden'); //toggle site overlay
		pushy.toggleClass(pushyClass);
		container.toggleClass(containerClass);
		menuBtn.toggleClass('hidden');
	}

	//Open mobile frame when clicking site mobile button
	menuBtn.click(function() {
		togglePushy();
	});

	//close mobile frame when clicking site overlay
	siteOverlay.click(function(){ 
		togglePushy();
	});

});