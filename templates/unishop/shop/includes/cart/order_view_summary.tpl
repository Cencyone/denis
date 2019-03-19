<div class="cart-summary">

  <div class="cart-summary__items">

    <!-- Product BEGIN -->
    {foreach $items as $item}
      <div class="cart-summary__row">

        <div class="cart-summary__cell">
          <div class="cart-summary__product">
            {view('shop/includes/cart/cart_product.tpl', [
            'product_url' => $item->getSProducts()->getRouteUrl(),
            'product_image' => $item->getVariant()->getSmallPhoto(),
            'product_title' => $item->getSProducts()->getName(),
            'variant_title' => $item->variant_name,
            'product_brand' => $item->getSProducts()->getBrand(),
            'product_model' => $item->getSProducts(),
            'product_variant' => $item->getVariant(),
            'product_quantity' => $item->getQuantity()
            ])}
          </div>
        </div>

        <!-- Quantity of product BEGIN -->
        <div class="cart-summary__cell">
          <div class="cart-summary__quantity">
            {echo $item->getQuantity()} {echo SStringHelper::Pluralize($item->getQuantity(), array(tlang('pluralize item 1'), tlang('pluralize item 2'), tlang('pluralize item 3')))}
          </div>
        </div>
        <!-- END Quantity of product -->

        <!-- Product Price BEGIN -->
        <div class="cart-summary__cell hidden-xs">
          <div class="cart-summary__price">

            <div class="cart-price">
              <div class="cart-price__main cart-price__main--small">
                {echo emmet_money($item->getFinalPrice() * $item->getQuantity(), 'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
              </div>
            </div>

          </div>
        </div>
        <!-- END Product Price -->

      </div>
    {/foreach}
    <!-- END Product -->

    <!-- Product kit BEGIN -->
    {foreach $model->getOrderKits() as $kit_main}
      <div class="cart-summary__row">
        <div class="cart-summary__cell">
          <!-- Product kit Main product -->
          <div class="cart-summary__product cart-summary__product--kit">
            {view('shop/includes/cart/cart_product.tpl', [
            'product_url' =>  $kit_main->getKit()->getMainProduct()->getRouteUrl(),
            'product_image' => $kit_main->getKit()->getMainProduct()->firstVariant->getSmallPhoto(),
            'product_title' => $kit_main->getKit()->getMainProduct()->getName(),
            'variant_title' => $kit_main->variant_name,
            'product_brand' => $kit_main->getKit()->getMainProduct()->getBrand()
            ])}
          </div>
          <!-- Product kit discount products -->
          {foreach $kit_main->getKit()->getShopKitProducts() as $kit}
            <div class="cart-summary__product cart-summary__product--kit">
              {view('shop/includes/cart/cart_product.tpl', [
              'product_url' =>  $kit->getSProducts()->firstVariant->getRouteUrl(),
              'product_image' => $kit->getSProducts()->firstVariant->getSmallPhoto(),
              'product_title' => $kit->getSProducts()->getName(),
              'variant_title' => $kit->getSProducts()->firstVariant->getName(),
              'product_brand' => $kit->getSProducts()->getBrand()
              ])}
            </div>
          {/foreach}
        </div>

        <!-- Kit Quantity BEGIN -->
        <div class="cart-summary__cell">
          <div class="cart-summary__quantity">
            {echo $kit_main->getQuantity()} {echo SStringHelper::Pluralize($kit_main->getQuantity(), array(tlang('pluralize kit 1'), tlang('pluralize kit 2'), tlang('pluralize kit 3')))}
          </div>
        </div>
        <!-- END Kit Quantity -->

        <!-- Kit Price BEGIN -->
        <div class="cart-summary__cell hidden-xs">
          <div class="cart-summary__price">

            <div class="cart-price">
              <div class="cart-price__main cart-price__main--small">
                {echo emmet_money(tpl_get_kit_order_price($kit_main) *  $kit_main->getQuantity(), 'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
              </div>
            </div>

          </div>
        </div>
        <!-- END Kit Price -->

      </div>
    {/foreach}
    <!-- END Product kit -->

  </div><!-- /.cart-summary__items -->


  {view('shop/includes/cart/cart_total.tpl', [
  'coupon' => false,
  'parent_type' => 'order',
  ])}


</div><!-- /.cart-summary -->