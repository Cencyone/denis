<!DOCTYPE html>
<html lang="{current_language()}">
<head>

  <!-- Page meta params. Should always be placed before any others head info -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Meta data -->
  <title>{$site_title}</title>
  <meta name="description" content="{$site_description}">
  <meta name="keywords" content="{$site_keywords}">
  <meta name="generator" content="ImageCMS">

  <!-- Final compiled and minified stylesheet -->
  <!--
  * !WARNING! Do not modify final.min.css file! It has been generated automatically
  * All changes will be lost when sources are regenerated!
  * Use Sass files _src/scss instead. Read more here http://docs.imagecms.net/rabota-s-shablonom-multishop/rabota-s-css-i-javasctipt-dlia-razrabotchikov
  -->
  <link rel="stylesheet" href="{$THEME}_css/final.min.css">

  <!--
  * Uncomment this file if you want to put custom styles and do not want to use Sass and Gulp
  -->
  <!-- <link rel="stylesheet" href="{$THEME}_css/custom.css"> -->

  <!-- Shortcut icons -->
  <link rel="shortcut icon" href="{siteinfo('siteinfo_favicon_url')}" type="image/x-icon">
</head>
<body class="page">

<!-- Main content frame -->
<div class="page__body" data-page-pushy-container>
  <div class="page__wrapper">

    <header class="page__hgroup">
      <!-- Header -->
      {view('includes/header.tpl')}

      <!-- Main Navigation -->
      <div class="page__mainnav-hor hidden-xs hidden-sm">
        <div class="page__container">
          <!-- condition getOpenLevels() == all allows to output mega menu in case of appropriate admin settings -->
          {if getOpenLevels() == 'all'}
            {load_catalog_menu('navs/catalog_cols')}
          {else:}
            {load_catalog_menu('navs/catalog_tree')}
          {/if}
        </div>
      </div>

      {if $CI->core->core_data['data_type'] == 'main'}
        <div class="page__main-banner">
          <div class="page__container">
            {view('includes/header_banners.tpl')}
          </div>
        </div>
      {/if}

    </header>

    <!-- Bread Crumbs -->
    {widget('breadcrumbs')}

    <div class="page__content">
      {$content}
    </div>

    <!-- Viewed products widget. Hidden on order page -->
    {if $CI->core->core_data['data_type'] != 'cart'}
      {widget('products_viewed')}
    {/if}

  </div><!-- .page__wrapper -->

  <!-- Footer -->
  <footer class="page__fgroup">
    {view('includes/footer.tpl')}
  </footer>

</div><!-- .page__body -->

<!-- Mobile slide frame -->
<div class="page__mobile" data-page-pushy-mobile>
  {view('includes/mobile_frame.tpl')}
</div>

<!-- Site background overlay when mobile menu is open -->
<div class="page__overlay hidden" data-page-pushy-overlay></div>

<!-- Final compiled and minified JS -->
<script src="{$THEME}_js/vendor.min.js"></script>
<script src="{$THEME}_js/final.min.js"></script>
<!--
* Uncomment this file if you want to put custom styles and do not want to use Gulp build
-->
<!-- <script src="{$THEME}_js/custom.js"></script> -->
<!-- Social networks login module styles init -->
{if array_key_exists('socauth', $modules)}
  {tpl_register_asset('socauth/css/style.css', 'before')}
  {if !$CI->dx_auth->is_logged_in()}
    {tpl_register_asset('socauth/js/socauth.js', 'after')}
  {/if}
{/if}
</body>
</html>