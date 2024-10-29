<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()) ?></h1>
    <h2 class="nav-tab-wrapper">
        <?php
        $active_tab = isset( $_GET['tab'] ) ? esc_attr(sanitize_text_field(wp_unslash($_GET['tab']))) : esc_attr__('information', 'ainisa-smart-reviewer');
        ?>
        <a href="?page=ainisa-smart-reviewer-admin&tab=information" class="nav-tab <?php echo $active_tab === 'information' ? 'nav-tab-active' : '' ?>"><?php esc_html_e( 'Information', 'ainisa-smart-reviewer' ); ?></a>
        <a href="?page=ainisa-smart-reviewer-admin&tab=settings" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : '' ?>"><?php esc_html_e( 'Settings', 'ainisa-smart-reviewer' ); ?></a>
    </h2>
    <form action="options.php" method="POST">
        <input type="hidden" name="ainisa_nonce" value="<?php echo wp_unslash(wp_create_nonce( 'ainisa_nonce' )); ?>">
        <?php
        if( $active_tab === 'information' ) {
            settings_fields('ainisa_smart_reviewer_group');
            do_settings_sections('ainisa_smart_reviewer_page1');
        } else {
            settings_fields('ainisa_smart_reviewer_group');
            do_settings_sections('ainisa_smart_reviewer_page2');

            submit_button( esc_html__( 'Save', 'ainisa-smart-reviewer' ) );
        }

        ?>
    </form>
</div>