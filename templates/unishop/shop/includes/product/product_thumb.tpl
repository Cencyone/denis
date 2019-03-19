<article class="product-thumb">

  <!-- Photo  -->
  <div class="product-thumb__photo">
    <div class="product-photo">
      <button class="product-photo__item product-photo__item--sm" type="button"
              data-product-photo-href="{site_url($model->getRouteUrl())}">
        <img class="product-photo__img" src="{echo $model->firstVariant->getSmallPhoto()}"
             alt="{echo $model->getName()}" title="{echo $model->getName()}">
      </button>
    </div>
  </div>

  <div class="product-thumb__inner">
    <!-- Title -->
    <h2 class="product-thumb__title">
      <a class="product-thumb__title-link" href="{site_url($model->getRouteUrl())}">{echo $model->getName()}</a>
    </h2>

    <!-- Price -->
    <div class="product-thumb__price">

      <div class="product-price product-price--bg">
        {if $model->firstVariant->getDiscountStatic() > 0}
          <div class="product-price__item">
            <div class="product-price__old">
              {echo emmet_money($model->firstVariant->getOriginPrice(), 'span.product-price__old-value', '', 'span.product-price__old-cur')}
            </div>
          </div>
        {/if}
        <div class="product-price__item">
          <div class="product-price__main">
            {echo emmet_money($model->firstVariant->getFinalPrice(), 'span.product-price__main-value', '', 'span.product-price__main-cur')}
          </div>
        </div>
        {$loc_additional_prices = emmet_money_additional($model->firstVariant->getFinalPrice(), 'span.product-price__addition-value', '', 'span.product-price__addition-cur')}
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
  </div>

</article>