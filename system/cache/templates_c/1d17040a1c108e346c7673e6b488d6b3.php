<?php $in_cart = getAmountInCart('SProducts', $model->firstVariant->getId())?>
<?php $in_stock = $model->firstVariant->getStock()?>
<div class="product-buy">
  <!-- Items in stock -->
  <div class="product-buy__available <?php if(isset($parent_modifier)){ echo $parent_modifier; } ?> <?php echo $in_stock > 0 ? '' : 'hidden' ?>"
       data-product-available>
    <form action="<?php echo site_url ('/shop/cart/addProductByVariantId/'.$model->firstVariant->getId()); ?>" method="get"
          data-product-button--form
          data-product-button--path="<?php echo site_url ('/shop/cart/api/addProductByVariantId'); ?>"
          data-product-button--variant="<?php echo $model->firstVariant->getId()?>"
          data-product-button--modal-url="<?php echo shop_url ('cart'); ?>"
          data-product-button--modal-template="includes/cart/cart_modal">

      <!-- Input product quantity, you wish to buy -->
      <?php if($parent_quantity): ?>
        <div class="product-buy__quantity <?php echo $in_cart > 0 ? 'hidden' : '' ?>"
             data-product-button--quantity
             data-product-button-item>

          <?php view('includes/forms/input-quantity.tpl', [
          'parent_name' => 'quantity',
          'parent_value' => 1,
          'parent_mod_class' => 'form-input--product-base'
          ])?>
        </div>
      <?php endif; ?>

      <!-- Add to cart button -->
      <div class="product-buy__buttons <?php echo $in_cart > 0 ? 'hidden' : '' ?>"
           data-product-button--add
           data-product-button-item>
        <button class="product-buy__btn product-buy__btn--buy" type="submit"
                data-product-button--loader>
          <span><?php echo tlang ('Add to Cart'); ?></span>
          <i class="button--loader hidden"
             data-button-loader="loader"><svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__refresh"></use></svg></i>
        </button>
      </div>

      <!-- Already in cart button -->
      <div class="product-buy__buttons <?php echo $in_cart > 0 ? '' : 'hidden' ?>"
           data-product-button--view
           data-product-button-item>
        <a class="product-buy__btn product-buy__btn--in-cart" href="<?php echo shop_url ('cart'); ?>"
           data-modal="includes/cart/cart_modal"><?php echo tlang ('View in Cart'); ?></a>
      </div>

      <input type="hidden" name="redirect" value="cart">
      <?php echo form_csrf (); ?>
    </form>
  </div>

  <!-- No items available -->
  <div class="product-buy__unavailable  <?php echo $in_stock > 0 ? 'hidden' : '' ?>"
       data-product-unavailable>
    <div class="product-buy__unavailable-info">
      <?php echo tlang ('Not available'); ?>
    </div>
    <div class="product-buy__unavailable-notify">
      <a class="product-buy__unavailable-link" href="<?php echo shop_url ('ajax/getNotifyingRequest'); ?>"
         data-product-notify="<?php echo $model->getId()?>"
         data-product-notify-variant="<?php echo $model->firstVariant->getId()?>"
         rel="nofollow"
      >
        <?php echo tlang ('Notify when available'); ?>
      </a>
    </div>
  </div>

</div>
<?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/product/product_buy.tpl ?>