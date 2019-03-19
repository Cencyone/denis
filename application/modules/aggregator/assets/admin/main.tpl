<div class="container">
    <section class="mini-layout">
        <div class="frame_title clearfix">
            <div class="pull-left">
                <span class="help-inline"></span>
                <span class="title">{lang('Price aggregators management', 'aggregator')}</span>
            </div>
            <div class="pull-right">
                <div class="d-i_b">
                    <button type="button" class="btn btn-small btn-primary action_on formSubmit"
                            data-form="#settings_form"><i class="icon-ok"></i>{lang('Save','aggregator')}</button>
                </div>
            </div>
        </div>
        <form id="settings_form" action="/admin/components/cp/aggregator/save" method="post" class="m-t_10">

            <div class="clearfix">
                <div class="btn-group myTab m-t_20 pull-left" data-toggle="buttons-radio">
                    {foreach $aggregators as $key => $aggregator}
                        <a href="#{echo $aggregator->getId()}"
                           class="btn btn-small {echo $key?'':'active'}">{echo $aggregator->getName()}</a>
                    {/foreach}
                </div>
            </div>

            <div class="tab-content">
                {foreach $aggregators as $key => $aggregator}
                    <div class="tab-pane {echo $key?'':'active'}" id="{echo $aggregator->getId()}">
                        {echo $configsView[$aggregator->getId()]}
                    </div>
                {/foreach}
            </div>

        </form>
    </section>
    </form>
</div>
