<div class="star-rating">
  <div class="star-rating__stars">
    <?php $rating = $model->getRating()?>
    <?php for($i = 1; $i <= 5; $i++){?>
      <i class="star-rating__star <?php if($i <= $rating): ?>star-rating__star--active<?php endif; ?>"
         title="<?php echo $model->getRating()?> <?php echo tlang ('out of 5 stars'); ?>">
        <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__star"></use></svg>
      </i>
    <?php } ?>
  </div>
  <div class="star-rating__votes">
    <a class="star-rating__votes-link"
       href="<?php echo site_url ($model->getRouteUrl()); ?>#comments-list"><?php echo tlang ('Reviews'); ?>: <?php echo tpl_product_comments_votes ($model); ?></a>
  </div>
</div><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903784; ///home/user2263/public_html/templates/unishop/shop/includes/product/product_rating.tpl ?>