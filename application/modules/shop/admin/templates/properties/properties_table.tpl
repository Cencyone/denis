<div class="control-group">
    <label class="control-label">{echo $model->getLabel('Data')}:</label>
    <div class="controls">

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>
                    <div id="main_value" class="input-append">
                        <input id="value_input" type="text" placeholder="{lang('Enter value and press + button','admin')}"/>
                        <button onclick="cloneInput()" type="button"
                                class="btn btn-small btn-success">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody id="property_values" class="sortable">
            {foreach $model->getTranslatedValues($locale) as $value}
                {$placeholder = $value->getTranslation(MY_Controller::defaultLocale())->getValue()}
                {$value->setLocale($locale)}
                    <tr>
                        <td>
                            <div class="input-append">
                                <input placeholder="{htmlspecialchars($placeholder)}" type="text" name="property_value[id_{echo $value->getId()}]"
                                       value="{echo htmlspecialchars($value->getValue())}">
                                <button onclick="removeValue(this)" type="button" class="btn btn-small">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
            {/foreach}
            <tr id="clone" style="display: none">
                <td>
                    <div class="input-append">
                        <input type="text">
                        <button onclick="removeValue(this)" type="button" class="btn btn-small">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </td>
            </tr>

            </tbody>
        </table>
    </div>
</div>

{/* todo: move to some js file */}
{literal}
    <script type="text/javascript">

        function cloneInput() {
            var value = $('#value_input').val().trim();
            if (value) {
                var tr = $('#clone').clone();
                tr.find('input').val(value).attr('name', 'property_value[]');
                tr.prependTo('#property_values');
                tr.show();
                $('#value_input').val('');
            }
        }

        function removeValue(button) {
            console.log($(button).prev('input'));
            $(button).closest('tr').remove();
        }

    </script>
{/literal}
