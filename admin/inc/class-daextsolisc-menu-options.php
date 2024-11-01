<?php

/**
 * This class adds the options with the related callbacks and validations.
 */
class Daextsolisc_Menu_Options {

	private $shared = null;

	public function __construct( $shared ) {

		//assign an instance of the plugin info
		$this->shared = $shared;

	}

	public function register_options() {

		//Section General ----------------------------------------------------------------------------------------------
		add_settings_section(
			'daextsolisc_general_settings_section',
			null,
			null,
			'daextsolisc_general_options'
		);

		add_settings_field(
			'text_primary_color',
			esc_html__( 'Text Primary Color', 'soccer-live-scores' ),
			array( $this, 'text_primary_color_callback' ),
			'daextsolisc_general_options',
			'daextsolisc_general_settings_section'
		);

		register_setting(
			'daextsolisc_general_options',
			'daextsolisc_text_primary_color',
			array( $this, 'text_primary_color_validation' )
		);

		add_settings_field(
			'text_secondary_color',
			esc_html__( 'Text Secondary Color', 'soccer-live-scores' ),
			array( $this, 'text_secondary_color_callback' ),
			'daextsolisc_general_options',
			'daextsolisc_general_settings_section'
		);

		register_setting(
			'daextsolisc_general_options',
			'daextsolisc_text_secondary_color',
			array( $this, 'text_secondary_color_validation' )
		);

		add_settings_field(
			'separator_color',
			esc_html__( 'Separator Color', 'soccer-live-scores' ),
			array( $this, 'separator_color_callback' ),
			'daextsolisc_general_options',
			'daextsolisc_general_settings_section'
		);

		register_setting(
			'daextsolisc_general_options',
			'daextsolisc_separator_color',
			array( $this, 'separator_color_validation' )
		);

        add_settings_field(
            'font_family',
            esc_html__( 'Font Family', 'soccer-live-scores' ),
            array( $this, 'font_family_callback' ),
            'daextsolisc_general_options',
            'daextsolisc_general_settings_section'
        );

        register_setting(
            'daextsolisc_general_options',
            'daextsolisc_font_family',
            array( $this, 'font_family_validation' )
        );

        add_settings_field(
            'google_fonts',
            esc_html__( 'Google Fonts', 'soccer-live-scores' ),
            array( $this, 'google_fonts_callback' ),
            'daextsolisc_general_options',
            'daextsolisc_general_settings_section'
        );

        register_setting(
            'daextsolisc_general_options',
            'daextsolisc_google_fonts',
            array( $this, 'google_fonts_validation' )
        );

        add_settings_field(
            'update_time',
            esc_html__( 'Update Time', 'soccer-live-scores' ),
            array( $this, 'update_time_callback' ),
            'daextsolisc_general_options',
            'daextsolisc_general_settings_section'
        );

        register_setting(
            'daextsolisc_general_options',
            'daextsolisc_update_time',
            array( $this, 'update_time_validation' )
        );

        add_settings_field(
            'responsive_breakpoint',
            esc_html__( 'Responsive Breakpoint', 'soccer-live-scores' ),
            array( $this, 'responsive_breakpoint_callback' ),
            'daextsolisc_general_options',
            'daextsolisc_general_settings_section'
        );

        register_setting(
            'daextsolisc_general_options',
            'daextsolisc_responsive_breakpoint',
            array( $this, 'responsive_breakpoint_validation' )
        );

        add_settings_field(
            'top_margin',
            esc_html__( 'Top Margin', 'soccer-live-scores' ),
            array( $this, 'top_margin_callback' ),
            'daextsolisc_general_options',
            'daextsolisc_general_settings_section'
        );

        register_setting(
            'daextsolisc_general_options',
            'daextsolisc_top_margin',
            array( $this, 'top_margin_validation' )
        );

        add_settings_field(
            'bottom_margin',
            esc_html__( 'Bottom Margin', 'soccer-live-scores' ),
            array( $this, 'bottom_margin_callback' ),
            'daextsolisc_general_options',
            'daextsolisc_general_settings_section'
        );

        register_setting(
            'daextsolisc_general_options',
            'daextsolisc_bottom_margin',
            array( $this, 'bottom_margin_validation' )
        );

	}

