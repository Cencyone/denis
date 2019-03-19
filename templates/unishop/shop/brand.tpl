<div class="content">
  <div class="content__container">
    <div class="row">

      <!-- Left side, Categories filter -->
      <div class="col-sm-4 col-md-3 col-lg-2 hidden-xs">
        <div class="content__sidebar-item">
          {view('shop/includes/search_and_brand/cat_filter.tpl', [
          "parent_url" => "brand/" . $model->getUrl() . "/__item__?".tpl_http_query_filter(['category', 'per_page'])
          ])}
        </div>
        {if $model->getImage()}
        <div class="content__sidebar-item">
          <div class="brand-sidebar">
            <i class="brand-sidebar__photo">
              <img class="brand-sidebar__img" src="{media_url('/uploads/shop/brands/'.$model->getImage())}"
                   alt="{echo $model->getName()}">
            </i>
          </div>
        </div>
        {/if}
      </div>

      <!-- Center -->
      <div class="col-sm-8 col-md-9 col-lg-10">

        <!-- Brand title -->
        <div class="content__header">
          <h1 class="content__title">
            {echo $model->getName()}
          </h1>
          <span class="content__hinfo">
            {tlang('Result')}:
            {if $totalProducts > 0}
              <i class="content__hinfo-number">{echo tpl_encode($CI->input->get('per_page')) ? tpl_encode($CI->input->get('per_page')) : 1}</i>
               -
              <i class="content__hinfo-number">{echo tpl_encode($CI->input->get('per_page')) + count($products)}</i>
              {tlang('of')}
            {/if}
            <i class="content__hinfo-number">{$totalProducts}</i>
            {echo SStringHelper::Pluralize($totalProducts, array(tlang('pluralize item 1'), tlang('pluralize item 2'), tlang('pluralize item 3')))}
            {if tpl_encode($CI->input->get('category'))}
              {tlang('in')}
              <q>{tpl_category_active_filter(tpl_encode($CI->input->get('category')), $categories)}</q>
            {/if}
          </span>
        </div>

        <!-- Horisontal banner -->
        {if $loc_banner = getBanner('catalog_horisontal_banner', 'object')}
          <div class="content__row content__row--sm">
            {view('xbanners/banners/banner_simple.tpl', [
              'parent_banner' => $loc_banner
            ])}
          </div>
        {/if}

        <!-- Products order and view change -->
        {view('shop/includes/category/category_toolbar.tpl', [
          'parent_default_order' => false
        ])}

        <!-- Filter selected results -->
        {if tpl_encode($CI->input->get('category'))}
          <div class="content__row content__row--sm">
            {view('shop/includes/search_and_brand/active_filters.tpl', [
            'parent_url' => 'brand/'.$model->getUrl().'/?'.tpl_http_query_filter(['category'])
            ])}
          </div>
        {/if}

        <!-- Product list -->
        <div class="content__row">
          {view('shop/includes/category/category_products.tpl')}
        </div>

        <!-- Brand description -->
        {if trim($model->getDescription()) != "" and $page_number < 2}
          <div class="content__row">
            <div class="typo">{echo $model->getDescription()}</div>
          </div>
        {/if}

      </div><!-- /.col -->
      <!-- END Center -->

    </div><!-- /.row -->
  </div><!-- /.content__container -->
  {view('shop/includes/category/category_form.tpl')}
</div><!-- /.content -->