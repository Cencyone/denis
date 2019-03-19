<!-- Checking error in main comment, not in answer form -->
{$loc_main_comment_error = !$parent_id && $validation_errors}

<form class="form"
      action="{site_url('comments/addPost')}#comments-anchor"
      method="post"
      data-comments-form="main"
      data-comments-form-url="{site_url('comments/commentsapi/newPost')}"
      data-comments-form-list-url="{site_url('comments/commentsapi/renderPosts')}"
>

  <!-- Messages BEGIN -->
  <div class="form__messages hidden"
       data-comments-error-frame>
    <div class="message message--error">
      <div class="message__list"
           data-comments-error-list>
        {foreach $validation_errors as $message}
          <p class="message__item">{$message}</p>
        {/foreach}
      </div>
    </div>
  </div>

  <div class="form__messages hidden"
       data-comments-success>
    <p class="message message--success">
      {if $use_moderation}
        {tlang('Your comment will be published after moderation')}
      {else:}
        {tlang('Your comment has been successfully published')}
      {/if}
    </p>
  </div>
  <!-- END Messages -->

  <!-- Name field input BEGIN -->
  {if !$is_logged_in}
    {view('includes/forms/input-base.tpl', [
    'label' => tlang('Name'),
    'type' => 'text',
    'name' => 'comment_author',
    'value' => get_value('comment_author'),
    'required' => true,
    'modifier' => 'form__field--hor'
    ])}
  {else:}
    <div class="form__field form__field--hor  form__field--static">
      <div class="form__label">{tlang('Name')}</div>
      <div class="form__inner">{echo $CI->dx_auth->get_username()}</div>
    </div>
  {/if}
  <!-- END Name field input -->


  {if !$is_logged_in}
    <!-- Email field input BEGIN -->
    {view('includes/forms/input-base.tpl', [
    'label' => tlang('E-mail'),
    'type' => 'email',
    'name' => 'comment_email',
    'value' => get_value('comment_email'),
    'required' => true,
    'modifier' => 'form__field--hor'
    ])}
    <!-- END Name field input -->
  {/if}


  <!-- Review field textarea BEGIN -->
  {view('includes/forms/textarea-base.tpl', [
  'label' => tlang('Review'),
  'name' => 'comment_text',
  'value' => get_value('comment_text'),
  'rows' => 5,
  'required' => true,
  'modifier' => 'form__field--hor'
  ])}
  <!-- END Review field textarea -->


  <!-- Rating field BEGIN -->
  <div class="form__field form__field--hor form__field--static"
       data-comments-form-rating>
    <div class="form__label">{tlang('Rating')}</div>
    <div class="form__inner">

      <div class="star-voting">
        <div class="star-voting__wrap">
          {for $i = 5; $i > 0; $i--}
            <input class="star-voting__input"
                   id="star-voting-{$i}"
                   type="radio"
                   name="ratec"
                   value="{$i}" {if $i == $old_ratec} checked{/if}>
            <label class="star-voting__ico"
                   for="star-voting-{$i}"
                   title="{$i} {tlang('out of 5 stars')}"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__star"></use></svg></label>
          {/for}
        </div>
      </div>

    </div>
  </div>
  <!-- END Rating field -->


  <!-- Captcha field BEGIN -->
  {if $use_captcha}
    {view('includes/forms/input-captcha.tpl', [
    'label' => tlang('Security code'),
    'type' => 'text',
    'name' => 'captcha',
    'value' => get_value('captcha'),
    'required' => true,
    'desc' => tlang('Type the characters you see in this image.'),
    'modifier' => 'form__field--hor'
    ])}
  {/if}
  <!-- END Captcha field -->

  <!-- Submit button BEGIN -->
  <div class="form__field form__field--hor">
    <div class="form__label"></div>
    <div class="form__inner">
      <button type="submit"
              class="btn btn-default">{tlang('Send')}</button>
    </div>
  </div>
  <!-- END Submit button -->

  <input type="hidden"
         name="action"
         value="newPost">
  <input type="hidden"
         name="comment_parent"
         value="0"
         data-comments-parent>
  {form_csrf()}
</form>
