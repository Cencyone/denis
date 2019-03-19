{$in_cart = getAmountInCart('SProducts', $model->firstVariant->getId())}
{$in_stock = $model->firstVariant->getStock()}
<div class="product-buy">
  <!-- Items in stock -->
  <div class="product-buy__available {$parent_modifier} {echo $in_stock > 0 ? '' : 'hidden' }"
       data-product-available>
    <form action="{site_url('/shop/cart/addProductByVariantId/'.$model->firstVariant->getId())}" method="get"
          data-product-button--form
          data-product-button--path="{site_url('/shop/cart/api/addProductByVariantId')}"
          data-product-button--variant="{echo $model->firstVariant->getId()}"
          data-product-button--modal-url="{shop_url('cart')}"
          data-product-button--modal-template="includes/cart/cart_modal">

      <!-- Input product quantity, you wish to buy -->
      {if $parent_quantity}
        <div class="product-buy__quantity {echo $in_cart > 0 ? 'hidden' : '' }"
             data-product-button--quantity
             data-product-button-item>

          {view('includes/forms/input-quantity.tpl', [
          'parent_name' => 'quantity',
          'parent_value' => 1,
          'parent_mod_class' => 'form-input--product-base'
          ])}
        </div>
      {/if}

      <!-- Add to cart button -->
      <div class="product-buy__buttons {echo $in_cart > 0 ? 'hidden' : '' }"
           data-product-button--add
           data-product-button-item>
        <button class="product-buy__btn product-buy__btn--buy" type="submit"
                data-product-button--loader>
          <span>{tlang('Add to Cart')}</span>
          <i class="button--loader hidden"
             data-button-loader="loader"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__refresh"></use></svg></i>
        </button>
      </div>

      <!-- Already in cart button -->
      <div class="product-buy__buttons {echo $in_cart > 0 ? '' : 'hidden' }"
           data-product-button--view
           data-product-button-item>
        <a class="product-buy__btn product-buy__btn--in-cart" href="{shop_url('cart')}"
           data-modal="includes/cart/cart_modal">{tlang('View in Cart')}</a>
      </div>

      <input type="hidden" name="redirect" value="cart">
      {form_csrf()}
    </form>
  </div>

  <!-- No items available -->
  <div class="product-buy__unavailable  {echo $in_stock > 0 ? 'hidden' : '' }"
       data-product-unavailable>
    <div class="product-buy__unavailable-info">
      {tlang('Not available')}
    </div>
    <div class="product-buy__unavailable-notify">
      <a class="product-buy__unavailable-link" href="{shop_url('ajax/getNotifyingRequest')}"
         data-product-notify="{echo $model->getId()}"
         data-product-notify-variant="{echo $model->firstVariant->getId()}"
         rel="nofollow"
      >
        {tlang('Notify when available')}
      </a>
    </div>
  </div>

</div>
