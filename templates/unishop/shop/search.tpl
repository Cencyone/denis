<div class="content">
  <div class="content__container">
    <div class="row">

      <!-- Left side, Categories filter -->
      <div class="col-sm-4 col-md-3 col-lg-2 hidden-xs">
        {view('shop/includes/search_and_brand/cat_filter.tpl', [
        'parent_url' => "search?category=__item__&".tpl_http_query_filter(['category', 'per_page'])
        ])}
      </div>

      <!-- Center -->
      <div class="col-sm-8 col-md-9 col-lg-10">

        <!-- Query title -->
        <div class="content__header">
          <h1 class="content__title">
            {tlang('Result for')} <q class="content__quote">{echo tpl_encode($CI->input->get('text'))}</q>
          </h1>
          <span class="content__hinfo">
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
            'parent_url' => "search?".tpl_http_query_filter(['category'])
            ])}
          </div>
        {/if}

        <!-- Product list -->
        <div class="content__row">
          {view('shop/includes/category/category_products.tpl')}
        </div>


      </div><!-- /.col -->
      <!-- END Center -->

    </div><!-- /.row -->
  </div><!-- /.content__container -->
  {view('shop/includes/category/category_form.tpl')}
</div><!-- /.content -->