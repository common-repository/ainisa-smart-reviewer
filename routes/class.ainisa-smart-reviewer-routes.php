<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AinisaSmartReviewerRoutes {

    /**
     * Construct and init rest api
     */
    public function __construct()
    {
        require_once AINISA_SMART_REVIEWER_PATH.'includes/class.ainisa-validator.php';

        add_action( 'rest_api_init', [$this, 'ainisaSmartReviewerRoutes'] );
    }

    /**
     * Register routes for Ainisa smart reviewer
     * @return void
     */
    public function ainisaSmartReviewerRoutes()
    {
        $route_namespace = 'ainisa-smart-reviewer-api/v1';
        register_rest_route(
            $route_namespace,
            '/create-review/',
            array(
                'methods'  => 'POST',
                'callback' => [$this, 'createReview'],
                'permission_callback' => [$this, 'requiredPermissions']
            )
        );

        register_rest_route(
            $route_namespace,
            '/add-review/',
            array(
                'methods'  => 'POST',
                'callback' => [$this, 'saveReview'],
                'permission_callback' => [$this, 'requiredPermissions']
            )
        );

        register_rest_route(
            $route_namespace,
            '/get-posts/',
            array(
                'methods'  => 'POST',
                'callback' => [$this, 'getPosts'],
                'permission_callback' => [$this, 'requiredPermissions']
            )
        );
    }

    /**
     * Check permission - if user can moderate comments or not
     * @param $request
     * @return bool
     */
    public function requiredPermissions($request) {
        return current_user_can('moderate_comments');
    }

    /**
     * Create review with ai
     * @param $request
     * @return array|WP_Error
     */
    public function createReview($request)
    {
        $domain = 'ainisa-smart-reviewer';
        $namings = require AINISA_SMART_REVIEWER_PATH.'config/namings.php';
        $ainisa_get_review_namings = $namings['ai_smart_reviewer_get_review'];
        $rules = ['ainisa_smart_review_prompt' => ['required', 'min_length:20'],];
        $aiNisaValidator = new AiNisaValidator($request->get_body_params(), $domain, $ainisa_get_review_namings);
        $aiNisaValidator->validate($rules);
        if($aiNisaValidator->hasErrors()) {
            return new WP_Error(400, 'Problem happened', ['errors' => $aiNisaValidator->createErrorsList()]);
        }

        require_once AINISA_SMART_REVIEWER_PATH.'includes/class.ainisa-openai.php';
        $config = require AINISA_SMART_REVIEWER_PATH.'config/config.php';

        $prompt_parts = $config['prompt_parts'];
        $prompt = $request['ainisa_smart_review_prompt'].'. '.$prompt_parts['make_grammar_mistakes'].'. '.$prompt_parts['return_type'];
        $aiNisaOpenAi = new AiNisaOpenAi();
        $params = [
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ];
        $message = $aiNisaOpenAi->sendApiRequest($aiNisaOpenAi->completionsUrl, 'POST', $params);
        $cc = json_decode($message);
        if($cc) {
            $title = $cc->title;
            $text = $cc->review;
        } else {
            $title = '';
            $text = '';
        }

        if($title != '' && $text != '') {
            $ui_message = esc_html__('Review is ready ! You can save it', 'ainisa-smart-reviewer');
            $success = true;
        } else {
            $ui_message = esc_html__('Could not get review. Please try again', 'ainisa-smart-reviewer');
            $success = false;
        }
        return ['message' => $ui_message, 'success' => $success, 'pp' => $cc, 'content' => [$title, $text]];
    }

    /**
     * Add new user and save review
     * @param $request
     * @return string[]|WP_Error
     */
    public function saveReview($request)
    {
        $domain = 'ainisa-smart-reviewer';
        $namings = require AINISA_SMART_REVIEWER_PATH.'config/namings.php';
        $ainisa_save_review_namings = $namings['ai_smart_reviewer_save_review'];
        $aiNisaValidator = new AiNisaValidator($request->get_body_params(), $domain, $ainisa_save_review_namings);
        $rules = [
            'ainisa_smart_reviewer_email' => ['required', 'email', 'email_exists'],
            'ainisa_smart_reviewer_firstname' => ['required'],
            'ainisa_smart_reviewer_lastname' => ['required'],
            'ainisa_smart_reviewer_review' => ['required'],
            'ainisa_smart_reviewer_post_id' => ['required', 'numeric']
        ];

        $aiNisaValidator->validate($rules);
        if($aiNisaValidator->hasErrors()) {
            return new WP_Error(400, 'Problem happened', ['errors' => $aiNisaValidator->createErrorsList()]);
        } else {
            $user_firstname = sanitize_text_field($request['ainisa_smart_reviewer_firstname']);
            $user_lastname = sanitize_text_field($request['ainisa_smart_reviewer_lastname']);
            $review = sanitize_text_field($request['ainisa_smart_reviewer_review']);
            $user_email = sanitize_email($request['ainisa_smart_reviewer_email']);
            $post_id = intval($request['ainisa_smart_reviewer_post_id']);
            $user_login = $user_firstname.'_'.$user_lastname.wp_rand(99, 999999);
            $registration_timestamp = wp_rand(1700000000, time());
            $user_data = [
                'user_login' => $user_login,
                'first_name' => $user_firstname,
                'last_name' => $user_lastname,
                'user_email' => $user_email,
                'user_pass' => wp_rand(100000, 999999999),
                'role' => 'subscriber',
                'user_registered' => gmdate('Y-m-d H:i:s', $registration_timestamp),
            ];

            $user = wp_insert_user($user_data);
            if(is_int($user)) {
                $comment_date = gmdate('Y-m-d H:i:s', ($registration_timestamp+1440));
                $comment = [
                    'comment_agent' => '',
                    'comment_author' => $user_firstname.' '.$user_lastname,
                    'comment_author_email' => $user_email,
                    'comment_author_url' => '',
                    'comment_author_IP' => ains_get_ip_address(),
                    'comment_date' => $comment_date,
                    'comment_date_gmt' => get_gmt_from_date($comment_date),
                    'comment_post_ID' => $post_id,
                    'comment_content' => $review,
                    'comment_karma' => 0,
                    'comment_approved' => 1,
                    'comment_type' => 'comment',
                    'comment_parent' => 0,
                    'user_id' => $user

                ];
                wp_insert_comment($comment);
                return ['message' => esc_html__('Review successfully created', 'ainisa-smart-reviewer')];
            } else {
                return new WP_Error(400, 'Problem happened', ['errors' => $user->get_error_messages()]);
            }
        }
    }

    /**
     * Get posts array for which we need to add review/comment
     * @param $request
     * @return mixed
     */
    public function getPosts($request)
    {
        global $wpdb;

        $domain = 'ainisa-smart-reviewer';
        $namings = require AINISA_SMART_REVIEWER_PATH.'config/namings.php';
        $ainisa_get_posts_namings = $namings['ai_smart_reviewer_get_posts'];
        $aiNisaValidator = new AiNisaValidator($request->get_body_params(), $domain, $ainisa_get_posts_namings);
        $rules = [
            'ainisa_get_posts_type' => ['required'],
            'ainisa_get_posts_comments' => ['required', 'numeric'],
            'ainisa_get_posts_numbers' => ['required', 'numeric']
        ];

        $aiNisaValidator->validate($rules);
        if($aiNisaValidator->hasErrors()) {
            return new WP_Error(400, esc_html__('Problem happened', 'ainisa-smart-reviewer'), ['errors' => $aiNisaValidator->createErrorsList()]);
        } else {
            $post_type = $request['ainisa_get_posts_type'];
            $comment_count = $request['ainisa_get_posts_comments'];
            $post_numbers = $request['ainisa_get_posts_numbers'];


            $q = $wpdb->prepare("SELECT p.ID, p.post_title, p.post_status, p.post_type, p.comment_count
        FROM $wpdb->posts as p WHERE p.post_status = 'publish' AND p.post_type = %s AND p.comment_count <= %d
        ORDER BY p.post_date DESC LIMIT %d ", $post_type, $comment_count, $post_numbers);

            $posts = $wpdb->get_results($q, ARRAY_A);

            return ['posts' => $posts, 'message' => esc_html__('Number of data', 'ainisa-smart-reviewer').': '.count($posts)];
        }

    }
}