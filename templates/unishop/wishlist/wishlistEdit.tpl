<div class="modal modal--sm">
  <form action="{site_url('wishlist/wishlistApi/updateWL')}" method="post"
    data-wishlist-edit="wishlist_update"
    data-ajax-request="wishlist_update"
    data-ajax-loader="wishlist_update"
    data-ajax-response="wishlist_update">
    {view('includes/modal/modal_header.tpl', [
      'title' => tlang('Edit your list')
    ])}
    <div class="modal__content">
      
      <div class="modal__content-cell hidden" data-wishlist-edit--error>
        <div class="message message--error" data-wishlist-edit--error-message></div>
      </div>
      
      <div class="modal__content-cell">
        <div class="form">
          {foreach $wishlists as $wishlist}

            {view('includes/forms/input-base.tpl', [
              'label' => tlang('List name'),
              'type' => 'test',
              'name' => 'title',
              'value' => $wishlist['0']['title'],
              'required' => true
            ])}

            <input type="hidden" name="WLID" value="{echo $wishlist['0']['wish_list_id']}">
            <input type="hidden" name="access" value="shared">        
          {/foreach}
        </div>
      </div>

    </div><!-- /.modal__content -->
    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset" data-modal-close>{tlang('Close')}</button>
        </div>
        <div class="modal__footer-btn">
          <button class="btn btn--va-m btn-primary" type="submit" data-wishlist-edit--button>
            <span>{tlang('Edit list')}</span>
            <i class="button--loader hidden"
               data-button-loader="loader"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__refresh"></use></svg></i>
          </button>
        </div>
      </div>
    </div><!-- /.modal__footer -->
  </form>
</div><!-- /.modal -->