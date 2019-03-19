<div class="product-sales">
  {foreach $posts as $post}
      {$post_data = $post->getPageData()}
      {$time_left = tpl_date_dif($post->getActiveTo())}
      <div class="product-sales__item">
        <div class="row">
          <div class="col-md-8">
            <div class="product-sales__title">
              <a class="product-sales__link" href="{site_url($post_data['full_url'])}"
                 target="_blank">{echo $post_data['title']}</a>
            </div>
            <div class="product-sales__desc">
              {echo $post_data['prev_text']}
              <a class="product-sales__read-more" href="{site_url($post_data['full_url'])}"
                 target="_blank">{tlang('Read more')}</a>
            </div>
          </div>
          {if $post->getPermanent() == false}
          <div class="col-md-4 col--spacer-md">
            <!-- Countdown, is shown if mod_link is active -->
            <div class="countdown-product" data-countdown="{date('c', $post->getActiveTo())}">
              <div class="countdown-product__row">
                <div class="countdown-product__title">{tlang('Expires after')}</div>
              </div>
              <div class="countdown-product__row">
                <div class="countdown-product__item countdown-product__item--no-marker"
                     data-countdown-item="days">
                  {echo $time_left->format('%d')}
                </div>
                <div class="countdown-product__item countdown-product__item--label countdown-product__item--no-marker">
                  {echo SStringHelper::Pluralize($time_left->format('%d'), array(tlang('pluralize day 1'), tlang('pluralize day 2'), tlang('pluralize day 3')))}
                </div>
              </div>
              <div class="countdown-product__row">
                <div class="countdown-product__item"
                     data-countdown-item="hours">
                  {echo $time_left->format('%h')}
                </div>
                <div class="countdown-product__item"
                     data-countdown-item="minutes">
                  {echo $time_left->format('%i')}
                </div>
                <div class="countdown-product__item countdown-product__item--no-marker"
                     data-countdown-item="seconds">
                  {echo $time_left->format('%s')}
                </div>
              </div>
            </div>
            <!-- /.countdown-product -->
          </div>
          {/if}
        </div>
      </div>
  {/foreach}
</div>