<!--
  Global form, which grab all catalog page fields like: filter, order, per-page etc.
-->
<form action="{tpl_self_url()}" method="get" class="hidden" id="catalog-form">

  <!-- Include extra data from search form and category filter -->
  {if tpl_encode($CI->input->get('text'))}
    <input type="hidden" name="text" value="{echo tpl_encode($CI->input->get('text'))}">
  {/if}

  {if tpl_encode($CI->input->get('category'))}
    <input type="hidden" name="category" value="{echo tpl_encode($CI->input->get('category'))}">
  {/if}

</form>