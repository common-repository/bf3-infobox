<?php
/*
Plugin Name: Battlefield 3 Infobox
Plugin URI: https://github.com/Calaelen/wordpress-bf3info-plugin
Description: 2013 Update! Display your Battlefield 3 Player Statistics from bf3stats.com in a sidebar widget. <strong>Show/hide values via the settings</strong> (e.g. hide Origin username).
Version: 1.0.1
License: GPLv2 or later
Author: Calaelen
Author URI: http://www.calaelen.com/about/
Text Domain: bf3infobox
Domain Path: /languages
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('BF3_INFOBOX_VERSION', 1.0);
if( !class_exists( 'WP_Http' ) ) { include_once( ABSPATH . WPINC. '/class-http.php' ); }

require_once(dirname( __FILE__ ) . '/inc/bf3stats-api.php');
require_once(dirname( __FILE__ ) . '/inc/bf3infobox-widget.php');
require_once(dirname( __FILE__ ) . '/inc/bf3infobox-options.php');


//--------------------------- Widget --------------------------//
add_action('widgets_init', 'bf3infobox_register_widget');
function bf3infobox_register_widget() {
    register_widget('bf3infobox');
}

add_action( 'wp_enqueue_scripts', 'bf3infobox_stylesheet' );
function bf3infobox_stylesheet() {
    wp_register_style( 'bf3infobox-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'bf3infobox-style' );
}

//--------------------------- Options Page --------------------------//
add_action('admin_menu', 'bf3infobox_register_optionspage');
function bf3infobox_register_optionspage() {
    bf3infobox_options::add_options_menu();
}

add_action('admin_init', 'bf3infobox_init_optionspage');
function bf3infobox_init_optionspage() {
    new bf3infobox_options();
}

register_activation_hook(__FILE__, 'bf3infobox_add_defaults');
function bf3infobox_add_defaults() {
    bf3infobox_options::add_default_options_on_activation();
}

//Settings Link - Info: http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
add_filter( 'plugin_row_meta', 'set_plugin_meta', 10, 2 );
function set_plugin_meta($links, $file) {
    $plugin = plugin_basename(__FILE__);
    if ($file == $plugin) {
        return array_merge(
            $links,
            array( sprintf( '<a href="options-general.php?page=bf3infobox-options">%s</a>', __('Settings') ) )
        );
    }
    return $links;
}

//---------------------- Translation Files ---------------------//
add_action('init', 'bf3infobox_load_plugin_textdomain');
function bf3infobox_load_plugin_textdomain() {
    $plugin_path = plugin_basename( dirname( __FILE__ ) .'/languages' );
    load_plugin_textdomain( 'bf3infobox', '', $plugin_path );
}

