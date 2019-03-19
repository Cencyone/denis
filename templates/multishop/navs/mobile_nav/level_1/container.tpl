<ul class="mobile-nav__list mobile-nav__list--drop hidden" data-mobile-nav-list>
  <li class="mobile-nav__item" data-mobile-nav-item>
    <button class="mobile-nav__link mobile-nav__link--go-back" data-mobile-nav-go-back>
      <span>{tlang('Go back')}</span>
      <span class="mobile-nav__has-children"><svg class="svg-icon"><use xlink:href="#svg-icon__angle-left"></use></svg></span>
    </button>
  </li>
  <li class="mobile-nav__item" data-mobile-nav-item>
    <a class="mobile-nav__link mobile-nav__link--view-all" href="{tpl_menu_parent_link($link)}">
      {tlang('View all')}
    </a>
  </li>
  {$wrapper}
</ul>