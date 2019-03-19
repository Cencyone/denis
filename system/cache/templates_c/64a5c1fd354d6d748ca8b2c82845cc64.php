<span class="product-photo__labels">
  <?php if($model->getHit()): ?>
    <i class="product-photo__label product-photo__label--hit">
	    <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__hit"></use></svg>
	    <span class="product-photo__label-text"><?php echo tlang ('Hit'); ?></span>
    </i>
  <?php endif; ?>
  <?php if($model->getHot()): ?>
    <i class="product-photo__label product-photo__label--hot">
      <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__new"></use></svg>
      <span class="product-photo__label-text"><?php echo tlang ('New'); ?></span>
    </i>
  <?php endif; ?>
  <?php if($model->getAction()): ?>
    <i class="product-photo__label product-photo__label--action">
      <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__new"></use></svg>
      <span class="product-photo__label-text"><?php echo tlang ('Sale'); ?></span>
    </i>
  <?php endif; ?>
  <?php if($model->firstVariant->getDiscountPercent()): ?>
    <i class="product-photo__label product-photo__label--discount">
			<span class="product-photo__label-text">-<?php echo $model->firstVariant->getDiscountPercent()?>%</span>
			<svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__new"></use></svg>
		</i>
  <?php endif; ?>
</span><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903784; ///home/user2263/public_html/templates/unishop/shop/includes/product/product_labels.tpl ?>