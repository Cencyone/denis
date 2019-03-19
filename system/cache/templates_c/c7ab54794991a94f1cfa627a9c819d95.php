<?php include ('/home/user2263/public_html/application/libraries/mabilis/functions/func.truncate.php');  ?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo lang ("Operation panel","admin"); ?> | <?php if(MAINSITE): ?>Premmerce<?php else:?>Image CMS<?php endif; ?></title>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
        <meta name="description" content="<?php echo lang ("Operation panel","admin"); ?> - Image CMS" />
        <meta name="generator" content="ImageCMS">

        <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

        <script src="https://use.fontawesome.com/9ea243d1fb.js"></script>

        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>css/bootstrap_complete.css">
        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>css/style.css">
        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>css/bootstrap-responsive.css">
        <!--
        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>css/bootstrap-notify.css">
        -->

        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>css/jquery/custom-theme/jquery-ui-1.8.16.custom.css">
        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>css/jquery/custom-theme/jquery.ui.1.8.16.ie.css">

        <!--Elfinder -->
        <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" />

        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url ('/application/third_party/studio-42/elfinder/css/elfinder.min.css'); ?>">

        <!-- Mac OS X Finder style for jQuery UI smoothness theme (OPTIONAL) -->
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url ('/application/third_party/studio-42/elfinder/css/theme.css'); ?>">

        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/lib/codemirror.css'); ?>">
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/addon/fold/foldgutter.css'); ?>">


        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>js/colorpicker/css/colorpicker.css" media="screen" charset="utf-8">
        <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery-1.8.2.min.js" type="text/javascript"></script>
        <script async="async" src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery-validate/jquery.validate.min.js" type="text/javascript"></script>
        <script async="async" src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery-validate/jquery.validate.i18n.js" type="text/javascript"></script>

        <!-- http://trentrichardson.com/examples/timepicker/ -->
        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>js/timepicker/jquery-ui-timepicker-addon.css">
        <link rel="stylesheet" type="text/css" href="<?php if(isset($THEME)){ echo $THEME; } ?>css/cloud-zoom.css">

        <link rel="icon" type="image/x-icon" href="<?php if(isset($THEME)){ echo $THEME; } ?>images/<?php if(MAINSITE): ?>premmerce_<?php endif; ?>favicon.png"/>

        <link rel="stylesheet" type="text/css" href="<?php echo site_url('/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css')?>" media="screen" charset="utf-8">
    </head>
    <body>
            <style>
                .imagecms-close{cursor: pointer;position: absolute;right: -100px;top: 0;height: 31px;background-color: #4e5a68;width: 95px;display: none;z-index: 3;}
                .imagecms-top-fixed-header.imagecms-active{height: 31px;background-color: #37414d;}
                .imagecms-toggle-close-text{color: #fff;}
                .imagecms-top-fixed-header.imagecms-active + .main_body header{padding-top: 31px;}
                .imagecms-top-fixed-header{height: 0;position: fixed;top: 0;left: 0;width: 100%;font-family: Arial, sans-serif;font-size: 12px;color: #223340;vertical-align: baseline;z-index: 1000}
                .imagecms-top-fixed-header .container{position: relative;}
                .imagecms-logo{float: left;}
                .imagecms-ref-skype, .imagecms-phone{font-size: 0;}
                .imagecms-phone{margin-right: 32px;}
                .imagecms-phone .imagecms-text-el{font-size: 12px;color: #fff;}
                .imagecms-ref-skype .imagecms-text-el{font-size: 12px;color: #fff;}
                .imagecms-ref-skype{color: #223340;text-decoration: none;}
                .imagecms-ref-skype:hover{color: #223340;text-decoration: none;}
                .imagecms-list{list-style: none;margin: 0;float: left;display: none;}
                .imagecms-list > li{height: 31px;vertical-align: top;padding: 0 23px;text-align: left;border-right: 1px solid #525f6f;display: inline-block;}
                .imagecms-list > li > a{line-height: 31px;}
                .imagecms-list > li:first-child{border-left: 1px solid #525f6f;}
                .imagecms-ref{color: #fff;text-decoration: none;text-transform: uppercase;font-size: 11px;}
                .imagecms-ref:hover{color: #fff;text-decoration: none;}
                .imagecms-ico-phone, .imagecms-ico-skype{width: auto !important;height: auto !important;position: relative !important;vertical-align: baseline;}
                .imagecms-ico-skype{position: relative;top: 3px;margin-right: 10px;}
                .imagecms-ico-phone{position: relative;top: 2px;margin-right: 6px;}
                .imagecms-buy-license > a{text-decoration: none;height: 100%;display: block;padding: 0 20px;font-size: 0;}
                .imagecms-buy-license > a > .imagecms-text-el{color: #fff;font-weight: normal;font-size: 11px;line-height: 31px;text-transform: uppercase;}
                .imagecms-buy-license{
                    display: none;float: right;height: 31px;box-shadow: 0 1px 1px rgba(0,0,0,.1);
                    background: #0eb48e; /* Old browsers */
                    background: -moz-linear-gradient(top,  #0eb48e 0%, #09a77d 100%); /* FF3.6+ */
                    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#0eb48e), color-stop(100%,#09a77d)); /* Chrome,Safari4+ */
                    background: -webkit-linear-gradient(top,  #0eb48e 0%,#09a77d 100%); /* Chrome10+,Safari5.1+ */
                    background: -o-linear-gradient(top,  #0eb48e 0%,#09a77d 100%); /* Opera 11.10+ */
                    background: -ms-linear-gradient(top,  #0eb48e 0%,#09a77d 100%); /* IE10+ */
                    background: linear-gradient(to bottom,  #0eb48e 0%,#09a77d 100%); /* W3C */
                    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0eb48e', endColorstr='#09a77d',GradientType=0 ); /* IE6-9 */
                }
                .imagecms-contacts{text-align: center;padding-top: 6px;display: none;}
                .imagecms-buy-license .imagecms-text-el{vertical-align: middle;}
                .imagecms-buy-license .imagecms-ico-donwload{vertical-align: middle;margin-left: 11px;}

                .imagecms-active .imagecms-buy-license, .imagecms-active .imagecms-list, .imagecms-active .imagecms-contacts{display: block;}
            </style>
        
        <?php $this->include_tpl('inc/javascriptVars', '/home/user2263/public_html/templates/administrator'); ?>
        <?php $this->include_tpl('inc/php_to_js_variables', '/home/user2263/public_html/templates/administrator'); ?>
        <?php $this->include_tpl('inc/jsLangs.tpl', '/home/user2263/public_html/templates/administrator'); ?>
        <?php $langDomain = $CI->land->gettext_domain?>
        <?php $CI->lang->load('admin')?>
        <?php if(SHOP_INSTALLED && (trim($content) == 'Строк тестовой лицензии истек' OR trim($content) == 'Ошибка проверки лицензии.')): ?>
            <div class="imagecms-top-fixed-header<?php if($_COOKIE['condPromoToolbar'] == '1' || $_COOKIE['condPromoToolbar'] == NULL): ?> imagecms-active<?php endif; ?>">
                <div class="imagecms-inside">
                    <div class="container">
                        <button type="button" class="imagecms-close" <?php if($_COOKIE['condPromoToolbar'] == '1' || $_COOKIE['condPromoToolbar'] == NULL): ?>style="display: block;"<?php endif; ?> onclick="setCookie('condPromoToolbar', '0');
                                $('.imagecms-top-fixed-header').removeClass('imagecms-active');
                                $(this).hide().next().show();
                                $(window).scroll();
                                        $(.frame_zH_frame_title).css('margin-top', '0');">
                            <span class="imagecms-toggle-close-text imagecms-bar-close-text"><span style="font-size: 14px;">↑</span> Скрыть</span>
                        </button>
                        <button type="button" class="imagecms-close" <?php if($_COOKIE['condPromoToolbar'] == '0'): ?>style="display: block;"<?php endif; ?> onclick="setCookie('condPromoToolbar', '1');
                                $('.imagecms-top-fixed-header').addClass('imagecms-active');
                                $(this).hide().prev().show();
                                $(window).scroll();
                                        $('.frame_zH_frame_title').css('margin-top', '-30px');">
                            <span class="imagecms-toggle-close-text imagecms-bar-show-text"><span style="font-size: 14px;">↓</span> Показать</span>
                        </button>
                        <div class="imagecms-buy-license">
                            <a href="http://www.imagecms.net/shop/prices" target="_blank" onclick="_gaq.push(['_trackEvent', 'demoshop-admin', '/shop/prices']);">
                                <span class="imagecms-text-el">Купить лицензицю</span>
                            </a>
                        </div>
                        <ul class="imagecms-list">
                            <li>
                                <a href="http://www.imagecms.net" target="_blank" class="imagecms-ref" onclick="_gaq.push(['_trackEvent', 'demoshop-admin', 'obzor-product-shop']);">Обзор продукта</a>
                            </li>
                            <li>
                                <a href="http://www.imagecms.net/kliuchevye-preimushchestva/vozmozhnosti" target="_blank" class="imagecms-ref" onclick="_gaq.push(['_trackEvent', 'demoshop-admin', '/kliuchevye-preimushchestva/vozmozhnosti']);">преимущества продукта</a>
                            </li>
                            <li>
                                <a href="http://www.imagecms.net/store/category/shoptemplates" target="_blank" class="imagecms-ref" onclick="_gaq.push(['_trackEvent', 'demoshop-admin', 'shoptemplates']);"><?php echo lang ('Шаблоны для Shop', 'newLevel'); ?></a>
                            </li>
                        </ul>
                        <div class="imagecms-contacts">
                            <span class="imagecms-phone">
                                <img src="<?php if(isset($THEME)){ echo $THEME; } ?>icon_phone.png" class="imagecms-ico-phone"/>
                                <span class="imagecms-text-el">+7 (499) 703-37-51</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="main_body">
            <div id="fixPage"></div>
            <!-- Here be notifications -->
            <div class="notifications top-right"></div>
            <header>
                <section class="container">
                    <div class="row-fluid">
                        <div class="span3 left-header">

                            <a href="<?php if(SHOP_INSTALLED): ?><?php echo base_url ('admin/components/run/shop/dashboard'); ?><?php else:?>/admin/dashboard<?php endif; ?>" class="logo pull-left pjax">
                                <?php if(MAINSITE): ?>
                                    <img src="<?php if(isset($THEME)){ echo $THEME; } ?>img/logo_premmerce.png"/>
                                <?php else:?>
                                    <img src="<?php if(isset($THEME)){ echo $THEME; } ?>img/logo_new.png"/>
                                <?php endif; ?>
                                <span class="helper"></span>
                            </a>

                        </div>

                        <div class="span6 center-header">
                            <span class="frame-prem frame-prem-header">
                                <span class="helper"></span>
                                <div class="">
                                    <div class="frame-prem-site"><a href="<?php echo rtrim(site_url(),'/')?>" target="_blank"><?php echo rtrim(site_url(),'/')?></a></div>
                                        <?php if(MAINSITE): ?>
                                        <div class="frame-prem-right">
                                            <span class="title d-i_b v-a_m"><?php echo lang('Balance:', 'admin')?></span>
                                            <span class="f-s_0 d-i_b v-a_m">
                                                <span class="text-el text-c-day"><?php echo $CI->load->module('mainsaas')->getDaysLeft()?></span>
                                                <span class="text-el text-days"><?php echo lang('days', 'admin')?></span>
                                            </span>
                                            <a href="<?php echo $CI->load->module('mainsaas')->getDomainBiling()?>/saas/orders/payments" class="icon-plus-tarif-money my_icon"></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </span>
                        </div>

                        <?php if($CI->dx_auth->is_logged_in()): ?>
                            <div class="pull-right span3 f-s_0 right-header">
                                <span class="helper"></span>
                                <ul class="d_i-b f-s_0">
                                    <?php if(SHOP_INSTALLED): ?>

                                        <li class="">
                                            <a href="#" data-drop=".frame-add-info-header">
                                                <span class="helper"></span>
                                                <span class="icon-help"></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <li class="dropdown d-i_b v-a_m">
                                        <a data-toggle="dropdown" class="btn-personal-area">
                                            <span class="helper"></span>
                                            <span class="my_icon icon-personal-area"></span>
                                        </a>
                                        <?php if(MAINSITE): ?>
                                            <?php echo $CI->load->module('mainsaas')->getSaasDropMenu()?>
                                        <?php else:?>
                                            <ul class="frame-dropdown dropdown-menu drop_menu_black">

                                                <?php if($CI->dx_auth->get_username()): ?>
                                                    <li>
                                                        <a href="
                                                           <?php if(SHOP_INSTALLED): ?>/admin/components/run/shop/users/edit/<?php echo $CI->dx_auth->get_user_id()?>
                                                           <?php else:?>/admin/components/cp/user_manager/edit_user/<?php echo $CI->dx_auth->get_user_id()?>
                                                           <?php endif; ?>"
                                                           id="user_name">
                                                            <?php echo lang ("Personal data", "admin"); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <a href="/auth/logout">
                                                        <?php echo lang ("Exit", "admin"); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                    <li class="">
                                        <a href="<?php if(isset($BASE_URL)){ echo $BASE_URL; } ?>" target="_blank">
                                            <span class="helper"></span>
                                            <span class="my_icon icon-to-the-site"></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </header>
            <?php if(MAINSITE): ?>
                <div class="frame-add-info-header" style="display: none;">
                    <div class="container">
                        <button type="button" class="icon-close2" data-closed=".frame-add-info-header"></button>
                        <ul class="items items-add-info">
                            <?php $contacts = $CI->load->module('mainsaas')->getContacts()?>
                            <li class="item-manager">
                                <div class="frame-title f-s_0">
                                    <span class="icon-manager"></span>
                                    <span class="title"><?php echo lang ('Менеджер', 'admin'); ?></span>
                                </div>
                                <ul class="items-menu-col">
                                    <?php if($contacts['addphone2']): ?>
                                        <li>
                                            <?php echo $contacts['addphone2']?>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($contacts['addphone1']): ?>
                                        <li>
                                            <?php echo $contacts['addphone1']?>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($contacts['addphone3']): ?>
                                        <li>
                                            <?php echo $contacts['addphone3']?>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($contacts['siteinfo_mainphone'] && !$contacts['addphone2']): ?>
                                        <li>
                                            <?php echo $contacts['siteinfo_mainphone']?>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($contacts['Email']): ?>
                                        <li>
                                            <?php echo $contacts['Email']?>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                            <li class="item-support">
                                <div class="frame-title f-s_0">
                                    <span class="icon-maintain"></span>
                                    <span class="title"><?php echo lang ('Служба поддержки', 'admin'); ?></span>
                                </div>
                                <ul class="items-menu-col">
                                    <li class="f-s_0">
                                        <a href="<?php echo $CI->load->module('mainsaas')->getDomainBiling()?>/saas/support" class="text-el"><?php echo lang ('Ваши вопросы', 'admin'); ?></a>
                                        <?php if($support_answers_count): ?>
                                            <span class="badge-new">
                                                <?php echo $support_answers_count?>
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <a href="<?php echo $CI->load->module('mainsaas')->getDomainBiling()?>/saas/support/#create-ticket"><?php echo lang ('Задать вопрос', 'admin'); ?></a>
                                    </li>
                                </ul>
                            </li>
                            <li class="item-instruction">
                                <?php echo $CI->load->module('mainsaas')->getInstruction()?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <?php if(SHOP_INSTALLED): ?>
                    <div class="frame-add-info-header full-width" style="<?php if(true == $d_b): ?>display: block<?php else:?>display: none<?php endif; ?>;">
                        <div class="container">
                            <button type="button" class="icon-close2" data-closed=".frame-add-info-header"></button>
                            <ul class="items items-add-info">
                                <li class="item-instruction">
                                    <div class="frame-title f-s_0">
                                        <span class="icon-instr"></span>
                                        <span class="title"><?php echo lang ('Instructions for filling', 'admin'); ?></span>
                                    </div>

                                    <ul class="items items-menu-row">
                                        <?php if(is_true_array($CI->load->module('admin/docs')->getPages())){ foreach ($CI->load->module('admin/docs')->getPages() as $page){ ?>
                                            <?php if(stripos($page->full_url, $active_docs_page)): ?>
                                                <li><span><?php echo func_truncate ($page->title, 25); ?></span></li>
                                                    <?php else:?>
                                                <li><a href="<?php echo $page->full_url?>"><?php echo func_truncate ($page->title, 25); ?></a></li>
                                                <?php endif; ?>
                                            <?php }} ?>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Admin Menu  -->
            <?php echo $CI->load->module('admin_menu')->show()?>

            <div id="loading"></div>
            <?php $CI->lang->load($langDomain)?>
            <div class="container" id="mainContent"><script>setTimeout(function () {
                        $('.mini-layout').css('padding-top', $('.frame_title:not(.no_fixed)').outerHeight());
                    }, 0);</script>
                        <?php if(isset($content)){ echo $content; } ?>
                </div>
                <?php $CI->lang->load('admin')?>
                <div class="hfooter"></div>
            </div>
            <footer>
                <div class="container">
                    <div class="row-fluid">
                        <div class="span4">
                            <?php echo lang ('Interface','admin'); ?>:
                            <?php echo create_admin_language_select()?>
                        </div>
                        <div class="span4 t-a_c">
                            <?php if(MAINSITE == ''): ?>
                                <?php echo lang ("Version","admin"); ?>: <b><?php echo getCMSNumber()?></b>
                            <?php endif; ?>
                            <div class="muted"><?php echo lang ('Help us get better','admin'); ?> - <a href="#"  onclick="$('.addNotificationMessage').modal();
                                    return false;"><?php echo lang ('report an error','admin'); ?></a></div>
                        </div>
                        <?php if(!MAINSITE): ?>
                            <div class="span4 t-a_r">
                                <div class="muted">Copyright © ImageCMS <?php echo date('Y')?></div>
                                <a href="<?php if(MAINSITE): ?>http://docs.premmerce.com/<?php else:?>http://docs.imagecms.net<?php endif; ?>" target="blank"><?php echo lang ('Documentation','admin'); ?></a>
                            </div>
                        <?php else:?>
                            <div class="span4 t-a_r">
                                <div class="muted">Copyright © Premmerce <?php echo date('Y')?></div>
                                <a href="http://docs.premmerce.com" target="blank"><?php echo lang ('Documentation','admin'); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </footer>
            <div id="elfinder"></div>
            <div class="standart_form frame_rep_bug">

            </div>


            <div class="addNotificationMessage modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3><?php echo lang ("Report an error","admin"); ?></h3>
                </div>
                <form class="form-vetical">
                    <div class="modal-body">

                        <div class="control-group">
                            <label class="control-label">
                                <?php echo lang ('Your Name','admin'); ?>:
                            </label>
                            <div class="controls">
                                <input type=text name="name"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo lang ('Your Email','admin'); ?>:
                            </label>
                            <div class="controls">
                                <input type=text name="email"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo lang ('Your remark', "admin"); ?>:
                            </label>
                            <div class="controls">
                                <textarea name='text'></textarea>
                            </div>
                        </div>
                        <input type="hidden" name='ip' value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" id="ip_address"/>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn" onclick="$('.modal').modal('hide');"><?php echo lang ('Cancel','admin'); ?></a>
                        <button type="submit" class="btn btn-primary"><?php echo lang ("Send","admin"); ?></button>
                    </div>
                </form>
            </div>

            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery-i18n/datepicker/jquery.ui.datepicker-<?php echo MY_Controller::getAdminInterfaceLocale()?>.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/pjax/jquery.pjax.min.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery-ui-1.8.23.custom.min.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/timepicker/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery-i18n/timepicker/jquery-ui-timepicker-<?php echo MY_Controller::getAdminInterfaceLocale()?>.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/bootstrap.min.js" type="text/javascript"></script>
            <script async="async" src="<?php if(isset($THEME)){ echo $THEME; } ?>js/bootstrap-notify.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery.form.js" type="text/javascript"></script>

            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/chosen.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery.synctranslit.min.js" type="text/javascript"></script>

            <script type="text/javascript" src="<?php echo site_url('application/third_party/tinymce/tinymce/tinymce.min.js')?>"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/functions.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/scripts.js" type="text/javascript"></script>

            <script type="text/javascript" src="<?php echo site_url ('/application/third_party/studio-42/elfinder/js/elfinder.min.js'); ?>"></script>

                                                        <!-- CodeMirror-->
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/lib/codemirror.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/mode/javascript/javascript.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/mode/htmlmixed/htmlmixed.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/mode/php/php.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/mode/xml/xml.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/mode/css/css.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/mode/clike/clike.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/addon/fold/foldcode.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/addon/fold/foldgutter.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/addon/fold/brace-fold.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/addon/fold/xml-fold.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/addon/fold/markdown-fold.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/addon/fold/comment-fold.js'); ?>"></script>
            <script src="<?php echo site_url ('/application/third_party/tinymce/tinymce/plugins/codemirror/CodeMirror/addon/selection/selection-pointer.js'); ?>"></script>

            <script type="text/javascript" src="<?php echo site_url('/js/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js')?>"></script>


            <?php if($this->CI->config->item('language') == 'ru_RU'): ?>
                <script async="async" src="<?php if(isset($THEME)){ echo $THEME; } ?>js/jquery-validate/messages_ru.js" type="text/javascript"></script>
            <?php endif; ?>

            <?php if($admin_interface_locale): ?>
                <script type="text/javascript" src="<?php echo site_url ('/application/third_party/studio-42/elfinder/js/i18n/elfinder.' . $admin_interface_locale .'.js'); ?>"></script>
            <?php endif; ?>
        <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/admin_base_i.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/admin_base_m.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/admin_base_r.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/admin_base_v.js" type="text/javascript"></script>
            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/cloud-zoom.1.0.3.min.js" type="text/javascript"></script>

            <script src="<?php if(isset($THEME)){ echo $THEME; } ?>js/autosearch.js" type="text/javascript"></script>
            <?php if(MAINSITE): ?>
                <script src="/application/modules/mainsaas/assets/js/mainsaas.js" type="text/javascript"></script>
            <?php endif; ?>

            <script>

                    $(document).ready(function () {
                        $('.cloud-zoom').CloudZoom({showTitle: false});
                        if (!isShop)
                        {
                            $('#shopAdminMenu').hide();
                            //$('#topPanelNotifications').hide();
                        }
                        else
                            $('#baseAdminMenu').hide();
                    })

                    function number_tooltip_live() {
                        $('.number input').each(function () {
                            $(this).attr({
                                'data-placement': 'top',
                                'data-title': lang_only_number
                            });
                        });
                    }
                    function prod_on_off() {
                        $('.prod-on_off').die('click').live('click', function () {
                            var $this = $(this);
                            if (!$this.hasClass('disabled')) {
                                if ($this.hasClass('disable_tovar')) {
                                    $this.animate({
                                        'left': '0'
                                    }, 200).removeClass('disable_tovar');
                                    if ($this.parent().data('only-original-title') == undefined) {
                                        $this.parent().attr('data-original-title', show_tovar_text)
                                        $('.tooltip-inner').text(show_tovar_text);
                                    }
                                    $this.next().attr('checked', true).end().closest('td').next().children().not('.currencies-value').removeClass('disabled').removeAttr('disabled');
                                    if ($this.attr('data-page') != undefined)
                                        $('.setHit, .setHot, .setAction').removeClass('disabled').removeAttr('disabled');
                                }
                                else {
                                    $this.animate({
                                        'left': '-28px'
                                    }, 200).addClass('disable_tovar');
                                    if ($this.parent().data('only-original-title') == undefined) {
                                        $this.parent().attr('data-original-title', hide_tovar_text)
                                        $('.tooltip-inner').text(hide_tovar_text);
                                    }
                                    $this.next().attr('checked', false).end().closest('td').next().children().not('.currencies-value').addClass('disabled').attr('disabled', 'disabled');
                                    if ($this.attr('data-page') != undefined)
                                        $('.setHit, .setHot, .setAction').addClass('disabled').attr('disabled', 'disabled')
                                }
                            }
                        });
                    }
                    $(window).load(function () {
                        number_tooltip_live();
                        prod_on_off();
                    })
                
                </script>
                <div id="jsOutput" style="display: none;"></div>
            </body>
        </html>
<?php $mabilis_ttl=1552752710; $mabilis_last_modified=1491903742; ///home/user2263/public_html/templates/administrator/main.tpl ?>