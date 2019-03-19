<div class="cart-summary {if $parent_type == 'modal'}cart-summary--in-modal{/if}">

  <div class="cart-summary__items">

    {foreach $items as $item}

      <!-- url for item delete button depending on if it's simple product or kit -->
      {$loc_delete_url = $item->instance == 'ShopKit' ? "removeKit" : "removeProductByVariantId"}

      <!-- url for change quantity depending on if it's simple product or kit -->
      {$loc_quantity_url = $item->instance == 'ShopKit' ? "setQuantityKitById" : "setQuantityProductByVariantId"}
      <div class="cart-summary__row">

        <!-- Delete kit of product -->
        <div class="cart-summary__cell cart-summary__cell--delete">
          <div class="cart-summary__delete">
            <a class="cart-summary__delete-icon" href="{media_url('shop/cart/'. $loc_delete_url .'/'.$item->getId())}"
               title="{tlang('Delete')}"
               data-cart-summary--delete
               data-cart-summary--item-id="{echo $item->getId()}"
               data-cart-summary--href="{media_url('shop/cart/api/'. $loc_delete_url .'/'.$item->getId())}">
              <svg class="svg-icon">
                <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__delete"></use>
              </svg>
            </a>
          </div>
        </div>

        <!-- Product kit -->
        {if $item->instance == 'ShopKit'}
          <div class="cart-summary__cell">
            {foreach $item->items as $kit}
              <div class="cart-summary__product cart-summary__product--kit">
                {view('shop/includes/cart/cart_product.tpl', [
                'product_url' => $kit->getSProducts()->getRouteUrl(),
                'product_image' => $kit->getSmallPhoto(),
                'product_title' => $kit->getSProducts()->getName(),
                'variant_title' => $kit->getName(),
                'product_brand' => $kit->getSProducts()->getBrand()
                ])}
              </div>
            {/foreach}
          </div>
          <!-- Simple Product -->
        {else:}
          <div class="cart-summary__cell">
            <div class="cart-summary__product">
              {view('shop/includes/cart/cart_product.tpl', [
              'product_url' => $item->getSProducts()->getRouteUrl(),
              'product_image' => $item->getSmallPhoto(),
              'product_title' => $item->getSProducts()->getName(),
              'variant_title' => $item->getName(),
              'product_brand' => $item->getSProducts()->getBrand(),
              'product_model' => $item->getSProducts(),
              'product_variant' => $item->model,
              'product_quantity' => $item->getQuantity()
              ])}
            </div>
          </div>
        {/if}
        <!-- END Including products -->


        <!-- Quantity of product -->
        <div class="cart-summary__cell">
          <form class="cart-summary__quantity" action="{shop_url('cart/'. $loc_quantity_url .'/' . $item->getId())}"
                method="get"
                data-cart-summary--quantity
                data-cart-summary--href="{shop_url('cart/api/'. $loc_quantity_url .'/' . $item->getId())}">

            {view('includes/forms/input-quantity.tpl', [
            'parent_name' => 'quantity',
            'parent_value' => $item->getQuantity(),
            'parent_cart' => true
            ])}

            {if $_POST['deliveryMethodId']}
              <input type="hidden" name="deliveryMethodId" value="{$_POST['deliveryMethodId']}">
            {/if}

          </form>
        </div>


        <!-- Product Price -->
        <div class="cart-summary__cell">
          <div class="cart-summary__price">

            <div class="cart-price">
              <div class="cart-price__main cart-price__main--small">
                {echo emmet_money($item->getFinalPrice() * $item->getQuantity(),'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
              </div>
            </div>

          </div>
        </div>


      </div>
      <!-- /.__row -->
    {/foreach}
  </div><!-- /.__items -->

  <!-- Gift coupon -->
  {view('shop/includes/cart/cart_total.tpl', [
  'parent_type' => $parent_type,
  'parent_coupon' => $parent_coupon,
  'model' => $cart
  ])}

</div><!-- /.cart-summary -->