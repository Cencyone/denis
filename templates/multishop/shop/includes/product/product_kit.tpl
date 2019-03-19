<div class="product-kit">
  <div class="product-kit__header">
    <div class="product-kit__title">
      {tlang('Frequently bought together')}
    </div>
  </div>
  <div class="product-kit__inner">

    <!-- List of kist -->
    {foreach $model->getShopKits() as $kit}
    <div class="product-kit__item">


      <!--
      **************************************
      Kit products box BEGIN
      **************************************
      -->
      <div class="product-kit__products">
      
        <!-- All products in kit -->
        <div class="row row--ib row--vindent-s-sm">
        
          <!-- Main Product -->
          <div class="product-kit__product col-xs-12 col-sm-4 col-md-4 col-lg-4">
            {view('shop/includes/product/product_kit_item_main.tpl', array(
              'model' => $kit->getSProducts()
            ))}
          </div>

          <!-- Kit products -->
          {foreach $kit->getShopKitProducts() as $kit_product}
          <div class="product-kit__product col-xs-12 col-sm-4 col-md-4 col-lg-4">
            {view('shop/includes/product/product_kit_item_add.tpl', array(
              'model' => $kit_product
            ))}
          </div>
          {/foreach}

        </div><!-- /.row -->

      </div><!-- /.product-kit__products -->
      <!--
      **************************************
      END Kit products
      **************************************
      -->



      <!--
      **************************************
      Add to cart and total price box BEGIN
      **************************************
      -->
      <div class="product-kit__purchase">

        <!-- Kit total price -->
        <div class="product-kit__price">
          <div class="product-price">
            <!-- Old Price -->
            <div class="product-price__old">
               {echo emmet_money($kit->getOriginPrice(), 'span.product-price__old-value', '', 'span.product-price__old-cur')}
            </div>
            <!--  Main Price -->
            <div class="product-price__main product-price__main--vertical">
              {echo emmet_money($kit->getFinalPrice(), 'span.product-price__main-value', '', 'span.product-price__main-cur')}
            </div>

            {$loc_additional_prices = emmet_money_additional($kit->getFinalPrice(), 'span.product-price__addition-value', '', 'span.product-price__addition-cur')}
            {if count($loc_additional_prices) > 0}
              <div class="product-price__addition">
                {foreach $loc_additional_prices as $additional_price}
                  <div class="product-price__addition-item">
                    {$additional_price}
                  </div>
                {/foreach}
              </div>
            {/if}

          </div>
        </div>

        <!-- Kit discount value -->
        <div class="product-kit__discount">
          <span class="product-kit__discount-title">
            {tlang('You save')}
          </span>
          <span class="product-kit__discount-val">
            <!-- str_replace - Remove spaces between currency and price -->
            {echo str_replace(" ","", emmet_money($kit->getDiscountStatic()))}
          </span>
        </div>

        <!-- Kit add to cart button -->
        <div class="product-kit__btn">
          {view('shop/includes/product/product_kit_button.tpl', [
            'model' => $kit
          ])}
        </div> 

      </div><!-- /.product-kit__purchase -->
      <!--
      **************************************
      END Add to cart and total price box
      **************************************
      -->

    </div><!-- /.product-kit__item -->
    {/foreach}

  </div><!-- /.product-kit__inner -->
</div><!-- /.product-kit -->