<div class="modal">

  <!-- Modal Header -->
  {view('includes/modal/modal_header.tpl', [
    'title' => tlang('Feedback')
  ])}

  <form action="{site_url('feedback')}" method="post" data-form-ajax="feedback_modal">
    
    <!-- Modal Content -->
    <div class="modal__content">
      {if $message_sent == 1}
        <div class="typo">{tlang('Your message has been sent')}</div>
      {else:}
        <div class="form">      
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
          <!-- Message -->
          {view('includes/forms/textarea-base.tpl', [
            'label' => tlang('Message'),
            'name' => 'message',
            'value' => get_value('message'),
            'rows' => 6,
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
      {/if}
    </div><!-- \.modal__content -->
    
    <!-- Modal Footer -->
    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset" 
            data-modal-close
          >{tlang('Close')}</button>
        </div>
        {if $message_sent != 1}
        <div class="modal__footer-btn">
          <input class="btn btn-primary" type="submit" value="{tlang('Send')}">
        </div>
        {/if}
      </div>
    </div>

    <input type="hidden" name="template" value="feedback_modal">
    {form_csrf()}
  </form><!-- \.modal__container -->
</div><!-- \.modal -->