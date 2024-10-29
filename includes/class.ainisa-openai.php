<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AiNisaOpenAi {

    public $apiUrl;
    public $completionsUrl;

    private $secretKey;

    private $model;

    private $maxTokens;

    private $temperature;

    private $timeout = 200;

    private $headers = [];

    public function __construct()
    {
        $this->setApiUrl();
        $this->setOpenAiParameters();

    }

    public function setApiUrl()
    {
        $config = require AINISA_SMART_REVIEWER_PATH.'config/config.php';
        $apiUrl = $config['openai']['origin'] . '/'. $config['openai']['version'];
        $this->apiUrl = $apiUrl;
        $this->completionsUrl = $apiUrl . '/chat/completions';
    }

    public function setOpenAiParameters()
    {
        $option = get_option('ainisa_smart_reviewer_options');
        $this->secretKey = $option['ainisa_openai_secret_key'] ?? '';
        $this->model = $option['ainisa_openai_model'] ?? '';
        $this->maxTokens = $option['ainisa_openai_max_tokens'] ?? '';
        $this->temperature = $option['ainisa_openai_temperature'] ?? '';
    }

    public function sendApiRequest(string $url, string $method = 'POST', array $params = [])
    {
        $this->headers['Authorization'] = 'Bearer ' . $this->secretKey;
        $this->headers['Content-Type'] = 'application/json';
        $params['model'] = $this->model;
        $params['temperature'] = $this->temperature;
        $params['max_tokens'] = $this->maxTokens;

        $post_fields = wp_json_encode($params);
        $stream = false;

        $request_args = array(
            'timeout' => $this->timeout,
            'headers' => $this->headers,
            'method' => $method,
            'body' => $post_fields,
            'stream' => $stream
        );

        $response = wp_safe_remote_post($url, $request_args);

        if (is_wp_error($response)) {
            return 'Error: ' . $response->get_error_message();
        } else {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            if( isset( $data['error'] ) ) {
                return $data['error']['message'];
            }

            $message = '';
            foreach ($data['choices'] as $choice) {
                $message .= $choice['message']['content'].'. ';
            }
            return $data['choices'][0]['message']['content'];
        }

    }
}