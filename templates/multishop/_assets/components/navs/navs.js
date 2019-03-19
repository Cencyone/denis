/*
 * Tree navigation menu
 * Right to left direction if menu doesn't fit to window size
 * */
$('[data-nav-hover-item]').on('mouseenter', function () {

  var dropList = this.querySelectorAll('[data-nav-direction]');
  var windowWidth = document.documentElement.clientWidth;
  var remoteItemPos = windowWidth;

  /* find max right coordinate among all drop-down menus within current hover item */
  for (var i = 0; i < dropList.length; i++) {
    var dropItem = dropList[i];
    dropItem.setAttribute('data-nav-direction', 'ltr');
    var itemPos = dropItem.getBoundingClientRect().right;
    remoteItemPos = itemPos > windowWidth ? itemPos : remoteItemPos;
  }

  /* apply right direction if max right coordinate is bigger then window width */
  if (remoteItemPos > windowWidth) {
    for (var j = 0; j < dropList.length; j++) {
      dropList[j].setAttribute('data-nav-direction', 'rtl');
    }
  }

});


/*
 * Mega menu
 * Make menu fit to container width
 * */
document.addEventListener('DOMContentLoaded', function () {

  if (document.querySelector('[data-megamenu-container]') != null) {
    mlsMegamenu.renderCols();
    mlsMegamenu.fitHorizontal({
      scope: '[data-megamenu-container]',
      items: '[data-megamenu-item]',
      wrap: '[data-megamenu-wrap]'
    });
  }

});