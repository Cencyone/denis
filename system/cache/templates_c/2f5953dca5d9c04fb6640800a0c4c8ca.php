<ul class="mobile-nav__list mobile-nav__list--drop hidden" data-mobile-nav-list>
  <li class="mobile-nav__item" data-mobile-nav-item>
    <button class="mobile-nav__link mobile-nav__link--go-back" data-mobile-nav-go-back>
      <?php echo tlang ('Go back'); ?>
      <span class="mobile-nav__has-children"><i class="mobile-nav__ico"><svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-right"></use></svg></i></span>
    </button>
  </li>
  <li class="mobile-nav__item hidden" data-mobile-nav-item>
    <a class="mobile-nav__link mobile-nav__link--view-all" href="<?php if(isset($link)){ echo $link; } ?>" data-mobile-nav-viewAll>
      <?php echo tlang ('View all'); ?>
    </a>
  </li>
  <?php if(isset($wrapper)){ echo $wrapper; } ?>
</ul><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903781; ///home/user2263/public_html/templates/unishop//navs/mobile_nav/level_1/container.tpl ?>