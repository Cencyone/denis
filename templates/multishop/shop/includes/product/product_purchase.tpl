{$in_cart = getAmountInCart('SProducts', $model->firstVariant->getId())}
{$in_stock = $model->firstVariant->getStock()}

<!-- Items in stock -->
<div class="product-intro__purchase-wrapper {echo $in_stock > 0 ? '' : 'hidden' }"
     data-product-available>
  <form action="{site_url('/shop/cart/addProductByVariantId/'.$model->firstVariant->getId())}"
        method="get"
        data-product-button--form
        data-product-button--path="{site_url('/shop/cart/api/addProductByVariantId')}"
        data-product-button--variant="{echo $model->firstVariant->getId()}"
        data-product-button--modal-url="{shop_url('cart')}"
        data-product-button--modal-template="includes/cart/cart_modal">

    <!-- Input product quantity, you wish to buy -->
    <div class="product-intro__purchase-quantity {echo $in_cart > 0 ? 'hidden' : '' }"
         data-product-button--quantity
         data-product-button-item>

      {view('includes/forms/input-quantity.tpl', [
      'parent_name' => 'quantity',
      'parent_value' => 1,
      'parent_mod_class' => 'form-input--product-base'
      ])}
    </div>

    <!-- Add to cart button -->
    <div class="product-intro__purchase-btn {echo $in_cart > 0 ? 'hidden' : '' }"
         data-product-button--add
         data-product-button-item>
      <button class="btn btn-primary btn-lg"
              type="submit"
              data-product-button--loader>
        <svg class="svg-icon svg-icon--in-big-btn"><use xlink:href="#svg-icon__shopping-cart"></use></svg>
        <span>{tlang('Add to Cart')}</span>
        <svg class="svg-icon svg-icon--in-big-btn svg-icon--spinner hidden" data-button-loader="loader"><use xlink:href="#svg-icon__refresh"></use></svg>
      </button>
    </div>

    <!-- Already in cart button -->
    <div class="product-intro__purchase-btn {echo $in_cart > 0 ? '' : 'hidden' }"
         data-product-button--view
         data-product-button-item>
      <a class="btn btn-default btn-lg"
         href="{shop_url('cart')}"
         data-modal="includes/cart/cart_modal">
        <svg class="svg-icon svg-icon--in-big-btn"><use xlink:href="#svg-icon__shopping-cart"></use></svg>
        <span>{tlang('View in Cart')}</span>
      </a>
    </div>

    <input type="hidden"
           name="redirect"
           value="cart">
    {form_csrf()}
  </form>
</div>

<!-- No items available -->
<div class="product-intro__purchase-not-available  {echo $in_stock > 0 ? 'hidden' : '' }"
     data-product-unavailable>
  <div class="product-intro__purchase-not-available-info">
    {tlang('Not available')}
  </div>
  <div class="product-intro__purchase-not-available-notify">
    <a class="product-intro__purchase-not-available-btn"
       href="{shop_url('ajax/getNotifyingRequest')}"
       data-product-notify="{echo $model->getId()}"
       data-product-notify-variant="{echo $model->firstVariant->getId()}"
       rel="nofollow"
    >
      {tlang('Notify when available')}
    </a>
  </div>
</div>
