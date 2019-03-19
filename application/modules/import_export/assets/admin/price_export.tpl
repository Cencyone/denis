<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang('Import-Export CSV/XLS','import_export')}</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="{$BASE_URL}admin/components/init_window/import_export" class="t-d_n m-r_15"><span class="f-s_14">←</span> <span class="t-d_u">{lang("Back", 'admin')}</span></a>
            </div>
        </div>
    </div>
    <div class="btn-group myTab m-t_20" data-toggle="buttons-radio">
        <a href="{$BASE_URL}admin/components/init_window/import_export/getTpl/import" class="btn btn-small">{lang('Import', 'import_export')}</a>
        {if $usePriceType}

            <a href="{echo site_url('/admin/components/init_window/import_export/getTpl/price_import')}" class="btn btn-small">{lang('Price import', 'import_export')}</a>
            <a href="{echo site_url('/admin/components/init_window/import_export/getTpl/price_export')}" class="active btn btn-small">{lang('Price export', 'import_export')}</a>

        {/if}
        <a href="{$BASE_URL}admin/components/init_window/import_export/getTpl/export" class="btn btn-small">{lang('Export', 'import_export')}</a>
        <a href="{$BASE_URL}admin/components/init_window/import_export/getTpl/archiveList" class="btn btn-small">{lang('List archives exports', 'import_export')}</a>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="exportcsv">
            <table class="table  table-bordered table-hover table-condensed content_big_td">
                <thead>
                <tr>
                    <th colspan="6">{lang('Price export','import_export')}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="6">
                        <form action="{site_url('/admin/components/init_window/import_export/getPriceExport')}" method="post" id="makePriceExportForm">
                            {$categories = ShopCore::app()->SCategoryTree->getTree()}
                            <div class="inside_padd form-horizontal row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="">{lang('Categories','import_export')}:</label>
                                    <div class="controls">
                                        <div>
                                            <select name="selectedCats[]" multiple="multiple" class="selectedCats span5" id="selectedCats">
                                                {foreach $categories as $category}
                                                    <option value="{echo $category->getId()}">
                                                        {str_repeat('-',$category->getLevel())} {echo ShopCore::encode($category->getName())}
                                                    </option>
                                                {/foreach}
                                            </select>
                                            <span class="help-block">{lang('Tighten Ctrl to select multiple items', 'admin')}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="type">{lang('Filetype','import_export')}:</label>
                                    <div class="controls">

                                            <span class="frame_label no_connection m-r_15">
                                                <span class="niceRadio b_n">
                                                    <input type="radio" name="type" checked value="xlsx" />
                                                </span>
                                                XLSX
                                            </span>
                                            <span class="frame_label no_connection m-r_15">
                                                <span class="niceRadio b_n">
                                                    <input type="radio" name="type" value="xls" />
                                                </span>
                                                XLS
                                            </span>
                                    </div>
                                </div>
                                <!-- Start. Let's go Button ;) -->
                                <div class="control-group">
                                    <div class="control-label"></div>
                                    <label class="controls">
                                        <button type="button" id="reNameButton" class="btn btn-success runPriceExport">{lang('Start export all categories','import_export')}</button>
                                    </label>
                                </div>
                                <!-- End. Let's go Button ;) -->
                            </div>
                            <input type="hidden" value="0" name="formed_file_type" />
                            {form_csrf()}
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
