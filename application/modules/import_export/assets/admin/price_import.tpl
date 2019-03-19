<section class="mini-layout">
    <div class="frame_title clearfix">
        <div class="pull-left">
            <span class="help-inline"></span>
            <span class="title">{lang('Import-Export CSV/XLS','import_export')}</span>
        </div>
        <div class="pull-right">
            <div class="d-i_b">
                <a href="{$BASE_URL}admin/components/" class="t-d_n m-r_15"><span class="f-s_14">←</span> <span class="t-d_u">{lang("Back", 'admin')}</span></a>
            </div>
        </div>
    </div>
    <div class="btn-group myTab m-t_20" data-toggle="buttons-radio">
        <a href="{$BASE_URL}admin/components/init_window/import_export/getTpl/import" class="btn btn-small">{lang('Import', 'import_export')}</a>
        {if $usePriceType}

            <a href="{echo site_url('/admin/components/init_window/import_export/getTpl/price_import')}" class="active btn btn-small">{lang('Price import', 'import_export')}</a>
            <a href="{echo site_url('/admin/components/init_window/import_export/getTpl/price_export')}" class="btn btn-small">{lang('Price export', 'import_export')}</a>

        {/if}
        <a href="{$BASE_URL}admin/components/init_window/import_export/getTpl/export" class="btn btn-small">{lang('Export', 'import_export')}</a>
        <a href="{$BASE_URL}admin/components/init_window/import_export/getTpl/archiveList" class="btn btn-small">{lang('List archives exports', 'import_export')}</a>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="importcsv">
            <table class="table  table-bordered table-hover table-condensed content_big_td">
                <thead>
                <tr>
                    <th colspan="6">{lang('Price import','import_export')}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="6">
                        <div class="importProcess">
                            <!-- Start. Choose file and load to server on checked slot  -->

                            <!-- End. Choose file and load to server on checked slot  -->

                            <!-- Start. Choose file and load to server on checked slot  -->
                            <form action="{site_url('/admin/components/init_window/import_export/getPriceImport')}" method="post" enctype="multipart/form-data" id="makeImportForm">
                                <div class="inside_padd form-horizontal row-fluid">
                                    <div class="control-group form-horizontal">
                                        <label class="control-label"></label>
                                        <div class="controls">
                                                <span class="btn btn-small p_r pull-left">
                                                    <i class="icon-folder-open"></i>&nbsp;&nbsp;{lang('Select the file','import_export')}
                                                    <input type="file" id="import_file" name="import_file" class="btn-small btn" />
                                                </span>
                                        </div>
                                    </div>
                                    <!-- Start. Let's go Button ;) -->

                                    <div class="control-group">
                                            <span class="controls span2">
                                                <button class="btn btn-success" id="makePriceImport" type="button">{lang('Start import','import_export')}</button
                                            </span>
                                    </div>


                                    <!-- End. Let's go Button ;) -->
                                </div>

                                {form_csrf()}
                            </form>
                            <!-- End. Choose file and load to server on checked slot  -->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <label id="progressLabel"></label>
                        <div class="progress progress-striped active" id="progressBlock" style="display:none;margin-top:15px;">
                            <div id="percent" class="bar" style="width: 1%;">
                                <div style="position: relative; top:1px">
                                    <span id="ratio" style="color: black;"></span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
