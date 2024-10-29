<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap ainisa-root">
    <div class="ainisa-paper ainisa-m-20">
        <div class="ainisa-p-8 ainisa-border-b ainisa-border-slate-200">
            <h1 class="ainisa-title ainisa-title--1"><?php echo esc_html(get_admin_page_title()) ?></h1>
            <p class="ainisa-text-lg ainisa-mt-3"><?php esc_html_e( 'Find posts to select', 'ainisa-smart-reviewer' ); ?></p>
        </div>
        <form action="#" class="ainisa-flex ainisa-flex-col ainisa-h-full ainisa_get_posts_form" method="POST">
            <div class="ainisa-flex-grow ainisa-p-8">
                <div class="ainisa-grid ainisa-gap-6 ainisa-grid-cols-1 sm:ainisa-grid-cols-2 min-[783px]:ainisa-grid-cols-1 lg:ainisa-grid-cols-2 2xl:ainisa-grid-cols-3 min-[1800px]:ainisa-grid-cols-4">
                    <div class="ainisa-text-field">
                        <div class="ainisa-flex ainisa-items-center ainisa-mb-2">
                            <label for="ainisa_get_posts_type" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'Select post type', 'ainisa-smart-reviewer' ); ?></label>
                        </div>
                        <div class="ainisa-validation-input">
                            <select id="ainisa_get_posts_type" name="ainisa_get_posts_type" class="ainisa-text-input ainisa-validation-input__input ainisa-text-field__input ainisa-max-w-full">
                                <?php
                                foreach ($post_types as $post_type):
                                    ?>
                                    <option value="<?php echo esc_html($post_type) ?>"><?php echo esc_html(ucfirst($post_type)) ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="ainisa-text-field">
                        <div class="ainisa-flex ainisa-items-center ainisa-mb-2">
                            <label for="ainisa_get_posts_numbers" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'Number of posts', 'ainisa-smart-reviewer' ); ?></label>
                        </div>
                        <div class="ainisa-validation-input">
                            <input type="number" max="300" value="100" id="ainisa_get_posts_numbers" name="ainisa_get_posts_numbers" spellcheck="false" autocapitalize="off" class="ainisa-text-input ainisa-validation-input__input ainisa-text-field__input ainisa-max-w-full">
                        </div>
                    </div>
                    <div class="ainisa-text-field">
                        <div class="ainisa-flex ainisa-items-center ainisa-mb-2">
                            <label for="ainisa_get_posts_comments" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'Posts which have comments less than', 'ainisa-smart-reviewer' ); ?></label>
                        </div>
                        <div class="ainisa-validation-input">
                            <input type="number" id="ainisa_get_posts_comments" value="5" name="ainisa_get_posts_comments" spellcheck="false" autocapitalize="off" class="ainisa-text-input ainisa-validation-input__input ainisa-text-field__input ainisa-max-w-full">
                        </div>
                    </div>
                </div>
                <div class="ainisa-py-4">
                    <button id="ainisa_get_posts_get" class="ainisa-button ainisa-button--primary"><i class="fa fa-spinner ainisa_hidden ainisa_hidden_icon"></i>&nbsp; <?php echo esc_html__( 'Get posts', 'ainisa-smart-reviewer' ); ?></button>
                </div>
            </div>
        </form>
        <div class="ainisa-px-8 ainisa-pb-8 ainisa-border-b ainisa-border-slate-200">
            <p class="ainisa-text-lg ainisa-mt-3"><?php esc_html_e( 'Select your post, create your review and save it', 'ainisa-smart-reviewer' ); ?></p>
        </div>
        <form action="#" class="ainisa_review_form" method="POST">
            <div class="ainisa-flex-grow ainisa-p-8">
                <div class="row">
                    <div class="col-md-12">
                        <div class="lg:ainisa-mt-0 lg:ainisa-col-span-2 ainisa-space-y-8">
                            <div class="ainisa-grid ainisa-gap-6 ainisa-grid-cols-1 sm:ainisa-grid-cols-2 min-[783px]:ainisa-grid-cols-2 lg:ainisa-grid-cols-3 2xl:ainisa-grid-cols-3 min-[1800px]:ainisa-grid-cols-4">
                                <div class="ainisa-text-field">
                                    <div class="ainisa-flex ainisa-items-center ainisa-mb-2">
                                        <label for="ainisa_smart_reviewer_email" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'User e-mail', 'ainisa-smart-reviewer' ); ?></label>
                                    </div>
                                    <div class="ainisa-validation-input">
                                        <input type="text" id="ainisa_smart_reviewer_email" name="ainisa_smart_reviewer_email" spellcheck="false" autocapitalize="off" class="ainisa-text-input ainisa-validation-input__input ainisa-text-field__input ainisa-max-w-full">
                                    </div>
                                </div>
                                <div class="ainisa-text-field">
                                    <div class="ainisa-flex ainisa-items-center ainisa-mb-2">
                                        <label for="ainisa_smart_reviewer_firstname" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'User firstname', 'ainisa-smart-reviewer' ); ?></label>
                                    </div>
                                    <div class="ainisa-validation-input">
                                        <input type="text" id="ainisa_smart_reviewer_firstname" name="ainisa_smart_reviewer_firstname" spellcheck="false" autocapitalize="off" class="ainisa-text-input ainisa-validation-input__input ainisa-text-field__input ainisa-max-w-full">
                                    </div>
                                </div>
                                <div class="ainisa-text-field">
                                    <div class="ainisa-flex ainisa-items-center ainisa-mb-2">
                                        <label for="ainisa_smart_reviewer_lastname" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'User lastname', 'ainisa-smart-reviewer' ); ?></label>
                                    </div>
                                    <div class="ainisa-validation-input">
                                        <input type="text" id="ainisa_smart_reviewer_lastname" name="ainisa_smart_reviewer_lastname" spellcheck="false" autocapitalize="off" class="ainisa-text-input ainisa-validation-input__input ainisa-text-field__input ainisa-max-w-full">
                                    </div>
                                </div>
                            </div>

                            <div class="ainisa-grid ainisa-gap-6 ainisa-grid-cols-1 sm:ainisa-grid-cols-2 min-[783px]:ainisa-grid-cols-1 lg:ainisa-grid-cols-2 2xl:ainisa-grid-cols-2 min-[1800px]:ainisa-grid-cols-2">
                                <div class="ainisa-text-field">
                                    <div class="ainisa-flex ainisa-items-center ainisa-mb-2">
                                        <label for="ainisa_smart_reviewer_post_id" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'Select post', 'ainisa-smart-reviewer' ); ?></label>
                                    </div>
                                    <div class="ainisa-validation-input">
                                        <select id="ainisa_smart_reviewer_post_id" name="ainisa_smart_reviewer_post_id" class="ainisa-text-input ainisa-validation-input__input ainisa-text-field__input ainisa-max-w-full">
                                            <option value="0"><?php esc_html_e( 'Select post', 'ainisa-smart-reviewer' ); ?></option>
                                            <?php
                                            if ($wpdb->num_rows > 0):
                                                foreach ($posts as $post):
                                                    ?>
                                                    <option value="<?php echo intval($post['ID']) ?>"><?php echo esc_html($post['post_title']); ?></option>
                                                <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="ainisa-text-field">
                                    <div class="ainisa-flex ainisa-items-center ainisa-mb-2">
                                        <label for="ainisa_smart_reviewer_title" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'Review title', 'ainisa-smart-reviewer' ); ?></label>
                                    </div>
                                    <div class="ainisa-validation-input">
                                        <input type="text" id="ainisa_smart_reviewer_title" name="ainisa_smart_reviewer_title" spellcheck="false" autocapitalize="off" class="ainisa-text-input ainisa-validation-input__input ainisa-text-field__input ainisa-max-w-full">
                                    </div>
                                    <p class="ainisa-text-field__description"><?php esc_html_e( 'Will be created by AI', 'ainisa-smart-reviewer' ); ?></p>
                                </div>
                            </div>

                            <div class="ainisa-grid ainisa-gap-6 ainisa-grid-cols-1 sm:ainisa-grid-cols-2 min-[783px]:ainisa-grid-cols-1 lg:ainisa-grid-cols-2 2xl:ainisa-grid-cols-2 min-[1800px]:ainisa-grid-cols-2">
                                <div class="ainisa-items-center">
                                    <label for="ainisa_smart_review_prompt" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'Review prompt', 'ainisa-smart-reviewer' ); ?></label>
                                    <textarea rows="5" id="ainisa_smart_review_prompt" name="ainisa_smart_review_prompt" class="ainisa-textarea ainisa-validation-input__input ainisa-textarea-field__input ainisa-mt-2"><?php echo isset($ai_smart_reviewer_options['ainisa_openai_default_prompt']) ? esc_attr(trim($ai_smart_reviewer_options['ainisa_openai_default_prompt'])) : '' ?></textarea>
                                </div>
                                <div class="ainisa-items-center">
                                    <label for="ainisa_smart_reviewer_review" class="ainisa-label ainisa-text-field__label"><?php esc_html_e( 'Review', 'ainisa-smart-reviewer' ); ?></label>
                                    <textarea rows="5" id="ainisa_smart_reviewer_review" name="ainisa_smart_reviewer_review" class="ainisa-textarea ainisa-validation-input__input ainisa-textarea-field__input ainisa-mt-2"></textarea>
                                    <p class="ainisa-text-field__description"><?php esc_html_e( 'Will be created by AI', 'ainisa-smart-reviewer' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="ainisa-py-4 ainisa-flex ainisa-gap-x-4">
                            <button id="ainisa_get_gpt_review" class="ainisa-button ainisa-button--primary"><i class="fa fa-spinner ainisa_hidden ainisa_hidden_icon"></i>&nbsp; <?php echo esc_html__( 'Get AI review', 'ainisa-smart-reviewer' ); ?></button>
                            <button id="ainisa_save_review" class="ainisa-button ainisa-button--primary"><i class="fa fa-spinner ainisa_hidden ainisa_hidden_icon"></i>&nbsp; <?php echo esc_html__( 'Save review', 'ainisa-smart-reviewer' ); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>