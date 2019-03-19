<div class="product-sales">
  {foreach $posts as $post}
      {$post_data = $post->getPageData()}
      {$time_left = tpl_date_dif($post->getActiveTo())}
      <div class="product-sales__item">
        <div class="product-sales__cell">
          <h3 class="product-sales__title">
            <a class="product-sales__link"
               href="{site_url($post_data['full_url'])}">{echo $post_data['title']}</a>
          </h3>

          {if $post_data['prev_text']}
            <div class="product-sales__info">
              <div class="typo">
                {echo $post_data['prev_text']}
              </div>
            </div>
          {/if}

        </div>
        {if $post->getPermanent() == false}
        <div class="product-sales__cell product-sales__cell--accent">
          <!-- Countdown, is shown if mod_link is active -->
          <div class="countdown-product"
               data-countdown="{date('c', $post->getActiveTo())}">

            <h3 class="countdown-product__title">{tlang('Expires after')}</h3>
            <div class="countdown-product__time">

              <div class="countdown-product__time-row">
                <span class="countdown-product__item countdown-product__item--no-marker"
                      data-countdown-item="days">
                  {echo $time_left->days}
                </span>
                <span class="countdown-product__item countdown-product__item--small">
                  {echo SStringHelper::Pluralize($time_left->days, array(tlang('pluralize day 1'), tlang('pluralize day 2'), tlang('pluralize day 3')))}
                </span>
              </div>
              <div class="countdown-product__time-row">
                <span class="countdown-product__item"
                     data-countdown-item="hours">{echo $time_left->format('%H')}</span>
                <span class="countdown-product__item"
                     data-countdown-item="minutes">{echo $time_left->format('%I')}</span>
                <span class="countdown-product__item"
                     data-countdown-item="seconds">{echo $time_left->format('%S')}</span>
              </div>
            </div>
          </div>
          <!-- /.countdown-product -->
        </div>
        {/if}
      </div>
  {/foreach}
</div>