<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang('Settings','smart_filter')}</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="{$admin_url}" class="t-d_n m-r_15"><span
                            class="f-s_14">←</span> <span class="t-d_u">{lang('Go back','admin')}</span></a>
                <button type="button" class="btn btn-small btn-primary formSubmit" data-form="#seo_form" data-submit>
                    <i class="icon-plus-sign icon-white"></i>{lang('Save','admin')}</button>
                {echo create_language_select($languages, $locale, "/admin/components/cp/smart_filter/settings" , FALSE)}

            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="modules">

            <table class="table  table-bordered table-hover table-condensed content_big_td">
                <thead>
                <tr>
                    <th colspan="6">
                        {lang('Default templates','smart_filter')}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="6">
                        <div class="inside_padd">
                            <form method="post" id="seo_form"
                                  action="{$admin_url}/settings{$urlLocale}" class="form-horizontal">

                                <input type="hidden" name="locale" data-locale value="{$locale}">

                                <div class="control-group">
                                    <div class="control-label"></div>
                                    <div class="controls">
                                        <span class="frame_label no_connection">
                                            <span class="niceCheck">
                                                <input type="checkbox" name="active" {echo $active?"checked":""}/>
                                            </span>
                                            {lang('Use default templates for filter pages ','smart_filter')}
                                        </span>
                                    </div>
                                </div>


                                <div class="control-group">
                                    <label class="control-label" for="h1">

                                    <span data-title="{lang('Variables, can use to', 'smart_filter')}"
                                          class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('settings_help_text')}
                                        {lang('H1','smart_filter')}:

                                    </label>
                                    <div class="controls">
                                        <input data-insert-var type="text" class="span12" name="h1" value="{$h1}"/>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="meta_title">

                                    <span data-title="{lang('Variables, can use to', 'smart_filter')}"
                                          class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('settings_help_text')}
                                        {lang('Meta title','smart_filter')}:


                                    </label>
                                    <div class="controls">
                                        <textarea data-insert-var name="meta_title">{$meta_title}</textarea>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="meta_description">

                                    <span data-title="{lang('Variables, can use to', 'smart_filter')}"
                                          class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('settings_help_text')}
                                        {lang('Meta description','smart_filter')}:


                                    </label>
                                    <div class="controls">
                                        <textarea data-insert-var name="meta_description">{$meta_description}</textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="meta_keywords">

                                    <span data-title="{lang('Variables, can use to', 'smart_filter')}"
                                          class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('settings_help_text')}
                                        {lang('Meta keywords','smart_filter')}:
                                    </label>
                                    <div class="controls">
                                        <textarea data-insert-var name="meta_keywords">{$meta_keywords}</textarea>
                                    </div>
                                </div>


                                <div class="control-group">
                                    <label class="control-label" for="seo_text">

                                        <span data-title="{lang('Categories text in main page', 'smart_filter')}"
                                              class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('settings_help_text')}

                                        {lang('Seo text','smart_filter')}:
                                    </label>
                                    <div class="controls">
                                        <textarea data-insert-var class="elRTE" id="seo_text"
                                                  name="seo_text">{$seo_text}</textarea>
                                    </div>
                                </div>
                                {form_csrf()}
                            </form>

                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
