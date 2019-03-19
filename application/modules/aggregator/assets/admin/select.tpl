<label class="control-label">{$label}:</label>
<div class="controls">
    <select name="{echo $aggregator->getId()}[{$name}]{echo $multiple?'[]':""}" {echo $multiple?'multiple':""}
            style="width:285px;height:129px;">
        {if !$multiple }
            <option value="">--{echo lang('No selected', 'aggregator')}--</option>
        {/if}

        {foreach $options as $value => $string}
            {if $multiple}
                {$selected = in_array($value , $aggregator->getConfigItem($name))?'selected':''}
            {else: }
                {$selected = $aggregator->getConfigItem($name) == $value }
            {/if}
            <option value="{$value}" {$selected}>
                {$string}
            </option>
        {/foreach}
    </select>
</div>