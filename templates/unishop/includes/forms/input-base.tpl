{$required = $required ? "required" : ""}
{$placeholder = $placeholder ? "placeholder=\"". $placeholder ."\"" : ""}

<div class="form__field {$modifier}">
  {if $label}
    <div class="form__label">
       {$label}
       {if $required}<i class="form__require-mark"></i>{/if}
    </div>
  {/if}
  <div class="form__inner">
    <input class="form-control" type="{$type}" name="{$name}" value="{htmlspecialchars($value)}" {$placeholder} {$required}>
    {if trim(strip_tags($desc)) != ""}
    	<div class="form__info form__info--help">{$desc}</div>
    {/if}
    {if get_error($name)}
    	<div class="form__info form__info--error">{get_error($name)}</div>
    {/if}
  </div>
</div>