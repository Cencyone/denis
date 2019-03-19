<div class="banner-group">
  <div class="row row--gutter-collapse">

    <?php if($loc_main_banner = getBanner('main_banner_big', 'object')): ?>
      <div class="col-xs-12 col-md-6 col-md-push-2">
        <div class="banner-group__main">
          <?php view('xbanners/banners/banner_simple.tpl', [
          'parent_banner' => $loc_main_banner
          ])?>
        </div>
      </div>
    <?php endif; ?>

    <?php if($loc_main_banner = getBanner('main_banner_left', 'object')): ?>
      <div class="col-xs-4 col-md-2 col-md-pull-6">
        <div class="banner-group__left">
          <?php view('xbanners/banners/banner_simple.tpl', [
          'parent_banner' => $loc_main_banner
          ])?>
        </div>
      </div>
    <?php endif; ?>

    <div class="col-xs-8 col-md-4">
      <div class="banner-group__right">
        <?php if($loc_main_banner = getBanner('main_banner_right_top', 'object')): ?>
          <div class="banner-group__right-top">
            <?php view('xbanners/banners/banner_simple.tpl', [
            'parent_banner' => $loc_main_banner
            ])?>
          </div>
        <?php endif; ?>

        <?php if($loc_main_banner = getBanner('main_banner_right_bot', 'object')): ?>
          <div class="banner-group__right-bot">
            <?php view('xbanners/banners/banner_simple.tpl', [
            'parent_banner' => $loc_main_banner
            ])?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div><?php $mabilis_ttl=1552752299; $mabilis_last_modified=1491903754; ///home/user2263/public_html/templates/unishop/includes/header_banners.tpl ?>