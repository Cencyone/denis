<div class="modal modal--sm">
  
  {view('includes/modal/modal_header.tpl', [
    'title' => tlang('Sign in')
  ])}

  <form action="{site_url('auth')}" method="post" data-profile-ajax="login_modal">

    <div class="modal__content">
      
      <!-- Error Message if user was not found -->
      {if $info_message}
      <div class="modal__content-cell">
        <div class="message message--error">{$info_message}</div>
      </div>
      {/if}
      
      <!-- Message if you adding item to wishlist and not authorised -->
      {if strip_tags(trim($_REQUEST['wishlist'])) and !$success}
      <div class="modal__content-cell">
        <div class="typo">{tlang('You must login to add items to your wishlist')}</div>
      </div>
      {/if}
      
      <!-- Login form -->
      {if !$success}
        <div class="form">

          <!-- Login in via Social Networks -->
          {if array_key_exists('socauth', $modules)}
            <div class="form__field">
              <div class="form__label">{tlang('Sign in with')}</div>
              <div class="form__inner">
                {module('socauth')->renderLogin();}
              </div>
            </div>
          {/if}

          <!-- User Email field -->
          {view('includes/forms/input-base.tpl', [
            'label' => tlang('E-mail'),
            'type' => 'email',
            'name' => 'email',
            'value' => get_value('email'),
            'required' => true
          ])}

          <!-- User Password field -->
          {view('includes/forms/input-base.tpl', [
            'label' => tlang('Password'),
            'type' => 'password',
            'name' => 'password',
            'value' => '',
            'required' => true
          ])}

          <!-- Captcha field -->
          {if $cap_image}
            {view('includes/forms/input-captcha.tpl', [
              'label' => tlang('Security code'),
              'type' => 'text',
              'name' => 'captcha',
              'value' => get_value('captcha'),
              'required' => true,
              'desc' => tlang('Type the characters you see in this image.')
            ])}
          {/if}

        </div>
      {else:}
        <div class="typo">
          <h3>{tlang('Welcome!')}</h3>
          <p>{tlang('You have successfully authenticated as ')} {echo $CI->dx_auth->get_username()}</p>
        </div>
      {/if}
    </div><!-- \.modal__content -->

    <!-- Modal Footer -->
    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset" data-modal-close>{tlang('Close')}</button>
        </div>
        <div class="modal__footer-btn">
          {if !$success}
          <input class="btn btn-primary" type="submit" value="{tlang('Log in')}">
          {else:}
          <button class="btn btn--va-m btn-primary" data-profile-button type="button">
            <span>{tlang('Logining in...')}</span>
            <i class="button--loader hidden"
               data-button-loader="loader"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__refresh"></use></svg></i>
          </button>
          {/if}
        </div>
      </div>
      {if !$success}
      <div class="modal__footer-row">
        <div class="typo">{tlang("Don't Have an Account?")}<br><a class="form__link" href="{site_url('auth/register')}">{tlang('Create Account')}</a> / <a class="form__link" href="{site_url('auth/forgot_password')}">{tlang('Forgot password?')}</a></div>
      </div>
      {/if}
    </div>
  
    <input type="hidden" name="template" value="login_popup">
    {if strip_tags(trim($_REQUEST['wishlist']))}
    <input type="hidden" name="wishlist" value="{strip_tags(trim($_REQUEST['wishlist']))}" data-profile-wishlist="{strip_tags(trim($_REQUEST['wishlist']))}">
    {/if}
    {form_csrf()}

  </form>
</div>