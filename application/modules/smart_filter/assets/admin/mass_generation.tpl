<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang('Mass generation','smart_filter')}</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="{$admin_url}" class="t-d_n m-r_15"><span
                            class="f-s_14">←</span> <span class="t-d_u">{lang('Go back','admin')}</span></a>
                <button type="button" class="btn btn-small btn-primary formSubmit" data-form="#seo_form" data-submit>
                    <i class="icon-plus-sign icon-white"></i>{lang('Generate','admin')}</button>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="modules">

            <table class="table  table-bordered table-hover table-condensed content_big_td">
                <thead>
                <tr>
                    <th colspan="6">
                        {lang('Information','admin')}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="6">
                        <div class="inside_padd">
                            <form method="post" id="seo_form"
                                  action="{$admin_url}/mass_generation" class="form-horizontal">

                                <input type="hidden" name="locale" data-locale value="{$locale}">
                                <div class="control-group m-t_10">
                                    <label class="control-label" for="category_id">{lang('Category','admin')}:</label>
                                    <div class="controls">
                                        <select name="category_id" data-categories
                                                data-change="data-properties,data-brands">
                                            {foreach $categories as $category}
                                                <option value="{echo $category->getId()}">{echo str_repeat('-', $category->getVirtualColumn('level')) . $category->getName()}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group m-t_10" data-hidden>
                                    <label class="control-label"
                                           for="brand_id">{lang('Brand','admin')}:</label>
                                    <div class="controls">
                                        <select multiple name="brand_id[]" data-brands="ajaxGetBrandsMultiple">
                                            {foreach $brands as $brand}
                                                <option value="{echo $brand['id']}">{echo $brand['value']}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>


                                <div class="control-group m-t_10" data-hidden>
                                    <label class="control-label" for="property_id">{lang('Property','admin')}:</label>
                                    <div class="controls">
                                        <select multiple name="property_id[]"
                                                data-properties="ajaxGetPropertiesMultiple">
                                            {foreach $properties as $property}
                                                <option value="{echo $property['id']}">{echo $property['value']}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>


                                <div class="control-group m-t_10 ">
                                    <label class="control-label" for="follow">Index:</label>
                                    <div class="controls">
                                        <select name="index">
                                            {foreach $index as $name => $value}
                                                <option value="{$name}">{$value}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group m-t_10 ">
                                    <label class="control-label" for="follow">Follow:</label>
                                    <div class="controls">
                                        <select name="follow">
                                            {foreach $follow as $name => $value}
                                                <option value="{$name}">{$value}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"></div>
                                    <div class="controls">
                                        <span class="frame_label no_connection">
                                            <span class="niceCheck">
                                                <input type="checkbox" name="active" checked/>
                                            </span>
                                            {lang('Active','admin')}
                                        </span>
                                    </div>
                                </div>


                                <div class="control-group">
                                    <label class="control-label" for="h1">

                                    <span data-title="{lang('Variables, can use to', 'smart_filter')}"
                                          class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('help_text')}
                                        {lang('H1','smart_filter')}:

                                    </label>
                                    <div class="controls">
                                        <input data-insert-var type="text" class="span12" name="h1"/>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="meta_title">

                                    <span data-title="{lang('Variables, can use to', 'smart_filter')}"
                                          class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('help_text')}
                                        {lang('Meta title','smart_filter')}:


                                    </label>
                                    <div class="controls">
                                        <textarea data-insert-var name="meta_title"></textarea>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="meta_description">

                                    <span data-title="{lang('Variables, can use to', 'smart_filter')}"
                                          class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('help_text')}
                                        {lang('Meta description','smart_filter')}:


                                    </label>
                                    <div class="controls">
                                        <textarea data-insert-var name="meta_description"></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="meta_keywords">

                                    <span data-title="{lang('Variables, can use to', 'smart_filter')}"
                                          class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('help_text')}
                                        {lang('Meta keywords','smart_filter')}:
                                    </label>
                                    <div class="controls">
                                        <textarea data-insert-var name="meta_keywords"></textarea>
                                    </div>
                                </div>


                                <div class="control-group">
                                    <label class="control-label" for="seo_text">

                                        <span data-title="{lang('Categories text in main page', 'smart_filter')}"
                                              class="popover_ref" data-original-title="" data-placement="right">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                        {include_tpl('help_text')}

                                        {lang('Seo text','smart_filter')}:
                                    </label>
                                    <div class="controls">
                                        <textarea data-insert-var class="elRTE" id="seo_text"
                                                  name="seo_text"></textarea>
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
