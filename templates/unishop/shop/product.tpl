<div class="content">
  <div class="content__container">

    <div class="row">
      <div class="col-lg-9">

        <!-- Product main data -->
        <div class="content__row" data-product-scope data-product-cat-url="{site_url($model->getMainCategory()->getRouteUrl())}">
          <div class="row">

            <!-- Product photo -->
            <div class="col-sm-5">
              {view('shop/includes/product/product_photo.tpl', [
              'model' => $model
              ])}
            </div>

            <!-- Product intro -->
            <div class="col-sm-7 col--spacer-xs">
              <div class="content__header content__header--xs">
                <h1 class="content__title">
                  {echo $model->getName()}
                </h1>
              </div>
              <div class="content__row">
                {view('shop/includes/product/product_intro.tpl')}
              </div>
            </div>

          </div><!-- /.row -->
        </div><!-- /.content__row -->

        <!-- Product Kits -->
        {if count($model->getShopKits()) > 0}
          <!-- If product is not archived -->
          {if !tpl_is_product_archived($model)}
            <div class="content__row">
              {view('shop/includes/product/product_kit.tpl')}
            </div>
          {/if}
        {/if}

        <!-- Product Full information -->
        <div class="content__row">
          {view('shop/includes/product/product_full_info.tpl')}
        </div>

      </div><!-- /.col -->


      <!-- Product sidebar -->
      <aside class="col-lg-3 visible-lg">
        <div class="content__sidebar">

          <!-- Product shipping details: delivery and payment methods, contact phone etc -->
          <div class="content__sidebar-item">
            {view('shop/includes/product/product_shipping.tpl')}
          </div>

          <!-- Sidebar banner -->
          {if $loc_banner = getBanner('sidebar_banner', 'object')}
            <div class="content__sidebar-item">
              {view('xbanners/banners/banner_simple.tpl', [
              'parent_banner' => $loc_banner
              ])}
            </div>
          {/if}

          <!-- Similar products -->
          {widget('products_similar_sidebar')}

        </div>
      </aside><!-- /.col -->


    </div><!-- /.row -->

  </div><!-- /.content__container -->
</div><!-- /.content -->
