<select class="form-control input-sm" name="paymentMethodId[{echo $delivery->getId()}]">
	{foreach $delivery->getPaymentMethodss() as $payment}
	<option value="{echo $payment->getId()}" {if $payment->getId() == get_value('paymentMethodId['.$delivery->getId().']')}selected{/if}>{echo $payment->getName()}</option>
	{/foreach}
</select>