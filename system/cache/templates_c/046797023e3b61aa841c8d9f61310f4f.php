<div class="user-panel">

  <!-- User wishlist items -->
  <div class="user-panel__item" data-ajax-inject="wishlist-total">
    <?php echo view ('wishlist/wishlist_total.tpl'); ?>
  </div>

  <!-- User compare items -->
  <div class="user-panel__item">
    <?php echo view ('shop/includes/compare/compare_total.tpl'); ?>
  </div>

  <!-- User profile and auth menu -->
  <?php echo view ('includes/header_profile.tpl'); ?>

  <?php echo widget ("languages"); ?>

</div><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903755; ///home/user2263/public_html/templates/unishop/includes/header_toolbar.tpl ?>