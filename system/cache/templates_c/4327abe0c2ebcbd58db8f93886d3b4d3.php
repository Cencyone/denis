<?php $total = $CI->session->userdata('shopForCompare') ? count($CI->session->userdata('shopForCompare')) : 0;?>

<a class="user-panel__link <?php if(!$total): ?>user-panel__link--empty<?php endif; ?>"
   href="<?php echo shop_url ('compare'); ?>"
   rel="nofollow"
   data-compare-removeclass="user-panel__link--empty">
  <i class="user-panel__ico user-panel__ico--compare">
    <svg class="svg-icon">
      <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__compare"></use>
    </svg>
  </i>
  <?php echo tlang ('Compare'); ?> (<span data-compare-total><?php if(isset($total)){ echo $total; } ?></span>)
</a><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/compare/compare_total.tpl ?>