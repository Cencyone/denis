<div class="addCategoryModalOasis  modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>{lang("Create a category","admin")}</h3>
    </div>
    <div class="modal-body">

        <form action="/admin/components/run/shop/products/fastCategoryCreate" method="post" id="fast_add_formOasis"
              class="form-horizontal">
            <input type="hidden" name="locale" value="{echo $locale}">
            <div class="control-group">
                <label class="control-label">
                    {lang("Title","admin")}:
                    <span class="must">*</span>
                </label>
                <div class="controls">
                    <input type="text" name="name" value="" class="required">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">
                    {lang("Parent","admin")}:
                </label>
                <div class="controls">


                    <select name="parent_id">
                        <option value="0" selected="selected">{lang("No","admin")}
                            <git st
                            /option>
                            {foreach $categories as $category}
                        <option {if $category->getLevel() == 0}style="font-weight: bold;"{/if}
                                value="{echo $category->getId()}">{str_repeat('-',$category->getLevel())} {echo ShopCore::encode($category->getName())}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </form>

    </div>
    <div class="modal-footer">
        <a href="#" class="btn" onclick="$('.modal').modal('hide');">{lang('Cancel','admin')}</a>
        <a href="#" class="btn btn-primary" onclick="addCategoryModalOasis()">{lang('Create','admin')}</a>
    </div>
</div>


