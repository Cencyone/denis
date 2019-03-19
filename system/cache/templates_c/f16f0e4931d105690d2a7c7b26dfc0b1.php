<div class="cart-header">
  <div class="cart-header__aside">
    <a class="cart-header__ico <?php if(!$model->getTotalItems()): ?> cart-header__ico--empty <?php endif; ?>" href="<?php echo shop_url ('cart'); ?>" rel="nofollow" data-modal="includes/cart/cart_modal">
      <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__cart"></use></svg>
      <span class="cart-header__badge hidden-lg"><?php echo $model->getTotalItems() ? : 0?></span>
    </a>
  </div>
  <div class="cart-header__inner visible-lg">
    <div class="cart-header__title">
      <a class="cart-header__link <?php if(!$model->getTotalItems()): ?> cart-header__link--empty <?php endif; ?>" href="<?php echo shop_url ('cart'); ?>" rel="nofollow" data-modal="includes/cart/cart_modal"><?php echo tlang ('Cart'); ?></a>
    </div>
    <div class="cart-header__desc">
      <?php if($model->getTotalItems() > 0): ?>
        <?php echo $model->getTotalItems() ? : 0?> - <?php echo str_replace(" ", "", emmet_money($model->getFinalPrice()))?>
      <?php else:?>
        <?php echo tlang ('Empty'); ?>
      <?php endif; ?>
    </div>
  </div>  
</div><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903782; ///home/user2263/public_html/templates/unishop/shop/includes/cart/cart_header.tpl ?>