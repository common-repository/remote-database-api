<?php

/**
 * Class AirtableApi
 * -----------------------
 */

class AirtableApiAdmin extends AirtableApiCore {

    public function __construct() {

    }

    public static function init()
    {
        self::hooks();
    }

    private function hooks()
    {
        add_action('admin_init', array('AirtableApiAdmin', 'settings_fields'), 5 );
        add_action('admin_menu', array('AirtableApiAdmin', 'settings'));
        add_action('admin_enqueue_scripts', array('AirtableApiAdmin', 'admin_styles'), 11 );
        add_action('admin_enqueue_scripts', array('AirtableApiAdmin', 'admin_scripts'), 12 );

    }

    public static function  admin_styles() {

        $admin_handle = self::get_plugin_slug() . '_admin_css';

        wp_register_style( $admin_handle, AIRTABLEAPI_URL_STYLES . '/admin.css', false, '1.0.0' );
        wp_enqueue_style( $admin_handle );

        wp_register_style( self::get_plugin_slug() . '_codemirror', AIRTABLEAPI_URL_STYLES . '/lib/codemirror.css', false, '1.0.0' );
        wp_enqueue_style( self::get_plugin_slug() . '_codemirror' );
    }

    public static function  admin_scripts($hook) {

        $admin_handle = self::get_plugin_slug() . '_admin_js';

        wp_enqueue_script( self::get_plugin_slug() . '_codemirror', AIRTABLEAPI_URL_SCRIPTS . '/codemirror/codemirror.js', array('jquery') );
        wp_enqueue_script( $admin_handle, AIRTABLEAPI_URL_SCRIPTS . '/admin.js', array(
            'jquery', self::get_plugin_slug() . '_codemirror'));

        $translation_array = array(
            'remove_table' => __( 'Do you want to delete this table conversion?', self::get_locale_slug() ),
            'remove_field' => __( 'Do you want to delete this field conversion?', self::get_locale_slug() ),
            'duplicate_entry' => __( 'The conversion for this field already exists.', self::get_locale_slug() )
        );
        wp_localize_script( $admin_handle, 'alert_message', $translation_array );

    }

    public static function settings()
    {
        add_options_page( __('Remote Database API', self::get_locale_slug()), __('Remote Database API', self::get_locale_slug()), 'manage_options', self::settings_slug(), array('AirtableApiAdmin', 'settings_page'));
    }

    public static function settings_page()
    {

        $action = self::get_plugin_slug() . '-update-settings';
        $nonce = wp_create_nonce( $action );

        ?>
        <div class="wrap airtable-api-settings">
            <h2><?php _e('Remote Database API - Settings', self::get_locale_slug()); ?></h2>
            <form method="post" action="options.php">
                <?php

                settings_fields( self::settings_slug() );
                do_settings_sections( self::settings_slug() );

                ?>
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save changes', self::get_locale_slug()); ?>"></p>
            </form>
        </div>


        <div class="clear"></div>
        <?php
    }

    public static function settings_fields() {

        // SECTION - SETTINGS

        add_settings_section(
            self::settings_slug('_'),
            __('Main settings', self::get_locale_slug()),
            array('AirtableApiAdmin', 'settings_section'),
            self::settings_slug()
        );

        add_settings_field(
            self::settings_slug('_') . '_airtable_apikey',
            __( 'Airtable - API KEY', self::get_locale_slug() ),
            array('AirtableApiAdmin', 'settings_fields_airtable_apikey'),
            self::settings_slug(),
            self::settings_slug('_')
        );

        add_settings_field(
            self::settings_slug('_') . '_airtable_database',
            __( 'Airtable - Database ID', self::get_locale_slug()),
            array('AirtableApiAdmin', 'settings_fields_airtable_database'),
            self::settings_slug(),
            self::settings_slug('_')
        );

        add_settings_field(
            self::settings_slug('_') . '_airtable_view',
            __( 'Airtable - View', self::get_locale_slug()),
            array('AirtableApiAdmin', 'settings_fields_airtable_view'),
            self::settings_slug(),
            self::settings_slug('_')
        );

        add_settings_field(
            self::settings_slug('_') . '_cache_lifetime',
            __( 'Cache lifetime', self::get_locale_slug()),
            array('AirtableApiAdmin', 'settings_fields_cache_lifetime'),
            self::settings_slug(),
            self::settings_slug('_')
        );


        add_settings_field(
            self::settings_slug('_') . '_google_api_key',
            __( 'Google Api Key', self::get_locale_slug()),
            array('AirtableApiAdmin', 'settings_fields_google_api_key'),
            self::settings_slug(),
            self::settings_slug('_')
        );

        // SECTION - MAPPING

        add_settings_section(
            self::settings_slug('_') . '_mapping',
            __('Mapping with Airtable', self::get_locale_slug()),
            array('AirtableApiAdmin', 'mapping_section'),
            self::settings_slug()
        );

        add_settings_field(
            self::settings_slug('_') . '_airtable_mapping_table',
            __( 'Mapping tables', self::get_locale_slug()),
            array('AirtableApiAdmin', 'settings_fields_airtable_mapping_table'),
            self::settings_slug(),
            self::settings_slug('_') . '_mapping'
        );

        register_setting( self::settings_slug(), self::settings_slug('_') . '_airtable_apikey' );
        register_setting( self::settings_slug(), self::settings_slug('_') . '_airtable_database' );
        register_setting( self::settings_slug(), self::settings_slug('_') . '_airtable_view' );
        register_setting( self::settings_slug(), self::settings_slug('_') . '_airtable_cache_lifetime' );
        register_setting( self::settings_slug(), self::settings_slug('_') . '_airtable_google_api_key' );
        register_setting( self::settings_slug(), self::settings_slug('_') . '_airtable_mapping_table',  array('AirtableApiAdmin', 'settings_fields_airtable_mapping_table_validate'));

    }

