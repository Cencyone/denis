<!-- Top Headline -->
<div class="page__headline hidden-xs hidden-sm">
  <div class="page__container">

    <div class="row row--ib row--ib-mid">
      <div class="col-md-6">
        <div class="page__top-menu">
          <?php echo load_menu ('info_header'); ?>
        </div>
      </div>
      <div class="col-md-6 clearfix">
        <div class="page__user-bar">
          <?php echo view ('includes/header_toolbar.tpl'); ?>
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
            <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__hamburger"></use>
          </svg>
        </button>
        <button class="btn-mobile-icon hidden" data-page-mobile-btn>
          <svg class="svg-icon">
            <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__close-bold"></use>
          </svg>
        </button>
      </div>
      <!-- Logo -->
      <div class="col-xs-6 col-md-3 col-lg-2 col--align-center col--align-left-md">
        <?php if(siteinfo('siteinfo_logo') != ""): ?>
          <?php if($CI->core->core_data['data_type'] != 'main'): ?><a href="<?php echo site_url (''); ?>"><?php endif; ?>
          <img src="<?php echo siteinfo('siteinfo_logo')?>" alt="<?php echo $CI->core->settings['site_title']?>">
          <?php if($CI->core->core_data['data_type'] != 'main'): ?></a><?php endif; ?>
        <?php endif; ?>
      </div>
      <!-- Phones and call-back -->
      <div class="col-md-3 col-lg-2 col-md-push-5 col-lg-push-4 hidden-xs hidden-sm">
        <?php echo view ('includes/header_phone.tpl'); ?>
      </div>
      <!-- Schedule -->
      <div class="col-lg-2 col-lg-push-4 hidden-xs hidden-sm hidden-md">
        <?php echo view ('includes/header_schedule.tpl'); ?>
      </div>
      <!-- Cart -->
      <div class="col-xs-3 col-md-1 col-lg-2 col-md-push-5 col-lg-push-4 clearfix">
        <?php if(!ShopCore::app()->SSettings->useCatalogMode()): ?>
        <div class="pull-right" data-ajax-inject="cart-header">
          <?php echo view ('shop/includes/cart/cart_header.tpl', ['model' => \Cart\BaseCart::getInstance()]); ?>
        </div>
        <?php endif; ?>
      </div>
      <!-- Search -->
      <div class="col-xs-12 col-md-5 col-lg-4 col-md-pull-4 col-lg-pull-6 col--spacer-sm">
        <?php echo view ('shop/includes/search_and_brand/autocomplete.tpl'); ?>
      </div>
    </div>

  </div>
</div><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903754; ///home/user2263/public_html/templates/unishop/includes/header.tpl ?>