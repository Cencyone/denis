<div class="content">
  <div class="content__container">

    <!-- Section title -->
    <div class="content__header">
      <h1 class="content__title">
        {$title}
      </h1>
    </div>

    <div class="content__row">
      <div class="row">
        <div class="col-md-9">
          {view('shop/includes/category/section_items.tpl')}
        </div>
        <div class="col-md-3 col--spacer-sm hidden-xs hidden-sm">
          <div class="content__sidebar">
            <!-- Sidebar banner -->
            {if $loc_banner = getBanner('sidebar_banner', 'object')}
              <div class="content__sidebar-item">
                {view('xbanners/banners/banner_simple.tpl', [
                'parent_banner' => $loc_banner
                ])}
              </div>
            {/if}
            {widget('popular_products_category')}
          </div>
        </div>
      </div>
    </div>

    <!-- Category description -->
    {if trim($category->getDescription()) != "" and $page_number < 2}
      <div class="content__row">
        <div class="typo">{echo trim($category->getDescription())}</div>
      </div>
    {/if}

  </div>
</div>