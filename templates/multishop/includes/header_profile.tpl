<div class="user-panel__item">
  <button class="user-panel__link">
    <span>{tlang('Profile')}</span>
    <i class="user-panel__icon">
      <svg class="svg-icon svg-icon--caret"
           aria-hidden="true">
        <use xlink:href="#svg-icon__caret-down"></use>
      </svg>
    </i>
  </button>
  <div class="user-panel__drop user-panel__drop--rtl">
    <div class="overlay">

      {if !$CI->dx_auth->is_logged_in()}
        <!-- User auto menu. Visible when user is not authorized -->
        <div class="overlay__item">
          <a class="overlay__link"
             href="{site_url('auth')}"
             data-modal
             rel="nofollow"
          >{tlang('Sign in')}</a>
        </div>
        <div class="overlay__item">
          <a class="overlay__link"
             href="{site_url('auth/register')}"
             rel="nofollow">{tlang('Create Account')}</a>
        </div>
      {else:}
        <!-- User profile menu. Visible when user is logged in -->
        <div class="overlay__item">
          <a class="overlay__link"
             href="{shop_url('profile')}" rel="nofollow">{tlang('Your Account')}</a>
        </div>
        {if array_key_exists('pricespy', $modules)}
          <div class="overlay__item">
            <a class="overlay__link" href="{site_url('pricespy')}" rel="nofollow">{tlang('Tracking price')}</a>
          </div>
        {/if}
        <div class="overlay__item">
          <a class="overlay__link"
             href="{site_url('auth/change_password')}" rel="nofollow">{tlang('Change Password')}</a>
        </div>
        <div class="overlay__item">
          <a class="overlay__link"
             href="{site_url('auth/logout')}" rel="nofollow">{tlang('Sign out')}</a>
        </div>
      {/if}
    </div>
  </div>
</div>