{$sale = array_key_exists('mod_link', $modules) ? module('mod_link')->getLinkByPage($page.id) : null}
<div class="content">
  <div class="content__container">

    {if !($sale && $sale->getPermanent() == false)}
      <div class="content__header">
        <h1 class="content__title">{$page.title}</h1>
      </div>
    {/if}
    <!-- Post title -->
    <div class="content__row">
      <div class="row">
        {$img = !trim($page.field_list_image) ? 'uploads/shop/nophoto/nophoto.jpg' : $page.field_list_image}
        <div class="col-xs-12 col-md-6">
          <img src="{media_url($img)}"
                alt="{$page_title}"
                class="content__img">
        </div>        
        {if $sale && $sale->getPermanent() == false}
          <div class="col-xs-12 col-md-6">
            <h1 class="content__title">
              {$page.title}
            </h1>
            <!-- Countdown, is shown if mod_link is active -->

            {$time_left = tpl_date_dif($sale->getActiveTo())}
            <section class="countdown-post"
                     data-countdown="{date('c', $sale->getActiveTo())}">
              <header class="countdown-post__header">
                <h2 class="countdown-post__title">{tlang('Sale expires after')}:</h2>
              </header>
              <div class="countdown-post__content">
                <div class="countdown-post__cell">
                  <div class="countdown-post__number">
                    <div data-countdown-item="days">{echo $time_left->days}</div>
                    <div class="countdown-post__marker">{echo SStringHelper::Pluralize($time_left->format('%d'), array(tlang('pluralize day 1'), tlang('pluralize day 2'), tlang('pluralize day 3')))}</div>
                  </div>

                </div>
                <div class="countdown-post__cell">
                  <div class="countdown-post__number">
                    <div data-countdown-item="hours">{echo $time_left->format('%h')}</div>
                    <div class="countdown-post__marker">{echo SStringHelper::Pluralize($time_left->format('%h'), array(tlang('pluralize hour 1'), tlang('pluralize hour 2'), tlang('pluralize hour 3')))}</div>
                  </div>
                </div>
                <div class="countdown-post__cell">
                  <div class="countdown-post__number">
                    <div data-countdown-item="minutes">{echo $time_left->format('%i')}</div>
                    <div class="countdown-post__marker">{tlang("minutes")}</div>
                  </div>
                </div>
                <div class="countdown-post__cell">
                  <div class="countdown-post__number">
                    <div data-countdown-item="seconds">{echo $time_left->format('%S')}</div>
                    <div class="countdown-post__marker">{tlang("seconds")}</div>
                  </div>
                </div>
              </div>
              <footer class="countdown-post__footer">
                <i class="countdown-post__icon hidden-xs">
                  <svg class="svg-icon">
                    <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__calendar"></use>
                  </svg>
                </i>
                <span class="countdown-post__basement"> <span>{tlang("Sales period")}:</span>
                  <span>{tlang("From")}</span>
                  <time datetime="{date('d-m-y', $sale->getActiveFrom())}">{tpl_locale_date('d F', $sale->getActiveFrom($sale->active_from))}</time>
                  <span>{tlang("to")}</span>
                  <time datetime="{date('d-m-y', $sale->getActiveTo())}">{tpl_locale_date('d F Y', $sale->getActiveTo($sale->active_from))}</time>
                  <span>{tlang("year")}</span>
              </footer>
            </section>
            <!-- /.countdown-post -->
          </div>
        {/if}
      </div>
    </div>


    <!-- Post description -->
    <div class="content__row">
      <section class="frame-content">
        <div class="frame-content__header">
          <h2 class="frame-content__title">{tlang('Description')}</h2>
        </div>
      </section>
      <div class="typo">{$page.full_text}</div>
    </div>

    <!-- Product list, attached to the post via mod_link module -->
    {if $sale && $sale_products = $sale->getLinkedProducts()}
      <div class="content__row">
        <section class="frame-content">
          <div class="frame-content__header">
            <h2 class="frame-content__title">{tlang('Sales product list')}</h2>
          </div>
          <div class="frame-content__inner">
            <div class="row row--ib row--vindent-m">
              {foreach $sale_products as $product}
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                  {view('shop/includes/product/product_cut.tpl', array(
                  'model' => $product
                  ))}
                </div>
              {/foreach}
            </div>
          </div>
        </section>
      </div>
    {/if}

    <!-- Comments -->
    {if $page.comments_status == 1}
      <div class="content__row">
        <section class="frame-content">
          <div class="frame-content__header">
            <h2 class="frame-content__title">{tlang('Comments')}</h2>
          </div>
          <div class="frame-content__inner"
               data-comments>
            {tpl_load_comments()}
          </div>
        </section>
      </div>
    {/if}

  </div><!-- /.content__container -->
</div><!-- /.content -->