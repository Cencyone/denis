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
    <textarea class="form-control" name="{$name}" rows="{$rows}" {$placeholder} {$required}>{$value}</textarea>
    {if trim(strip_tags($desc)) != ""}
    	<div class="form__info form__info--help">{$desc}</div>
    {/if}
    {if get_error($name)}
    	<div class="form__info form__info--error">{get_error($name)}</div>
    {/if}
  </div>
</div>