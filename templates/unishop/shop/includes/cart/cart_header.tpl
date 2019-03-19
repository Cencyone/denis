<div class="cart-header">
  <div class="cart-header__aside">
    <a class="cart-header__ico {if !$model->getTotalItems()} cart-header__ico--empty {/if}" href="{shop_url('cart')}" rel="nofollow" data-modal="includes/cart/cart_modal">
      <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__cart"></use></svg>
      <span class="cart-header__badge hidden-lg">{echo $model->getTotalItems() ? : 0}</span>
    </a>
  </div>
  <div class="cart-header__inner visible-lg">
    <div class="cart-header__title">
      <a class="cart-header__link {if !$model->getTotalItems()} cart-header__link--empty {/if}" href="{shop_url('cart')}" rel="nofollow" data-modal="includes/cart/cart_modal">{tlang('Cart')}</a>
    </div>
    <div class="cart-header__desc">
      {if $model->getTotalItems() > 0}
        {echo $model->getTotalItems() ? : 0} - {echo str_replace(" ", "", emmet_money($model->getFinalPrice()))}
      {else:}
        {tlang('Empty')}
      {/if}
    </div>
  </div>  
</div>