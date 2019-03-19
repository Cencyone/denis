{foreach $properties as $key => $property}
<div class="control-group" id="edit-properties" >
    <label class="control-label" for="num_{$key}"><a href="/admin/components/run/shop/properties/edit/{echo $property->getId()}/{$locale}">{echo $property->getName()}</a>:</label>
    <div class="controls">

        {$multiple = $property->isMultiple()?'multiple':false}

        <select {$multiple} data-locale="{echo $property->getLocale()}" data-id="{echo $property->getId()}" name="productProperties[{echo $property->getId()}]{echo $multiple?'[]':''}" >
            {if !$multiple}
            <option value='' >- {lang('Unspecified')} -</option>
            {/if}

            {foreach $property->getTranslatedValues() as $value}
            <option {if in_array($value->getId(), $propertiesData[$property->getId()])}selected {/if}value="{echo $value->getId()}">{echo $value->getValue()}</option>
            {/foreach}
        </select>

        <button type="button" data-rel="tooltip" data-close-tooltip="{lang('Cancel', 'admin')}"
                data-add-tooltip="{lang('Add new property value')}"
                data-title="{lang('Add new property value')}"
                onclick="PropertyFastCreator.showAddForm(this)" class="btn btn-small" style="margin-left: -3px;">
            <i class="icon-plus"></i>
        </button>
    </div>
    <br>
    <div style ="margin-bottom: -5px;">
        <div style="display:none; margin-left: 224px;margin-top: -21px;" class="addPropertyToProduct">
            <input type="text" style="" onkeypress="PropertyFastCreator.addPropertyValue(event, this)">
            <button type="button" data-rel="tooltip" data-title="{lang('Add new property value')}" onclick="PropertyFastCreator.addPropertyValue(event, this)" class="btn btn-small" style="margin-left: -3px;">
                <i class="icon-ok" style="margin-right: 0!important;"></i>
            </button>
        </div>
    </div>
</div>
{/foreach}
