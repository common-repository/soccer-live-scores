<?php

/*
 * This class should be used to work with the public side of wordpress.
 */

class Daextsolisc_Public {

	protected static $instance = null;
	private $shared = null;

	private function __construct() {

		//Assign an instance of the plugin shared class
		$this->shared = Daextsolisc_Shared::get_instance();

        //Load public css and js
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'en_public_scripts' ) );

		//[soccer-live-scores] shortcode
        add_shortcode('soccer-live-scores', array($this, 'display_soccer_live_scores'));

	}

	/*
	 * Creates an instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

    /*
     * enqueue public-specific style sheets
     */
    public function enqueue_styles() {

        //if is set load a google font
        if( strlen( trim( get_option( $this->shared->get("slug") . "_google_fonts" ) ) )  > 0 ){

            wp_enqueue_style( $this->shared->get( 'slug' ) . '-google-fonts',
                esc_url( get_option( $this->shared->get( 'slug' ) . '_google_fonts' ) ), false );

        }

        //Enqueue the main public CSS file
        wp_enqueue_style( $this->shared->get('slug') . '-general', $this->shared->get('url') . 'public/assets/css/general.css', array(), $this->shared->get('ver') );

        //Enqueue the main public CSS file
        wp_enqueue_style( $this->shared->get('slug') .'-custom', $this->shared->get('url') . 'public/assets/css/custom-' . get_current_blog_id() . '.css', array(), $this->shared->get('ver') );

    }

    /*
     * enqueue public-specific javascript
     */
    public function en_public_scripts() {

        //Enqueue the main public JavaScript file
        wp_enqueue_script( $this->shared->get('slug') . '-general', $this->shared->get('url') . 'public/assets/js/general.js', array( 'jquery' ), $this->shared->get('ver') );

        //Add parameters before the script
        $parameters_script = 'window.DAEXTSOLISC_PARAMETERS = {';
        $parameters_script .= "nonce: '" . wp_create_nonce('daextsolisc') . "',";
        $parameters_script .= "ajaxUrl: '" . admin_url('admin-ajax.php') . "',";
	    $parameters_script .= "updateTime: '" . intval( get_option( $this->shared->get("slug") . "_update_time" ), 10 ) . "'";
        $parameters_script .= '};';
        wp_add_inline_script( $this->shared->get('slug') . '-general', $parameters_script, 'before' );

    }

	/**
	 * Generates the output of the [soccer-live-scores] shortcode.
	 *
	 * @param $atts
	 *
	 * @return string
	 */
    public function display_soccer_live_scores($atts)
    {

        /*
         * Parse the shortcode only inside the full content of posts and pages if the "Limit Shortcode Parsing" option
         * is enabled. Do not parse the shortcode inside feeds.
         */
        if (!is_feed() and ((is_single() or is_page()) or intval(get_option($this->shared->get('slug') . '_limit_shortcode_parsing'), 10) == 0)) {

            //get the table id
            if (isset($atts['id'])) {
                $match_id = intval($atts['id'], 10);
            } else {
                return '<p>' . esc_html__('Please enter the identifier of the match.', 'soccer-live-scores') . '</p>';
            }

            global $wpdb;

            //get the match_obj that contains a single row with the match data
            $table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
            $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE match_id = %d ", $match_id);
            $match_obj = $wpdb->get_row($safe_sql);

            //get the events_a array that containes all the events related to this match
            $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
            $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE match_id = %d ORDER BY minute ASC, event_id ASC ", $match_id);
            $events_a = $wpdb->get_results($safe_sql, ARRAY_A);

            //if the $match_obj or the $events_a return NULL do not proceed and output a message
	        if ( is_null( $match_obj ) or is_null( $events_a ) ) {
		        return "<p><strong>" . esc_html__( 'Invalid shortcode.', 'soccer-live-score' ) . "</strong></p>";
	        }

            //store the output in the $content variable
	        $events_hash = hash('sha512', json_encode($events_a));
            $content = '<div class="daextsolisc-container" data-match-id="' . esc_attr($match_id) . '" data-live="' . esc_attr($match_obj->live) . '">';

            //HEAD -----------------------------------------------------------------------------------------------------

	        //show the team_1 icon only if the url length is > than 0
	        if(strlen( esc_attr(stripslashes($match_obj->team_1_logo)) ) > 0){
		        $team_1_logo_exists = true;
		        $team_1_logo_container_class = '';
	        }else{
		        $team_1_logo_exists = false;
		        $team_1_logo_container_class = ' daextsolisc-no-logo';
	        }

            $content .= '<div class="daextsolisc-head' . esc_attr($team_1_logo_container_class) . '">';
	        $content .= '<div class="daextsolisc-head-left' . esc_attr($team_1_logo_container_class) . '">';

	        $content .= '<div class="daextsolisc-team-1-logo">';
	        if( $team_1_logo_exists ){
		        $content .= '<img src="' . esc_attr(stripslashes($match_obj->team_1_logo)) . '">';
	        }
	        $content .= '</div>';

	        $content .= '<div class="daextsolisc-team-1-name">' . esc_html(stripslashes($match_obj->team_1_name)) . '</div>';
	        $content .= '</div>';//#daextsolisc-head-left

	        //Calculate the score
	        $team_1_score = $this->shared->get_number_of_goals($match_id, 0);
	        $team_2_score = $this->shared->get_number_of_goals($match_id, 1);
	        if(intval($match_obj->additional_score_mode, 10) === 1 or
	           intval($match_obj->additional_score_mode, 10) === 2) {

	        	$head_center_class = ' daextsolisc-head-center-with-additional';

		        if ( intval( $match_obj->additional_score_mode, 10 ) === 2 ) {
			        $team_1_first_left_score = $team_1_score + $match_obj->team_1_first_leg_score;
			        $team_2_first_left_score = $team_2_score + $match_obj->team_2_first_leg_score;
		        } else {
			        $team_1_first_left_score = $match_obj->team_1_first_leg_score;
			        $team_2_first_left_score = $match_obj->team_2_first_leg_score;
		        }

	        }else {

		        $head_center_class = '';
		        $team_1_first_left_score = 0;
		        $team_2_first_left_score = 0;

	        }

	        $score_hash = hash('sha512', json_encode($team_1_score . $team_2_score . $team_1_first_left_score . $team_2_first_left_score));
	        $content .= '<div class="daextsolisc-head-center' . esc_attr($head_center_class) . '" data-hash="' . esc_attr($score_hash) . '">';
	        $content .= '<div class="daextsolisc-score"><div class="daextsolisc-team-1-score">' . esc_html($team_1_score) . '</div> - <div class="daextsolisc-team-2-score">' . esc_html($team_2_score) . '</div></div>';
	        $content .= '<div class="daextsolisc-additional-score"><div class="daextsolisc-team-1-first-leg-score">(' . esc_html($team_1_first_left_score) . '</div> - <div class="daextsolisc-team-2-first-leg-score">' . esc_html($team_2_first_left_score) . ')</div></div>';
	        $content .= '</div>';//#daextsolisc-head-center

	        //show the team_2 icon only if the url length is > than 0
	        if(strlen( esc_attr(stripslashes($match_obj->team_2_logo)) ) > 0){
		        $team_2_logo_exists = true;
		        $team_1_logo_container_class = '';
	        }else{
		        $team_2_logo_exists = false;
		        $team_1_logo_container_class = ' daextsolisc-no-logo';
	        }

	        $content .= '<div class="daextsolisc-head-right' . esc_attr($team_1_logo_container_class) . '">';

            $content .= '<div class="daextsolisc-team-2-logo">';
	        if( $team_2_logo_exists ){
                $content .= '<img src="' . esc_attr(stripslashes($match_obj->team_2_logo)) . '">';
            }
            $content .= '</div>';

            $content .= '<div class="daextsolisc-team-2-name">' . esc_attr(stripslashes($match_obj->team_2_name)) . '</div>';
	        $content .= '</div>';//.daextsolisc-head-right

            $content .= '</div>';//.daextsolisc-head

            //BODY -----------------------------------------------------------------------------------------------------

            $content .= '<div class="daextsolisc-body" data-hash="' . esc_attr($events_hash) . '">';

            foreach($events_a as $key => $result){

                $even_or_odd = ($key % 2 == 0) ? "odd" : "even";

	            if( $result['team'] == 0 ){
		            $row_side_class = 'daextsolisc-row-left';
	            }else{
		            $row_side_class = 'daextsolisc-row-right';
	            }

                $content .= '<div class="daextsolisc-row daextsolisc-row-' . esc_attr($even_or_odd) . ' ' . esc_attr($row_side_class) . '">';

	            $content .= '<div class="daextsolisc-event-type daextsolisc-event-icon"><img src="' . esc_url($this->shared->get_event_icon($result['event_id'])) . '"></div>';

	            $content .= '<div class="daextsolisc-minute">' . esc_attr(stripslashes($result['minute'])) .'\'</div>';

	            if((strlen(trim($result['additional_information'])) > 0)){
	            	$additional_information = ' <span class="daextsolisc-additional-information">' . esc_html(stripslashes($result['additional_information'])) . '</span>';
	            }else{
	            	$additional_information = '';
	            }

	            $content .= '<div class="daextsolisc-event-description">' . esc_html(stripslashes($result['description'])) . $additional_information . '</div>';

                $content .= '</div>';//.daextsolisc-row

            }

            $content .= '</div>';//.daextsolisc-body

            $content .= '</div>';//.daextsolisc-container

            return $content;

        }

    }

}