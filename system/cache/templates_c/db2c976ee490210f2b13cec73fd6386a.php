<section class="widget-primary" data-slider="widget-primary">
  <div class="widget-primary__title"><?php if(isset($parent_title)){ echo $parent_title; } ?></div>
  <div class="widget-primary__inner">
    <div class="row row--ib row--vindent-m" data-slider-slides="1,2,3,6">
      <?php if(is_true_array($parent_products)){ foreach ($parent_products as $product){ ?>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2" data-slider-slide>
          <?php view('shop/includes/product/product_cut.tpl', [
          'model' => $product
          ])?>
        </div>
      <?php }} ?>
    </div>
  </div>
  <div class="widget-primary__arrow widget-primary__arrow--left hidden" data-slider-arrow-left>
    <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-big-left"></use></svg>
  </div>
  <div class="widget-primary__arrow widget-primary__arrow--right hidden" data-slider-arrow-right>
    <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-big-right"></use></svg>
  </div>
</section><?php $mabilis_ttl=1552752298; $mabilis_last_modified=1491903770; ///home/user2263/public_html/templates/unishop/widgets/includes/widget_primary.tpl ?>