<li class="tree-nav__item" {if $wrapper}data-global-doubletap{/if} data-nav-setactive-item>
  <a class="tree-nav__link" href="{$link}" data-nav-setactive-link>{$title}
    {if $wrapper}
      <i class="tree-nav__arrow tree-nav__arrow--right">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__arrow-right"></use>
        </svg>
      </i>
    {/if}
  </a>
  {$wrapper}
</li>