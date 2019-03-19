{$loc_href = $wish_list_id ? "/wishlist/moveItem/".$varId . "/" . $wish_list_id : "/wishlist/addItem/".$varId}

<div class="modal modal--sm">
  {view('includes/modal/modal_header.tpl', [
    'title' => tlang('Add to your list')
  ])}
  
  <form action="{site_url($loc_href)}" method="post" data-wishlist-ajax="{if $wish_list_id}move{else:}add{/if}">
    
    <div class="modal__content">

      {if $errors}
      <div class="modal__content-cell">
        {foreach $errors as $error}
        <div class="message message--error">{$error}</div>
        {/foreach}
      </div>
      {/if}
      
      <div class="modal__content-cell">
        
        {if !$success}
        <div class="form">
          <!-- Select from existing lists -->
          {if count($wish_lists) > 0}
            {view('includes/forms/checkgroup.tpl', [
              'label' => tlang('Select a list'),
              'name' => 'wishlist',
              'type' => 'radio',  
              'data' => [$wish_lists, 'id', 'title'],
              'required' => false,
              'disabled' => $wish_list_id
            ])}
          {/if}
          <!-- Create new list field -->
          <div class="form__field">
            <div class="form__label">{tlang('Create a new list')}</div>
            <div class="form__inner">
              <label class="form__checkbox"
                data-wishlist-new-scope>
                <span class="form__checkbox-field">
                  <input type="radio" name="wishlist" value=""
                    data-wishlist-new-radio>
                </span>
                <span class="form__checkbox-inner">
                  <span class="form__checkbox-title">
                    <input class="form-control" type="text" name="wishListName"  value=""
                      data-wishlist-new-input>
                  </span>
                </span>
              </label>              
            </div>
          </div>
        </div><!-- /.form -->
        {else:}
        <div class="typo">
          {tlang('Item successfuly added to your Wishlist!')}
        </div>
        {/if}

      </div><!-- /.modal__content-cell -->        
    </div><!-- /.modal__content -->

    <div class="modal__footer">
      <div class="modal__footer-row">
        <div class="modal__footer-btn hidden-xs">
          <button class="btn btn-default" type="reset" 
            data-modal-close
          >{tlang('Close')}</button>
        </div>
        <div class="modal__footer-btn">
          {if !$success}
            {if !$wish_list_id}
              <button class="btn btn-primary" type="submit">{tlang('Add to list')}</button>
            {else:}
              <button class="btn btn--va-m btn-primary" type="submit" data-wishlist-move-loader>
                <span>{tlang('Change list')}
                  <i class="button--loader hidden"
                     data-button-loader="loader"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__refresh"></use></svg></i>
              </button>
            {/if}
          {else:}
          <a class="btn btn--va-m btn-primary" href="{site_url('wishlist')}"
            data-button-loader="button"
          >
            <span>{tlang('Go to wishlist')}</span>
            <i class="button--loader hidden"
               data-button-loader="loader"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__refresh"></use></svg></i>
          </a>
          {/if}
        </div>
      </div>
    </div><!-- /.modal__footer -->

    <div class="hidden" data-ajax-grab="wishlist-total">
      {view('wishlist/wishlist_total.tpl')}
    </div>

    {if $success}
      <div class="hidden" data-ajax-grab="wishlist-link-{$varId}">
        {module('wishlist')->renderWLButton($varId, ['type' => 'link'])}
      </div>
      <div class="hidden" data-ajax-grab="wishlist-btn-{$varId}">
        {module('wishlist')->renderWLButton($varId, ['type' => 'button'])}
      </div>
    {/if}

  {form_csrf()}
  </form>
</div>