<div class="modal modal--sm">

  <!-- Modal Header -->
  {view('includes/modal/modal_header.tpl', [
  'title' => tlang('Buy in one click')
  ])}

  <form action="{site_url('one_click_order/make_order')}" method="post" data-one-click-form>

    <!-- Modal Content -->
    <div class="modal__content">

      <!-- Validation errors -->
      <div class="modal__content-cell">
        <div class="message message--error hidden" data-one-click-validation-list>{$info_message}</div>
      </div>

      <!-- Success form submit message -->
      <div class="modal__content-cell hidden" data-one-click-success>
        <div class="typo">
          {tlang('Thank you for your order! Our manager will contact you as soon as possible.')}
        </div>
      </div>

      <div class="form" data-one-click-fields>

        <!-- Name field -->
        {if $settings['name_input']}
          {view('includes/forms/input-base.tpl', [
          'label' => tlang('Name'),
          'type' => 'text',
          'name' => 'name',
          'value' => get_value('name'),
          'required' => true
          ])}
        {else:}
          <input type="hidden" name="name" value="oneclick-order">
        {/if}

        <!-- E-mail field -->
        {if $settings['e-mail_input']}

          {if $CI->dx_auth->get_user_email()}
            <div class="form__field">
              <div class="form__label">{tlang('E-mail')}: {echo $CI->dx_auth->get_user_email()}</div>
              <input type="hidden" name="mail" value="{echo $CI->dx_auth->get_user_email()}">
            </div>
          {else:}
            {view('includes/forms/input-base.tpl', [
            'label' => tlang('E-mail'),
            'type' => 'email',
            'name' => 'mail',
            'value' => get_value('mail'),
            'required' => true
            ])}
          {/if}

        {else:}
          <input type="hidden" name="mail" value="oneclick@noemail.com">
        {/if}

        <!-- Phone field -->
        {if $settings['phone_input']}
          {view('includes/forms/input-base.tpl', [
          'label' => tlang('Phone number'),
          'type' => 'text',
          'name' => 'phone',
          'value' => get_value('phone'),
          'required' => true
          ])}
        {/if}

        <!-- Message -->
        {if $settings['comment_input']}
          {view('includes/forms/textarea-base.tpl', [
          'label' => tlang('Message'),
          'name' => 'comment',
          'value' => get_value('comment'),
          'rows' => 4,
          'required' => false
          ])}
        {/if}

      </div>

    </div><!-- /.modal__content -->

    <!-- Modal Footer -->
    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset" data-modal-close>{tlang('Close')}</button>
        </div>
        <div class="modal__footer-btn" data-one-click-submit>
          <input class="btn btn-primary" type="submit" value="{tlang('Send')}">
        </div>
      </div>
    </div>

    <input type="hidden" name="variant_id" value="{echo $variant_id}">
    {form_csrf()}
  </form>
</div><!-- /.modal -->