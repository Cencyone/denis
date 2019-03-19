<?php if(!$CI->dx_auth->is_logged_in()): ?>
  <li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
    <a class="mobile-nav__link" href="<?php echo site_url ('auth'); ?>" data-nav-setactive-link><?php echo tlang ('Sign in'); ?></a>
  </li>
  <li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
    <a class="mobile-nav__link" href="<?php echo site_url ('auth/register'); ?>" data-nav-setactive-link><?php echo tlang ('Create Account'); ?></a>
  </li>
<?php else:?>
  <li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
    <a class="mobile-nav__link" href="<?php echo shop_url ('profile'); ?>" data-nav-setactive-link><?php echo tlang ('Your Account'); ?></a>
  </li>
  <li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
    <a class="mobile-nav__link" href="<?php echo site_url ('auth/change_password'); ?>" data-nav-setactive-link><?php echo tlang ('Change Password'); ?></a>
  </li>
<?php endif; ?>

<li class="mobile-nav__item" data-nav-setactive-item>
  <a class="mobile-nav__link" href="<?php echo shop_url ('cart'); ?>" data-nav-setactive-link><?php echo tlang ('Cart'); ?></a>
</li>

<?php if($CI->dx_auth->is_logged_in()): ?>
  <li class="mobile-nav__item" data-nav-setactive-item>
    <a class="mobile-nav__link" href="<?php echo site_url ('wishlist'); ?>" data-nav-setactive-link><?php echo tlang ('Wishlist'); ?></a>
  </li>
<?php endif; ?>

<li class="mobile-nav__item" data-nav-setactive-item>
  <a class="mobile-nav__link" href="<?php echo shop_url ('compare'); ?>" data-nav-setactive-link><?php echo tlang ('Compare'); ?></a>
</li>

<?php if($CI->dx_auth->is_logged_in() && array_key_exists('pricespy', $modules)): ?>
  <li class="mobile-nav__item" data-nav-setactive-item>
    <a class="mobile-nav__link" href="<?php echo site_url ('pricespy'); ?>" rel="nofollow" data-nav-setactive-link><?php echo tlang ('Tracking price'); ?></a>
  </li>
<?php endif; ?>

<?php if($CI->dx_auth->is_logged_in()): ?>
  <li class="mobile-nav__item" data-nav-setactive-item>
    <a class="mobile-nav__link" href="<?php echo site_url ('auth/logout'); ?>" data-nav-setactive-link><?php echo tlang ('Sign out'); ?></a>
  </li>
<?php endif; ?><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903755; ///home/user2263/public_html/templates/unishop/includes/mobile_profile.tpl ?>