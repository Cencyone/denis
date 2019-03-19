<label class="control-label">{$label}:</label>
<div class="controls">
    <select class="notchosen" name="{echo $aggregator->getId()}[{$name}]
            style=" width:285px;height:129px;">
    <option value="">--{echo lang('No selected', 'aggregator')}--</option>

    {foreach $options as $value => $string}
        {$selected = $productValue == $value ?'selected':''}
        <option value="{$value}" {$selected}>
            {$string}
        </option>
    {/foreach}
    </select>
</div>