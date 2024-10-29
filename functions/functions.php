<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Function sets AINISA_SMART_REVIEWER_OPTIONS object and wpApiSettings for javascript file
 */
if( ! function_exists( 'ains_set_fake_names' ) ) {
    function ains_set_fake_names() {

        $domain = 'ainisa-smart-reviewer';
        $fakes = require_once AINISA_SMART_REVIEWER_PATH.'config/fakes.php';
        $translations = require AINISA_SMART_REVIEWER_PATH.'config/validations.php';
        $fake_names = $fakes['names'];
        wp_localize_script('ainisa-smart-reviewer-main', 'AINISA_SMART_REVIEWER_OPTIONS', [
            'fakeNames' => $fake_names,
            'translations' => $translations['additional']
        ]);
        wp_localize_script( 'ainisa-smart-reviewer-main', 'wpApiSettings', array(
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        ) );

    }
}

/**
 * Function for getting ip address
 */
if( ! function_exists( 'ains_get_ip_address' ) ) {
    function ains_get_ip_address() {
        return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : null;
    }
}

/**
 * Function for getting post types for Ainisa
 */
if( ! function_exists( 'ains_get_post_types_for_ainisa' ) ) {
    function ains_get_post_types_for_ainisa() {
        $post_types = get_post_types();
        $post_types_to_be_removed = [
            'attachment',
            'revision',
            'nav_menu_item',
            'custom_css',
            'customize_changeset',
            'oembed_cache',
            'user_request',
            'wp_block',
            'wp_template',
            'wp_template_part',
            'wp_global_styles',
            'wp_navigation',
            'wp_font_family',
            'wp_font_face'
        ];
        foreach ($post_types_to_be_removed as $pt) {
            unset($post_types[$pt]);
        }

        return $post_types;
    }
}