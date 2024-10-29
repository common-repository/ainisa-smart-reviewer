<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('AiNisaSmartReviewerSettings') ) {
    /**
     * Class for Ainisa smart reviewer options/settings
     * This class helps to create sections with fields for Ainisa smart reviewer options
     * And makes validations during post request
     */
    class AiNisaSmartReviewerSettings {
        /**
         * Ai smart reviewer options
         * @var false|mixed|null
         */
        public static $options;

        /**
         * Chatgpt models
         * @var array
         */
        public static $models;

        /**
         * Default prompt text
         * @var mixed|string
         */
        public static $defaultPrompt = '';

        /**
         * Chatgpt models by groups
         * @var mixed
         */
        public static $modelsWithGroups;

        public function __construct()
        {
            $config = require AINISA_SMART_REVIEWER_PATH.'config/config.php';
            self::$options = get_option('ainisa_smart_reviewer_options');
            self::$defaultPrompt = $config['prompt_parts']['default_prompt'];

            self::$modelsWithGroups = $config['openai']['model_with_groups'];
            self::$models = [];
            foreach (self::$modelsWithGroups as $k => $models) {
                foreach ($models as $m) {
                    self::$models[] = $m;
                }
            }
            add_action('admin_init', [$this, 'adminInit']);
        }

        /**
         * Initialize settings with fields
         * @return void
         */
        public function adminInit()
        {
            register_setting(
                'ainisa_smart_reviewer_group',
                'ainisa_smart_reviewer_options',
                [$this, 'aiNisaOpenAiSettingsValidation']
            );

            ### Add section about plugin
            add_settings_section(
                'ainisa_smart_reviewer_main_section',
                esc_html__( 'How does it work', 'ainisa-smart-reviewer' ),
                [$this, 'AiNisaSmartReviewerInformationCallback'],
                'ainisa_smart_reviewer_page1'
            );

            ### End adding section and fields for openai info

            ### Add section for openai settings
            add_settings_section(
                'ainisa_smart_reviewer_settings_section',
                esc_html__( 'Settings', 'ainisa-smart-reviewer' ),
                null,
                'ainisa_smart_reviewer_page2'
            );

            ## Add section fields for openai settings
            add_settings_field(
                'ainisa_openai_temperature',
                esc_html__( 'Temperature', 'ainisa-smart-reviewer' ),
                [$this, 'AiNisaSmartReviewerOpenAiTemperatureCallback'],
                'ainisa_smart_reviewer_page2',
                'ainisa_smart_reviewer_settings_section'
            );

            add_settings_field(
                'ainisa_openai_max_tokens',
                esc_html__( 'Maximum tokens', 'ainisa-smart-reviewer' ),
                [$this, 'AiNisaSmartReviewerOpenAiMaxTokensCallback'],
                'ainisa_smart_reviewer_page2',
                'ainisa_smart_reviewer_settings_section'
            );

            add_settings_field(
                'ainisa_openai_secret_key',
                esc_html__( 'Open Ai Secret Key', 'ainisa-smart-reviewer' ),
                [$this, 'AiNisaSmartReviewerOpenAiSecretKeyCallback'],
                'ainisa_smart_reviewer_page2',
                'ainisa_smart_reviewer_settings_section',
                [
                    'label_for' => 'ainisa_openai_secret_key'
                ]
            );

            add_settings_field(
                'ainisa_openai_model',
                esc_html__( 'Ai Model', 'ainisa-smart-reviewer' ),
                [$this, 'AiNisaSmartReviewerOpenAiModelCallback'],
                'ainisa_smart_reviewer_page2',
                'ainisa_smart_reviewer_settings_section',
                [
                    'models' => self::$models,
                    'models_with_groups' => self::$modelsWithGroups,
                    'label_for' => 'ainisa_openai_model'
                ]
            );
            add_settings_field(
                'ainisa_openai_default_prompt',
                esc_html__( 'Ai default prompt', 'ainisa-smart-reviewer' ),
                [$this, 'AiNisaSmartReviewerOpenAiDefaultPromptCallback'],
                'ainisa_smart_reviewer_page2',
                'ainisa_smart_reviewer_settings_section',
                [
                    'label_for' => 'ainisa_openai_default_prompt',
                    'default' => self::$defaultPrompt
                ]
            );
            ### End adding section and fields for openai settings
            
        }

        public function AiNisaSmartReviewerInformationCallback()
        {
            ?>
            <article>
                <strong><?php echo wp_kses_post(__('This plugin is powered by <a href="https://ainisa.com" target="_blank">Ainisa</a>', 'ainisa-smart-reviewer')); ?>. <br></strong>
                <?php echo esc_html(__('It helps you to create reviews and comments like human', 'ainisa-smart-reviewer')); ?>.
                <?php echo esc_html(__('Please, go to link to read how to use the plugin', 'ainisa-smart-reviewer')); ?>: <a href="https://ainisa.com/en/wordpress-plugins/1-smart-reviewer" target="_blank"><?php echo esc_html(__('Click here', 'ainisa-smart-reviewer')); ?></a>

            </article>
            <?php
        }

        public function AiNisaSmartReviewerOpenAiMaxTokensCallback()
        {
            ?>
            <input type="number"
                   name="ainisa_smart_reviewer_options[ainisa_openai_max_tokens]"
                   max="4096"
                   id="ainisa_openai_max_tokens"
                   value="<?php echo isset(self::$options['ainisa_openai_max_tokens']) ? esc_attr(self::$options['ainisa_openai_max_tokens']) : 1500 ?>"
            >
            <?php
        }

        public function AiNisaSmartReviewerOpenAiTemperatureCallback()
        {
            ?>
            <input type="number"
                   name="ainisa_smart_reviewer_options[ainisa_openai_temperature]"
                   max="2"
                   step="0.1"
                   id="ainisa_openai_temperature"
                   value="<?php echo isset(self::$options['ainisa_openai_temperature']) ? esc_attr(self::$options['ainisa_openai_temperature']) : esc_attr(0.7) ?>"
            >
            <?php
        }

        public function AiNisaSmartReviewerOpenAiSecretKeyCallback()
        {
            ?>
            <input type="text"
                   name="ainisa_smart_reviewer_options[ainisa_openai_secret_key]"
                   id="ainisa_openai_secret_key"
                   value="<?php echo isset(self::$options['ainisa_openai_secret_key']) ? esc_attr(self::$options['ainisa_openai_secret_key']) : '' ?>"
            >
            <?php
        }

        public function AiNisaSmartReviewerOpenAiModelCallback($args)
        {
            $modelGroups = $args['models_with_groups'];
            $modelsArray = $args['models'];
            ?>
            <select name="ainisa_smart_reviewer_options[ainisa_openai_model]" id="ainisa_openai_model">
                <?php foreach ($modelGroups as $k => $models): ?>
                    <optgroup label="<?php echo esc_html($k); ?>">
                        <?php foreach ($models as $model): ?>
                        <option value="<?php echo esc_attr($model) ?>" <?php isset(self::$options['ainisa_openai_model']) ? selected($model, self::$options['ainisa_openai_model']) : selected($modelsArray[0], true); ?> >
                            <?php echo esc_html($model) ?>
                        </option>
                        <?php endforeach; ?>
                    </optgroup>

                <?php endforeach; ?>
            </select>
            <?php
        }

        public function AiNisaSmartReviewerOpenAiDefaultPromptCallback($args)
        {
            ?>
                <textarea rows="5" id="ainisa_openai_default_prompt" name="ainisa_smart_reviewer_options[ainisa_openai_default_prompt]"><?php echo !empty(self::$options['ainisa_openai_default_prompt']) ? esc_attr(self::$options['ainisa_openai_default_prompt']) : esc_attr($args['default']) ?></textarea>
            <?php
        }

        /**
         * Do validation for Ainisa smart reviewer options
         * @param $input
         * @return array
         */
        public function aiNisaOpenAiSettingsValidation($input)
        {
            $new_input = [];
            $domain = 'ainisa-smart-reviewer';
            $namings = require AINISA_SMART_REVIEWER_PATH.'config/namings.php';
            $ainisa_save_reviewer_options_namings = $namings['ainisa_smart_reviewer_options'];
            require_once AINISA_SMART_REVIEWER_PATH.'includes/class.ainisa-validator.php';
            $aiNisaValidator = new AiNisaValidator($input, $domain, $ainisa_save_reviewer_options_namings);

            if( !isset( $_POST['ainisa_nonce'] ) ) {
                add_settings_error( 'ainisa_smart_reviewer_options', 'ainisa_smart_reviewer_option_message', esc_html( 'No nonce !' ), 'error' );
            }

            if( !empty( sanitize_text_field(wp_unslash($_POST['ainisa_nonce'])) ) ) {
                if( !wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['ainisa_nonce'])), 'ainisa_nonce' ) ) {
                    add_settings_error( 'ainisa_smart_reviewer_options', 'ainisa_smart_reviewer_option_message', esc_html( 'Wrong nonce !' ), 'error' );
                }
            }

            $rules = [
                'ainisa_openai_secret_key' => ['required'],
                'ainisa_openai_model' => ['required'],
                'ainisa_openai_temperature' => ['required', 'between_numbers:0,2'],
                'ainisa_openai_max_tokens' => ['required', 'between_numbers:0,4096'],
                'ainisa_openai_default_prompt' => ['required'],
            ];

            $aiNisaValidator->validate($rules);
            if($aiNisaValidator->hasErrors()) {
                $aiNisaValidator->addToWpSettingsError('ainisa_smart_reviewer_options', 'ainisa_smart_reviewer_message');
            }

            $new_input['ainisa_openai_secret_key'] = sanitize_text_field($input['ainisa_openai_secret_key']);
            $new_input['ainisa_openai_model'] = sanitize_text_field($input['ainisa_openai_model']);
            $new_input['ainisa_openai_default_prompt'] = sanitize_text_field($input['ainisa_openai_default_prompt']);
            $new_input['ainisa_openai_temperature'] = floatval($input['ainisa_openai_temperature']);
            $new_input['ainisa_openai_max_tokens'] = intval($input['ainisa_openai_max_tokens']);

            return $new_input;
        }
    }
}
