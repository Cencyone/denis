{if count($recent_news) > 0}
<div class="page__benefits">
  <div class="page__container">
    
    <div class="benefits">
      <div class="row row--ib row--ib-mid row--vindent-s">
        {foreach $recent_news as $item}
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
          <div class="benefits__item">
            <div class="benefits__ico">
              <img src="{$item.field_list_image}" alt="{$item.title}">
            </div>
            <div class="benefits__inner">
              <div class="benefits__title">{$item.title}</div>
              <div class="benefits__desc">{$item.prev_text}</div>
            </div>
          </div>
        </div>
        {/foreach}
      </div>
    </div>
    
  </div>
</div>
{/if}