    public static function settings_section() {
        echo "<p><i>" . __('You can also define a base and a default view when you leave these fields using the shortcode.', self::get_locale_slug()) . "</i></p>";
    }

    public static function mapping_section() {
        echo "<p><i>" . __('You can create links between the column names and Airtable table id, and your own nomenclature to be used within the site.', self::get_locale_slug()) . "</i></p>";
    }

    public static function settings_fields_airtable_apikey()
    {
        echo '<input name="' . self::settings_slug('_') . '_airtable_apikey' . '" id="'. self::settings_slug('_') . '_airtable_apikey' . '" type="text" value="' . self::get_options('airtable_apikey') . '" />';
    }

    public static function settings_fields_airtable_database()
    {
        echo '<input name="' . self::settings_slug('_') . '_airtable_database' . '" id="'. self::settings_slug('_') . '_airtable_database' . '" type="text" value="' . self::get_options('airtable_database') . '" />';
    }

    public static function settings_fields_airtable_view()
    {
        echo '<input name="' . self::settings_slug('_') . '_airtable_view' . '" id="'. self::settings_slug('_') . '_airtable_view' . '" type="text" value="' . self::get_options('airtable_view') . '" />';
    }

    public static function settings_fields_cache_lifetime()
    {
        echo '<input name="' . self::settings_slug('_') . '_airtable_cache_lifetime' . '" id="'. self::settings_slug('_') . '_airtable_cache_lifetime' . '" type="number" min="0" value="' . get_option( self::settings_slug('_') . '_airtable_cache_lifetime') . '" />';
        ?>
        <p><small><?php _e('You can set a time for the cache of all queries. If you leave empty or set the value to zero, no caching will be applied.', self::get_locale_slug()); ?></small></p>
        <?php
    }

    public static function settings_fields_google_api_key()
    {
        echo '<input name="' . self::settings_slug('_') . '_airtable_google_api_key' . '" id="'. self::settings_slug('_') . '_airtable_google_api_key' . '" type="text" value="' . self::get_options('airtable_google_api_key') . '" />';
    }

