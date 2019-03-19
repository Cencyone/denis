<div class="container">
    <!-- ---------------------------------------------------Блок видалення---------------------------------------------------- -->
    <div class="modal hide fade modal_del">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>{lang('Remove price type','admin')}</h3>
        </div>
        <div class="modal-body">
            <p>{lang('Remove selected price type?','admin')}</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-primary"
               onclick="delete_function.deleteFunctionConfirm('{site_url('/admin/components/run/shop/variantprice/delete')}')">{lang('Delete','admin')}</a>
            <a href="#" class="btn" onclick="$('.modal').modal('hide');">{lang('Cancel','admin')}</a>
        </div>
    </div>


    <div id="delete_dialog" title="{lang('Removing Price Variant','admin')}" style="display: none">
        {lang('Remove Price Variant?','admin')}
    </div>
    <!-- ---------------------------------------------------Блок видалення---------------------------------------------------- -->
    <div>
        <section class="mini-layout">
            <div class="frame_title clearfix">
                <div class="pull-left">
                    <span class="help-inline"></span>
                    <span class="title">{lang('List price type','admin')}</span>
                </div>
                <div class="pull-right">
                    <div class="d-i_b">
                        <a class="btn btn-small btn-success"
                           href="{site_url('/admin/components/run/shop/variantprice/create')}"><i
                                    class="icon-plus-sign icon-white"></i>{lang('Create price type','admin')}</a>
                        <button type="button" class="btn btn-small btn-danger disabled action_on"
                                onclick="delete_function.deleteFunction()"><i
                                    class="icon-trash"></i>{lang('Delete','admin')}</button>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                {if count($content) > 0}
                    <table class="table  table-bordered table-hover table-condensed t-l_a">
                        <thead>
                        <tr>
                            <th class="t-a_c span1">
                                    <span class="frame_label">
                                        <span class="niceCheck b_n">
                                            <input type="checkbox"/>
                                        </span>
                                    </span>
                            </th>
                            <th>{lang('ID','admin')}</th>
                            <th>{lang('Title','admin')}</th>
                            <th>{lang('User role','admin')}</th>
                            <th>{lang('Currency','admin')}</th>
                            <th>{lang('Status', 'admin')}</th>
                        </tr>
                        </thead>
                        <tbody class="sortable save_positions" data-url="/admin/components/run/shop/variantprice/save_positions">

                        {foreach $content as $model}

                            <tr id="currency_tr{echo $model->getId()}">
                                <td class="t-a_c">
                                        <span class="frame_label">
                                            <span class="niceCheck b_n">
                                                <input type="checkbox" name="ids" value="{echo $model->getId()}"/>
                                            </span>
                                        </span>
                                </td>
                                <td><span>{echo $model->getId()}</span></td>
                                <td class="share_alt">
                                    <a href="{site_url('/admin/components/run/shop/variantprice/edit/' . $model->getId())}" class="title d_i"
                                       data-rel="tooltip" data-placement="top"
                                       data-original-title="{lang('Edit price typet','admin')}">{echo $model->getNameType()}</a>
                                </td>
                                <td>
                                    {echo $model->getUsedRbac()}
                                </td>
                                <td>
                                    <span>
                                        {if $model->getPriceType() != '1'}

                                            {echo $defaultCurrency}

                                        {else:}

                                            {echo $model->getCurrency()->getCode()}

                                        {/if}
                                    </span>
                                </td>
                                <td>


                                    <div class="frame_prod-on_off" data-rel="tooltip" data-placement="top" data-original-title="{lang('show','admin')}">
                                        <span class="prod-on_off variant_price_show_in_site {if $model->getStatus() != 1} disable_tovar{/if}" data-id="{echo $model->getId()}"></span>
                                    </div>

                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                {else:}
                    <div class="alert alert-info" style="margin-bottom: 18px; margin-top: 18px;">
                        {lang('Empty price type list.','admin')}
                    </div>
                {/if}
            </div>
            <div class="clearfix">
            </div>
        </section>
    </div>
</div>