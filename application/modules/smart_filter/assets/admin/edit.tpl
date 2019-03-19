<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang('Edit pattern','smart_filter')}: {echo $pattern->getName()}</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="{$BASE_URL}admin/components/cp/smart_filter" class="t-d_n m-r_15"><span
                            class="f-s_14">←</span> <span class="t-d_u">{lang('Go back','admin')}</span></a>
                <button type="button" class="btn btn-small btn-primary formSubmit" data-form="#seo_form" data-submit>
                    <i class="icon-ok icon-white"></i>{lang('Save','admin')}</button>
                <button type="button" class="btn btn-small formSubmit" data-form="#seo_form" data-action="exit"><i
                            class="icon-check"></i>{lang('Save and exit','admin')}</button>
                {echo create_language_select($languages, $locale, "/admin/components/cp/smart_filter/edit/" . $pattern->getId() , FALSE)}
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div>


            <table class="table  table-bordered table-hover table-condensed content_big_td">
                <thead>
                <tr>
                    <th colspan="3">
                        {lang('Information','admin')}
                    </th>
                    <th>
                        {lang('Show on site','admin')}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="3">
                        <div class="inside_padd">
                            <form method="post" id="seo_form"
                                  action="{$BASE_URL}admin/components/cp/smart_filter/edit/{echo $pattern->getId()}/{$locale}"
                                  class="form-horizontal">

                                <input type="hidden" name="locale" data-locale value="{$locale}">

                                <div class="control-group m-t_10">
                                    <label class="control-label" for="category_id">{lang('Category','admin')}:</label>
                                    <div class="controls">
                                        <select name="category_id" data-categories
                                                data-change="data-properties,data-brands">
                                            {foreach $categories as $category}
                                                <option {echo $category->getId() == $pattern->getCategoryId()?'selected':''}
                                                        value="{echo $category->getId()}">{echo str_repeat('-', $category->getVirtualColumn('level')) . $category->getName()}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group m-t_10" data-hidden>
                                    <label class="control-label"
                                           for="brand_id">{lang('Brand','admin')}:</label>
                                    <div class="controls">
                                        <select name="brand_id" data-brands="ajaxGetBrands">
                                            {foreach $brands as $brand}
                                                <option {echo $pattern->getDataBrandId() == $brand['id'] ?'selected':''}
                                                        value="{echo $brand['id']}">{echo $brand['value']}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>


                                <div class="control-group m-t_10" data-hidden>
                                    <label class="control-label" for="property_id">{lang('Property','admin')}:</label>
                                    <div class="controls">
                                        <select name="property_id" data-properties="ajaxGetProperties"
                                                data-change="data-values">
                                            {foreach $properties as $property}
                                                <option {echo $pattern->getDataPropertyId() == $property['id'] ?'selected':''}
                                                        value="{echo $property['id']}">{echo $property['value']}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group m-t_10 {echo !count($values)?'d_n':''}" data-hidden>
                                    <label class="control-label" for="value_id">{lang('Property value','smart_filter')}
                                        :</label>
                                    <div class="controls">
                                        <select name="value_id" data-values="ajaxGetPropertyValues">
                                            {foreach $values as $value}
                                                <option {echo $pattern->getDataPropertyValueId() == $value['id'] ?'selected':''}
                                                        value="{echo $value['id']}">{echo $value['value']}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group m-t_10 ">
                                    <label class="control-label" for="follow">Index:</label>
                                    <div class="controls">
                                        <select name="index">
                                            {foreach $index as $name => $value}
                                                <option value="{$name}" {echo $pattern->getMetaIndex() == $name?'selected':''}>{$value}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group m-t_10 ">
                                    <label class="control-label" for="follow">Follow:</label>
                                    <div class="controls">
                                        <select name="follow">
                                            {foreach $follow as $name => $value}
                                                <option value="{$name}" {echo $pattern->getMetaFollow() == $name?'selected':''}>{$value}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label"></div>
                                    <div class="controls">
                                        <span class="frame_label no_connection">
                                            <span class="niceCheck">
                                                <input type="checkbox"
                                                       name="active" {echo $pattern->isActive()?'checked':''}/>
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
                                        <input data-insert-var type="text" name="h1" class="span12"
                                               value="{echo $pattern->getH1()}"/>
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
                                        <textarea data-insert-var
                                                  name="meta_title">{echo $pattern->getMetaTitle()}</textarea>
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
                                        <textarea data-insert-var
                                                  name="meta_description">{echo $pattern->getMetaDescription()}</textarea>
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
                                        <textarea data-insert-var
                                                  name="meta_keywords">{echo $pattern->getMetaKeywords()}</textarea>
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
                                        <textarea data-insert-var class="elRTE"
                                                  name="seo_text">{echo $pattern->getSeoText()}</textarea>
                                    </div>
                                </div>
                                {form_csrf()}
                            </form>

                        </div>
                    </td>
                    <td>
                        {if !$pattern->hasValuesSubstitution()}
                            <a data-rel="tooltip" data-title="{lang('View on site','admin')}" target="_blank"
                               href="{echo $urlLocale . $pattern->getFullUrl()}">{echo $pattern->getName()}
                            </a>
                        {else:}
                            {foreach $values as $value}
                                {if $value['id'] != 0 }
                                    <a class="" target="_blank" data-rel="tooltip"
                                       data-title="{lang('View on site','admin')}"
                                       href="{echo $urlLocale . str_replace('*', $value['id'], $pattern->getFullUrl())}">
                                        {echo $pattern->getName() . " : ". $value['value']}
                                    </a>
                                    <hr style="margin: 10px 0 10px;">
                                {/if}
                            {/foreach}
                            <br/>
                        {/if}
                        <br/>

                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
