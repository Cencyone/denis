<li class="overlay__item" {if $wrapper}data-global-doubletap{/if}>
  <a class="overlay__link" href="{$link}" {$target}>{$title}
    {if $wrapper}
      <i class="overlay__arrow overlay__arrow--right"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__arrow-right"></use></svg></i>
    {/if}
  </a>
  {$wrapper}
</li>