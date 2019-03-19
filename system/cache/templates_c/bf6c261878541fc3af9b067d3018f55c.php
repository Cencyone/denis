<?php $login = $CI->dx_auth->is_logged_in() ? '' : '?wishlist='.$varId?>

<?php if($type == 'button'): ?>

  <!-- Button link, used in catalog page -->
  <?php if($class != 'btn inWL'): ?>
    <a class="btn-white" href="<?php if(isset($href)){ echo $href; } ?><?php if(isset($login)){ echo $login; } ?>" data-modal rel="nofollow">
      <i class="btn-white__ico btn-white__ico--wishlist">
        <svg class="svg-icon">
          <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__heart"></use>
        </svg>
      </i>
    </a>
  <?php else:?>
    <a class="btn-white btn-white--active" href="<?php echo site_url ('wishlist'); ?>" rel="nofollow">
      <i class="btn-white__ico btn-white__ico--wishlist">
        <svg class="svg-icon">
          <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__heart"></use>
        </svg>
      </i>
    </a>
  <?php endif; ?>

<?php else:?>
  <div class="product-actions__ico product-actions__ico--wishlist">
    <svg class="svg-icon">
      <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__heart"></use>
    </svg>
  </div>
  <!-- Text link, used in product page -->
  <?php if($class != 'btn inWL'): ?>
    <a class="product-actions__link" href="<?php if(isset($href)){ echo $href; } ?><?php if(isset($login)){ echo $login; } ?>" data-modal rel="nofollow"><?php echo tlang ('Add to Wishlist'); ?></a>
  <?php else:?>
    <a class="product-actions__link product-actions__link--open" href="<?php echo site_url ('wishlist'); ?>"
       rel="nofollow"><?php echo tlang ('Open in Wishlist'); ?></a>
  <?php endif; ?>

<?php endif; ?>
<?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903758; //templates/unishop/wishlist/button.tpl ?>