<div class="content">
  <div class="content__container">

    <div class="content__header">
      <h1 class="content__title">
        {tlang('Your Account')}
      </h1>
    </div>
    <div class="content__row">
      <div class="row">
        <div class="col-sm-5">

          <!-- Discount info -->
          {$loc_discount = module('mod_discount/discount_api');}
          {if $loc_discount->discountsExists() > 0}
            <div class="content__row">
              <div class="frame-content">
                <div class="frame-content__header frame-content__header--sm">
                  <div class="frame-content__title frame-content__title--sm">{tlang('Discount information')}</div>
                </div>
                <div class="frame-content__inner">
                  <div class="frame-content__row">
                    {view('shop/includes/profile/discount_info.tpl', [
                    'discount' => $loc_discount
                    ])}
                  </div>
                </div>
              </div>
            </div>
          {/if}

          <!-- System bonus module. User earned points -->
          {if array_key_exists('system_bonus', $modules) && module('system_bonus')->getBonusForUser()}
            {view('system_bonus/user_profile.tpl')}
          {/if}


          <!-- Personal user data -->
          <div class="content__row">
            <div class="frame-content">
              <div class="frame-content__header frame-content__header--sm">
                <div class="frame-content__title frame-content__title--sm">{tlang('Personal information')}</div>
              </div>
              <div class="frame-content__inner">
                {view('shop/includes/profile/user_data.tpl')}
              </div>
            </div>
          </div>

        </div><!-- /.col -->
        <div class="col-sm-7 col--spacer-xs">

          <!-- Order history -->
          <div class="content__row">
            <div class="frame-content">
              <div class="frame-content__header frame-content__header--sm">
                <div class="frame-content__title frame-content__title--sm">{tlang('Order history')}</div>
              </div>
              <div class="frame-content__inner">
                {if count($orders) > 0}
                  {view('shop/includes/profile/order_history.tpl')}
                {else:}
                  <p class="typo">{tlang("You haven't made any orders yet")}</p>
                {/if}
              </div>
            </div>
          </div>

        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.content__row -->

  </div><!-- /.content__container -->
</div><!-- /.content -->