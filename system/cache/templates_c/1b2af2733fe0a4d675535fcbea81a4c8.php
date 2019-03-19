<ul class="footer__items">
  <?php if(!$CI->dx_auth->is_logged_in()): ?>
    <li class="footer__item">
      <a class="footer__link" href="<?php echo site_url ('auth'); ?>" data-modal rel="nofollow"><?php echo tlang ('Sign in'); ?></a>
    </li>
    <li class="footer__item">
      <a class="footer__link" href="<?php echo site_url ('auth/register'); ?>" rel="nofollow"><?php echo tlang ('Create Account'); ?></a>
    </li>
  <?php else:?>
    <li class="footer__item">
      <a class="footer__link" href="<?php echo shop_url ('profile'); ?>" rel="nofollow"><?php echo tlang ('Your Account'); ?></a>
    </li>
    <li class="footer__item">
      <a class="footer__link" href="<?php echo site_url ('auth/change_password'); ?>" rel="nofollow"><?php echo tlang ('Change Password'); ?></a>
    </li>
    <li class="footer__item">
      <a class="footer__link" href="<?php echo site_url ('wishlist'); ?>" rel="nofollow"><?php echo tlang ('Wishlist'); ?></a>
    </li>
    <?php if(array_key_exists('pricespy', $modules)): ?>
      <li class="footer__item">
        <a class="footer__link" href="<?php echo site_url ('pricespy'); ?>" rel="nofollow"><?php echo tlang ('Tracking price'); ?></a>
      </li>
    <?php endif; ?>
  <?php endif; ?>
  <li class="footer__item">
    <a class="footer__link" href="<?php echo shop_url ('compare'); ?>" rel="nofollow"><?php echo tlang ('Compare'); ?></a>
  </li>
  <li class="footer__item">
    <a class="footer__link" href="<?php echo site_url ('callbacks'); ?>" data-modal="callbacks_modal"
       rel="nofollow"><?php echo tlang ('Callback'); ?></a>
  </li>
  <?php if($CI->dx_auth->is_logged_in()): ?>
    <li class="footer__item">
      <a class="footer__link" href="<?php echo site_url ('auth/logout'); ?>" rel="nofollow"><?php echo tlang ('Sign out'); ?></a>
    </li>
  <?php endif; ?>
</ul><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903754; ///home/user2263/public_html/templates/unishop/includes/footer_profile.tpl ?>