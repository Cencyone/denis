<div class="content">
  <div class="content__container">

    <div class="content__header">
      <h1 class="content__title">
        {tlang('Brands')}
      </h1>
    </div>

    <div class="content__row">
      <div class="row row--ib row--vindent-m">
        {foreach $model as $m}
          <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">

            <a class="brand-image" href="{shop_url('brand/'.$m.url)}">
              <span class="brand-image__photo">
                <img class="brand-image__img" src="{media_url('/uploads/shop/brands/'.$m.image)}" alt="{$m.name}">
              </span>
              <span class="brand-image__title">
                {$m.name}
              </span>
            </a>

          </div>
        {/foreach}
      </div>
    </div>

  </div><!-- /.content__container -->
</div><!-- /.content -->