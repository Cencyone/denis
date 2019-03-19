{if trim(siteinfo('mainphone')) != ""}
  <div class="site-info">
    <div class="site-info__aside hidden-xs">
      <div class="site-info__icon">
        <svg class="svg-icon">
          <use xlink:href="#svg-icon__phone"></use>
        </svg>
      </div>
    </div>
    <div class="site-info__inner">
      <div class="site-info__title">{nl2br(siteinfo('mainphone'))}</div>
      <div class="site-info__desc">
        <a class="site-info__link"
           href="{site_url('callbacks')}"
           data-modal="callbacks_modal"
           rel="nofollow"
        >
          {tlang('Callback')}
        </a>
      </div>
    </div>
  </div>
{/if}