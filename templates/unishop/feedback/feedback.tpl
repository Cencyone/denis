<div class="content">
  <div class="content__container">

    <div class="content__header">
      <h1 class="content__title">
        {tlang('Feedback')}
      </h1>
    </div>
    <div class="content__row">
      <div class="row">
        <div class="col-sm-7">
          <form class="form" action="{site_url('feedback')}" method="post">

            {if $message_sent == 1}
              <div class="form__messages">
                <p class="message message--success">{tlang('Your message has been sent')}</p>
              </div>
            {else:}

              <!-- User Name field -->
              {view('includes/forms/input-base.tpl', [
              'label' => tlang('Name'),
              'type' => 'text',
              'name' => 'name',
              'value' => get_value('name'),
              'required' => true
              ])}


              <!-- User E-mail field -->
              {view('includes/forms/input-base.tpl', [
              'label' => tlang('E-mail'),
              'type' => 'email',
              'name' => 'email',
              'value' => get_value('email'),
              'required' => true
              ])}


              <!-- Subject field -->
              {view('includes/forms/input-base.tpl', [
              'label' => tlang('Subject'),
              'type' => 'text',
              'name' => 'theme',
              'value' => get_value('theme')
              ])}


              <!-- Message -->
              {view('includes/forms/textarea-base.tpl', [
              'label' => tlang('Message'),
              'name' => 'message',
              'value' => get_value('message'),
              'rows' => 15,
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
                  <input class="btn btn-primary" type="submit" value="{tlang('Send')}">
                </div>
              </div>
            {/if}

            {form_csrf()}
          </form>
        </div>
        <div class="col-sm-5"></div>
      </div>
    </div>

  </div><!-- /.content__container -->
</div><!-- /.content -->