<div class="content">
  <div class="content__container">

    {if $wishlist != 'empty'}
      {$list = $wishlist[0];}
      <div class="content__header">
        <h1 class="content__title">
          {tlang('Wish list')} <q class="content__quote">{$list.title}</q>
        </h1>
      </div>
      <div class="content__row">
        <div class="row row--ib row--vindent-m">
          {foreach $wishlist as $item}
            {$product = getProduct($item.id)}
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
              {view('shop/includes/product/product_cut.tpl', [
              'model' => $product,
              'parent_wishlist' => $item
              ])}
            </div>
          {/foreach}
        </div>
      </div>
    {else:}
    {/if}

  </div><!-- /.content__container -->
</div><!-- /.content -->