<?php

if ( ! current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'soccer-live-scores'));
}

?>

<!-- output -->

<div class="wrap">

    <h2><?php esc_html_e('Soccer Live Scores - Help', 'soccer-live-scores'); ?></h2>

    <div id="daext-menu-wrapper">

        <p><?php esc_html_e('Visit the resources below to find your answers or to ask questions directly to the plugin developers.', 'soccer-live-scores'); ?></p>
        <ul>
            <li><a href="https://daext.com/support/"><?php esc_html_e('Support Conditions', 'soccer-live-scores'); ?></li>
            <li><a href="https://daext.com/"><?php esc_html_e('Developer Website', 'soccer-live-scores'); ?></a></li>
            <li><a href="https://daext.com/soccer-engine/"><?php esc_html_e('Pro Version', 'soccer-live-scores'); ?></a></li>
            <li><a href="https://wordpress.org/plugins/soccer-live-scores/"><?php esc_html_e('WordPress.org Plugin Page', 'soccer-live-scores'); ?></a></li>
            <li><a href="https://wordpress.org/support/plugin/soccer-live-scores/"><?php esc_html_e('WordPress.org Support Forum', 'soccer-live-scores'); ?></a></li>
        </ul>

    </div>

</div>