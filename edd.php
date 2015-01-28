<?php
/*
Plugin Name: EDD Promo
Plugin URI: http://cbfreeman.com/downloads/edd_promo/
Description: Adds a customizable content rich HTML emailer to your Easy Digital Downloads plugin.
Version: 1.0
Author: cbfreeman
Author URI: http://cbfreeman.com
License: GPLv2
 */



  /**
  * Add Global EDD Promo Settings
  *
  * create subscriber table
  * get general settings
  * email form
  * unsubscribe message & link
  */
  global $wpdb, $wp_version;
   define("EDD_PROMO_TABLE", $wpdb->prefix . "edd_customers");
  define("EDD_PROMO_RECORDS_TABLE", $wpdb->prefix . "eddpromorecords");
  define("EDD_PROMO_NEWSLETTER_TABLE", $wpdb->prefix . "eddpromonewsletter");
  
  function eddpromo_install()
{
    global $wpdb, $wp_version;
    
    $wpdb->query("
            ALTER TABLE `". EDD_PROMO_TABLE . "`
              ADD COLUMN `mail` char(3) NOT NULL default 'Y'
            ");
  
    
     if(strtoupper($wpdb->get_var("show tables like '". EDD_PROMO_RECORDS_TABLE . "'")) != strtoupper(EDD_PROMO_RECORDS_TABLE))
    {
        $wpdb->query("
            CREATE TABLE IF NOT EXISTS `". EDD_PROMO_RECORDS_TABLE . "` (
              `id` int(11) NOT NULL auto_increment,
              `edrsubject` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
              `edremail` char(250) NOT NULL ,
              `edrsent` char(3) NOT NULL default '0',
               `edropen` char(3) NOT NULL default '0',
                `edrbounce` char(3) NOT NULL default '0',
              `edrdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY  (`id`) )
            ");
        
       
    if(strtoupper($wpdb->get_var("show tables like '". EDD_PROMO_NEWSLETTER_TABLE . "'")) != strtoupper(EDD_PROMO_NEWSLETTER_TABLE))
    {
        $wpdb->query("
            CREATE TABLE `". EDD_PROMO_NEWSLETTER_TABLE . "` (
              `id` int(11) NOT NULL auto_increment,
              `edfrom` char(250) NOT NULL ,
              `edsubject` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
              `edtemplate` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
              `edmember` char(250) NOT NULL ,
              `edsub` char(250) NOT NULL ,
              `edsent` char(3) NOT NULL default '0',
               `edopen` char(3) NOT NULL default '0',
                `edbounce` char(3) NOT NULL default '0',
              `eddate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY  (`id`) )
            ");
            
    }
  
}

}

           
  // GET WP General Settings
  $url = site_url();
  $name = get_bloginfo();
  $admin = get_option( 'admin_email' );
	$plugin = plugins_url();
	
	// Send Preview Email
  if(isset($_POST['edd_preview'])) {
  if(isset($_POST['admin']))
  $email = ($_POST['admin']);
  if(isset($_POST['subject_email']))
  $subject = ($_POST['subject_email']);
  if(isset($_POST['template']))
  $template = ($_POST['template']);
  $content = "$template";
  $to = "$email";
  $subject = "$subject";
  $sender = "$name Support for you" ;
  $email = "$name<$admin>";
  $headers = "From: " . $email . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $sent = mail($to, $subject, $content, $headers) ;
 }


  // Send HTML Email
  if(isset($_POST['edd_promo'])) {
  if(isset($_POST['subject_email']))
  $subject = ($_POST['subject_email']);
  if(isset($_POST['template']))
  $template = ($_POST['template']);
  global $wpdb;
  $registrant = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}edd_customers ");
  $recipient = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}edd_customers WHERE mail='Y' ");
  
  $table_name = $wpdb->prefix . "eddpromonewsletter";
  $wpdb->insert( $table_name, array( 'edfrom' => $admin ,'edsubject' => $_POST['subject_email'], 'edtemplate' => $_POST['template'], 'edmember' => $registrant ,'edsub' => $recipient, 'edsent' => $recipient ) );

  $customers = $wpdb->get_results(
  "
	SELECT email
	FROM {$wpdb->prefix}edd_customers WHERE mail='Y' "
  );
  foreach ( $customers as $customers )
 {
  $email = $customers->email;
  $table_name2 = $wpdb->prefix . "eddpromorecords";
  $wpdb->insert( $table_name2, array( 'edrsubject' => $_POST['subject_email'], 'edremail' => $email, 'edrsent' => 1 ) );
  $author = rawurlencode($subject);
  $message = "$template";
  $to = "$email";
  $subject = "$subject";
  $sender = "$name Support for you" ;
  $email = "$name<$admin>";
  $headers = "From: " . $email . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $sent = mail($to, $subject, $message, $headers) ;

 }

}