    public static function settings_fields_airtable_mapping_table()
    {
        $options = self::get_options('airtable_mapping_table');

        $options[] = array(
            'table-id'      => '',
            'table-variable-key'    => ''
        );

        $count_options = 0;
        foreach ($options as $option):

            $table_fields = null;

            if(!empty($option['table-id']))
            {
                $request = new AirtableApiRequest(self::get_options('airtable_database'), $option['table-id']);

                $table_fields = $request->get(
                    array(
                        'type' => 'all',
                        'parameters' => array(
                            'maxRecords' => 1
                        )),
                    false
                );
            }

        ?>
        <div class="form-row mapping-table-field-row" data-index="<?php echo $count_options; ?>">
            <div class="mapping-table">
                <h3 class="table-name"><?php _e('Table:', self::get_locale_slug()); ?> <span class="value"><?php echo $option['table-variable-key']; ?></span></h3>
                <input class="table-id table-input" data-name="table-id" data-variable="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table" name="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table[<?php echo $count_options; ?>][table-id]" type="text" value="<?php echo $option['table-id']; ?>" placeholder="<?php _e('Airtable table id', self::get_locale_slug()); ?>"/> <span>=</span>
                <input class="table-variable-key table-input" data-name="table-variable-key" data-variable="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table" class="" name="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table[<?php echo $count_options; ?>][table-variable-key]" type="text" value="<?php echo $option['table-variable-key']; ?>" placeholder="<?php _e('Internal table key', self::get_locale_slug()); ?>"/>
                <a class="mapping-table-delete" href="#"><?php _e('Delete', self::get_locale_slug()); ?></a>
            </div>
            <div class="tabs">
            <?php
            if(isset($table_fields) && count($table_fields) == 1):

                $table_field_mapping = array_values($table_fields)[0]['fields'];

                ?>
                <div class="mapping-fields tab">
                    <div class="indications">
                        <?php
                        $count_field = 0;
                        $airtable_col_name = array();
                        foreach ($table_field_mapping as $key => $field)
                        {
                            ?><?php echo ($count_field != 0)?' | ':''; ?> <span class="field-name"><?php echo $key; ?></span><?php
                            $airtable_col_name[] = $key;
                            $count_field++;
                        }
                        ?>
                    </div>
                    <div class="table-mapping-fields">
                        <?php

                        $fields_mapped = $option['fields'];

                        if(!isset($fields_mapped) || empty($fields_mapped))
                            $fields_mapped = array();

                        $fields_mapped[] = array(
                            'field-id' => '',
                            'field-variable-key' => ''
                        );

                        $count_field_mapping = 0;

                        foreach ($fields_mapped as $field_mapped):

                        ?>
                        <div class="form-row mapping-field-row <?php echo ($field_mapped['field-id'] != "" && !in_array($field_mapped['field-id'], $airtable_col_name))?'not-found':''; ?>">
                            <input class="field-id field-input" data-variable="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table" data-name="field-id" name="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table[<?php echo $count_options; ?>][fields][<?php echo $count_field_mapping; ?>][field-id]" type="text" value="<?php echo $field_mapped['field-id']; ?>" placeholder="<?php _e('Airtable field id', self::get_locale_slug()); ?>"/> <span>=</span>
                            <input class="field-variable-key field-input" data-variable="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table" data-name="field-variable-key" name="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table[<?php echo $count_options; ?>][fields][<?php echo $count_field_mapping; ?>][field-variable-key]" type="text" value="<?php echo $field_mapped['field-variable-key']; ?>" placeholder="<?php _e('Internal field key', self::get_locale_slug()); ?>"/>
                            <a class="mapping-field-delete" href="#"><?php _e('Delete', self::get_locale_slug()); ?></a>
                        </div>
                        <?php
                            $count_field_mapping++;
                        endforeach;

                        ?>
                        <a class="mapping-field-add" href="#"><?php _e('Add new field conversion name', self::get_locale_slug()); ?></a>
                    </div>
                </div>
            <?php endif;

            /*
            ?>

                <div class="tab">
                    <textarea class="table-template code-mirror" name="<?php echo self::settings_slug('_'); ?>_airtable_mapping_table[<?php echo $count_options; ?>][templates]"><?php echo $option['templates']; ?></textarea>
                </div>
            <?php
            */
            ?>

            </div>
        </div>
        <?php
            $count_options++;
        endforeach;
        ?>
        <a class="mapping-table-add" href="#"><?php _e('Add new table conversion name', self::get_locale_slug()); ?></a>
        <?php
    }

    public static function settings_fields_airtable_mapping_table_validate($input_rows)
    {
        $valid_inputs = array();

        foreach ($input_rows as $input_row)
        {
            if(!empty($input_row['table-id']) || !empty($input_row['table-variable-key']))
            {
                if(!empty($input_row['table-variable-key']))
                    $input_row['table-variable-key'] = sanitize_title($input_row['table-variable-key']);

                if(isset($input_row['fields']) && is_array($input_row['fields']))
                {
                    $valid_field = array();
                    foreach ($input_row['fields'] as $field)
                    {
                        if(!empty($field['field-id']) || !empty($field['field-id']))
                        {
                            $valid_field[] = $field;
                        }
                    }

                    $input_row['fields'] = $valid_field;
                }

                $valid_inputs[] = $input_row;
            }
        }

        return $valid_inputs;
    }

    public static function get_option_table_id($table_key, $field_key = "")
    {
        $table_options = self::get_options('airtable_mapping_table');

        foreach ($table_options as $option)
        {
            if($option['table-variable-key'] == $table_key){
                if($field_key == "")
                    return $option['table-id'];
                else
                {
                    if(is_array($option['fields']) && count($option['fields']) > 0)
                    {
                        foreach ($option['fields'] as $field)
                        {
                            if($field['field-variable-key'] == $field_key)
                                return $field['field-id'];
                        }
                    }
                    else
                    {
                        return false;
                    }
                }

            }
        }

        return false;
    }

    public static function get_options($option_name)
    {
        return get_option(self::settings_slug('_') . '_' . $option_name);
    }

    public static function get_options_for_table_key($table_key)
    {
        $table_options = self::get_options('airtable_mapping_table');

        foreach ($table_options as $option)
        {
            if($option['table-variable-key'] == $table_key)
                return $option;
        }

        return false;
    }

    // TODO : Fix php template
    public static function get_template($table_key)
    {
        $options = self::get_options_for_table_key($table_key);

        if(!empty($options['templates']))
            return $options['templates'];

        return false;
    }

    private function settings_slug($separator = '-')
    {
        if($separator == '-') return apply_filters( self::get_plugin_slug() . '_admin_settings_slug',  self::get_plugin_slug() . '-settings');
        else return apply_filters( self::get_plugin_slug() . '_admin_settings_slug_underscore',  self::get_plugin_slug() . '_settings');
    }
}