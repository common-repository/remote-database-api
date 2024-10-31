=== Remote Database API ===
Contributors: The other pole
Donate link : http://theotherpole.com
Tags: Remote Database API, Database API, Airtable, Airtable API, API Restful
Requires at least: 3.2
Tested up to: 4.5
Stable tag: 1.2
License: GPLv2 or later

Our plugin is the best way to interconnect the Airtable service with Wordpress and create a simple mapping interface.

== Description ==

Our plugin is the best way to interconnect the *Airtable service* (https://airtable.com/invite/n0r7W43o) with Wordpress and create a simple mapping interface.
You can create complex connections with Airtable and use it to display data directly on your site and add data within Airtable from Wordpress.

*Major features in Remote Database API include:*

* Simplify calls to the API Airtable with Wordpress
* Caching the requests for increasing the speed of information display.
* Create internal nomenclature table names and column names to avoid breaking the code when a change in Airtable.
* Proposal easy shortcuts between the names of the existing columns in Airtable and correlation table.
* Examples of templates already designed to easily build complex structure and multiple calls to different databases or tables.

PS: You'll need an [airtable.com API key](http://airtable.com/) to use it.

This plugin is developed by a third-party developer team, and is not supported by Airtable.

= Docs & Support =

Soon ;)

= Remote Database API Needs Your Support =

It is hard to continue development and support for this free plugin without contributions from users like you. If you enjoy using Remote Database API Plugin and find it useful, please contact us on hello@theotherpole.com, telling us your plugin uses.

== Installation ==

Upload the Remote Database API plugin to your wordpress, Activate it, then enter your [airtable.com API key](http://airtable.com/) in the settings.

Use [airtable-api] shortcode.

Examples : [airtable-api table-map="benevoles" template="map-benevoles"]

For shortcode templates,
You can create a folder named your-theme/airtable_api/ (in your active theme folder) to process and display the data retrieved with a shortcode from Airtable with our plugin.
This directory must contains all the templates you create and you can call them by the name of the file: TEMPLATENAME.php as shortcode parameter : template="TEMPLATENAME".

Within this template file, you can get all the retrieved data with the local variable named : $content_from_api, for example :

-PHP

    $items = $content_from_api;

    foreach ( $items as $item ) {
        $item_fields = $item['fields'];
    }

-END PHP

Have fun!

== Screenshots ==

1. screenshot-1.png
1. screenshot-2.png
1. screenshot-3.png

== Changelog ==

= 1.1 =
*Release Date - May 19, 2016*

* Added cache system
* Overhaul of the class system and plugin structure
* Improved plugin interface in the backoffice
* Adding the conversion table function table names and column

= 1.0 =
*Release Date - January 1, 2016*

* Initial version
