<div class="user-panel">

  <!-- User wishlist items -->
  <div class="user-panel__item" data-ajax-inject="wishlist-total">
    {view('wishlist/wishlist_total.tpl')}
  </div>

  <!-- User compare items -->
  <div class="user-panel__item">
    {view('shop/includes/compare/compare_total.tpl')}
  </div>

  <!-- User profile and auth menu -->
  {view('includes/header_profile.tpl')}

  {widget("languages")}

</div>