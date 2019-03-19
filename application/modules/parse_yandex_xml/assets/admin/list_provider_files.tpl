<div class="container">


    <section class="mini-layout">
        <div class="frame_title clearfix">
            <div class="pull-left">
                <span class="help-inline"></span>
                <span class="title">{lang('Список соответсвия поставщиков и файлов', 'parse_yandex_xml')}</span>
            </div>
            <div class="pull-right">
                <div class="d-i_b">
                    <a href="{$BASE_URL}admin/components/cp/parse_yandex_xml" class="t-d_n m-r_15 pjax">
                        <span class="f-s_14">←</span>
                        <span class="t-d_u">{lang('Go back', 'parse_yandex_xml')}</span>
                    </a>
                </div>

            </div>
        </div>
        <div class="tab-pane" id="variables">
            <div class="inside_padd">
                <table class="table  table-bordered table-hover table-condensed variablesTable t-l_a">
                    <thead>
                    <th>{lang('имя постащика латиницей (как в импорте-єкспорте)', 'parse_yandex_xml')}</th>
                    <th>{lang('Ссылка на YML файл поставщика', 'parse_yandex_xml')}</th>
                    <th>{lang('Edit', 'parse_yandex_xml')}</th>
                    <th>{lang('Delete', 'parse_yandex_xml')}</th>
                    </thead>
                    {foreach $prov_settings as $variable => $variable_value}
                        <tr>
                            <td class="span5">
                                <div class="variable">
                                    {echo $variable}
                                </div>
                                <input type="text" name="variableEdit" class="variableEdit" style="display: none"/>
                            </td>
                            <td class="span5">
                                <div class="variableValue">
                                    {echo $variable_value}
                                </div>
                                <input type="text" name="variableValueEdit" class="variableValueEdit"
                                       style="display: none"/>
                            </td>
                            <td style="width: 100px">
                                <button class="btn my_btn_s btn-small editVariable" type="button">
                                    <i class="icon-edit"></i>
                                </button>
                                <button data-update="count"
                                        onclick="providerVariables.update($(this),  '{echo $variable}', '{echo $locale}')"
                                        class="btn btn-small refreshVariable my_btn_s" type="button"
                                        style="display: none;">
                                    <i class="icon-ok"></i>
                                </button>
                            </td>
                            <td class="span1">
                                <button class="btn my_btn_s btn-small btn-danger " type="button"
                                        onclick="providerVariables.delete('{echo $variable}', $(this), '{echo $locale}')">
                                    <i class="icon-trash"></i>
                                </button>
                            </td>
                        </tr>
                    {/foreach}
                    <tr class="addVariableContainer" style="display: none">
                        <td class="span5">
                            <input type="text" name="variableEdit" class="variableEdit"/>
                        </td>
                        <td class="span5">
                            <input type="text" name="variableValueEdit" class="variableValueEdit"/>
                        </td>
                        <td style="width: 100px" colspan="2">
                            <button data-update="count"
                                    onclick="providerVariables.add($(this), '{echo $locale}');"
                                    data-variable="" class="btn btn-small" type="button"
                                    style="display: block; margin-top: 4px;margin-left: 4px">
                                <i class="icon-plus"></i>
                            </button>
                        </td>
                    </tr>
                </table>
                <button class="btn btn-small btn-success addVariable">
                    <i class="icon-plus icon-white"></i>&nbsp;{lang('Add new variable', 'parse_yandex_xml')}
                </button>
            </div>
        </div>
</div>
</section>
</div>