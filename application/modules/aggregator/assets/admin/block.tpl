<div class="tab-pane" id="{echo $aggregator->getId()}">
    <table class="table  table-bordered table-hover table-condensed content_big_td">
        <thead>
        <tr>
            <th colspan="6">
                {echo $aggregator->getName()}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="6">
                <div class="inside_padd">
                    <div class="control-group">
                        {$fields}
                    </div>

                    <div class="control-group">
                        <a target="_blank" href="{echo site_url('aggregator/service/' .$aggregator->getId())}">{echo $aggregator->getName()} xml</a>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
