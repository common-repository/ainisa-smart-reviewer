<?php
if ( ! defined( 'ABSPATH' ) ) exit;
return [
    'ai_smart_reviewer_get_posts' => [
        'ainisa_get_posts_type' => esc_html__('Select post type', 'ainisa-smart-reviewer'),
        'ainisa_get_posts_comments' => esc_html__('Posts which have comments less than', 'ainisa-smart-reviewer'),
        'ainisa_get_posts_numbers' => esc_html__('Number of posts', 'ainisa-smart-reviewer'),
    ],
    'ai_smart_reviewer_get_review' => [
        'ainisa_smart_review_prompt' => esc_html__('Review prompt', 'ainisa-smart-reviewer'),
    ],
    'ai_smart_reviewer_save_review' => [
        'ainisa_smart_reviewer_email' => esc_html__('User e-mail', 'ainisa-smart-reviewer'),
        'ainisa_smart_reviewer_firstname' => esc_html__('User firstname', 'ainisa-smart-reviewer'),
        'ainisa_smart_reviewer_lastname' => esc_html__('User lastname', 'ainisa-smart-reviewer'),
        'ainisa_smart_reviewer_review' => esc_html__('Review', 'ainisa-smart-reviewer'),
        'ainisa_smart_reviewer_post_id' => esc_html__('Select post', 'ainisa-smart-reviewer'),
    ],
    'ainisa_smart_reviewer_options' => [
        'ainisa_openai_secret_key' => esc_html__( 'Open Ai Secret Key', 'ainisa-smart-reviewer' ),
        'ainisa_openai_model' => esc_html__( 'Ai Model', 'ainisa-smart-reviewer' ),
        'ainisa_openai_temperature' => esc_html__( 'Temperature', 'ainisa-smart-reviewer' ),
        'ainisa_openai_max_tokens' => esc_html__( 'Maximum tokens', 'ainisa-smart-reviewer' ),
        'ainisa_openai_default_prompt' => esc_html__( 'Ai default prompt', 'ainisa-smart-reviewer' ),
    ]

];