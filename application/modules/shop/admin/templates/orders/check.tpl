<table border="0" width="550">
    <tr>	
        <td align="center"><font size="18px">{lang('Order','admin')} № {echo $model->getId()} {$pageNumber}</font></td>
    </tr>	
</table>
<br/>
<br/>
<table border="0" width="550">
    <tr>	
        <td>{lang('User name','admin')}:
            {echo $model->getUserFullName()}
            <br/>
            E-Mail:
            {echo $model->UserEmail}
            <br/>
            {if $model->UserPhone}
                {lang('Phone','admin')}:
                {echo $model->UserPhone}
                <br/>
            {/if}
            {$s_field = ShopCore::app()->CustomFieldsHelper->getOneCustomFieldsByNameArray('city','order', $model->getId())}
            {if $s_field.field_data && $s_field.field_data !== ''}
                {lang('City','admin')}:
                {echo $s_field.field_data}
                <br/>
            {/if}
            {if $model->user_deliver_to}
                {lang('Delivery Address','admin')}:
                {echo $model->user_deliver_to}
                <br/>
            {/if}
            {lang('Order date','admin')}:
            {date('d.m.Y, H:i:s.',$model->getDateCreated())}
            <br/>
            {if $model->getDeliveryMethod()}
                {lang('Delivery method','admin')}:
                {if $model->getDeliveryMethod() > 0}
                    {echo $deliverMethod->getName()}
                {/if}
                <br/>
            {/if}
            {if $paymentMethod}
                {lang('Payment method','admin')}:
                {if $paymentMethod->getName()}
                    {echo ShopCore::t($paymentMethod->getName())}
                {/if}
                <br/>
            {/if}
            <br/>
            {if $model->userComment}
                {lang('Comment','admin')}:
                {echo $model->userComment}
                <br/>
            {/if}
        </td>
        <td align="right">{if siteinfo('logo') != "" and file_exists('.' . siteinfo('logo'))}<img src="{echo siteinfo('siteinfo_logo_url')}" width="150" alt="logo"/>{/if}</td>
    </tr>	
</table>	        
<br/>
{$productsTable}