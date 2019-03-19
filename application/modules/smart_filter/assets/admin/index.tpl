<!-- ---------------------------------------------------Блок видалення---------------------------------------------------- -->
<div class="modal hide fade modal_del" delete-filter>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>{lang('Items removing','smart_filter')}</h3>
    </div>
    <div class="modal-body">
        <p>{lang('Remove selected items?','smart_filter')}</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" data-delete="{echo $admin_url . '/delete'}" >{lang('Delete','admin')}</a>
        <a href="#" class="btn" onclick="$('.modal').modal('hide');">{lang('Cancel','admin')}</a>
    </div>
</div>

<!-- ---------------------------------------------------Блок видалення---------------------------------------------------- -->

<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang('Smart filter','smart_filter')}</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a class="btn btn-primary btn-small"
                   href="{$admin_url}/settings">{lang('Settings', 'smart_filter')}</a>
                <a class="btn btn-primary btn-small"
                   href="{$admin_url}/mass_generation">{lang('Mass generation', 'smart_filter')}</a>
                <button class="d_n btn btn-small action_on listFilterSubmitButton"
                        onclick="$('#brands_filter').submit();"><i class="icon-filter"></i>{lang('Filter','admin')}
                </button>
                <a class="btn btn-small btn-success" href="{echo $admin_url . '/create'}">
                    <i class="icon-plus-sign icon-white"></i>{lang('Create','admin')}</a>
                <button type="button" class="btn btn-small btn-danger disabled action_on"
                        onclick="$('[delete-filter]').modal();">
                    <i class="icon-trash"></i>{lang('Delete','admin')}</button>


                {$disabled = count(array_intersect(['id', 'category_id', 'url_pattern', 'active'], array_keys($_GET)))?'':'disabled'}
                <a title="{lang('Reset filter','admin')}"
                   href="{$admin_url}"
                   type="button" class="btn btn-small {$disabled}">
                    <i class="icon-refresh"></i>{lang('Cancel filter','admin')}
                </a>


            </div>
        </div>
    </div>
    <div class="tab-content">


        <div class="row-fluid">
            <table class="table  table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th class="t-a_c span1">
                            <span class="frame_label">
                                <span class="niceCheck b_n">
                                    <input type="checkbox"/>
                                </span>
                            </span>
                    </th>
                    <th class="span1">{lang('ID','admin')}</th>
                    <th>{lang('Value','admin')}</th>
                    <th>{lang('Category','admin')}</th>
                    <th>{lang('URL','admin')}</th>
                    <th>Robots</th>
                    <th>{lang('Active','admin')}</th>
                </tr>
                <tr class="head_body">
                    <form action="" data-filter method="get">
                        <td class="t-a_c span1"></td>
                        <td class="span1">
                            <input type="text" data-submit class="searchInp" onkeypress="validateN(event)"
                                   name="id"
                                   value="{echo  $CI->input->get('id')?:''}">
                        </td>
                        <td>
                            <input data-submit type="text" name="name"
                                   {if !empty($_GET['name'])}value="{htmlentities($_GET['name'])}"{/if}>
                        </td>
                        <td>
                            <select data-submit name="category_id">
                                {foreach $categories as $id => $category}
                                    <option value="{$id}" {echo $id == $category_id?'selected':''}>{echo $category->getName()}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td>
                            <input data-submit type="text" name="url_pattern"
                                   {if !empty($_GET['url_pattern'])}value="{htmlentities($_GET['url_pattern'])}"{/if}>
                        </td>

                        <td>

                        </td>
                        <td>
                            <select data-submit name="active">
                                <option value="">{lang('All','admin')}</option>
                                <option value="true"
                                        {if $_GET['active'] == 'true'}selected="selected"{/if}>{lang('Active','admin')}</option>
                                <option value="false"
                                        {if $_GET['active'] == 'false'}selected="selected"{/if}>{lang('Not active','admin')}</option>
                            </select>
                        </td>

                    </form>
                </tr>

                </thead>
                </thead>
                <tbody>
                {if count($items)>0}

                    {foreach $items as $item}
                        <tr class="simple_tr" data-id="{echo $item->getId()}">
                            <td class="t-a_c">
                            <span class="frame_label">
                                <span class="niceCheck b_n">
                                    <input type="checkbox" name="ids" value="{echo $item->getId()}"/>
                                </span>
                            </span>
                            </td>
                            <td>{echo $item->getId()}</td>
                            <td>
                                <a href="{echo $admin_url}/edit/{echo $item->getId()}">{echo $item->getName()}</a>
                            </td>
                            <td>
                                {echo isset($categories[$item->getCategoryId()])?$categories[$item->getCategoryId()]->getName():''}
                            </td>
                            <td>
                                {if false === strpos($item->getUrlPattern(), "*")}
                                    <a target="_blank"
                                       href="{echo $item->getFullUrl()}"> {echo $item->getUrlPattern()}</a>
                                {else: }
                                    {echo $item->getUrlPattern()}
                                {/if}
                            </td>
                            <td>{echo $item->getMetaRobots()}</td>
                            <td>
                                <div class="frame_prod-on_off" data-rel="tooltip" data-placement="top"
                                     data-original-title="{lang('show','admin')}">
                                    <span data-change-active
                                          class="prod-on_off {echo $item->isActive()?'':'disable_tovar'}"
                                          data-id="{echo $item->getId()}"></span>
                                </div>
                            </td>
                        </tr>
                    {/foreach}

                {else:}
                    <tr>
                        <td colspan="4">
                            <div style="text-align: center; padding: 5px;">
                                {lang('Patterns list is empty', 'smart_filter')}
                            </div>
                        </td>
                    </tr>
                {/if}
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pagination pull-left">
                    {$pagination}
                </div>
            </div>
        </div>
    </div>
    <div id="setMessage"></div>
</section>

