<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Plugin name: Ainisa Smart Reviewer
 * Plugin Uri: https://ainisa.com
 * Description: Smart AI comment/review writer for your wordpress website by AiNisa
 * Version: 1.0
 * Requires at least: 6.0
 * Author: Javid Karimov
 * Author URI: ainisa.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ainisa-smart-reviewer
 * Domain Path: /languages
 */

class AinisaSmartReviewer {
    public function __construct()
    {
        $this->defineConstants();
        //$this->loadTextDomain();
        add_action('plugins_loaded', [$this, 'loadTextDomain']);
        require_once AINISA_SMART_REVIEWER_PATH.'functions/functions.php';

        add_action('admin_menu', [$this, 'addMenu']);

        require_once AINISA_SMART_REVIEWER_PATH.'class.ainisa-smart-reviewer-settings.php';
        require_once AINISA_SMART_REVIEWER_PATH.'routes/class.ainisa-smart-reviewer-routes.php';
        $aiNisaSmartReviewerSettings = new AiNisaSmartReviewerSettings();
        $aiNisaSmartReviewerRoutes = new AinisaSmartReviewerRoutes();

        add_action('admin_enqueue_scripts', [$this, 'registerAdminScripts'], 999);
    }

    public function defineConstants()
    {
        define( 'AINISA_SMART_REVIEWER_PATH', plugin_dir_path(__FILE__) );
        define( 'AINISA_SMART_REVIEWER_URL', plugin_dir_url(__FILE__) );
        define( 'AINISA_SMART_REVIEWER_VERSION', '1.0.0' );
    }

    public static function activate()
    {
        update_option('rewrite_rules', '');
    }

    public static function deactivate()
    {
        flush_rewrite_rules();
    }

    public static function uninstall()
    {
        delete_option('ainisa_smart_reviewer_options');
    }

    public function addMenu()
    {
        add_menu_page(
            esc_html__('AI Smart Reviewer Settings', 'ainisa-smart-reviewer' ),
            esc_html__( 'AI Smart Reviewer', 'ainisa-smart-reviewer' ),
            'manage_options',
            'ainisa-smart-reviewer-admin',
            [$this, 'AiNisaSmartReviewerSettingsPage'],
            'dashicons-format-gallery',
            10
        );

        add_submenu_page(
            'ainisa-smart-reviewer-admin',
            esc_html__( 'Add new review', 'ainisa-smart-reviewer' ),
            esc_html__( 'Add new review', 'ainisa-smart-reviewer' ),
            'manage_options',
            'ainisa-smart-reviewer-admin-new-review',
            [$this, 'AiNisaSmartReviewerNewReviewPage'],
            null
        );

    }

    public function AiNisaSmartReviewerSettingsPage()
    {
        if( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $number_of_errors = count(get_settings_errors());
        $errors = get_settings_errors();
        $has_errors = !(($number_of_errors == 1 && $errors[0]['type'] === 'success'));
        if( isset( $_GET['settings-updated'] ) ) {
            if($has_errors) {
                settings_errors('ainisa_smart_reviewer_options');
            } else {
                add_settings_error( 'ainisa_smart_reviewer_options', 'ainisa_smart_reviewer_option_message', esc_html__( 'Settings saved !', 'ainisa-smart-reviewer' ), 'success' );
                settings_errors('ainisa_smart_reviewer_options');
            }
            set_transient( 'ainisa_smart_reviewer_options', get_settings_errors(), 30 ); // 30 seconds.

        }

        require_once AINISA_SMART_REVIEWER_PATH.'views/settings_page.php';
    }

    public function AiNisaSmartReviewerNewReviewPage()
    {
        if( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        wp_enqueue_style('ainisa-smart-reviewer-ainisa');
        wp_enqueue_style('ainisa-smart-reviewer-fontawesome');
        wp_enqueue_style('ainisa-smart-reviewer-waitme');
        wp_enqueue_style('ainisa-smart-reviewer-toastr-css');
        wp_enqueue_style('ainisa-smart-reviewer-style');
        wp_enqueue_script('ainisa-smart-reviewer-waitme-js');
        wp_enqueue_script('ainisa-smart-reviewer-toastr-js');
        wp_enqueue_script('ainisa-smart-reviewer-main');
        ains_set_fake_names();

        $config = require_once AINISA_SMART_REVIEWER_PATH.'functions/functions.php';
        $ai_smart_reviewer_options = get_option('ainisa_smart_reviewer_options');

        global $wpdb;
        $default_post_types = ['page', 'post'];
        $post_type_placeholders = implode( ', ', array_fill( 0, count( $default_post_types ), '%d' ) );

        $q = $wpdb->prepare("SELECT p.ID, p.post_title, p.post_status, p.post_type
        FROM $wpdb->posts as p WHERE p.post_status = %s AND p.post_type IN ('page', 'post')
        ORDER BY p.post_date DESC LIMIT %d ", 'publish', 100);

        $posts = $wpdb->get_results($q, ARRAY_A);
        $post_types = ains_get_post_types_for_ainisa();

        require_once AINISA_SMART_REVIEWER_PATH.'/views/new_review_page.php';
    }

    public function registerAdminScripts()
    {
        wp_register_style('ainisa-smart-reviewer-ainisa', AINISA_SMART_REVIEWER_URL.'assets/css/ainisa_tailwind.css', [], AINISA_SMART_REVIEWER_VERSION, 'all');
        wp_register_style('ainisa-smart-reviewer-fontawesome', AINISA_SMART_REVIEWER_URL.'assets/css/font-awesome.min.css', [], AINISA_SMART_REVIEWER_VERSION, 'all');
        wp_register_style('ainisa-smart-reviewer-waitme', AINISA_SMART_REVIEWER_URL.'vendor/waitMe/waitMe.min.css', [], AINISA_SMART_REVIEWER_VERSION, 'all');
        wp_register_style('ainisa-smart-reviewer-toastr-css', AINISA_SMART_REVIEWER_URL.'vendor/toastr/toastr.css', [], AINISA_SMART_REVIEWER_VERSION, 'all');
        wp_register_style('ainisa-smart-reviewer-style', AINISA_SMART_REVIEWER_URL.'assets/css/style.css', [], AINISA_SMART_REVIEWER_VERSION, 'all');
        wp_register_script('ainisa-smart-reviewer-waitme-js', AINISA_SMART_REVIEWER_URL.'vendor/waitMe/waitMe.min.js',
            ['jquery'],
            AINISA_SMART_REVIEWER_VERSION,
            true
        );
        wp_register_script('ainisa-smart-reviewer-toastr-js', AINISA_SMART_REVIEWER_URL.'vendor/toastr/toastr.js',
            ['jquery'],
            AINISA_SMART_REVIEWER_VERSION,
            true
        );
        wp_register_script('ainisa-smart-reviewer-main', AINISA_SMART_REVIEWER_URL.'assets/js/ainisa_admin.js',
            [],
            AINISA_SMART_REVIEWER_VERSION,
            true
        );
    }

    public function loadTextDomain()
    {
        load_plugin_textdomain(
            'ainisa-smart-reviewer',
            false,
            dirname( plugin_basename( __FILE__ ) ).'/languages/'
        );
    }

}

if( class_exists( 'AinisaSmartReviewer' ) ) {
    register_activation_hook( __FILE__, ['AinisaSmartReviewer', 'activate'] );
    register_deactivation_hook( __FILE__, ['AinisaSmartReviewer', 'deactivate'] );
    register_uninstall_hook( __FILE__, ['AinisaSmartReviewer', 'uninstall'] );

    $AinisaSmartReviewer = new AinisaSmartReviewer();
}