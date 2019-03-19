<script type="text/javascript">
    <?php $settings = $CI->cms_admin->get_settings();?>
    var textEditor = '<?php echo $settings['text_editor']; ?>';

    <?php if($CI->dx_auth->is_logged_in()): ?>
    var userLogined = true;
    <?php else:?>
    var userLogined = false;
    <?php endif; ?>

    var locale = '<?php echo $this->CI->config->item('language')?>';

    <?php if($CI->uri->segment('4') == 'shop'): ?>
    var isShop = true;
    <?php else:?>
    var isShop = false;
    <?php endif; ?>

    <?php if(MAINSITE): ?>
    var MAINSITE = true;
    <?php else:?>
    var MAINSITE = false;
    <?php endif; ?>

    var lang_only_number = "<?php echo lang ("numbers only","admin"); ?>";
    var show_tovar_text = "<?php echo lang ("show","admin"); ?>";
    var hide_tovar_text = "<?php echo lang ("don't show", 'admin'); ?>";

    base_url = '<?php if(isset($BASE_URL)){ echo $BASE_URL; } ?>';
    theme_url = '<?php if(isset($THEME)){ echo $THEME; } ?>';

    var elfToken = '<?php echo $CI->lib_csrf->get_token()?>';
    window.CMS_JS = {
        server_settings: {
            
            max_file_uploads: <?php echo ini_get ('max_file_uploads'); ?>,
        }
    };
    
</script>

<?php $mabilis_ttl=1552752710; $mabilis_last_modified=1491903751; ///home/user2263/public_html/templates/administrator/inc/php_to_js_variables.tpl ?>