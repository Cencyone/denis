<!--
  Global form, which grab all catalog page fields like: filter, order, per-page etc.
-->
<form action="<?php echo tpl_self_url (); ?>" method="get" class="hidden" id="catalog-form">

  <!-- Include extra data from search form and category filter -->
  <?php if(tpl_encode($CI->input->get('text'))): ?>
    <input type="hidden" name="text" value="<?php echo tpl_encode($CI->input->get('text'))?>">
  <?php endif; ?>

  <?php if(tpl_encode($CI->input->get('category'))): ?>
    <input type="hidden" name="category" value="<?php echo tpl_encode($CI->input->get('category'))?>">
  <?php endif; ?>

</form><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/category/category_form.tpl ?>