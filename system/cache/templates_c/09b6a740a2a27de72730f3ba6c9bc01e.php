<?php $total = module('wishlist')->getUserWishListItemsCount($CI->dx_auth->get_user_id())?>

<a class="user-panel__link <?php if(!$total): ?>user-panel__link--empty<?php endif; ?>" href="<?php echo site_url ('wishlist'); ?>" rel="nofollow">
  <i class="user-panel__ico user-panel__ico--wishlist">
    <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__heart"></use></svg>
  </i>
  <?php echo tlang ('Wishlist'); ?> (<?php if(isset($total)){ echo $total; } ?>)
</a><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903758; ///home/user2263/public_html/templates/unishop/wishlist/wishlist_total.tpl ?>