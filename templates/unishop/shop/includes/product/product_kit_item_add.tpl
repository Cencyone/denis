<article class="product-thumb">

  <!-- Photo  -->
  <div class="product-thumb__photo hidden-xs">
    <div class="product-photo">
      <a class="product-photo__item product-photo__item--sm"
         href="{site_url($model->getSProducts()->getRouteUrl())}">
        <img class="product-photo__img" src="{echo $model->getSProducts()->firstVariant->getSmallPhoto()}"
             alt="{echo $model->getSProducts()->getName()}" title="{echo $model->getSProducts()->getName()}">
        <div class="product-photo__labels">
          {if $model->getDiscountPercent()}
            <i class="product-photo__label product-photo__label--small product-photo__label--discount">
              <span class="product-photo__label-text">-{echo $model->getDiscountPercent()}%</span>
              <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__new"></use></svg>
            </i>
          {/if}
        </div>
      </a>
    </div>
  </div>

  <!-- Title -->
  <h2 class="product-thumb__title">
    <a class="product-thumb__title-link"
       href="{site_url($model->getSProducts()->getRouteUrl())}">{echo $model->getSProducts()->getName()}</a>
  </h2>


  <!-- Price -->
  <div class="product-thumb__price">

    <div class="product-price">

      <div class="product-price__item">
        <div class="product-price__old">
          {echo emmet_money($model->getOriginPrice(), 'span.product-price__old-value', '', 'span.product-price__old-cur')}
        </div>
      </div>

      <div class="product-price__item">
        <div class="product-price__main">
          {echo emmet_money($model->getFinalPrice(), 'span.product-price__main-value', '', 'span.product-price__main-cur')}
        </div>
      </div>

      {$loc_additional_prices = emmet_money_additional($model->getFinalPrice(), 'span.product-price__addition-value', '', 'span.product-price__addition-cur')}
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