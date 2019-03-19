<div class="comments" data-comments>
  
  <!-- List of user comments -->
  {if $comments_arr}
    <div class="comments__list">
      {view('comments/comments_list.tpl')}
    </div>
  {else:}
    <div class="comments__list">
      {tlang('Be the first to review this item. Share your rating and review so that other customers can decide if this is the right item for them.')}
    </div>
  {/if}
  
  <!-- Message if user must to sign in to leave a comment -->
  {if $can_comment == 1 && !$is_logged_in}
    <div class="message message--info">
      <div class="typo">
        {tlang('You have to log in to leave a comment.')} <a href="{site_url('auth')}">{tlang('Sign in')}</a>
      </div>
    </div>
  {else:}
    <!-- Main comment form  -->
    <div id="comments-anchor"></div>
    <div class="comments__form">
      <div class="comments__form-header">{tlang('Write a review')}</div>
      <div class="comments__form-body">
        {view('comments/main_form.tpl')}
        <div class="hidden" data-reply-form>{view('comments/reply_form.tpl')}</div>
      </div>
    </div>
  {/if}
</div>