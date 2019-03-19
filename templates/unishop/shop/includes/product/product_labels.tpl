<span class="product-photo__labels">
  {if $model->getHit()}
    <i class="product-photo__label product-photo__label--hit">
	    <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__hit"></use></svg>
	    <span class="product-photo__label-text">{tlang('Hit')}</span>
    </i>
  {/if}
  {if $model->getHot()}
    <i class="product-photo__label product-photo__label--hot">
      <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__new"></use></svg>
      <span class="product-photo__label-text">{tlang('New')}</span>
    </i>
  {/if}
  {if $model->getAction()}
    <i class="product-photo__label product-photo__label--action">
      <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__new"></use></svg>
      <span class="product-photo__label-text">{tlang('Sale')}</span>
    </i>
  {/if}
  {if $model->firstVariant->getDiscountPercent()}
    <i class="product-photo__label product-photo__label--discount">
			<span class="product-photo__label-text">-{echo $model->firstVariant->getDiscountPercent()}%</span>
			<svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__new"></use></svg>
		</i>
  {/if}
</span>