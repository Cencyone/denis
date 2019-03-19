{if !$CI->dx_auth->is_logged_in()}
  <li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
    <a class="mobile-nav__link" href="{site_url('auth')}" data-nav-setactive-link>{tlang('Sign in')}</a>
  </li>
  <li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
    <a class="mobile-nav__link" href="{site_url('auth/register')}" data-nav-setactive-link>{tlang('Create Account')}</a>
  </li>
{else:}
  <li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
    <a class="mobile-nav__link" href="{shop_url('profile')}" data-nav-setactive-link>{tlang('Your Account')}</a>
  </li>
  <li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
    <a class="mobile-nav__link" href="{site_url('auth/change_password')}" data-nav-setactive-link>{tlang('Change Password')}</a>
  </li>
{/if}

<li class="mobile-nav__item" data-nav-setactive-item>
  <a class="mobile-nav__link" href="{shop_url('cart')}" data-nav-setactive-link>{tlang('Cart')}</a>
</li>

{if $CI->dx_auth->is_logged_in()}
  <li class="mobile-nav__item" data-nav-setactive-item>
    <a class="mobile-nav__link" href="{site_url('wishlist')}" data-nav-setactive-link>{tlang('Wishlist')}</a>
  </li>
{/if}

<li class="mobile-nav__item" data-nav-setactive-item>
  <a class="mobile-nav__link" href="{shop_url('compare')}" data-nav-setactive-link>{tlang('Compare')}</a>
</li>

{if $CI->dx_auth->is_logged_in() && array_key_exists('pricespy', $modules)}
  <li class="mobile-nav__item" data-nav-setactive-item>
    <a class="mobile-nav__link" href="{site_url('pricespy')}" rel="nofollow" data-nav-setactive-link>{tlang('Tracking price')}</a>
  </li>
{/if}

{if $CI->dx_auth->is_logged_in()}
  <li class="mobile-nav__item" data-nav-setactive-item>
    <a class="mobile-nav__link" href="{site_url('auth/logout')}" data-nav-setactive-link>{tlang('Sign out')}</a>
  </li>
{/if}