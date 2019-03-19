<div class="compare">
  <div class="row row--gutter-collapse">

    <!-- Properties title list BEGIN -->
    <div class="col-xs-6 col-sm-3">
      <div class="compare__product"></div>
      <div class="compare__params">
        {foreach $parent_params as $param_name}
          <div class="compare__params-item compare__params-item--title">
            <span class="compare__params-value">
              <span class="compare__params-text">{$param_name}</span>
            </span>
          </div>
        {/foreach}
      </div>
    </div>
    <!-- END Properties title list -->

    <!-- Properties values list BEGIN -->
    <div class="col-xs-6 col-sm-9">
      <div class="row row--ib row--scroll row--gutter-collapse">
        {foreach $parent_products as $product}
          {if $product->getCategoryId() == $parent_category.Id}
            <div class="col-sm-6 col-md-3">
              <div class="compare__product">
                {view('shop/includes/product/product_thumb.tpl', [
                'model' => $product
                ])}
                <div class="compare__delete">
                  <a href="{shop_url('compare/remove/'.$product->getId())}"
                     class="compare__delete-link">{tlang('Delete item')}</a>
                </div>
              </div>
              <div class="compare__params">
                {$params = tpl_product_compare_properties($product, $parent_params)}
                {foreach $params as $param}
                  <div class="compare__params-item">
                    <span class="compare__params-value">
                      <span class="compare__params-text">{echo trim($param) != "" ? $param : "&mdash;"}</span>
                    </span>
                  </div>
                {/foreach}
              </div>
            </div>
          {/if}
        {/foreach}
      </div>
    </div>
    <!-- END Properties values list -->

  </div>
</div><!-- /.compare -->