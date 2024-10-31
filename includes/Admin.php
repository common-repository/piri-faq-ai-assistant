<?php
/**
 * Piri-FAQ-AI-Assistant
 *
 *
 * @package   Piri-FAQ-AI-Assistant
 * @author    PIRI
 * @license   GPL-3.0
 * @link      http://piri.ai/
 * @copyright 2019 PIRI
 */

namespace Piri\FAA;

/**
 * @subpackage Admin
 */
class Admin {

	/**
	 * Instance of this class.
	 * @since    1.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Plugin basename.
	 * @since    1.0
	 * @var      string
	 */
	protected $plugin_basename = null;

	/**
	 * Slug of the plugin screen.
	 * @since    1.0
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;


	/**
	 * Return an instance of this class.
	 * @since     1.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->do_hooks();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 * @since     1.0
	 */
	private function __construct() {
		$plugin = Plugin::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->version = $plugin->get_plugin_version();

		$this->plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) )  . 'piri-faq-ai-assistant.php' );
	}

	/**
	 * Handle WP actions and filters.
	 * @since 	1.0
	 */
	private function do_hooks() {
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add plugin action link point to settings page
		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'add_action_links' ) );

		// add settings
		add_action( 'admin_init',  array( $this, 'initialize_plugin_settings' ) );

		// add settings api error slug
		add_action( 'admin_notices', array( $this, 'admin_notices_action' ) );
	}


	/**
	 * Register and enqueue admin-specific style sheet.
	 * @since     1.0
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Register and enqueue admin-specific javascript
	 * @since     1.0
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
 
			wp_enqueue_script('jquery');
			wp_enqueue_media();
			wp_enqueue_script( 'wp-color-picker');
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', dirname( __FILE__ ) ), array( 'jquery' ), $this->version );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 * @since    1.0
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_menu_page( 'Piri FAQ AI', 'Piri FAQ AI', 'manage_options', $this->plugin_slug, array( __CLASS__ , 'display_plugin_admin_page'), 'dashicons-money' , '39' );
	}

	/**
	 * Render the settings page for this plugin.
	 * @since    1.0
	 */
	public function display_plugin_admin_page() {
		?>
			<div class="wrap">
				<h2>
					<?php _e( 'Piri FAQ AI Assistant Settings', 'piri' ); ?>
				</h2>

				<?php settings_errors( 'piri_faq_ai_assistant' ); ?>
				<?php self::display_settings(); ?>
			</div>
		<?php
	}

	/**
	 * Add setings api error slug
	 * @since 1.0
	 */
	public function admin_notices_action ( ){
		settings_errors( 'piri_faq_ai_assistant' );
	}

	/**
	 * Render the settings on the page using settings api
	 * @since 1.0
	 */
	public function display_settings () {
		?>
		<form method="post" action="options.php">
			<?php settings_fields     ( 'piri_faq_ai_assistant_settings' ); ?>
			<?php do_settings_sections( 'piri_faq_ai_assistant_page' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}
	/**
	 * Initialize plugin settings
	 * @since 1.0
	 */

	 public function initialize_plugin_settings (){
		add_settings_section(
			'piri_faq_ai_assistant_settings_section',         
			__( 'Settings', 'PIRI' ),                  
			array( __CLASS__, 'piri_faq_ai_assistant_settings_callback' ), 
			'piri_faq_ai_assistant_page'     
		);

		add_settings_field(
			'piri_faq_ai_assistant_company_value',
			__( 'Company', 'piri' ),
			array( __CLASS__ , 'piri_faq_ai_assistant_company_value_callback' ),
			'piri_faq_ai_assistant_page',
			'piri_faq_ai_assistant_settings_section',
			array( __( 'Name of your company', 'piri' ) )
		);

		register_setting(
			'piri_faq_ai_assistant_settings',
			'piri_faq_ai_assistant_company_value',
			array ( __CLASS__, 'piri_faq_ai_assistant_company_value_validation' )
		);

		add_settings_field(
			'piri_faq_ai_assistant_bot_name_value',
			__( 'Bot Name', 'piri' ),
			array( __CLASS__ , 'piri_faq_ai_assistant_bot_name_value_callback' ),
			'piri_faq_ai_assistant_page',
			'piri_faq_ai_assistant_settings_section',
			array( __( 'Displayed in chat header and message', 'piri' ) )
		);

		register_setting(
			'piri_faq_ai_assistant_settings',
			'piri_faq_ai_assistant_bot_name_value',
			array ( __CLASS__, 'piri_faq_ai_assistant_bot_name_value_validation' )
		);
	
		add_settings_field(
			'piri_faq_ai_assistant_base_url_value',
			__( 'Base Url', 'piri' ),
			array( __CLASS__ , 'piri_faq_ai_assistant_base_url_value_callback' ),
			'piri_faq_ai_assistant_page',
			'piri_faq_ai_assistant_settings_section',
			array( __( 'URL to backend AI', 'piri' ) )
		);

		register_setting(
			'piri_faq_ai_assistant_settings',
			'piri_faq_ai_assistant_base_url_value',
			array ( __CLASS__, 'piri_faq_ai_assistant_base_url_value_validation' )
		);

		add_settings_field(
			'piri_faq_ai_assistant_bot_src_value',
			__( 'Bot Icon', 'piri' ),
			array( __CLASS__ , 'piri_faq_ai_assistant_bot_src_value_callback' ),
			'piri_faq_ai_assistant_page',
			'piri_faq_ai_assistant_settings_section',
			array( __( 'Displayed in chat header and with messages.', 'piri' ) )
		);

		register_setting(
			'piri_faq_ai_assistant_settings',
			'piri_faq_ai_assistant_bot_src_value'
		);

		add_settings_field(
			'piri_faq_ai_assistant_bot_inverted_value',
			__( 'Bot Inverted', 'piri' ),
			array( __CLASS__ , 'piri_faq_ai_assistant_bot_inverted_value_callback' ),
			'piri_faq_ai_assistant_page',
			'piri_faq_ai_assistant_settings_section',
			array( __( 'Overlay icon, displayed over primary color.', 'piri' ) )
		);

		register_setting(
			'piri_faq_ai_assistant_settings',
			'piri_faq_ai_assistant_bot_inverted_value'
		);

		add_settings_field(
			'piri_faq_ai_assistant_primary_color_value',
			__( 'Primary color', 'piri' ),
			array( __CLASS__ , 'piri_faq_ai_assistant_primary_color_value_callback' ),
			'piri_faq_ai_assistant_page',
			'piri_faq_ai_assistant_settings_section',
			array( __( 'Color of chat window, toggle, message background.', 'piri' ) )
		);

		register_setting(
			'piri_faq_ai_assistant_settings',
			'piri_faq_ai_assistant_primary_color_value'
		);

		add_settings_field(
			'piri_faq_ai_assistant_initial_state_value',
			__( 'Welcome Messages', 'piri' ),
			array( __CLASS__ , 'piri_faq_ai_assistant_initial_state_value_callback' ),
			'piri_faq_ai_assistant_page',
			'piri_faq_ai_assistant_settings_section',
			array( __( 'Please add Welcome Messages.', 'piri' ) )
		);

		register_setting(
			'piri_faq_ai_assistant_settings',
			'piri_faq_ai_assistant_initial_state_value'
		);
	}

	public static function piri_faq_ai_assistant_settings_callback ( ){

	}

	/**
	 * Render the Company name field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_company_value_callback ( $args ){
		$piri_faq_ai_assistant_company_value = get_option( 'piri_faq_ai_assistant_company_value' );
		printf(
			'<input type="text" id="piri_faq_ai_assistant_company_value" name="piri_faq_ai_assistant_company_value" placeholder = "PIRI" value="%s" />',
			isset( $piri_faq_ai_assistant_company_value ) ? esc_attr( $piri_faq_ai_assistant_company_value ) : ''
		);
		$html = '<br><label for="piri_faq_ai_assistant_company_value"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Validate the Company name field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_company_value_validation ( $input ) {
		$output = '';
		if ( $input != '' ) {
			$output = stripslashes( $input) ;
		} else {
			$output = add_settings_error( 'piri_faq_ai_assistant', 'error found', __( 'Company name is required.', 'piri' ) );
		}
		return $output;
	}

	/**
	 * Render the Bot name field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_bot_name_value_callback ( $args ){
		$piri_faq_ai_assistant_bot_name_value = get_option( 'piri_faq_ai_assistant_bot_name_value' );
		printf(
			'<input type="text" id="piri_faq_ai_assistant_bot_name_value" name="piri_faq_ai_assistant_bot_name_value" placeholder = "Bot PIRI" value="%s" />',
			isset( $piri_faq_ai_assistant_bot_name_value ) ? esc_attr( $piri_faq_ai_assistant_bot_name_value ) : ''
		);
		$html = '<br><label for="piri_faq_ai_assistant_bot_name_value"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Validate the Company name field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_bot_name_value_validation ( $input ) {
		$output = '';
		if ( $input != '' ) {
			$output = stripslashes( $input) ;
		} else {
			$output = add_settings_error( 'piri_faq_ai_assistant', 'error found', __( 'Bot name is required.', 'piri' ) );
		}
		return $output;
	}

	/**
	 * Render the Base url field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_base_url_value_callback ( $args ){
		$piri_faq_ai_assistant_base_url_value = get_option( 'piri_faq_ai_assistant_base_url_value' );
		printf(
			'<input type="text" id="piri_faq_ai_assistant_base_url_value" name="piri_faq_ai_assistant_base_url_value" placeholder = "https://your-assistant-endpoint/" value="%s" />',
			isset( $piri_faq_ai_assistant_base_url_value ) ? esc_attr( $piri_faq_ai_assistant_base_url_value ) : ''
		);
		
		$html = '<br><label for="piri_faq_ai_assistant_base_url_value"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * validate the base url field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_base_url_value_validation ( $input ) {
		$output = '';
		if ( $input != '' ) {
			$output = stripslashes( $input) ;
		} else {
			$output = add_settings_error( 'piri_faq_ai_assistant', 'error found', __( 'Base url is required.', 'piri' ) );
		}
		return $output;
	}

	/**
	 * Render the Bot inverted field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_bot_inverted_value_callback ( $args ){
		
		$piri_faq_ai_assistant_bot_inverted_value = esc_attr( get_option( 'piri_faq_ai_assistant_bot_inverted_value' ) );
		
		echo '<input type="button" value="Upload icon" id="upload-picture-button-bot-inverted">
			  <input type="hidden" id="piri_faq_ai_assistant_bot_inverted_value" name="piri_faq_ai_assistant_bot_inverted_value" value="'.$piri_faq_ai_assistant_bot_inverted_value.'" />';

		$html = '<br><label for="piri_faq_ai_assistant_bot_inverted_value"> ' . $args[0] . '</label>';
		echo $html;
		?>
		<div style="display:block;width:100%; overflow:hidden; text-align:left;">
			  <div id="piri_faq_ai_assistant_bot_inverted_value_preview" style="background-image: url(<?php echo $piri_faq_ai_assistant_bot_inverted_value;?>);width:150px; height:150px;overflow:hidden;border-radius:50%;margin:20px auto;background-position:center center;background-repeat:no-repeat;background-size:cover;float:left;"></div>
		</div>
		<?php
	}

	/**
	 * Render the Bot Source image field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_bot_src_value_callback ( $args ){
		
		$piri_faq_ai_assistant_bot_src_value = esc_attr( get_option( 'piri_faq_ai_assistant_bot_src_value' ) );
		
		echo '<input type="button" value="Upload icon" id="upload-picture-button">
			  <input type="hidden" id="piri_faq_ai_assistant_bot_src_value" name="piri_faq_ai_assistant_bot_src_value" value="'.$piri_faq_ai_assistant_bot_src_value.'" />';

		$html = '<br><label for="piri_faq_ai_assistant_bot_src_value"> ' . $args[0] . '</label>';
		echo $html;
		?>
		<div style="display:block;width:100%; overflow:hidden; text-align:left;">
			  <div id="user-picture-preview" style="background-image: url(<?php echo $piri_faq_ai_assistant_bot_src_value;?>);width:150px; height:150px;overflow:hidden;border-radius:50%;margin:20px auto;background-position:center center;background-repeat:no-repeat;background-size:cover;float:left;"></div>
		</div>
		<?php
	}

	/**
	 * Render the Primary color field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_primary_color_value_callback ( $args ){
		
		$piri_faq_ai_assistant_primary_color_value = get_option( 'piri_faq_ai_assistant_primary_color_value' );
		printf(
			'<input type="text" id="piri_faq_ai_assistant_primary_color_value" name="piri_faq_ai_assistant_primary_color_value"  value="%s" />',
			isset( $piri_faq_ai_assistant_primary_color_value ) ? esc_attr( $piri_faq_ai_assistant_primary_color_value ) : ''
		);
		$html = '<label for="piri_faq_ai_assistant_primary_color_value"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * Render the Initial state field
	 * @since 1.0
	 */
	public static function piri_faq_ai_assistant_initial_state_value_callback ( $args ){
		
		$piri_faq_ai_assistant_initial_state_value = get_option( 'piri_faq_ai_assistant_initial_state_value' );
		printf(
			"<textarea id='piri_faq_ai_assistant_initial_state_value' name='piri_faq_ai_assistant_initial_state_value' placeholder = 'Hi, Welcome to PIRI' rows='7' cols='50' type='textarea'>%s</textarea>",
			isset( $piri_faq_ai_assistant_initial_state_value ) ? esc_attr( $piri_faq_ai_assistant_initial_state_value ) : ''
		);
		$html = '<br><label for="piri_faq_ai_assistant_initial_state_value">- Press enter to seperate messages<br/>- You may also add options, for example: [Help,About,Contact]</label>';
		echo $html;
	}
	
	/**
	 * Add settings action link to the plugins page.
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>',
			),
			$links
		);
	}
}
