<span class="product-photo__labels">
  {if $model->getHit()}
    <i class="product-photo__label product-photo__label--hit">{tlang('Hit')}</i>
  {/if}
  {if $model->getHot()}
    <i class="product-photo__label product-photo__label--hot">{tlang('New')}</i>
  {/if}
  {if $model->getAction()}
  <i class="product-photo__label product-photo__label--action">{tlang('Sale')}</i>
  {/if}
  {if $model->firstVariant->getDiscountPercent()}
  <i class="product-photo__label product-photo__label--discount">-{echo $model->firstVariant->getDiscountPercent()}%</i>
  {/if}
</span>