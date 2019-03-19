<div class="content">
  <div class="content__container">

    <div class="content__header">
      <h1 class="content__title">
        {tlang('Sign in')}
      </h1>
    </div>
    <div class="content__row">
      <div class="row">
        <div class="col-sm-7">
          <form class="form" action="{site_url('auth')}" method="post">

            <!-- Messages -->
            {if $info_message}
              <div class="form__messages">
                <div class="message message--error">{$info_message}</div>
              </div>
            {/if}

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

            <!-- Submit button -->
            <div class="form__field">
              <div class="form__label"></div>
              <div class="form__inner">
                <input class="btn btn-primary" type="submit" value="{tlang('Log in')}">
              </div>
            </div>

            <!-- Additional info -->
            <div class="form__field">
              <div class="form__label"></div>
              <div class="form__inner">
                <div class="form__desc">{tlang("Don't Have an Account?")}<br><a class="form__link"
                                                                                href="{site_url('auth/register')}">{tlang('Create Account')}</a>
                  / <a class="form__link" href="{site_url('auth/forgot_password')}">{tlang('Forgot password?')}</a>
                </div>
              </div>
            </div>

            {form_csrf()}
          </form>
        </div>
        <div class="col-sm-5"></div>
      </div>
    </div>

  </div><!-- /.content__container -->
</div><!-- /.content -->