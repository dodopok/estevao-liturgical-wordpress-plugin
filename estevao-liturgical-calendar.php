<?php
/**
 * Plugin Name: Estevão Liturgical Calendar
 * Plugin URI: https://github.com/douglas/estevao-liturgical-wordpress-plugin
 * Description: Exibe informações do calendário litúrgico anglicano usando a API Caminho Anglicano. Use o shortcode [liturgical_calendar] para exibir as informações.
 * Version: 1.0.1
 * Author: Douglas Araujo
 * Author URI: https://caminhoanglicano.com.br
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: estevao-liturgical-calendar
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('ESTEVAO_LITURGICAL_VERSION', '1.0.1');
define('ESTEVAO_LITURGICAL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ESTEVAO_LITURGICAL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ESTEVAO_LITURGICAL_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Estevao_Liturgical_Calendar {

    /**
     * Single instance of the class
     */
    private static $instance = null;

    /**
     * API Client instance
     */
    public $api_client;

    /**
     * Shortcodes instance
     */
    public $shortcodes;

    /**
     * Admin settings instance
     */
    public $admin;

    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once ESTEVAO_LITURGICAL_PLUGIN_DIR . 'includes/class-api-client.php';
        require_once ESTEVAO_LITURGICAL_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once ESTEVAO_LITURGICAL_PLUGIN_DIR . 'includes/class-admin-settings.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Initialize plugin
     */
    public function init() {
        $this->api_client = new Estevao_Liturgical_API_Client();
        $this->shortcodes = new Estevao_Liturgical_Shortcodes($this->api_client);

        if (is_admin()) {
            $this->admin = new Estevao_Liturgical_Admin($this->api_client);
        }
    }

    /**
     * Enqueue frontend CSS
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'estevao-liturgical-frontend',
            ESTEVAO_LITURGICAL_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            ESTEVAO_LITURGICAL_VERSION
        );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $defaults = array(
            'prayer_book_code' => 'loc_2015',
            'bible_version' => 'nvi',
        );

        foreach ($defaults as $key => $value) {
            if (false === get_option('estevao_liturgical_' . $key)) {
                add_option('estevao_liturgical_' . $key, $value);
            }
        }

        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear cached data
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_estevao_liturgical_%' OR option_name LIKE '_transient_timeout_estevao_liturgical_%'"
        );

        flush_rewrite_rules();
    }
}

/**
 * Initialize the plugin
 */
function estevao_liturgical_calendar() {
    return Estevao_Liturgical_Calendar::get_instance();
}

// Start the plugin
estevao_liturgical_calendar();
