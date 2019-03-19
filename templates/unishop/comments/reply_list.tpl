{if module('comments')->commentsapi->getCountCommentAnswersByCommentId($comment.id)}
<div class="comments__parent">
  {foreach $comment_ch as $subcomment}
    {if $comment.id == $subcomment.parent}
    <div class="comments__parent-item">
      <div class="comments__parent-header">
        <div class="comments__parent-author">{$subcomment.user_name}</div>
        <div class="comments__parent-date">{tpl_locale_date("d F Y H:i", $subcomment.date)}</div>
      </div>
      <div class="comments__parent-text">
        {$subcomment.text}
      </div>
    </div>
    {/if}
  {/foreach}
</div>
{/if}