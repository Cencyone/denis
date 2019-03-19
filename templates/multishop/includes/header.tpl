<!-- Top Headline -->
<div class="page__headline hidden-xs hidden-sm">
  <div class="page__container">

    <div class="row row--ib row--ib-mid">
      <div class="col-md-6">
        {load_menu('info_header')}
      </div>
      <div class="col-md-6 clearfix">
        <div class="pull-right">
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
        <button class="ico-mobile" data-page-mobile-btn>
          <svg class="svg-icon">
            <use xlink:href="#svg-icon__bars"></use>
          </svg>
        </button>
        <button class="ico-mobile hidden" data-page-mobile-btn>
          <svg class="svg-icon">
            <use xlink:href="#svg-icon__remove"></use>
          </svg>
        </button>
      </div>
      <!-- Logo -->
      <div class="col-xs-6 col-md-3 col--align-center col--align-left-md">
        {if siteinfo('siteinfo_logo') != ""}
          {if $CI->core->core_data['data_type'] != 'main'}<a href="{site_url('')}">{/if}
          <img src="{echo siteinfo('siteinfo_logo')}" alt="{echo $CI->core->settings['site_title']}">
          {if $CI->core->core_data['data_type'] != 'main'}</a>{/if}
        {/if}
      </div>
      <!-- Phones and call-back -->
      <div class="col-md-3 col-lg-2 col-md-push-5 hidden-xs hidden-sm">
        {view('includes/header_siteinfo.tpl')}
      </div>
      <!-- Cart -->
      <div class="col-xs-3 col-md-1 col-lg-2 col-md-push-5 clearfix">
        {if !ShopCore::app()->SSettings->useCatalogMode()}
        <div class="pull-right" data-ajax-inject="cart-header">
          {view('shop/includes/cart/cart_header.tpl', ['model' => \Cart\BaseCart::getInstance()])}
        </div>
        {/if}
      </div>
      <!-- Search -->
      <div class="col-xs-12 col-md-5 col-md-pull-4 col--spacer-sm">
        {view('shop/includes/search_and_brand/autocomplete.tpl')}
      </div>
    </div>

  </div>
</div>