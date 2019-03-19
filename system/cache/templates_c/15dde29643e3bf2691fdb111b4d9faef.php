<article class="product-cut <?php if(tpl_is_product_archived($model)): ?>product-cut--no-overlay<?php endif; ?>"
         data-product-scope>

  <!-- Block visible once page is loaded -->
  <div class="product-cut__main-info">

    <!-- Photo output BEGIN -->
    <div class="product-cut__photo">
      <?php view('shop/includes/product/product_cut_photo.tpl', [
      'model' => $model
      ])?>
    </div>

    <!-- Sales product takes part in via mod_link module -->
    <?php if(array_key_exists('mod_link', $modules)): ?>
      <?php $sales = module('mod_link')->getLinksByProduct($model->getId())?>
      <?php if($sales && $CI->core->core_data['data_type'] != 'page'): ?>
        <div class="product-cut__sale">
          <?php if(is_true_array($sales)){ foreach ($sales as $sale){ ?>
            <div class="product-cut__sale-item"><?php echo $sale->getPageData()['title']?></div>
          <?php }} ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <!-- Rating and reviews BEGIN -->
    <div class="product-cut__rating">
      <?php view('shop/includes/product/product_rating.tpl', [
      'model' => $model
      ])?>
    </div>


    <!-- Title BEGIN -->
    <div class="product-cut__title">
      <a class="product-cut__title-link"
         href="<?php echo site_url ($model->getRouteUrl()); ?>"><?php echo $model->getName()?></a>
    </div>

    <!-- If product is not archived -->
    <?php if(!tpl_is_product_archived($model)): ?>

      <!-- Product price -->
      <div class="product-cut__price">

        <?php view('shop/includes/product/product_price.tpl', [
        'variant' => $model->firstVariant,
        'parent_modifier' => 'product-price--bg'
        ])?>

        <!-- System bonus module -->
        <?php if(array_key_exists('system_bonus', $modules)): ?>
          <div class="product-cut__bonus">
            <?php view('system_bonus/system_bonus_product.tpl', [
            'model' => $model,
            'variant' => $model->firstVariant
            ])?>
          </div>
        <?php endif; ?>

      </div>
    <?php else:?>

      <!-- If archived product -->
      <div class="product-cut__archive">
        <?php echo tlang ('Product has been discontinued'); ?>
      </div>
    <?php endif; ?>

    <!-- Delete item from wishlist -->
    <?php if($parent_wishlist): ?>
      <div class="product-cut__delete">
        <a class="product-cut__delete-icon"
           href="<?php echo site_url ('/wishlist/deleteItem/' .  $parent_wishlist['variant_id']  .'/'.  $parent_wishlist['wish_list_id'] ); ?>"
           title="<?php echo tlang ('Remove from list'); ?>">
          <svg class="svg-icon">
            <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__delete"></use>
          </svg>
        </a>
      </div>
    <?php endif; ?>

    <!-- Move item between wishlists -->
    <?php if($parent_wishlist): ?>
      <div class="product-cut__move">
        <a class="product-cut__move-link"
           href="<?php echo site_url ('/wishlist/renderPopup/'.   $parent_wishlist['variant_id']  .'/'.  $parent_wishlist['wish_list_id']   .'/'.  $user['id'] ); ?>"
           data-modal
        ><?php echo tlang ('Change list'); ?></a>
      </div>
    <?php endif; ?>

    <!-- If archived product -->
    <?php if(!tpl_is_product_archived($model)): ?>

      <!-- Block hidden at once, visible on hover -->
      <div class="product-cut__extra-info">

        <!-- Product variants -->
        <?php $variants = $model->getProductVariants()?>
        <?php if(count($variants) > 1): ?>
          <div class="product-cut__variants">
            <?php view('shop/includes/product/variants/select.tpl', [
            'model' => $model,
            'variants' => $variants
            ])?>
          </div>
        <?php endif; ?>

        <div class="product-cut__actions">

          <!-- Add to cart button -->
          <?php if(!ShopCore::app()->SSettings->useCatalogMode()): ?>
            <div class="product-cut__action-item">
              <?php view('shop/includes/product/product_buy.tpl', [
              'model' => $model,
              'parent_quantity' => false
              ])?>
            </div>
          <?php endif; ?>

          <!-- Wishlist button -->
          <?php if(!$parent_wishlist): ?>
            <div class="product-cut__action-item"
                 data-ajax-inject="wishlist-btn-<?php echo $model->firstVariant->getId()?>">
              <!-- Wishlist buttons. Dont show button on whishlist page -->
              <?php echo module ('wishlist')->renderWLButton($model->firstVariant->getId(), ['type' => 'button']); ?>
            </div>
          <?php endif; ?>

          <!-- "Compare button -->
          <div class="product-cut__action-item">
            <?php echo view ('shop/includes/compare/compare_button.tpl', ['model' => $model, 'type' => 'button']); ?>
          </div>

        </div><!-- /.product-cut__actions -->

      </div>
      <!-- /.product-cut__extra-info -->
    <?php endif; ?>

  </div><!-- /.product-cut__main-info -->
</article><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/product/product_cut.tpl ?>