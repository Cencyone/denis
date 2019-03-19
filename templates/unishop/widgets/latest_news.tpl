{$loc_items_num = count($recent_news)}
{if $loc_items_num > 0}
  <!-- URL to Widget First Category | Used to Make Link to All Items Page -->
  {$loc_cat_url = str_replace(strrchr($recent_news[0]['full_url'], "/"), "", $recent_news[0]['full_url'])}
  <section class="widget-secondary">
    <div class="widget-secondary__header">
      <div class="widget-secondary__title">{getWidgetTitle('latest_news')}</div>
      <div class="widget-secondary__viewall">
        <a class="widget-secondary__hlink" href="{site_url($loc_cat_url)}">{tlang('View all')}</a>
      </div>
    </div>
    <div class="widget-secondary__inner">
      <div class="row row--ib row--vindent-s">
        {foreach $recent_news as $item}
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <article class="small-post">
              {if trim($item.field_list_image) != ""}
                <a class="small-post__image" href="{site_url($item.full_url)}">
                  <img src="{$item.field_list_image}" alt="{$item.title}">
                </a>
              {/if}
              <div class="small-post__inner">
                <time class="small-post__date"
                      datetime="{date('Y-m-d', $item.publish_date)}">{tpl_locale_date('d F Y', $item.publish_date)}</time>
                <div class="small-post__title">
                  <a class="small-post__title-link" href="{site_url($item.full_url)}">{$item.title}</a>
                </div>
                {if trim($item.prev_text) != ""}
                  <div class="small-post__desc">
                    <div class="typo typo--sub-color">{$item.prev_text}</div>
                  </div>
                  <a class="small-post__readmore" href="{site_url($item.full_url)}">{tlang('Read more')}</a>
                {/if}
              </div>
            </article>
          </div>
        {/foreach}
      </div>
    </div>
  </section>
{/if}