//Register post shortcode
 function edd_subscription_form($atts) {
   global $wp_version;
   require 'unsubscribe.php';
    
        return $results;
}
 add_shortcode("EDDPROMO", "edd_subscription_form");


   
   //Start
  if ( ! class_exists( 'EDD_Promo' ) ) {

	if ( ! defined( 'EDD_PROMO_JS_URL' ) )
		define( 'EDD_PROMO_JS_URL', plugin_dir_url( __FILE__ ) . 'js' );

	if ( ! defined( 'EDD_PROMO_CSS_URL' ) )
		define( 'EDD_PROMO_CSS_URL', plugin_dir_url( __FILE__ ) . 'css' );

	class EDD_Promo {

		var $options = array();
		var $page = '';

		/**
		 * Construct function
		 *
		 * @since 0.2
		 */
		function __construct() {
			global $wp_version;

			$this->get_options();
			

			if ( ! is_admin() )
				return;

			// Load translations
			load_plugin_textdomain( 'edd-promo', null, basename( dirname( __FILE__ ) ) . '/langs/' );

			// Actions
			add_action( 'admin_init',           array( $this, 'init' ) );
			add_action( 'admin_menu',           array( $this, 'admin_menu' ) );
			add_action('admin_enqueue_scripts', array($this, 'init') );

			if ( version_compare( $wp_version, '3.2.1', '<=' ) )
				add_action( 'admin_head', array( $this, 'load_wp_tiny_mce' ) );

			if ( version_compare( $wp_version, '3.2', '<' ) && version_compare( $wp_version, '3.0.6', '>' ) )
				add_action( 'admin_print_footer_scripts', 'wp_tiny_mce_preload_dialogs' );

			// Filters
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'settings_link' ) );
			add_filter( 'mce_external_plugins', array( $this, 'tinymce_plugins' ) );
			add_filter( 'mce_buttons',          array( $this, 'tinymce_buttons' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_config' ) );
			register_activation_hook(__FILE__, 'eddpromo_install');

		}
		
	

		/**
		 * Get recorded options
		 *
		 * @since 0.2
		 */
		function get_options() {
			$this->options = get_option( 'edd_promo_options' );
		}

		/**
		 * Set the default options
		 *
		 * @since 0.2
		 */
		function set_options() {

			// HTML default template
			$template = '';
			@require 'templates/template-1.php';
			

			// Setup options array
			$this->options = array(
				'subject_email'         => '',
				'template'           => $template
			);

			// If option doesn't exist, save default option
			if ( get_option( 'edd_promo_options' ) === false ) {
				add_option( 'edd_promo_options', $this->options );
			}
		}

		/**
		 * Init plugin options to white list our options & register our script
		 *
		 * @since 0.1
		 */
		function init() {
			register_setting( 'edd_promo_full_options', 'edd_promo_options', array( $this, 'validate_options' ) );
			wp_register_style( 'edmailer', EDD_PROMO_CSS_URL . '/edmailer.css' );
      wp_enqueue_style('jPages', EDD_PROMO_CSS_URL . '/jPages.css');
      wp_enqueue_script( 'jPages', EDD_PROMO_JS_URL . '/jPages.js', array( 'jquery'), 'null', true );
      wp_enqueue_script( 'edmailer-script', EDD_PROMO_JS_URL . '/edmailer-script.js', array( 'jquery'), 'null', true );
		}

		/**
		 * Settings link in the plugins page
		 *
		 * @since 0.1
		 *
		 * @param array   $links Plugin links
		 * @return array Plugins links with settings added
		 */
		function settings_link( $links ) {
			$links[] = '<a href="options-general.php?page=edd_promo_options">' . __( 'Settings', 'edd-promo' ) . '</a>';

			return $links;
		}

		/**
		 * Record options on plugin activation
		 *
		 * @since 0.1
		 * @global $wp_version
		 */
		function install() {
			global $wp_version;
			// Prevent activation if requirements are not met
			// WP 3.0 required
			if ( version_compare( $wp_version, '3.0', '<=' ) ) {
				deactivate_plugins( __FILE__ );

				wp_die( __( 'EDD Promo requires WordPress 2.8 or newer.', 'edd-promo' ), __( 'Upgrade your Wordpress installation.', 'edd-promo' ) );
			}

			$this->set_options();
		}

		/**
		 * Option page to the built-in settings menu
		 *
		 * @since 0.1
		 */
		function admin_menu() {
			$this->page = add_options_page( __( 'EDD Promo settings', 'edd-promo' ), __( 'EDD Promo', 'edd-promo' ), 'administrator', 'edd_promo_options',       array( $this, 'admin_page' ) );
			
			add_action( 'admin_print_styles-' . $this->page, array( $this, 'admin_print_style' ) );
		}

		/**
		 * Check if we're on the plugin page
		 *
		 * @since 0.2
		 * @global type $page_hook
		 * @return type
		 */
		function is_eddpromo_page() {
			global $page_hook;

			if ( $page_hook === $this->page )
				return true;

			return false;
		}


		/**
		 * Enqueue the style to display it on the options page
		 *
		 * @since 0.1
		 */
		function admin_print_style() {
			wp_enqueue_style( 'edmailer' );
			wp_enqueue_style( 'thickbox' );
		}

		/**
		 * Include admin options page
		 *
		 * @since 0.1
		 * @global $wp_version
		 */
		function admin_page() {
			global $wp_version;

			require 'edd-promo-options.php';
		}

		/**
		 * Sanitize each option value
		 *
		 * @since 0.1
		 * @param array   $input The options returned by the options page
		 * @return array $input Sanitized values
		 */
		function validate_options( $input ) {

			$subject_email = strtolower( $input['subject_email'] );
				$input['subject_email'] = esc_html( $input['subject_email'] );
			
	

			/** Check HTML template *****************************************/

			// Template is empty
			if ( empty( $input['template'] ) ) {
				add_settings_error( 'edd_promo_options', 'settings_updated', __( 'Promotion is empty', 'edd-promo' ) );
				
			}

			$input['template'] = $input['template'];


			return $input;
		}

	

		/**
		 * Checks the WP Promo Emails options
		 *
		 * @since 0.1
		 * @return bool
		 */
		function check_template() {
			if ( strpos( $this->options['template']) === false || empty( $this->options['template'] ) )
				return false;

			return true;
		}

		/**
		 * Always set content type to HTML
		 *
		 * @since 0.1
		 * @param string $content_type
		 * @return string $content_type
		 */
		function set_content_type( $content_type ) {
			// Only convert if the message is text/plain and the template is ok
			if ( $content_type == 'text/plain' && $this->check_template() === true ) {
				$this->send_as_html = true;
				return $content_type = 'text/html';
			} else {
				$this->send_as_html = false;
			}
			return $content_type;
		}


		/**
		 * Process the HTML version of the message
		 *
		 * @since 0.2.7
		 * @param string $message
		 * @return string
		 */
		function process_email_html( $message ) {

			// Clean < and > around text links in WP 3.1
			$message = $this->esc_textlinks( $message );

			// Convert line breaks & make links clickable
			$message = nl2br( make_clickable( $message ) );

			// Replace variables in email
			$message = apply_filters( 'edd_promo_html_body', $this->template_vars_replacement( $message ) );

			return $message;

		}

		/**
		 * Replaces the < & > of the 3.1 email text links
		 *
		 * @since 0.1.2
		 * @param string $body
		 * @return string
		 */
		function esc_textlinks( $body ) {
			return preg_replace( '#<(https?://[^*]+)>#', '$1', $body );
		}

		/**
		 * TinyMCE plugins
		 *
		 * Editing HTML emails requires some more plugins from TinyMCE:
		 *  - fullpage to handle html, meta, body tags
		 *  - codemirror for editing source
		 *
		 * @since 0.2
		 * @param array $external_plugins
		 * @return array
		 */
		function tinymce_plugins( $external_plugins ) {
			global $wp_version;

			if ( ! $this->is_eddpromo_page() )
				return $external_plugins;

			$fullpage = array();

			if ( version_compare( $wp_version, '3.2', '<' ) ) {
				$fullpage = array(
					'fullpage' => plugins_url( 'tinymce-plugins/3.3.x/fullpage/editor_plugin.js', __FILE__ )
				);
			} else {
				$fullpage = array(
					'fullpage' => plugins_url( 'tinymce-plugins/3.4.x/fullpage/editor_plugin.js', __FILE__ )
				);
			}

			$cmseditor = array(
				'cmseditor' => plugins_url( 'tinymce-plugins/cmseditor/editor_plugin.js', __FILE__ )
			);

			$external_plugins = $external_plugins + $fullpage + $cmseditor;

			return $external_plugins;
		}

		/**
		 * Button to the TinyMCE toolbar
		 *
		 * @since 0.2
		 * @global string $page
		 * @global type $page_hook
		 * @param type $buttons
		 * @return type
		 */
		function tinymce_buttons( $buttons ) {
			if ( $this->is_eddpromo_page() )
				array_push( $buttons, 'cmseditor' );

			return $buttons;
		}

		/**
		 * Prevent TinyMCE from removing line breaks
		 *
		 * @param array $init
		 * @return boolean
		 */
		function tinymce_config( $init ) {
			if ( ! $this->is_eddpromo_page() )
				return $init;

			$init['remove_linebreaks'] = false;
			$init['content_css']       = ''; // WP =< 3.0

			if ( isset( $init['extended_valid_elements'] ) )
				$init['extended_valid_elements'] = $init['extended_valid_elements'] . ',td[*]';

			return $init;
		}

		/**
		 * Load WP TinyMCE editor
		 *
		 * @since 0.2
		 */
		function load_wp_tiny_mce() {
			if ( ! $this->is_eddpromo_page() )
				return;

			$settings = array(
				'editor_selector' => 'edd_promo_template',
				'height'          => '400'
			);

			wp_tiny_mce( false, $settings );
		}

		/**
		 * Print WP TinyMCE editor to edit template
		 *
		 * @since 0.2
		 * @global string $wp_version
		 */
		function template_editor() {
			global $wp_version;

			if ( version_compare( $wp_version, '3.2.1', '<=' ) ) {
?>
				<textarea id="edd_promo_template" class="edd_promo_template" name="edd_promo_options[template]" cols="80" rows="10"><?php echo $this->options['template']; ?></textarea>
				<?php
			} else {
				// WP >= 3.3
				$settings = array(
					'wpautop'       => false,
					'editor_class'  => 'edd_promo_template',
					'quicktags'     => false,
					'textarea_name' => 'edd_promo_options[template]'
				);

				wp_editor( $this->options['template'], 'edd_promo_template', $settings );
			}
		}

	}

}


if ( class_exists( 'EDD_Promo' ) ) {
	$edd_promo = new EDD_Promo();
	register_activation_hook( __FILE__, array( $edd_promo, 'install' ) );
}