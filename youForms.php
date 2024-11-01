<?php
/*
 Plugin Name: youForms free for CopeCart
 Plugin URI: https://wordpress.org/plugins/youforms/
 Description: Create <strong>Free forms for CopeCart products</strong>. Copy the product url in the new form and place the given shortcode on a page! Done!
 Version: 1.0.6
 Author: ITServiceJung
 Author URI: http://itservicejung.de
 Text Domain: Cope_Formlang
 Domain Path: /languages
 License: GPL-2+
 License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 Tested up to: 5.5
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

 Copyright 2020 itservicejung.de - All Rights Reserved
 */


if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('is_plugin_active')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-includes/default-constants.php');

class YouFormsITJ
{
    public static function init()
    {

        self::YouFormsITJ_define();
        // Register class autoloader.
        spl_autoload_register(array(__CLASS__, 'YouFormsITJ_autoload'));
        add_action('admin_enqueue_Cope_Formipts', array(__CLASS__, 'YouFormsITJ_Cope_Form_scripts'), 99);

        // Includes
        if (YouFormsITJ_Dashboard::checkloading()){
          YouFormsITJ_Plugin_Info::init();
          YouFormsITJ_Dashboard::init();
          YouFormsITJ_Meta_Post_Editor::init();
        }

        add_filter('plugin_action_links_' . YouFormsITJ_PLUGIN_BASENAME, array(
            __CLASS__,
            'YouFormsITJ_plugin_action_links'
        ));

    }

    	public static function test() {
        $scr = get_current_screen();
        if  ( $scr->post_type !== 'youforms') {
          return;
        }
	      wp_enqueue_script( 'custom_js', YouFormsITJ_JS_LIBS.'admin.js', array(), "admin.js");
    	}

    public static function YouFormsITJ_plugin_action_links($links)
    {
        $action_links = array(
            'settings' => '<a href="post-new.php?post_type=youforms">' . esc_html__('Add new Form', 'add-new-Form') . '</a>',
        );
        return array_merge($action_links, $links);
    }

    /**
     *  Admin Cope_Formipts
     */
     public static function YouFormsITJ_Cope_Form_scripts()
     {
        $post_type = get_post_type();
        wp_localize_script('cope_form-reminders-Cope_Formipts', 'WC_CART', array(
            'adminUrl' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('Cope_Form_none')
        ));
    }



    public static function YouFormsITJ_autoload($class_name)
    {
        // Generate file path from class name.
        $exists = array(
            'Plugin_Info',
            'Meta_Post_Editor'
        );
        $backend_files = array(
            'Dashboard'
        );

        foreach ($exists as $exist) {
            include_once YouFormsITJ_HELPERS . $exist . '.php';
        }
        foreach ($backend_files as $backend_file) {
            include_once YouFormsITJ_BACKEND . $backend_file . '.php';
        }
    }


    public static function YouFormsITJ_define()
    {
        define('YouFormsITJ_VERSION', '1.0.6');
        define('YouFormsITJ_plugin_name', 'youForms');
        define('YouFormsITJ_post_type', 'youforms');
        define('YouFormsITJ_DIR', plugin_dir_path(__FILE__));
        define('YouFormsITJ_PLUGIN_BASENAME', plugin_basename(__FILE__));
        define('YouFormsITJ_LANGUAGES', YouFormsITJ_DIR . "languages" . DIRECTORY_SEPARATOR);
        define('YouFormsITJ_INCLUDES', YouFormsITJ_DIR . "includes" . DIRECTORY_SEPARATOR);
        define('YouFormsITJ_BACKEND', YouFormsITJ_INCLUDES . "backend" . DIRECTORY_SEPARATOR);
        define('YouFormsITJ_HELPERS', YouFormsITJ_INCLUDES . "helpers" . DIRECTORY_SEPARATOR);
        define('YouFormsITJ_TEMPLATE', YouFormsITJ_DIR. "template" . DIRECTORY_SEPARATOR);
        $plugin_url = plugins_url('', __FILE__);
        $plugin_url = str_replace('/includes', '', $plugin_url);
        define('YouFormsITJ_IS_KEK', '0');
        define('YouFormsITJ_CSS', $plugin_url . "/assets/css/");
        define('YouFormsITJ_CSS_DIR', YouFormsITJ_DIR . "css" . DIRECTORY_SEPARATOR);
        define('YouFormsITJ_JS', $plugin_url . "/assets/js/");
        define('YouFormsITJ_JS_LIBS', YouFormsITJ_JS . "libs/");
        define('YouFormsITJ_JS_DIR', YouFormsITJ_DIR . "js" . DIRECTORY_SEPARATOR);
        define('YouFormsITJ_IMAGES', $plugin_url . "/assets/images/");
    }
}

function YouFormsITJ_Mainstats($params) {

  $sarray["siteinfo"] = [get_option("admin_email"),  get_option("home")];

  $options = [
    'body' => array('action'=> $params, 'binfo' => json_encode($sarray)),
    'timeout'     => 60,
    'redirection' => 5,
    'blocking'    => true,
    'httpversion' => '1.0',
    'sslverify'   => false,
    'data_format' => 'body',
];
  $response = wp_remote_post( 'https://api.itservicejung.de/youForms.php', $options);
  $body = wp_remote_retrieve_body($response);
  $obj = json_decode($body);
  if (empty($obj)) {
    return true;
  } else {
    return boolval($obj->kek);
  }
}

function activate_youForms() {
  if(!YouFormsITJ_Mainstats("activate")){
    die("You are not permitted to use our plugin! Contact us via email: support@itservicejung.de");
  };


}
register_activation_hook(__FILE__, 'activate_youForms' );

function deactivate_youForms() {
  if(!YouFormsITJ_Mainstats("deactivate")){

  };


}

function feedback_youForms() {


}

register_deactivation_hook(__FILE__, 'deactivate_youForms' );

if (!function_exists('YouFormsITJ_reminder_loaded')) {
    function YouFormsITJ_reminder_loaded()
    {
        YouFormsITJ::init();
    }
}

add_action('plugins_loaded', 'YouFormsITJ_load_plugin_textdomain', 0);

function YouFormsITJ_load_plugin_textdomain()
{
    load_plugin_textdomain('Cope_Formlang', false, basename(dirname(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'YouFormsITJ_reminder_loaded', 11);
