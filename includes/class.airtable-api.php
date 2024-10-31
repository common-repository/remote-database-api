<?php

/**
 * Class AirtableApi
 * -----------------------
 */

class AirtableApi extends AirtableApiCore {

    public function __construct() {

    }

    public static function plugin_activation()
    {

    }

    public static function plugin_deactivation()
    {

    }

    public static function init()
    {
        self::load_textdomain();
        self::frontend_hooks();
    }

    private function load_textdomain()
    {
        do_action(self::get_plugin_slug() . '_load_textdomain');

        // Set filter for plugin's languages directory
        $sglang_dir =  AIRTABLEAPI_FILE_PATH . 'languages';
        $sglang_dir = apply_filters( self::get_plugin_slug() . '_languages_directory', $sglang_dir );

        $locale = apply_filters( 'plugin_locale', get_locale(), self::get_locale_slug() );

        load_textdomain( self::get_locale_slug(),  $sglang_dir . "/$locale.mo" );
    }

    private function frontend_hooks()
    {
        do_action(self::get_plugin_slug() . '_frontend_hooks');
    }
}