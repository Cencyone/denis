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
    <div class="row row--gutter-collapse">
      <div class="col-sm-6">
        <input class="form-control" type="{$type}" name="{$name}" value="{$value}" autocomplete="off" {$placeholder} {$required}>
      </div>
      <div class="col-sm-6">
        <div class="form__capcha" data-captcha-img>{$cap_image}</div>
      </div>
    </div>
    {if trim(strip_tags($desc)) != ""}
      <div class="form__info form__info--help">{$desc}</div>
    {/if}
    {if get_error($name)}
      <div class="form__info form__info--error">{get_error($name)}</div>
    {/if}
  </div>
</div>