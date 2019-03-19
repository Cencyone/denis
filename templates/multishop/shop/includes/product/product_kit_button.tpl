{$in_cart = getAmountInCart('ShopKit', $model->getId())}

<!-- Add to cart button -->
<a class="btn btn-primary {echo $in_cart > 0 ? 'hidden' : '' }" href="{shop_url('cart/api/addKit/'.$model->getId())}"
   data-product-kit="{echo $model->getId()}"
   data-product-kit--id="{echo $model->getId()}"
   data-product-kit--modal-url="{shop_url('cart')}"
   data-product-kit--modal-template="includes/cart/cart_modal"
>
    <span>{tlang('Add all to cart')}</span>
    <svg class="svg-icon svg-icon--in-btn svg-icon--spinner hidden" data-button-loader="loader">
        <use xlink:href="#svg-icon__refresh"></use>
    </svg>
</a>


<!-- Already in cart button -->
<a class="btn btn-default {echo $in_cart > 0 ? '' : 'hidden' }" href="{shop_url('cart')}"
   data-modal="includes/cart/cart_modal"
   data-product-kit--id="{echo $model->getId()}"
>
    <svg class="svg-icon svg-icon--in-btn"><use xlink:href="#svg-icon__shopping-cart"></use></svg>
    <span>{tlang('View in Cart')}</span>
</a>