<div class="banner-group">
  <div class="row row--gutter-collapse">

    {if $loc_main_banner = getBanner('main_banner_big', 'object')}
      <div class="col-xs-12 col-md-6 col-md-push-2">
        <div class="banner-group__main">
          {view('xbanners/banners/banner_simple.tpl', [
          'parent_banner' => $loc_main_banner
          ])}
        </div>
      </div>
    {/if}

    {if $loc_main_banner = getBanner('main_banner_left', 'object')}
      <div class="col-xs-4 col-md-2 col-md-pull-6">
        <div class="banner-group__left">
          {view('xbanners/banners/banner_simple.tpl', [
          'parent_banner' => $loc_main_banner
          ])}
        </div>
      </div>
    {/if}

    <div class="col-xs-8 col-md-4">
      <div class="banner-group__right">
        {if $loc_main_banner = getBanner('main_banner_right_top', 'object')}
          <div class="banner-group__right-top">
            {view('xbanners/banners/banner_simple.tpl', [
            'parent_banner' => $loc_main_banner
            ])}
          </div>
        {/if}

        {if $loc_main_banner = getBanner('main_banner_right_bot', 'object')}
          <div class="banner-group__right-bot">
            {view('xbanners/banners/banner_simple.tpl', [
            'parent_banner' => $loc_main_banner
            ])}
          </div>
        {/if}
      </div>
    </div>

  </div>
</div>