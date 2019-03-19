{$in_cart = getAmountInCart('SProducts', $model->firstVariant->getId())}
{$in_stock = $model->firstVariant->getStock()}

<!-- In there are products in stock -->
<div class="product-cut__purchase-wrapper {echo $in_stock > 0 ? '' : 'hidden' }"
     data-product-available>
  <form action="{site_url('/shop/cart/addProductByVariantId/'.$model->firstVariant->getId())}" method="get"
        data-product-button--form
        data-product-button--path="{site_url('/shop/cart/api/addProductByVariantId')}"
        data-product-button--variant="{echo $model->firstVariant->getId()}"
        data-product-button--modal-url="{shop_url('cart')}"
        data-product-button--modal-template="includes/cart/cart_modal">

    <!-- Input product quantity, you wish to buy -->
    <div class="product-cut__purchase-quantity hidden-xs {echo $in_cart > 0 ? 'hidden' : '' }"
         data-product-button--quantity
         data-product-button-item>
      {view('includes/forms/input-quantity.tpl', [
      'parent_name' => 'quantity',
      'parent_value' => 1
      ])}
    </div>

    <!-- Add to cart button -->
    <div class="product-cut__purchase-btn {echo $in_cart > 0 ? 'hidden' : '' }"
         data-product-button--add
         data-product-button-item>
      <button class="btn btn-primary" type="submit"
              data-product-button--loader>
        <svg class="svg-icon svg-icon--in-btn" aria-hidden="true">
          <use xlink:href="#svg-icon__shopping-cart"></use>
        </svg>
        <span>{tlang('Add to Cart')}</span>
        <svg class="svg-icon svg-icon--in-btn svg-icon--spinner hidden" aria-hidden="true" data-button-loader="loader">
          <use xlink:href="#svg-icon__refresh"></use>
        </svg>
      </button>
    </div>

    <!-- Already in cart button -->
    <div class="product-cut__purchase-btn {echo $in_cart > 0 ? '' : 'hidden' }"
         data-product-button--view
         data-product-button-item>
      <a class="btn btn-default" href="{shop_url('cart')}"
         data-modal="includes/cart/cart_modal">
        <svg class="svg-icon svg-icon--in-btn" aria-hidden="true">
          <use xlink:href="#svg-icon__shopping-cart"></use>
        </svg>
        <span>{tlang('View in Cart')}</span>
      </a>
    </div>

    <input type="hidden" name="redirect" value="cart">
    {form_csrf()}
  </form>
</div>

<!-- No items available -->
<div class="product-cut__not-available {echo $in_stock > 0 ? 'hidden' : '' }"
     data-product-unavailable>
  <div class="product-cut__not-available-info">
    {tlang('Not available')}
  </div>
  <div class="product-cut__not-available-notify">
    <a class="product-cut__not-available-link" href="{shop_url('ajax/getNotifyingRequest')}"
       data-product-notify="{echo $model->getId()}"
       data-product-notify-variant="{echo $model->firstVariant->getId()}"
       rel="nofollow"
    >
      {tlang('Notify when available')}
    </a>
  </div>
</div>