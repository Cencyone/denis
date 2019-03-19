{$sale = array_key_exists('mod_link', $modules) ? module('mod_link')->getLinkByPage($page.id) : null}

<div class="content">
  <div class="content__container">

    <!-- Post title -->
    <div class="content__header">
      <h1 class="content__title">
        {$page.title}
      </h1>
    </div>

    <!-- Countdown, is shown if mod_link is active -->
    {if $sale && $sale->getPermanent() == false}
      <div class="content__row content__row--sm">
        {$time_left = tpl_date_dif($sale->getActiveTo())}

        <div class="countdown-post" data-countdown="{date('c', $sale->getActiveTo())}">
          <div class="countdown-post__title">{tlang('Expires after')}:</div>
          <div class="countdown-post__date">
            <div class="countdown-post__item countdown-post__item--no-marker"
                 data-countdown-item="days">
              {echo $time_left->format('%d')}
            </div>
            <div class="countdown-post__label">
              {echo SStringHelper::Pluralize($time_left->format('%d'), array(tlang('pluralize day 1'), tlang('pluralize day 2'), tlang('pluralize day 3')))}
            </div>
            <div class="countdown-post__item"
                 data-countdown-item="hours">
              {echo $time_left->format('%h')}
            </div>
            <div class="countdown-post__item"
                 data-countdown-item="minutes">
              {echo $time_left->format('%i')}
            </div>
            <div class="countdown-post__item countdown-post__item--no-marker"
                 data-countdown-item="seconds">
              {echo $time_left->format('%s')}
            </div>
          </div>
        </div><!-- /.countdown-post -->

      </div>
    {/if}

    <!-- Post description -->
    <div class="content__row">
      <div class="typo">{$page.full_text}</div>
    </div>

    <!-- Sales module -->
    {if $sale && $sale_products = $sale->getLinkedProducts()}
      <div class="content__row">
        <section class="frame-content">
          <div class="frame-content__header">
            <h2 class="frame-content__title">{tlang('Product list')}</h2>
          </div>
          <div class="frame-content__inner">
            <div class="row row--ib row--vindent-m">
              {foreach $sale_products as $product}
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
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
          <div class="frame-content__inner" data-comments>
            {tpl_load_comments()}
          </div>
        </section>
      </div>
    {/if}

  </div><!-- /.content__container -->
</div><!-- /.content -->