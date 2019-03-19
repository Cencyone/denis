{if count($brands) > 0}
  <section class="widget-secondary">
    <div class="widget-secondary__header">
      <div class="widget-secondary__title">{getWidgetTitle('brands')}</div>
      <div class="widget-secondary__viewall">
        <a class="widget-secondary__hlink" href="{shop_url('brand')}">{tlang('View all')}</a>
      </div>
    </div>
    <div class="widget-secondary__inner">

      <div class="brands-widget">
        <div class="row row--ib row--vindent-s">
          {foreach $brands as $brand}
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
              <div class="brands-widget__item">
                <a class="brands-widget__link" href="{shop_url($brand.full_url)}">
                  {if $brand.img_fullpath}
                    <img class="brands-widget__img" src="{media_url($brand.img_fullpath)}" title="{$brand.name}"
                         alt="{$brand.name}">
                  {else:}
                    <span class="brands-widget__img">{$brand.name}</span>
                  {/if}
                </a>
              </div>
            </div>
          {/foreach}
        </div>
      </div>

    </div>
  </section>
{/if}