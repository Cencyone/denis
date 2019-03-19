<li class="list-nav__item"
    {if $wrapper}data-global-doubletap{/if}>
  <a class="list-nav__link"
     href="{$link}" {$target}>
    <span>{$title}</span>
    {if $wrapper}<i class="list-nav__icon" aria-hidden="true"><svg class="svg-icon svg-icon--caret"><use xlink:href="#svg-icon__caret-down"></use></svg></i>{/if}
  </a>
  {$wrapper}
</li>