jQuery(document).ready(function($){function i(){$rows=$settings_section.find(".mapping-table-field-row"),count=0,$rows.each(function(){$(this).data("index",count),$(this).find("input.table-input").each(function(){$(this).attr("name",$(this).data("variable")+"["+count+"]["+$(this).data("name")+"]")}),count_field=0,$rows_field=$(this).find(".mapping-field-row"),$rows_field.each(function(){$(this).find("input.field-input").each(function(){$(this).attr("name",$(this).data("variable")+"["+count+"][fields]["+count_field+"]["+$(this).data("name")+"]")}),count_field++}),count++})}0!=$(".airtable-api-settings").length&&($settings_section=$(".airtable-api-settings"),$settings_section.on("click",".mapping-table-delete, .mapping-field-delete",function(t){t.preventDefault();var e="";return e=$(this).hasClass("mapping-table-delete")?alert_message.remove_table:alert_message.remove_field,confirm(e)&&($(this).parent().remove(),i()),!1}),$settings_section.find(".mapping-table-add, .mapping-field-add").on("click",function(t){return t.preventDefault(),$item=$(this).parent().find(".form-row").first().clone(),$item.find("input").val(""),$item.find(".mapping-fields").remove(),$item.insertBefore($(this)),i(),!1}),$settings_section.find(".mapping-fields").each(function(){var t=$(this);t.find(".indications .field-name").on("click",function(){var e=t.find(".field-id"),n=$(this).html(),a=!1;e.each(function(){$(this).val()==n&&(a=!0)}),a?alert(alert_message.duplicate_entry):($item=t.find(".mapping-field-row").first().clone(),$item.find("input").val(""),$item.find(".field-id.field-input").val(n),$item.insertBefore(t.find(".mapping-field-row").first()),i())})}),$(".code-mirror").each(function(){var i=CodeMirror.fromTextArea(this,{lineNumbers:!0,matchBrackets:!0,mode:"application/x-httpd-php",indentUnit:4,indentWithTabs:!0})}))});