<div class="product-fullinfo">

  <!-- Product full description -->
  {if trim(strip_tags($model->getFullDescription())) != ""}
    <div class="product-fullinfo__item">
      <div class="product-fullinfo__header">
        <div class="product-fullinfo__title">{tlang('Details')}</div>
      </div>
      <div class="product-fullinfo__inner">
        <div class="typo">{echo $model->getFullDescription()}</div>
      </div>
    </div>
  {/if}


  <!-- Product all properties -->
  {$loc_params = ShopCore::app()->SPropertiesRenderer->renderPropertiesArray($model)}
  {if count($loc_params) > 0}
    <div class="product-fullinfo__item">
      <div class="product-fullinfo__header">
        <div class="product-fullinfo__title">{tlang('Specifications')}</div>
      </div>
      <div class="product-fullinfo__inner">
        {view('shop/includes/product/product_properties.tpl', [
        'items' => $loc_params
        ])}
      </div>
    </div>
  {/if}


  <!-- Product commetns -->
  {if $model->enable_comments == 1}
    <div id="comments-list"></div>
    <div class="product-fullinfo__item">
      <div class="product-fullinfo__header">
        <div class="product-fullinfo__title">{tlang('Customer Reviews')}</div>
      </div>
      <div class="product-fullinfo__inner" data-comments>
        {tpl_load_comments()}
      </div>
    </div>
  {/if}


  <!-- Product accessories -->
  {if $accessories}
    <div class="product-fullinfo__item">
      <div class="product-fullinfo__header">
        <div class="product-fullinfo__title">{tlang('Accessories')}</div>
      </div>
      <div class="product-fullinfo__inner">
        <div class="row row--ib row--vindent-m">
          {foreach $accessories as $product}
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
              {view('shop/includes/product/product_cut.tpl', array(
              'model' => $product
              ))}
            </div>
          {/foreach}
        </div>
      </div>
    </div>
  {/if}

</div><!-- /.product-fullinfo-->