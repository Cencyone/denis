<article class="product-thumb">

  <!-- Photo  -->
  <div class="product-thumb__photo hidden-xs">
    <div class="product-photo">
      <span class="product-photo__item product-photo__item--sm">
        <img class="product-photo__img" src="{echo $model->firstVariant->getSmallPhoto()}"
             alt="{echo $model->getName()}" title="{echo $model->getName()}">
      </span>
    </div>
  </div>

  <!-- Title -->
  <h2 class="product-thumb__title">
    {echo $model->getName()}
  </h2>

  <!-- Price -->
  <div class="product-thumb__price">

    <div class="product-price">
      <div class="product-price__item">
        <div class="product-price__main">
          {echo emmet_money($model->getFirstVariant('kit')->getPrice(), 'span.product-price__main-value', '', 'span.product-price__main-cur')}
        </div>
      </div>
      {$loc_additional_prices = emmet_money_additional($model->getFirstVariant('kit')->getPrice(), 'span.product-price__addition-value', '', 'span.product-price__addition-cur')}
      {if count($loc_additional_prices) > 0}
        <div class="product-price__item">
          <div class="product-price__addition">
            {foreach $loc_additional_prices as $additional_price}
              <div class="product-price__addition-item">
                {$additional_price}
              </div>
            {/foreach}
          </div>
        </div>
      {/if}
    </div>

  </div>

</article>