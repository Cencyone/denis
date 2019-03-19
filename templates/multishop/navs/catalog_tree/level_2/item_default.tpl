<li class="tree-nav__item"
    {if $wrapper}data-global-doubletap{/if}>
  <a class="tree-nav__link"
     href="{$link}">
    <span>{$title}</span>
    {if $wrapper}
      <i class="tree-nav__arrow">
        <svg class="svg-icon svg-icon--caret">
          <use xlink:href="#svg-icon__caret-right"></use>
        </svg>
      </i>
    {/if}
  </a>
  {$wrapper}
</li>