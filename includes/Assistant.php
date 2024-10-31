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
class Assistant {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
	
		// add faq assistant div

		add_action( 'wp_footer', array( $this , 'add_faq_assistant_div') );
	}


	/**
	 * Register and enqueue admin-specific style sheet.
	 * @since     1.0
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_script() {
		wp_enqueue_script( $this->plugin_slug . '-widget-script', plugins_url( 'assets/js/widget.js?v='.time(), dirname( __FILE__ ) ), array( 'jquery' ), $this->version );
		wp_enqueue_style( $this->plugin_slug . '-widget-style', plugins_url( 'assets/css/widget.css?v='.time(), dirname( __FILE__ ) ), $this->version );

		$object_name = 'piri_faa_object';

		$piri_faq_ai_assistant_company_value = get_option( 'piri_faq_ai_assistant_company_value' );
		$piri_faq_ai_assistant_bot_name_value = get_option( 'piri_faq_ai_assistant_bot_name_value' );
		$piri_faq_ai_assistant_base_url_value = get_option( 'piri_faq_ai_assistant_base_url_value' );
		$piri_faq_ai_assistant_bot_src_value = esc_attr( get_option( 'piri_faq_ai_assistant_bot_src_value' ) );
		$piri_faq_ai_assistant_bot_inverted_value = esc_attr( get_option( 'piri_faq_ai_assistant_bot_inverted_value' ) );

		$piri_faq_ai_assistant_primary_color_value = get_option( 'piri_faq_ai_assistant_primary_color_value' );
		$piri_faq_ai_assistant_initial_state_value = get_option( 'piri_faq_ai_assistant_initial_state_value' );

		$object = array(
			'company'        => $piri_faq_ai_assistant_company_value,
			'bot_name'       => $piri_faq_ai_assistant_bot_name_value,
			'base_url'	     => $piri_faq_ai_assistant_base_url_value,
			'bot_inverted'   => $piri_faq_ai_assistant_bot_inverted_value,
			'bot_src'      	 => $piri_faq_ai_assistant_bot_src_value,
			'primary_color'  => $piri_faq_ai_assistant_primary_color_value,
			'initial_state' => $piri_faq_ai_assistant_initial_state_value
		);
		wp_localize_script( $this->plugin_slug . '-widget-script', $object_name, $object );

	}


	function add_faq_assistant_div () {
		$object_name = 'piri_faa_object';
		?><div class="piri-faa-widget" id="piri-ai" data-object-id="<?php echo $object_name ?>"></div><?php
	}
	
}
