<div class="product-price {$parent_modifier}">

  <!-- Discount -->
  {if $variant->getDiscountStatic() > 0}
    <div class="product-price__item">
      <div class="product-price__old">
        {echo emmet_money($variant->getOriginPrice(), 'span.product-price__item-value[data-product-price--origin]', '', 'span.product-price__item-cur')}
      </div>
    </div>
  {/if}

  <!-- Main Price -->
  <div class="product-price__item">
    <div class="product-price__main">
      {echo emmet_money($variant->getFinalPrice(), 'span.product-price__item-value[data-product-price--main]', 'span.product-price__item-coins[data-product-price--coins]', 'span.product-price__item-cur')}
    </div>
  </div>

  {$loc_additional_prices = emmet_money_additional($variant->getFinalPrice(), 'span.product-price__addition-value[data-product-price--addition-value]', 'span.product-price__addition-coins[data-product-price--addition-coins]', 'span.product-price__addition-cur')}
  {if count($loc_additional_prices) > 0}
    <div class="product-price__item">
      <div class="product-price__addition">
        {foreach $loc_additional_prices as $additional_price}
          <div class="product-price__addition-item" data-product-price--addition-list>
            {$additional_price}
          </div>
        {/foreach}
      </div>
    </div>
  {/if}

</div>