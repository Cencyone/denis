<form method="post" class="form-horizontal" id="deleteCurrencyWithPriceType">
    <div class="control-group">
        {if count($model)}
            <h5 style="text-align: right; margin-right: 10px; color: red;">{lang('Price types with this currency','admin')}
                : {count($model)}!</h5>
        {else:}
            <p>{lang('Do you really want to delete the selected currency?','admin')}</p>
        {/if}
    </div>
    <input type="hidden" name="id" value="{echo $currencyId}">
    {if count($model)}
        <div class="control-group">
            <label class="control-label">
                {lang('Set another currency for these price type','admin')}:
            </label>

            <div class="controls">
                <input type="radio" value="1" checked="checked" name="moveOrDelete"/>
                <select name="CurrencySelectId" style="width: 90% !important;">
                    {foreach $currencies as $currency}
                        {if $currency->getId() != $currencyId}
                            <option value="{echo $currency->getId()}">{echo $currency->getName()}</option>{/if}
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">
                {lang('Delete all price types with this currency','admin')}
            </label>

            <div class="controls">
                <div class="form_text"><input type="radio" value="2" name="moveOrDelete"/>
                </div>
            </div>

            <div class="form_overflow"></div>

        </div>
        <br/>
    {/if}


</form>