{if count($brands) > 0}
<div class="content__row">
  <div class="content__container">
    <section class="widget-secondary">
      <div class="widget-secondary__header">
        <h2 class="widget-secondary__title">{getWidgetTitle('brands')}</h2>
        <div class="widget-secondary__viewall">
          <a class="widget-secondary__hlink" href="{shop_url('brand')}">{tlang('View all')}</a>
        </div>
      </div>      
      <div class="widget-secondary__inner">
        <div class="brands-widget" data-slider="mainpage-brands">
          <div class="brands-widget__arrow brands-widget__arrow--prev hidden" data-slider-arrow-left>
            <svg class="svg-icon svg-icon--angle"><use xlink:href="#svg-icon__angle-left"></use></svg>
          </div>
          <div class="brands-widget__arrow brands-widget__arrow--next hidden" data-slider-arrow-right>
            <svg class="svg-icon svg-icon--angle"><use xlink:href="#svg-icon__angle-right"></use></svg>
          </div>
          <div data-slider-slides="2,4,6,6">
            {foreach $brands as $brand}
              <div data-slider-slide>
                <a class="brands-widget__link" href="{shop_url($brand.full_url)}">
                  {if $brand.img_fullpath}
                  <img class="brands-widget__item" src="{media_url($brand.img_fullpath)}" title="{$brand.name}" alt="{$brand.name}">
                  {else:}
                  <span class="brands-widget__item">{$brand.name}</span>
                  {/if}
                </a>
              </div>
            {/foreach}
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
{/if}