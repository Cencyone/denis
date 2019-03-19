<div class="controls">
    <span class="frame_label no_connection">
        <span class="niceCheck b_n">
            <input type="checkbox" name="{echo $aggregator->getId()}[{$name}]" {echo $aggregator->getConfigItem($name)?'checked':''}/>
        </span>
        {$label}
    </span>
</div>