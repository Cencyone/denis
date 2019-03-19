{if trim(siteinfo('schedule')) != ""}
  <div class="site-info">
    <div class="site-info__aside hidden-xs">
      <div class="site-info__ico site-info__ico--clock-big">
        <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__clock-big"></use></svg>
      </div>
    </div>
    <div class="site-info__inner">
      <div class="site-info__desc">
        {echo siteinfo('schedule')}
      </div>
    </div>
  </div>
{/if}