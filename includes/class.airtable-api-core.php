<?php

/**
 * Class AirtableApiCore
 * -----------------------
 */

class AirtableApiCore {

    private static $plugin_slug = 'airtable_api';
    private static $locale_slug = 'airtable_api';

    public function __construct() {

    }

    protected function get_plugin_slug()
    {
        return self::$plugin_slug;
    }

    protected function get_locale_slug()
    {
        return self::$locale_slug;
    }

    protected function get_option($option)
    {
        $option_value = AirtableApiAdmin::get_options($option);
        return $option_value;
    }
}