<div class="content">
  <div class="content__container">

    <!-- Category title -->
    <div class="content__header">
      <h1 class="content__title">
        {$category.name}
      </h1>
    </div>

    <!-- Category description -->
    {if trim($category.short_desc)}
      <div class="content__row">
        <div class="typo">{$category.short_desc}</div>
      </div>
    {/if}

    <!-- Category post list BEGIN -->
    <div class="content__row">
      {if count($pages) > 0}
        <div class="row row--ib row--vindent-m">
          {foreach $pages as $item}
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
              <!-- Sales-Post -->
              <article class="sales-post">
                <a href="{site_url($item.full_url)}" class="sales-post__global-link">                  
                  {$img = !trim($item.field_list_image) ? 'uploads/shop/nophoto/nophoto.jpg' : $item.field_list_image}
                    <div class="sales-post__photo">
                      <img src="{media_url($img)}"
                           alt="{$item.title}"
                           class="sales-post__img">
                      <div class="sales-post__label">{tlang("Offer!")}</div>
                    </div>                  
                  <div class="sales-post__content">
                    {if array_key_exists('mod_link', $modules)}
                      {$sale = module('mod_link')->getLinkByPage($item.id)}
                      {if $sale && $sale->getPermanent() == false}
                        {$time_left = tpl_date_dif($sale->getActiveTo())}
                        <div class="sales-post__cell sales-post__cell--w70 sales-post__cell--ta-center">
                          <div class="sales-post__headline">{tlang("Left")}</div>
                          <time class="sales-post__days sales-post__days--big"
                                datetime="{echo $time_left->days}">
                            {echo $time_left->days}
                          </time>
                          <div class="sales-post__days">{echo SStringHelper::Pluralize($time_left->format('%d'), array(tlang('pluralize day 1'), tlang('pluralize day 2'), tlang('pluralize day 3')))}</div>
                        </div>
                      {/if}
                    {/if}
                    <div class="sales-post__cell">
                      {if $sale && $sale->getPermanent() == false}
                        <div class="sales-post__headline">
                          <i class="sales-post__icon">
                            <svg class="svg-icon">
                              <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__calendar"></use>
                            </svg>
                          </i>
                          <span>{tlang("From")}</span>
                          <time datetime="{date('d-m-y', $sale->getActiveFrom())}">
                            {tpl_locale_date('d F', $sale->getActiveFrom())}
                          </time>
                          <span>{tlang("to")}</span>
                          <time datetime="{date('d-m-y', $sale->getActiveTo())}">
                            {tpl_locale_date('d F', $sale->getActiveTo())}
                          </time>
                        </div>
                      {/if}
                      <h2 class="sales-post__title">
                        {$item.title}
                      </h2>
                    </div>
                  </div>
                </a>
              </article>

            </div><!-- /.col -->
          {/foreach}
        </div><!-- /.row -->
      {else:}
        <p class="typo">{tlang('There are no items to display. Please come back later!')}</p>
      {/if}

      <!-- Category pagination BEGIN -->
      {if $pagination}
        <div class="content__pagination">
          {$pagination}
        </div>
      {/if}
    </div><!-- /.content__row -->

  </div><!-- /.content__container -->
</div><!-- /.content -->