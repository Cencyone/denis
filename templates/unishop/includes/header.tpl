<!-- Top Headline -->
<div class="page__headline hidden-xs hidden-sm">
  <div class="page__container">

    <div class="row row--ib row--ib-mid">
      <div class="col-md-6">
        <div class="page__top-menu">
          {load_menu('info_header')}
        </div>
      </div>
      <div class="col-md-6 clearfix">
        <div class="page__user-bar">
          {view('includes/header_toolbar.tpl')}
        </div>
      </div>
    </div>

  </div>
</div>


<!-- Main Header -->
<div class="page__header">
  <div class="page__container">

    <div class="row row--ib row--ib-mid">
      <!-- Hamburger menu -->
      <div class="col-xs-3 visible-xs-inline-block visible-sm-inline-block">
        <button class="btn-mobile-icon" data-page-mobile-btn>
          <svg class="svg-icon">
            <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__hamburger"></use>
          </svg>
        </button>
        <button class="btn-mobile-icon hidden" data-page-mobile-btn>
          <svg class="svg-icon">
            <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__close-bold"></use>
          </svg>
        </button>
      </div>
      <!-- Logo -->
      <div class="col-xs-6 col-md-3 col-lg-2 col--align-center col--align-left-md">
        {if siteinfo('siteinfo_logo') != ""}
          {if $CI->core->core_data['data_type'] != 'main'}<a href="{site_url('')}">{/if}
          <img src="{echo siteinfo('siteinfo_logo')}" alt="{echo $CI->core->settings['site_title']}">
          {if $CI->core->core_data['data_type'] != 'main'}</a>{/if}
        {/if}
      </div>
      <!-- Phones and call-back -->
      <div class="col-md-3 col-lg-2 col-md-push-5 col-lg-push-4 hidden-xs hidden-sm">
        {view('includes/header_phone.tpl')}
      </div>
      <!-- Schedule -->
      <div class="col-lg-2 col-lg-push-4 hidden-xs hidden-sm hidden-md">
        {view('includes/header_schedule.tpl')}
      </div>
      <!-- Cart -->
      <div class="col-xs-3 col-md-1 col-lg-2 col-md-push-5 col-lg-push-4 clearfix">
        {if !ShopCore::app()->SSettings->useCatalogMode()}
        <div class="pull-right" data-ajax-inject="cart-header">
          {view('shop/includes/cart/cart_header.tpl', ['model' => \Cart\BaseCart::getInstance()])}
        </div>
        {/if}
      </div>
      <!-- Search -->
      <div class="col-xs-12 col-md-5 col-lg-4 col-md-pull-4 col-lg-pull-6 col--spacer-sm">
        {view('shop/includes/search_and_brand/autocomplete.tpl')}
      </div>
    </div>

  </div>
</div>