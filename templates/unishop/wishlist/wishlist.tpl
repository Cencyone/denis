<div class="content">
  <div class="content__container">

    {if count($wishlists) > 0}
      <div class="content__header">
        <h1 class="content__title">
          {tlang('Wish list')}
        </h1>
      </div>
      {foreach $wishlists as $list}
        {$group = $list[0];}
        <div class="content__row">
          <section class="frame-content">
            <div class="frame-content__header">
              <h2 class="frame-content__title">
                <a class="frame-content__title-link" href="{site_url('wishlist/show/'.$group.hash)}">
                  {$group.title}
                </a>
              </h2>
              <div class="frame-content__header-nav">
                <a class="frame-content__header-link" href="{site_url('/wishlist/editWL/'.$group.wish_list_id)}"
                   data-modal>{tlang('Edit')}</a>
              </div>
              <div class="frame-content__header-nav">
                <a class="frame-content__header-link"
                   href="{site_url('/wishlist/deleteWL/'.$group.wish_list_id)}">{tlang('Delete')}</a>
              </div>
            </div>
            <div class="frame-content__inner">

              {if $list[0]['id']}
                <div class="row row--ib row--vindent-m">
                  {foreach $list as $item}
                    {$product = getProduct($item.id)}
                    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
                      {view('shop/includes/product/product_cut.tpl', [
                      'model' => $product,
                      'parent_wishlist' => $item
                      ])}
                    </div>
                  {/foreach}
                </div>
              {else:}
                <div class="typo">
                  <h3>{tlang('This list is empty')}</h3>
                  <p>{tlang("Once added new items, you'll be able to continue shopping any time and also share the information about the purchase with your friends.")}</p>
                </div>
              {/if}

            </div>
          </section>
        </div>
      {/foreach}

    {else:}
      <div class="content__header">
        <h1 class="content__title">
          {tlang('Your wishlist is empty')}
        </h1>
      </div>
      <div class="content__row">
        <p class="typo">{tlang("Once added new items, you'll be able to continue shopping any time and also share the information about the purchase with your friends.")}</p>
      </div>
    {/if}

  </div><!-- /.content__container -->
</div><!-- /.content -->