	//General options callbacks and validations ------------------------------------------------------------------------
	public function text_primary_color_callback() {

		$html = '<input class="wp-color-picker" type="text" id="' . $this->shared->get( 'slug' ) . '_text_primary_color" name="' . $this->shared->get( 'slug' ) . '_text_primary_color" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_text_primary_color' ) ) . '" class="color" maxlength="7" size="6" />';
		$html .= '<div class="help-icon" title="' . esc_attr__( 'The primary text color of the layout elements generated by the plugin.',
				'soccer-live-scores' ) . '"></div>';
		echo $html;

	}

	public function text_primary_color_validation( $input ) {

		return sanitize_hex_color( $input );

	}

	public function text_secondary_color_callback() {

		$html = '<input class="wp-color-picker" type="text" id="' . $this->shared->get( 'slug' ) . '_text_secondary_color" name="' . $this->shared->get( 'slug' ) . '_text_secondary_color" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_text_secondary_color' ) ) . '" class="color" maxlength="7" size="6" />';
		$html .= '<div class="help-icon" title="' . esc_attr__( 'The secondary text color of the layout elements generated by the plugin.',
				'soccer-live-scores' ) . '"></div>';
		echo $html;

	}

	public function text_secondary_color_validation( $input ) {

		return sanitize_hex_color( $input );

	}

	public function separator_color_callback() {

		$html = '<input class="wp-color-picker" type="text" id="' . $this->shared->get( 'slug' ) . '_separator_color" name="' . $this->shared->get( 'slug' ) . '_separator_color" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_separator_color' ) ) . '" class="color" maxlength="7" size="6" />';
		$html .= '<div class="help-icon" title="' . esc_attr__( 'The color of the horizontal lines used to separate the events.',
				'soccer-live-scores' ) . '"></div>';
		echo $html;

	}

	public function separator_color_validation( $input ) {

		return sanitize_hex_color( $input );

	}

    public function font_family_callback() {

        $html = '<input type="text" id="' . $this->shared->get( 'slug' ) . '_font_family" name="' . $this->shared->get( 'slug' ) . '_font_family" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_font_family' ) ) . '" class="regular-text" maxlength="1000"/>';
        $html .= '<div class="help-icon" title="' . esc_attr__( 'The font family used in the layout elements generated by the plugin.',
                'soccer-live-scores' ) . '"></div>';
        echo $html;

    }

    public function font_family_validation( $input ) {

        $input = sanitize_text_field( $input );

        if ( ! preg_match( $this->shared->font_family_regex, $input ) ) {
            add_settings_error( 'daextsolisc_headings_font_family', 'daextsolisc_headings_font_family',
                esc_html__( 'Please enter a valid value in the "Font Family" option.', 'soccer-live-scores') );
            $output = get_option( 'daextsolisc_headings_font_family' );
        } else {
            $output = $input;
        }

        return $output;

    }

    public function google_fonts_callback() {

        $html = '<input type="text" id="' . $this->shared->get( 'slug' ) . '_google_fonts" name="' . $this->shared->get( 'slug' ) . '_google_fonts" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_google_fonts' ) ) . '" class="regular-text" maxlength="2048" />';
        $html .= '<div class="help-icon" title="' . esc_attr__( 'Load one or more Google Fonts in the front-end of your website by entering the embed code in this option.',
                'soccer-live-scores' ) . '"></div>';
        echo $html;

    }

    public function google_fonts_validation( $input ) {

        return esc_url_raw( $input );

    }

    public function update_time_callback() {

        $html = '<input type="text" id="' . $this->shared->get( 'slug' ) . '_update_time" name="' . $this->shared->get( 'slug' ) . '_update_time" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_update_time' ) ) . '" maxlength="4" size="6" />';
        $html .= '<div class="help-icon" title="' . esc_attr__( 'The time interval in seconds between the AJAX requests used to update the match score and the match events event in real time.',
                'soccer-live-scores' ) . '"></div>';
        echo $html;

    }

    public function update_time_validation( $input ) {

        return intval( $input, 10 );

    }

    public function responsive_breakpoint_callback() {

        $html = '<input type="text" id="' . $this->shared->get( 'slug' ) . '_responsive_breakpoint" name="' . $this->shared->get( 'slug' ) . '_responsive_breakpoint" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_responsive_breakpoint' ) ) . '" maxlength="4" size="6" />';
        $html .= '<div class="help-icon" title="' . esc_attr__( 'When the browser viewport width goes below the value in pixels defined with this option the mobile version of the layout elements generated by the plugin will be enabled.',
                'soccer-live-scores' ) . '"></div>';
        echo $html;

    }

    public function responsive_breakpoint_validation( $input ) {

        return intval( $input, 10 );

    }

    public function top_margin_callback() {

        $html = '<input type="text" id="' . $this->shared->get( 'slug' ) . '_top_margin" name="' . $this->shared->get( 'slug' ) . '_top_margin" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_top_margin' ) ) . '" maxlength="4" size="6" />';
        $html .= '<div class="help-icon" title="' . esc_attr__( 'The top margin of the layout elements generated by the plugin.',
                'soccer-live-scores' ) . '"></div>';
        echo $html;

    }

    public function top_margin_validation( $input ) {

        return intval( $input, 10 );

    }

    public function bottom_margin_callback() {

        $html = '<input type="text" id="' . $this->shared->get( 'slug' ) . '_bottom_margin" name="' . $this->shared->get( 'slug' ) . '_bottom_margin" value="' . esc_attr( get_option( $this->shared->get( 'slug' ) . '_bottom_margin' ) ) . '" maxlength="4" size="6" />';
        $html .= '<div class="help-icon" title="' . esc_attr__( 'The bottom margin of the layout elements generated by the plugin.',
                'soccer-live-scores' ) . '"></div>';
        echo $html;

    }

    public function bottom_margin_validation( $input ) {

        return intval( $input, 10 );

    }

}