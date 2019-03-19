<!DOCTYPE html>
<html lang="{current_language()}">
<head>
  <title>{tlang('Site is offline')}</title>
  <meta name="description" content="{$site_description}">
  <meta name="keywords" content="{$site_keywords}">
  <meta name="generator" content="ImageCMS">
  <link rel="stylesheet" href="{$THEME}_css/final.min.css">
  <link rel="shortcut icon" href="{siteinfo('siteinfo_favicon_url')}" type="image/x-icon">
</head>

<body>
  
  <div class="offline">
    <div class="offline__title">{tlang('Site is temporarily unavailable')}</div>
    <div class="offline__intro">{tlang('This site is down for maintenance. We will be back shortly, please check back again soon')}</div>
  </div>

</body>
</html>