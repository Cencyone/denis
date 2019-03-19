{$options = $data[0];}
{$value_label = $data[1];}
{$name_label = $data[2];}

{$required = $required ? "required" : ""}
<div class="form__field">
  <div class="form__label">{$label}{if $required}*{/if}</div>
  <div class="form__inner">    
  	{foreach $options as $option}
      {$field_status = $disabled == $option[$value_label] ? "disabled" : ""}
      <label class="form__checkbox">
        <span class="form__checkbox-field">
          <input type="{$type}" name="{$name}" value="{$option[$value_label]}" {$required} {$field_status}>
        </span>
        <span class="form__checkbox-inner">
          <span class="form__checkbox-title {if $field_status}form__checkbox-title--disabled{/if}">{$option[$name_label]}</span>
        </span>
      </label>	
		{/foreach}
    {if trim(strip_tags($desc)) != ""}
    	<div class="form__info form__info--help">{$desc}</div>
    {/if}
    {if get_error($name)}
    	<div class="form__info form__info--error">{get_error($name)}</div>
    {/if}
  </div>
</div>