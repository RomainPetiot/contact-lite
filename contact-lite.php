<?php
/**
 * Plugin name: Contact Lite
 * Plugin URI: https://github.com/RomainPetiot/contact-lite
 * Description: Formulaire de contact
 * Author : Romain Petiot
 * Author URI: https://www.romainpetiot.com
 * Contributors:Romain Petiot
 * Domain Path: /languages
 * Text Domain: contact-lite
 * Version: 1.1
 * Stable tag: 1.1
 */

 /**
 * Bloquer les accès directs
 */
 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 add_action( 'init', 'contact_lite_init' );
 function contact_lite_init() {
   $plugin_dir = basename( dirname( __FILE__ ) ) . '/languages';
   load_plugin_textdomain( 'contact-lite', false, $plugin_dir );
 }

define('CL_EMAIL_TO', get_option( 'admin_email') );
define('CL_EMAIL_FROM', __('no-reply@', 'contact-lite') . str_replace('www.', '', $_SERVER['SERVER_NAME'] ) );
define('CL_SUBJECT', __('Nouveau message depuis votre site web','contact-lite') );

require plugin_dir_path( __FILE__ ) . 'admin.php';
require plugin_dir_path( __FILE__ ) . 'front.php';

add_action( 'init', 'cl_custom_post_type');
function cl_custom_post_type() {
	register_post_type( 'contact-lite', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		array('labels' 			=> array(
				'name' 				=> __('Contacts', 'contact-lite'), /* This is the Title of the Group */
				'singular_name' 	=> __('Contact', 'contact-lite'), /* This is the individual type */
			), /* end of arrays */
			'menu_position' 	=> 18, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' 		=> 'dashicons-email-alt', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
			'hierarchical' 		=> false,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => false,
			'supports' 			=> array( 'title', 'editor')
	 	) /* end of options */
	);
}
