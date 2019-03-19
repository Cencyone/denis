<section class="widget-primary"
         data-slider="widget-primary">
  <div class="widget-primary__header">
    <div class="widget-primary__title">{$parent_title}</div>
    <div class="widget-primary__arrows">
      <div class="widget-primary__arrow hidden"
           data-slider-arrow-left>
        <svg class="svg-icon svg-icon--angle">
          <use xlink:href="#svg-icon__angle-left"></use>
        </svg>
      </div>
      <div class="widget-primary__arrow hidden"
           data-slider-arrow-right>
        <svg class="svg-icon svg-icon--angle">
          <use xlink:href="#svg-icon__angle-right"></use>
        </svg>
      </div>
    </div>
  </div>
  <div class="widget-primary__inner">
    <div class="row row--ib row--vindent-m"
         data-slider-slides="2,3,3,4">
      {foreach $parent_products as $product}
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3"
             data-slider-slide>
          {view('shop/includes/product/product_cut.tpl', [
          'model' => $product
          ])}
        </div>
      {/foreach}
    </div>
  </div>
</section>