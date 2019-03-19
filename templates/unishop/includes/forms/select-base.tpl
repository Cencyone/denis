{$options = $data[0];}
{$value_label = $data[1];}
{$name_label = $data[2];}
{$required = $required ? "required" : ""}

<div class="form__field">
  <div class="form__label">{$label}{if $required}*{/if}</div>
  <div class="form__inner">
    <select class="form-control" name="{$name}" {$required}>
      {foreach $options as $option}
      <option value="{$option[$value_label]}">{$option[$name_label]}</option>
      {/foreach}
    </select>    
    {if trim(strip_tags($desc)) != ""}
    <div class="form__info form__info--help">{$desc}</div>
    {/if}
    {if get_error($name)}
    <div class="form__info form__info--error">{get_error($name)}</div>
    {/if}
  </div>
</div>