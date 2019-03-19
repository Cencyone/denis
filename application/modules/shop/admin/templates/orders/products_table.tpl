
<table border="1" cellpadding="5" style="border-collapse: collapse" width="100%">
    <tbody>
    <tr>
        <td width="30">№</td>
        <td width="175">{lang('Product','admin')}</td>
        <td width="70">{lang('Quantity','admin')}</td>
        <td width="80">{lang('Price','admin')}</td>
        <td width="100">{lang('Discount','admin')}</td>
        <td width="100">{lang('Total price with discount','admin')}</td>
    </tr>
    {foreach $products as $n => $p}
        <tr>
            { /* } //set variant name if product name is not same as variant name{ */ }
            {$variantName = trim($p->getVariantName()) !=  trim($p->getProductName()) ? trim($p->getVariantName()) : null}
            { /* } //add article if present { */ }
            {$variantName = $variantName . (($p->getVariant() && $p->getVariant()->getNumber())? '('. $p->getVariant()->getNumber() .')':null)}

            {$totalProductPrice = $p->getPrice() * $p->getQuantity()}
            {$discountStatic = $p->getOriginPrice() - $p->getPrice()}
            {$discountPercent =  floor($discountStatic) > 0 ? round($discountStatic / $p->getOriginPrice() * 100, 2) . ' %': null }

            <td>{echo $n + $iterator}</td>
            <td>{echo trim($p->getProductName())} {$variantName}</td>
            <td>{echo $p->getQuantity()} {lang('pcs.','admin')}</td>
            <td>{emmet_money($p->getOriginPrice())}</td>
            <td>{$discountPercent}</td>
            <td>{emmet_money($totalProductPrice)}</td>
        </tr>
    {/foreach}
    </tbody>
</table>

{if !$countPage || ($countPage == $pageNumber)}

    {$discount = $model->getDiscount()}

    <div>
        <br>
        <table border="0" style="text-align: right; float: right">
            <tr>
                <td colspan="3"></td>
                <td colspan="2">{lang('Price','admin')}:</td>
                <td>{emmet_money($model->getOriginPrice())}</td>
            </tr>
            {if $delivery}
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">{lang('Delivery', 'admin')}:</td>
                    <td>{emmet_money($delivery->getOriginPrice())}</td>
                </tr>
            {/if}
            {if $discount > 0}
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">{lang('Discount','admin')}:</td>
                    <td>- {emmet_money($discount)}</td>
                </tr>
            {/if}
            {if $gift > 0}
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">{lang('Gift certificate','admin')}:</td>
                    <td>- {emmet_money($gift)}</td>
                </tr>
            {/if}
            <tr>
                <td colspan="3"></td>
                <td colspan="3" style="border-bottom: 1px solid black">
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td colspan="2">{lang('In total','admin')}:</td>
                <td>{echo emmet_money($totalPrice)}</td>
            </tr>
        </table>
        <div style="clear: both"></div>
    </div>
{/if}