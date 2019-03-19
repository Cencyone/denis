var mlsMegamenu = (function () {

  /* Activate callback function on resize event, but only if resize has been stopped */
  var _lazyResize = function (action, delay) {
    var resizeID;
    window.addEventListener("resize", function () {
      clearTimeout(resizeID);
      resizeID = setTimeout(action, delay);
    });
  };

  /* Find amount of coll in menu */
  var _findColsAmount = function (items) {
    return Array.prototype.reduce.call(items, function (count, item) {
      var value = item.dataset.megamenuCollItem;
      return (value > count) ? value : count;
    }, 1);
  };

  /* Create empty cols */
  var _createEmptyCols = function (coll, amount) {
    var emptyCols = [];
    for (var i = 2; i <= amount; i++) {
      emptyCols[i] = coll.cloneNode(false);
      emptyCols[i].dataset.megamenuColl = i;
    }
    return emptyCols;
  };

  /* Insert items into relative columns */
  var _moveItemsIntoCols = function (items, cols) {
    Array.prototype.forEach.call(items, function (item) {
      if (item.dataset.megamenuCollItem > 1) {
        cols[item.dataset.megamenuCollItem].appendChild(item);
      }
    });
  };

  /* Not allow sub menu go beyond parent menu container */
  var _fitHorizontal = function (selectors) {
    var menuContainer = document.querySelector(selectors.scope);
    var menuItems = menuContainer.querySelectorAll(selectors.items);
    var menuContainerWidth = menuContainer.offsetWidth;
    var menuPosition = menuContainer.getBoundingClientRect();

    Array.prototype.forEach.call(menuItems, function (item) {

      /* Reset menu item styles to default */
      item.style.left = '0';
      item.querySelector(selectors.wrap).dataset.megamenuWrap = 'false';

      var itemPosition = item.getBoundingClientRect();

      /* move menu item to the left if it go beyond the container */
      if (itemPosition.right > menuPosition.right) {
        item.style.left = '-' + (itemPosition.right - menuPosition.right) + 'px';

        /* move menu items to next row if item width exceeds container */
        if (item.offsetWidth > menuContainerWidth) {
          item.style.left = '-' + (itemPosition.left - menuPosition.left) + 'px';
          item.style.minWidth = menuContainerWidth + 'px';
          item.querySelector(selectors.wrap).dataset.megamenuWrap = 'true';
        }
      }
    });
  };

  /* Move menu items in columns */
  var _renderCols = function () {

    var subMenus = document.querySelectorAll('[data-megamenu-item]');

    /* Iterate each sub menu */
    Array.prototype.forEach.call(subMenus, function (menuItem) {
      var colsWrapper = menuItem.querySelector('[data-megamenu-wrap]');
      var coll = menuItem.querySelector('[data-megamenu-coll]');
      var collItems = menuItem.querySelectorAll('[data-megamenu-coll-item]');

      /* Find how much columns is needed */
      var collNum = _findColsAmount(collItems);

      /* Exit if we have only one column */
      if (collNum <= 1)
        return;

      /* Create empty cols */
      var emptyColNodes = _createEmptyCols(coll, collNum);

      /* Insert items into relative columns */
      _moveItemsIntoCols(collItems, emptyColNodes);

      /* Add cols with items into DOM */
      emptyColNodes.forEach(function (item) {
        colsWrapper.appendChild(item);
      });

    });

  };

  /* Public methods */
  return {
    renderCols: function () {
      _renderCols();
    },
    fitHorizontal: function (selectors) {
      /* Initial menu loading */
      _fitHorizontal(selectors);
      /* Reloading menu while window resizing */
      _lazyResize(function () {
        return _fitHorizontal(selectors);
      }, 500);
    }
  }

})();