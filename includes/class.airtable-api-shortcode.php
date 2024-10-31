<?php

/**
 * Class AirtableApiShortcode
 * -----------------------
 */

if(class_exists('AirtableApiRequest'))
{
    class AirtableApiShortcode extends AirtableApiCore
    {
        public function __construct() {
        }

        public static function init() {
            self::init_hooks();
        }

        public static function init_hooks() {
            do_action(self::get_plugin_slug() . '_shortcode_hooks_before');

            add_shortcode( 'airtable-unit-test', array('AirtableApiShortcode', 'shortcode_unit_test') );
            add_shortcode( 'airtable-api', array('AirtableApiShortcode', 'shortcode_airtable_api') );

            do_action(self::get_plugin_slug() . '_shortcode_hooks_after');
        }

        /*
         *              SHORTCODE - UNIT TEST
         */

        public static function shortcode_unit_test($atts)
        {
            if(!isset($atts['base']) && $atts['base'] == "")
                return __('You have to set a -base- parameter.', self::get_locale_slug());

            if(!isset($atts['table']) && $atts['table'] == "")
                return __('You have to set a -table- parameter.', self::get_locale_slug());

            $airtable_api = new AirtableApiRequest($atts['base'], $atts['table']);

            ob_start();

            $return = $airtable_api->get(array('type' => 'all'));

            ?>
            <p><strong><?php _e('Get all records from table:', self::get_locale_slug()); ?></strong> <?php echo count($return); ?></p>
            <?php

            if(isset($atts['view']) && $atts['view'] != ""):
                $return = $airtable_api->get(array('type' => 'all', 'parameters' => array('view' => $atts['view'])));
                ?>
                <p><strong><?php _e('Get all records from specific view:', self::get_locale_slug()); ?></strong> <?php echo count($return); ?></p>
                <?php
            endif;

            if(isset($atts['record']) && $atts['record'] != ""):

                $return = $airtable_api->retrieve(array('id' => $atts['record']));
                ?>
                <p><strong><?php _e('Get specific record:', self::get_locale_slug()); ?></strong> <?php echo (isset($return['fields']) && !empty($return['fields']))?$return['id']:'Not found'; ?></p>

                <?php

            endif;

            if(isset($atts['update_field']) && $atts['update_field'] != "" && isset($atts['record']) && $atts['record'] != ""):
                $return = $airtable_api->update($atts['record'], array($atts['update_field'] => time()));
                ?>
                <p><strong><?php _e('Update specific record:', self::get_locale_slug()); ?></strong>
                    <?php

                    if(isset($return->error))
                        echo $return->error->message . ' | ';

                    echo (!empty($return->id))?$return->id:'Not updated'; ?></p>

                <?php
            endif;

            if(isset($atts['create_field']) && $atts['create_field'] != ""):
                $return = $airtable_api->create(array("Name" => $atts['create_field'] . " - " . time()));
                ?>
                <p><strong><?php _e('Create record:', self::get_locale_slug()); ?></strong> <?php echo (!isset($return->id))?'Not created':$return->id; ?></p>
                <?php
            endif;

            $contents = ob_get_contents();
            ob_end_clean();

            return $contents;
        }

        /*
         *              SHORTCODE - AIRTABLE_API
         */

        public static function shortcode_airtable_api($atts)
        {
            ob_start();

            do_action(self::get_plugin_slug() . '_shortcode_airtable_before');

            if( (!isset($atts['table']) || empty($atts['table'])) && !isset($atts['table-map']) || empty($atts['table-map']))
            {
                ?>
                <p><strong><?php _e('Error:', self::get_locale_slug()); ?></strong> <?php _e('Empty table id.', self::get_locale_slug()); ?></p>
                <?php

                $contents = ob_get_contents();
                ob_end_clean();

                return $contents;
            }
            else
            {
                $table = "";
                $base = "";
                $view = "";

                // BASE from shortcode
                if(isset($atts['base']) && !empty($atts['base']))
                    $base = $atts['base'];
                else
                    $base = self::get_option('airtable_database');

                // TABLE from shortcode
                if(isset($atts['table']) && $atts['table'] != "")
                    $table = $atts['table'];
                elseif(isset($atts['table-map']) && $atts['table-map'] != "")
                    $table = AirtableApiAdmin::get_option_table_id($atts['table-map']);

                // VIEW from shortcode
                if(isset($atts['view']) && !empty($atts['view']))
                    $view = $atts['view'];

                $airtable_api = new AirtableApiRequest($base, $table, $view);
                $content_from_api = $airtable_api->get(array('type' => 'all'));

                // TEMPLATING shortcode

                $template_name = '';
                if(isset($atts['template']) && $atts['template'] != "")
                    $template_name = $atts['template'];

                $template_file_style = get_stylesheet_directory() . '/' . self::get_plugin_slug() . '/' . $template_name .'.php';
                $template_file = AIRTABLEAPI_FILE_PATH . AIRTABLEAPI_DIR_TEMPLATE . '/' . $template_name .'.php';

                if(file_exists($template_file_style))
                    include($template_file_style);
                elseif(file_exists($template_file))
                    include($template_file);
                else
                {
                    ?>
                    <p><strong><?php _e('Error:', self::get_locale_slug()); ?></strong> <?php _e('Template missing.', self::get_locale_slug()); ?></p>
                    <?php
                }

                $contents = apply_filters( self::get_plugin_slug() . '_shortcode_content', ob_get_contents());

                $class = self::get_plugin_slug(). '-shortcode';
                if($template_name != "")
                    $class .= " template-" . $template_name;

                $contents = '<div class="' . $class . '">' . $contents . '</div>';

                ob_end_clean();

                do_action(self::get_plugin_slug() . '_shortcode_airtable_after', $contents);

                return $contents;
            }

            return false;

        }

    }
}