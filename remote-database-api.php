<?php

/*
Plugin Name: Remote Database API
Plugin URI: http://theotherpole.com
Description: The best way to interconnect the Airtable service with Wordpress and create a simple mapping interface.
Version: 1.2
Author: Jean-Christophe Perrin
Author URI: http://theotherpole.com
*/

// Copyright (c) 2016 The other pole. All rights reserved.
//
// Released under the GPL license 2 or later
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************

define( 'AIRTABLEAPI_FILE_PATH', plugin_dir_path(__FILE__) );

define( 'AIRTABLEAPI_DIR_NAME',  basename( AIRTABLEAPI_FILE_PATH ) );

define( 'AIRTABLEAPI_DIR_TEMPLATE',  'templates' );
define( 'AIRTABLEAPI_URL_TEMPLATE',  plugins_url('/' . AIRTABLEAPI_DIR_TEMPLATE, __FILE__) );

define( 'AIRTABLEAPI_DIR_INCLUDES',  AIRTABLEAPI_FILE_PATH . 'includes' . '/' );

define( 'AIRTABLEAPI_DIR_SCRIPTS',  'js' );
define( 'AIRTABLEAPI_URL_SCRIPTS',  plugins_url('/' . AIRTABLEAPI_DIR_SCRIPTS, __FILE__));

define( 'AIRTABLEAPI_DIR_STYLES',  'css' );
define( 'AIRTABLEAPI_URL_STYLES',  plugins_url('/' . AIRTABLEAPI_DIR_STYLES, __FILE__) );

define( 'AIRTABLEAPI_AIRTABLE_API', 'https://api.airtable.com/v0/' );

/*

        AIRTABLE API CORE

*/

require_once(AIRTABLEAPI_DIR_INCLUDES . 'class.airtable-api-core.php');

require_once(AIRTABLEAPI_DIR_INCLUDES . 'class.airtable-api-request.php');

require_once(AIRTABLEAPI_DIR_INCLUDES . 'class.airtable-api.php');

register_activation_hook( __FILE__, array( 'AirtableApi', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'AirtableApi', 'plugin_deactivation' ) );
add_action( 'init', array( 'AirtableApi', 'init' ) );

/*


       AIRTABLE ADMIN

 */

require_once( AIRTABLEAPI_DIR_INCLUDES . 'class.airtable-api-admin.php' );
add_action( 'init', array( 'AirtableApiAdmin', 'init' ) );

/*

        AIRTABLE SECONDARY

*/

require_once(AIRTABLEAPI_DIR_INCLUDES . 'class.airtable-api-shortcode.php');
add_action( 'init', array( 'AirtableApiShortcode', 'init' ) );

/*

        AIRTABLE FUNCTIONS

*/

require_once(AIRTABLEAPI_DIR_INCLUDES . 'functions.tools.php');

