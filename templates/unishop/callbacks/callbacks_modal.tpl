<div class="modal modal--sm">
  
  <!-- Modal Header -->
  {view('includes/modal/modal_header.tpl', [
    'title' => tlang('Request a Call back')
  ])}

  <form action="{site_url('callbacks')}" method="post" data-form-ajax="callback_modal">

    <!-- Modal Content -->
    <div class="modal__content">
      {if !$success}
        <div class="modal__content-cell">
          <div class="typo">{tlang('Leave a number and we will call you back')}</div>
        </div>
        <div class="form">

          <!-- User Name field -->
          {view('includes/forms/input-base.tpl', [
            'label' => tlang('Name'),
            'type' => 'text',
            'name' => 'Name',
            'value' => get_value('Name'),
            'required' => true
          ])}
          
          <!-- User Phone field -->
          {view('includes/forms/input-base.tpl', [
            'label' => tlang('Phone number'),
            'type' => 'text',
            'name' => 'Phone',
            'value' => get_value('Phone'),
            'required' => true
          ])}

          <!-- Message -->
          {view('includes/forms/textarea-base.tpl', [
            'label' => tlang('Message'),
            'name' => 'Comment',
            'value' => get_value('Comment'),
            'rows' => 4,
            'required' => false
          ])}          

        </div>
      {else:}
        <div class="typo">{$success}</div>
      {/if}
    </div><!-- /.modal__content -->

    <!-- Modal Footer -->
    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset" data-modal-close>{tlang('Close')}</button>
        </div>
        {if !$success}
        <div class="modal__footer-btn">
          <input class="btn btn-primary" type="submit" value="{tlang('Call back')}">
        </div>
        {/if}
      </div>
    </div>

    <input type="hidden" name="template" value="callbacks_modal">
    {form_csrf()}
  </form>
</div><!-- /.modal -->