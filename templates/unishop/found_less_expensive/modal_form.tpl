<div class="modal modal--sm">

  <!-- Modal Header -->
  {view('includes/modal/modal_header.tpl', [
  'title' => tlang('Found cheaper?')
  ])}

  <form action="{site_url('found_less_expensive/save_message')}" method="post" data-found-cheaper>

    <!-- Modal Content -->
    <div class="modal__content">

      <!-- Validation errors -->
      <div class="modal__content-cell hidden"
           data-found-cheaper-errors-frame>
        <div class="message message--error">
          <div class="message__list"
               data-found-cheaper-errors-list></div>
        </div>
      </div>

      <!-- Form submit success -->
      <div class="typo hidden"
           data-found-cheaper-success-message>{tlang('Your message has been sent')}</div>

      <!-- Form -->
      <div class="form"
           data-found-cheaper-form-fields>
        <!-- User Name field -->
        {view('includes/forms/input-base.tpl', [
        'label' => tlang('Name'),
        'type' => 'text',
        'name' => 'name',
        'value' => get_value('name'),
        'required' => true
        ])}
        <!-- User Phone field -->
        {view('includes/forms/input-base.tpl', [
        'label' => tlang('Phone number'),
        'type' => 'text',
        'name' => 'phone',
        'value' => get_value('phone'),
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
        <!-- Product link field -->
        {view('includes/forms/input-base.tpl', [
        'label' => tlang('Product URL'),
        'type' => 'text',
        'name' => 'link',
        'value' => get_value('link'),
        'desc' => tlang('Web site url where you find it cheaper'),
        'required' => true
        ])}
        <!-- Message -->
        {view('includes/forms/textarea-base.tpl', [
        'label' => tlang('Message'),
        'name' => 'question',
        'value' => get_value('question'),
        'rows' => 6,
        'required' => false
        ])}
      </div>

    </div><!-- \.modal__content -->

    <!-- Modal Footer -->
    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset"
                  data-modal-close
          >{tlang('Close')}</button>
        </div>
        <div class="modal__footer-btn"
             data-found-cheaper-submit-btn>
          <input class="btn btn-primary" type="submit" value="{tlang('Send')}">
        </div>
      </div>
    </div>
    <input type="hidden" name="productUrl" value="{echo $url}">
    {form_csrf()}
  </form><!-- \.modal__container -->
</div><!-- \.modal -->