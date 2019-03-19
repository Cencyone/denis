<div class="content">
  <div class="content__container" data-cart-scope data-ajax-inject="cart-empty">

    <div class="content__header">
      <h1 class="content__title">
        {tlang('One Step Checkout')}
      </h1>
    </div>

    {if count($items) > 0}
      <div class="row">

        <!-- Order cart -->
        <div class="col-sm-6 col-sm-push-6 col-md-7 col-lg-6 col-md-push-5 col-lg-push-6">
          <div class="cart-frame">
            <div class="cart-frame__header">
              <div class="cart-frame__title">{tlang('Order summary')}</div>
            </div>
            <div class="cart-frame__inner"
                 data-cart-summary="page"
                 data-cart-summary--url="{shop_url('cart')}">
              {view('shop/includes/cart/cart_summary.tpl', [
                'parent_type' => 'order',
              'parent_coupon' => true
              ])}
            </div>
          </div>
        </div>

        <!-- Order form -->
        <div class="col-sm-6 col-sm-pull-6 col-md-5 col-lg-6 col-md-pull-7 col-lg-pull-6 col--spacer-xs">
          {if !$CI->dx_auth->is_logged_in()}
            <div class="content__row content__row--sm">
              <div class="typo">
                {tlang('Returning customer?')}
                <a href="{site_url('auth')}"
                   data-modal="login_popup">{tlang('Sign in')}
                </a>
              </div>
            </div>
          {/if}
          <div class="content__row">
            <!-- Ordering form -->
            {view('shop/includes/cart/cart_checkout.tpl')}
          </div>
        </div>

      </div>
    {else:}
      <div data-ajax-grab="cart-empty">
        <div class="content__header">
          <h1 class="content__title">
            {tlang('Cart is currently empty')}
          </h1>
        </div>
        <div class="content__row">
          <p class="typo">{tlang('You have no items in your shopping cart')}</p>
        </div>
        <div class="content__row">
          <a class="btn btn-primary" href="{site_url('')}">{tlang('Continue Shopping')}</a>
        </div>
      </div>
    {/if}

    <!--
        Insert Header cart template via Ajax
    -->
    <div class="hidden" data-ajax-grab="cart-header">
      {view('shop/includes/cart/cart_header.tpl', ['model' => $cart])}
    </div>

  </div><!-- /.content__container -->
</div><!-- /.content -->