<div class="content">
  <div class="content__container">
    <div class="content__header">
      <h1 class="content__title">
        {tlang('Your order is confirmed')}
      </h1>
    </div>
    <div class="row">
      <!-- Order form -->
      <div class="col-sm-6">
        {view('shop/includes/cart/order_view_info.tpl')}
      </div>
      <!-- Order cart -->
      <div class="col-sm-6 col--spacer-xs">
        <div class="cart-frame">
          <div class="cart-frame__header">
            <div class="cart-frame__title">{tlang('Order summary')}</div>
          </div>
          <div class="cart-frame__inner">
            <div class="cart-order">
              {view('shop/includes/cart/order_view_summary.tpl', [
              'items' => $model->getOrderProducts()
              ])}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /.content__container -->
</div><!-- /.content -->