<div class="modal">
  
  <!-- Modal Header -->
  {view('includes/modal/modal_header.tpl', [
    'title' => tlang('In stock reminder')
  ])}

  <form action="{shop_url('ajax/getNotifyingRequest')}" method="post" data-form-ajax="notify_modal">

    <!-- Modal Content -->
    <div class="modal__content">
      {if !$message}
        <div class="modal__content-cell">
          <div class="typo">{tlang("You'll receive a one email when this product arrives in stock")}</div>
        </div>
        <div class="form">

          <!-- User Name field -->
        {view('includes/forms/input-base.tpl', [
          'label' => tlang('Name'),
          'type' => 'text',
          'name' => 'UserName',
          'value' => $user_name,
          'required' => true
        ])}
        
        <!-- User Email field -->
        {view('includes/forms/input-base.tpl', [
          'label' => tlang('E-mail'),
          'type' => 'email',
          'name' => 'UserEmail',
          'value' => $user_email,
          'required' => true
        ])}

        </div>
      {else:}
        <div class="typo">{$message}</div>
      {/if}
    </div><!-- /.modal__content -->

    <!-- Modal Footer -->
    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset" data-modal-close>{tlang('Close')}</button>
        </div>
        {if !$message}
        <div class="modal__footer-btn">
          <input class="btn btn-primary" type="submit" value="{tlang('Email when available')}">
        </div>
        {/if}
      </div>
    </div>

    <input type="hidden" name="ProductId" value="{echo tpl_encode($CI->input->post('ProductId')) ? : tpl_encode($CI->input->get('ProductId'))}">
    <input type="hidden" name="VariantId" value="{echo tpl_encode($CI->input->post('VariantId')) ? : tpl_encode($CI->input->get('VariantId'))}">
    <input type="hidden" name="notifme" value="true">
    {form_csrf()}
  </form>
</div><!-- /.modal -->