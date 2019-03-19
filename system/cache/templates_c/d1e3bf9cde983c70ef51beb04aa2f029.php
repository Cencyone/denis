<div class="user-panel__item">
  <span class="user-panel__link">
    <i class="user-panel__ico user-panel__ico--profile">
      <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__user"></use></svg>
    </i>
    <?php echo tlang ('Profile'); ?>
    <i class="user-panel__arrow user-panel__arrow--down">
      <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-down"></use></svg>
    </i>
  </span>
  <div class="user-panel__drop user-panel__drop--rtl">
    <div class="overlay">

      <?php if(!$CI->dx_auth->is_logged_in()): ?>
        <!-- User auto menu. Visible when user is not authorized -->
        <div class="overlay__item">
          <a class="overlay__link" href="<?php echo site_url ('auth'); ?>"
             data-modal
             rel="nofollow"><?php echo tlang ('Sign in'); ?></a>
        </div>
        <div class="overlay__item">
          <a class="overlay__link" href="<?php echo site_url ('auth/register'); ?>" rel="nofollow"><?php echo tlang ('Create Account'); ?></a>
        </div>
      <?php else:?>
        <!-- User profile menu. Visible when user is logged in -->
        <div class="overlay__item">
          <a class="overlay__link" href="<?php echo shop_url ('profile'); ?>" rel="nofollow"><?php echo tlang ('Your Account'); ?></a>
        </div>
        <?php if(array_key_exists('pricespy', $modules)): ?>
          <div class="overlay__item">
            <a class="overlay__link" href="<?php echo site_url ('pricespy'); ?>" rel="nofollow"><?php echo tlang ('Tracking price'); ?></a>
          </div>
        <?php endif; ?>
        <div class="overlay__item">
          <a class="overlay__link" href="<?php echo site_url ('auth/change_password'); ?>"
             rel="nofollow"><?php echo tlang ('Change Password'); ?></a>
        </div>
        <div class="overlay__item">
          <a class="overlay__link" href="<?php echo site_url ('auth/logout'); ?>" rel="nofollow"><?php echo tlang ('Sign out'); ?></a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903755; ///home/user2263/public_html/templates/unishop/includes/header_profile.tpl ?>