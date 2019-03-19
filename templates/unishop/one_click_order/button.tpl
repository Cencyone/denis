<div class="product-actions__item {if $model->firstVariant->getStock() == 0}hidden{/if}" data-one-click-scope>
  <div class="product-actions__ico product-actions__ico--cart">
    <svg class="svg-icon">
      <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__cart-small"></use>
    </svg>
  </div>
  <div class="product-actions__link">
    <a class="link link--main" href="{site_url('one_click_order/make_order/'.$variant_id)}" rel="nofollow"
       data-one-click-btn="one_click_modal"
       data-one-click-variant="{$variant_id}"
       data-one-click-href="{site_url('one_click_order/make_order')}">
      {tlang('Buy in one click')}
    </a>
  </div>
</div>