<li class="list-nav__item" {if $wrapper}data-global-doubletap{/if}>
  <a class="list-nav__link" href="{$link}" {$target}>{$title}
    {if $wrapper}
      <i class="list-nav__arrow list-nav__arrow--down"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__arrow-down"></use></svg></i>
    {/if}
  </a>
  {$wrapper}
</li>