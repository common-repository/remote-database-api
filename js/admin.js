jQuery(document).ready(function($){

    if($('.airtable-api-settings').length == 0)
        return;

    $settings_section = $('.airtable-api-settings');

    // MAPPING TABLE - DELETE

    $settings_section.on('click', '.mapping-table-delete, .mapping-field-delete', function(e){
        e.preventDefault();

        var message = "";
        if($(this).hasClass('mapping-table-delete'))
            message = alert_message.remove_table;
        else
            message = alert_message.remove_field;

        if (confirm(message))
        {
            $(this).parent().remove();
            mapping_table_fields_calculate_index();
        }
        return false;
    });

    // MAPPING TABLE - ADD

    $settings_section.find('.mapping-table-add, .mapping-field-add').on('click', function(e){
        e.preventDefault();
        $item = $(this).parent().find('.form-row').first().clone();
        $item.find('input').val('');
        $item.find('.mapping-fields').remove();
        $item.insertBefore($(this));
        mapping_table_fields_calculate_index();
        return false;
    });

    // MAPPING TABLE - RESORT

    function mapping_table_fields_calculate_index()
    {
        $rows = $settings_section.find('.mapping-table-field-row');

        count = 0;
        $rows.each(function(){

            $(this).data('index', count);


            $(this).find('input.table-input').each(function()
            {
                $(this).attr('name', $(this).data('variable') + '[' + count + ']' + '[' + $(this).data('name') + ']');
            });

            count_field = 0;

            $rows_field = $(this).find('.mapping-field-row');
            $rows_field.each(function()
            {
                $(this).find('input.field-input').each(function()
                {
                    $(this).attr('name', $(this).data('variable') + '[' + count + '][fields][' + count_field + ']' + '[' + $(this).data('name') + ']');
                });

                count_field++;
            });

            count++;
        });
    }

    // MAPPING FIELD

    $settings_section.find('.mapping-fields').each(function(){
        var $mapping_field_section = $(this);

        $mapping_field_section.find('.indications .field-name').on('click', function(){

            var $inputs = $mapping_field_section.find('.field-id');

            var value_name = $(this).html();
            var already_exist_input_with_this_value = false;

            $inputs.each(function(){
                if($(this).val() == value_name){
                    already_exist_input_with_this_value = true;
                }
            });

            if (!already_exist_input_with_this_value) {
                $item = $mapping_field_section.find('.mapping-field-row').first().clone();
                $item.find('input').val('');
                $item.find('.field-id.field-input').val(value_name);
                $item.insertBefore($mapping_field_section.find('.mapping-field-row').first());
                mapping_table_fields_calculate_index();
            }
            else
            {
                alert(alert_message.duplicate_entry);
            }

        });
    });

    // TEMPLATE

    $('.code-mirror').each(function () {
        var myCodeMirror = CodeMirror.fromTextArea(this,{
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true
        });
    });

});