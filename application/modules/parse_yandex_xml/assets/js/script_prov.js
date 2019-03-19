$(document).ready(
    function () {
        $('body').on(
            'click',
            'table.variablesTable .editVariable',
            function () {
                var editor = $(this).closest('tr').find('div.variable');

                var editValue = $.trim(editor.text());
                editor.empty();
                editor.parent().find('.variableEdit').css('display', 'block').val(editValue);

                var editor = $(this).closest('tr').find('div.variableValue');
                var editValue = $.trim(editor.text());
                editor.empty();
                editor.parent().find('.variableValueEdit').css('display', 'block').val(editValue);

                $(this).css('display', 'none');
                $(this).closest('tr').find('.refreshVariable').css('display', 'block');

            }
        );

        $('body').on(
            'click',
            '.addVariable',
            function () {
                $('.addVariableContainer').show();
                $(this).hide();
            }
        );
    }
);

var providerVariables = {
    insertVariable: function (curElem) {
        var curEditor = $(curElem).closest('.control-group').find('div[id*="tinymce"].mce-edit-area');
        var insertedValue = ' ' + $(curElem).val() + ' ';
        $(curElem).closest('.control-group').find('iframe').contents().find('body').trigger('focus');

        if (tinyMCE.activeEditor) {
            var activeEditor = tinyMCE.activeEditor.contentAreaContainer;
            tinyMCE.execCommand("mceInsertContent", false, insertedValue);
        } else {
            $(curElem).closest('.control-group').find('textarea').insertAtCaret(insertedValue);
        }

    },
    delete: function (variable, curElement, locale) {
        $.ajax(
            {
                type: 'POST',
                data: {

                    variable: variable
                },
                url: '/admin/components/cp/parse_yandex_xml/deleteProvider/',
                success: function (data) {
                    if (!data) {
                        showMessage(lang('Error'), lang('Variable is not removed'), 'r');
                        return false;
                    }
                    curElement.closest('tr').remove();
                    showMessage(lang('Message'), lang('Variable successfully removed'));
                }
            }
        );
    },
    update: function (curElement, oldVariable, locale) {
        var closestTr = curElement.closest('tr');
        var variable = closestTr.find('.variableEdit');
        var variableValue = closestTr.find('.variableValueEdit');

        this.validateVariable(variable.val(), variableValue.val());

        $.ajax(
            {
                type: 'POST',
                data: {
                    variable: $.trim(variable.val()),
                    variableValue: $.trim(variableValue.val()),
                    oldVariable: oldVariable,
                },
                url: '/admin/components/cp/parse_yandex_xml/updateProv/' ,
                success: function (data) {
                    if (!data) {
                        showMessage(lang('Error'), lang('Variable is not updated'), 'r');
                        return false;
                    }
                    closestTr.find('.variable').text(variable.val());
                    closestTr.find('.variableValue').text(variableValue.val());
                    variable.css('display', 'none');
                    variableValue.css('display', 'none');
                    closestTr.find('.editVariable').css('display', 'block');
                    closestTr.find('.refreshVariable').css('display', 'none');
                    showMessage(lang('Message'), lang('Variable successfully updated'));
                }
            }
        );
    },
    add: function (curElement,  locale) {
        var variable = curElement.closest('tr').find('.variableEdit');
        var variableValue = curElement.closest('tr').find('.variableValueEdit');

        this.validateVariable(variable.val(), variableValue.val());

        $.ajax(
            {
                type: 'POST',
                data: {
                    variable: $.trim(variable.val()),
                    variableValue: $.trim(variableValue.val()),

                },
                url: '/admin/components/cp/parse_yandex_xml/addProvider/' + locale,
                success: function (data) {
                    if (!data) {
                        showMessage(lang('Error'), lang('Variable is not added'), 'r');
                        return false;
                    }
                    curElement.parent('div').find('.typeVariable').val('');
                    $('.addVariableContainer').css('display', 'none');
                    $('.addVariableContainer').find('input').val('');
                    $('.addVariable').show();
                    $(data).insertBefore('table.variablesTable .addVariableContainer');
                    showMessage(lang('Message'), lang('Variable successfully added'));
                    window.location.reload()
                }
            }
        );
    },
    updateVariablesList: function (curElement, template_id, locale) {
        if (!curElement.hasClass('active')) {
            $.ajax(
                {
                    type: 'POST',
                    data: {
                        template_id: template_id
                    },
                    url: '/admin/components/cp/parse_yandex_xml/getTemplateVariables/' + locale,
                    success: function (data) {
                        $('#userMailVariables').html(data);
                        $('#adminMailVariables').html(data);
                    }
                }
            );
        }
    },
    validateVariable: function (variable, variableValue) {
        var variable = $.trim(variable);
        var variableValue = $.trim(variableValue);

        if (!variable) {
            showMessage(lang('Error'), lang('Enter variable'), 'r');
            exit;
        }

        if (variable.match(/[а-яА-Я]{1,}/)) {
            showMessage(lang('Error'), lang('Variable should contain only Latin characters'), 'r');
            exit;
        }



        if (!variableValue) {
            showMessage(lang('Error'), lang('Variable must have a value'), 'r');
            exit;
        }
    }
};