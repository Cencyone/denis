<?php $state = in_array($model->getId(), $CI->session->userdata('shopForCompare')) ?>

<div data-compare-scope="add_to">

  <!-- Button link, used in catalog page -->
  <?php if($type == 'button'): ?>

    <!-- Item isn't in compare list -->
    <button class="btn-white <?php echo $state?'hidden':''?>" type="button"
       data-compare-add="<?php echo shop_url ('compare_api/add/'.$model->getId()); ?>" rel="nofollow">
      <i class="btn-white__ico btn-white__ico--compare">
        <svg class="svg-icon">
          <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__compare"></use>
        </svg>
      </i>
    </button>
    <!-- Item already is in compare list -->
    <a class="btn-white btn-white--active <?php echo $state?'':'hidden'?>" href="<?php echo shop_url ('compare'); ?>" data-compare-open
       rel="nofollow">
      <i class="btn-white__ico btn-white__ico--compare">
        <svg class="svg-icon">
          <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__compare"></use>
        </svg>
      </i>
    </a>
    <!-- Text link, used in product page -->
  <?php else:?>
    <div class="product-actions__ico product-actions__ico--compare">
      <svg class="svg-icon">
        <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__compare"></use>
      </svg>
    </div>
    <!-- Item isn't in compare list -->
    <button class="product-actions__link <?php echo $state?'hidden':''?>" type="button"
       data-compare-add="<?php echo shop_url ('compare_api/add/'.$model->getId()); ?>" data-loader="<?php echo tlang ('Loading...'); ?>" rel="nofollow">
      <?php echo tlang ('Add to compare'); ?>
    </button>
    <!-- Item already is in compare list -->
    <a class="product-actions__link product-actions__link--open <?php echo $state?'':'hidden'?>" href="<?php echo shop_url ('compare'); ?>"
       data-compare-open rel="nofollow">
      <?php echo tlang ('Open in compare list'); ?>
    </a>
  <?php endif; ?>
</div><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/compare/compare_button.tpl ?>