<?php

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have sufficient capabilities to access this page.', 'soccer-live-scores' ) );
}

//generate the custom.css file based on the current options
if( isset($_GET['settings-updated']) and $_GET['settings-updated'] == 'true' ){
    if( $this->write_custom_css() === false ){
        ?>
        <div id="setting-error-settings_updated" class="error settings-error notice is-dismissible below-h2">
            <p><strong><?php esc_html_e('The', 'soccer-live-scores'); ?> <?php echo $this->shared->get('dir') . 'public/assets/css/custom.css'; ?> <?php esc_html_e('file should be writable by the server. Please change its permissions and try again.', 'soccer-live-scores'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'soccer-live-scores'); ?></span></button>
        </div>
        <?php
    }else{
        settings_errors();
    }
}

?>

<div class="wrap">

    <h2><?php esc_attr_e( 'Soccer Live Scores - Options', 'soccer-live-scores' ); ?></h2>

    <div id="daext-options-wrapper">

		<?php
		//get current tab value
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general_options';
		?>

        <div class="nav-tab-wrapper">
            <a href="?page=daextsolisc-options&tab=general_options"
               class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General',
					'soccer-live-scores' ); ?></a>
        </div>

        <form method="post" action="options.php" autocomplete="off">

			<?php

			if ( $active_tab == 'general_options' ) {

				settings_fields( $this->shared->get( 'slug' ) . '_general_options' );
				do_settings_sections( $this->shared->get( 'slug' ) . '_general_options' );

			}

			?>

            <div class="daext-options-action">
                <input type="submit" name="submit" id="submit" class="button"
                       value="<?php esc_attr_e( 'Save Changes', 'soccer-live-scores' ); ?>">
            </div>

        </form>

    </div>

</div>

