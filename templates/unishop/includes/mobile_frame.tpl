<nav class="mobile-nav" data-mobile-nav data-nav-setactive-scope>
  <ul class="mobile-nav__list" data-mobile-nav-list>
    {load_catalog_menu('navs/mobile_nav')}
    <li class="mobile-nav__item mobile-nav__item--separator">{tlang('Store')}</li>
    {load_menu('info_mobile')}
    <li class="mobile-nav__item mobile-nav__item--separator">{tlang('User menu')}</li>
    {view('includes/mobile_profile.tpl')}
    {widget('mobile_langs')}
  </ul>
</nav>