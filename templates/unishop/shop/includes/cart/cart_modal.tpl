<div class="modal modal--lg">
    
  <!-- Modal Header -->
  {view('includes/modal/modal_header.tpl', [
    'title' => tlang('Shopping cart')
  ])}

  <div
    data-cart-summary="modal"
    data-cart-summary--tpl="includes/cart/cart_modal"
    data-cart-summary--url="{shop_url('cart')}">
    
    <!-- Modal Content -->
    <div class="modal__content">
      {if count($items) > 0}
        {view('shop/includes/cart/cart_summary.tpl', [
        'parent_type' => 'modal',
        'parent_coupon' => false
        ])}
      {else:}
        <p class="typo" data-ajax-grab="cart-empty">{tlang('You have no items in your shopping cart')}</p>
      {/if}
    </div><!-- \.modal__content -->
    
    <!-- Modal Footer -->
    {if count($items) > 0}    
    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset" 
            data-modal-close
          >{tlang('Continue Shopping')}</button>
        </div>
        <div class="modal__footer-btn">
          <a class="btn btn--va-m btn-primary" href="{shop_url('cart')}"
            data-button-loader="button"
          >
            <span>{tlang('Proceed to checkout')}</span>
            <i class="button--loader hidden"
               data-button-loader="loader"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__refresh"></use></svg></i>
          </a>
        </div>
      </div>
    </div>
    {/if}
  </div><!-- \. data-cart container -->

  <!-- Insert Header cart template via Ajax-->
  <div class="hidden" data-ajax-grab="cart-header">
    {view('shop/includes/cart/cart_header.tpl', ['model' => $cart])}
  </div>
  
</div><!-- \.modal -->