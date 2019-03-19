<nav class="mobile-nav" data-mobile-nav data-nav-setactive-scope>
  <ul class="mobile-nav__list" data-mobile-nav-list>
    <?php echo load_catalog_menu ('navs/mobile_nav'); ?>
    <li class="mobile-nav__item mobile-nav__item--separator"><?php echo tlang ('Store'); ?></li>
    <?php echo load_menu ('info_mobile'); ?>
    <li class="mobile-nav__item mobile-nav__item--separator"><?php echo tlang ('User menu'); ?></li>
    <?php echo view ('includes/mobile_profile.tpl'); ?>
    <?php echo widget ('mobile_langs'); ?>
  </ul>
</nav><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903755; ///home/user2263/public_html/templates/unishop/includes/mobile_frame.tpl ?>