<div class="container">
    <section class="mini-layout">
        <div class="frame_title clearfix">
            <div class="pull-left">
                <span class="help-inline"></span>
                <span class="title">{lang('parse_yandex_xml management', 'parse_yandex_xml')}</span>
            </div>
            <div class="pull-right">
                <div class="d-i_b">
                    <a class="btn btn-small " href="{$BASE_URL}admin/components/cp/parse_yandex_xml/list_provider_files">
                        <i class="icon-wrench"></i>
                        {lang('Список соответсвия поставщиков и файлов', 'parse_yandex_xml')}
                    </a>
                </div>

                <div class="d-i_b">
                    <button type="button" class="btn btn-small btn-primary  formSubmit"
                            data-form="#settings_form"><i class="icon-ok"></i>{lang('Save','parse_yandex_xml')}</button>
                </div>
            </div>
        </div>


        <div class="tab-pane active" id="parse_yandex_xml">
            <div class="span12" style="margin-left: 0px">
                <form id="settings_form" action={site_url('/admin/components/cp/parse_yandex_xml/save')} method="post"
                      class="">


                    <div class="span12" style="margin-left:  0px">
                        <table class="table  table-bordered table-hover table-condensed content_big_td">


                            <thead>
                            <tr>
                                <a href="{base_url()}admin/backup#backup_create" style="margin-left:15px;"><i
                                            class="icon-plus-sign"></i> {lang(' Перед началом парсинга, необходимо сделать бек-ап базы данных','parse_yandex_xml')}
                                </a>
                                <th colspan="4">
                                    {lang('Ручная загрузка','parse_yandex_xml')}
                                </th>
                                <th colspan="4">
                                    {lang('Настройка для автообновления по существующим поставщикам','parse_yandex_xml')}
                                </th>
                               {/*} <th colspan="4">
                                    {lang('Настройка для автосодания/автообновления','parse_yandex_xml')}
                                </th>{ */}


                            </tr>
                            </thead>
                            <tr>
                                <td colspan="4">


                                    <div class="inside_padd parse_yandex_xml__frame">
                                        <div class="control-group">
                                            <label class="control-label">{lang('Файл стандартной структуры для ЯндексМаркета YML','parse_yandex_xml')}</label>
                                            <div class="inside_padd parse_yandex_xml__frame">
                                                <div class="control-group">
                                                    <label class="control-label">
                                                        {echo lang('Модуль создает в магазине из файла новые категории,
                                    товары, бренды, свойства. Те товары,
что ранее были выгружены модулем, обновляют из файла
                                    цену, количество, активность','parse_yandex_xml')}
                                                    </label>


                                                </div>
                                            </div>
                                            <br/>
                                            <br/>

                                            <div class="control-group">
                                                <label class="control-label"
                                                       for="comment">{lang('Родительская категория (сохранить настройки после изминения) ','admin')}
                                                    :</label>
                                                <div class="controls">

                                                    <select name="main_set[CategoryIdOasis]" id="comment" class="span5">
                                                        <option value="0">НЕТ</option>
                                                        {foreach $categories as $category}
                                                            <option {if $category->getLevel() == 0}style="font-weight: bold;"{/if} {if $settings['main_set']['CategoryIdOasis'] == $category->getId()}selected="selected"{/if}
                                                                    value="{echo $category->getId()}">{str_repeat('-',$category->getLevel())} {echo $category->getName()}</option>
                                                        {/foreach}
                                                    </select>
                                                    <a onclick="$('.addCategoryModalOasis').modal();
                                            return false;" class="btn " href="#" style="margin-left:15px;"><i
                                                                class="icon-plus-sign"></i> {lang("Create a category","admin")}
                                                    </a>

                                                </div>
                                            </div>


                                            <div class="control-group">
                                                <label class="control-label"
                                                       for="comment">{lang('Названия поставщика для текущего файла ','admin')}
                                                    :</label>
                                                <div class="controls">

                                                    <input name="main_set[provider_name_to_current_parsing]" type="text"
                                                           value="{if $settings['main_set']['provider_name_to_current_parsing']}{echo $settings['main_set']['provider_name_to_current_parsing']}{else:}{/if}"
                                                           id="provider_name_to_current_parsing"/>

                                                </div>
                                            </div>





                                            <input name="main_set[url_oasis]" type="text"
                                                   value="{if $settings['main_set']['url_oasis']}{echo $settings['main_set']['url_oasis']}{else:}{/if}"
                                                   id="url_oasis"/>

                                            <a href="{echo site_url('application/third_party/filemanager/dialog.php?field_id=url_oasis');}"
                                               class="btn  iframe-btn" type="button">
                                                <i class="icon-file"></i>
                                                {lang('Select file', 'admin')}
                                            </a>


                                        </div>
                                        <a href="#" id="runScript" class="btn btn-primary pjax" data-action="run"
                                           onclick="url_oasis('0')">Старт</a>
                                    </div>

                                </td>
                                <td colspan="4">

                                    <div class="inside_padd parse_yandex_xml__frame">
                                        <div class="control-group">
                                            <label class="control-label">
                                                {echo lang('Укажите базовые настройки для обноления ранее созданных товаров по графику.','parse_yandex_xml')}
                                            </label>
                                            <label class="control-label">
                                                {echo lang('Ссылка для Крону(вставить в настройку планировщика на сервере).','parse_yandex_xml')}
                                                <br/>
                                                {echo base_url().'parse_yandex_xml/cron_updete/update/provider'}
                                                <br/>
                                                {echo lang('Вместо provider - указать названия постащика в планировщике','parse_yandex_xml')}

                                            </label>


                                        </div>
                                    </div>
                                    <br/>
                                    <br/>

                                    {/*}<div class="control-group">
                                    <label class="control-label">
                                        {echo lang('Ссылка на файл с товарами.','parse_yandex_xml')}
                                    </label>
                                    <input name="cron[url_oasis_cron]" type="text"
                                           value="{if $settings['cron']['url_oasis_cron']}{echo $settings['cron']['url_oasis_cron']}{else:}{/if}"
                                           id="url_oasis_cron"/>
                                    </div>{ */}


                                    <div class="control-group">
                                        <label class="control-label"
                                               for="del_prop">{lang('Включить обновление существующих товаров', 'nova_poshta_and')}
                                            :</label>

                                        <div class="controls">
                                                                <span class="frame_label">
                                                                        <span class="niceCheck b_n">
                                                                            <input name="cron[to_update][on_off]"
                                                                                   id="delMetod"
                                                                                   value="1" {if $settings['cron']['to_update']['on_off']== 1}  checked="checked"{/if}
                                                                                   type="checkbox"/>
                                                                        </span>
                                                                </span>
                                        </div>
                                    </div>

                                    <div class="control-group m-t_10 frame_label no_connection">

                                        <div class="controls ctext">
                                            <label class="control-label" for="main_types">{lang('Обновлять все параметры товара (количество, цену, описания)', 'parse_yandex_xml')}                                                   :</label>
                                                                <span class="niceRadio" style="background-position: -179px 0px;">
                                                                    <input name="cron[to_update][type]"
                                                                           id="main_types"
                                                                           value="all" {if $settings['cron']['to_update']['type']== 'all'}  checked="checked"{/if}
                                                                           type="radio"/>
                                                                </span>


                                        </div>
                                    </div>



                                    <div class="control-group">
                                        <label class="control-label"
                                               for="del_prop">{lang('Обновлять количество', 'parse_yandex_xml')}
                                            :</label>

                                        <div class="controls">
                                                                <span class="frame_label">
                                                                        <span class="niceRadio">
                                                                            <input name="cron[to_update][type]"
                                                                                   id="delMetod"
                                                                                   value="stk" {if $settings['cron']['to_update']['type']== 'stk'}  checked="checked"{/if}
                                                                                   type="radio"/>
                                                                        </span>
                                                                </span>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label"
                                               for="del_prop">{lang('Обновлять цену', 'parse_yandex_xml')}
                                            :</label>

                                        <div class="controls">
                                                                <span class="frame_label">
                                                                        <span class="niceRadio">
                                                                            <input name="cron[to_update][type]"
                                                                                   id="delMetod"
                                                                                   value="prc" {if $settings['cron']['to_update']['type']== 'prc'}  checked="checked"{/if}
                                                                                   type="radio"/>
                                                                        </span>
                                                                </span>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label"
                                               for="del_prop">{lang('Обновлять цену, количество', 'parse_yandex_xml')}
                                            :</label>

                                        <div class="controls">
                                                                <span class="frame_label">
                                                                        <span class="niceRadio">
                                                                            <input name="cron[to_update][type]"
                                                                                   id="delMetod"
                                                                                   value="prc_stk" {if $settings['cron']['to_update']['type']== 'prc_stk'}  checked="checked"{/if}
                                                                                   type="radio"/>
                                                                        </span>
                                                                </span>
                                        </div>
                                    </div>


                                </td>
                                {/*} <td colspan="4">
                                   <div class="control-group">
                                        <label class="control-label"
                                               for="del_prop">{lang('Включить создания новых товров (только в существующих на сайте категориях, ранее созданных из ресурса)', 'nova_poshta_and')}
                                            :</label>
                                        <label class="control-label">
                                            {echo lang('Ссылка для Крона по созданию новых товаров(вставить в настройку планировщика на сервере).','parse_yandex_xml')}
                                            <br/>
                                            {echo base_url().'parse_yandex_xml/cron_create/create/provider'}
                                            <br/>
                                            {echo lang('Вместо provider - указать названия постащика в планировщике','parse_yandex_xml')}


                                        </label>

                                        <div class="controls">
                                                                <span class="frame_label">
                                                                        <span class="niceCheck b_n">
                                                                            <input name="cron[to_create][on_off]"
                                                                                   id="delMetod"
                                                                                   value="1" {if $settings['cron']['to_create']['on_off']== 1}  checked="checked"{/if}
                                                                                   type="checkbox"/>
                                                                        </span>
                                                                </span>
                                        </div>
                                    </div>

                                </td>{ */}

                            </tr>

                            </tbody>
                        </table>
                    </div>


                </form>


                <div class="form">

                    <input id="offset" name="offset" type="hidden">
                    <div class="action_name" style="display: none;"></div>
                    <div class="action_prop" style="display: none;"></div>
                    <div class="action_fill_prop" style="display: none;"></div>
                    <div class="action_finish" style="display: none;"></div>


                    <div class="progress" style="display: none; background-color: lightgrey">
                        <div class="bar"></div>
                    </div>


                    {/*}<a href="#" id="refreshScript" class="btns" style="display: none;">Заново</a>{ */}
                </div>


            </div>
        </div>
    </section>

</div>

