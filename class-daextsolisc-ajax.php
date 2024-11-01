<?php

/*
 * This class should be used to include ajax actions.
 */

class Daextsolisc_Ajax {

	protected static $instance = null;
	private $shared = null;

	private function __construct() {

		//assign an instance of the plugin info
		$this->shared = Daextsolisc_Shared::get_instance();

		//ajax requests for logged-in and not logged-in users
		add_action( 'wp_ajax_daextsolisc_get_match_data', array( $this, 'get_match_data' ) );
		add_action( 'wp_ajax_nopriv_daextsolisc_get_match_data', array( $this, 'get_match_data' ) );

	}

	/*
	 * Return an istance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Returns the match data.
	 */
	public function get_match_data() {

		//Check the referer
		if ( ! check_ajax_referer( 'daextsolisc', 'security', false ) ) {
			json_encode(__('Invalid AJAX Request', 'soccer-live-scores'));
			die();
		}

		//check if it's an array and in case sanitize its values
		if(isset($_POST['data']) and is_array($_POST['data'])){
			$match_ids = array_map(function ($a){
				return intval($a, 10);
			}, $_POST['data']);
		}else{
			$match_ids = false;
		}

		//init vars
		$result_data = [];

		//generate the match data for all the matches included in the submitted data
		foreach($match_ids as $key => $match_id){

			$data['match_id'] = $match_id;

			$data['team_1_score'] = $this->shared->get_number_of_goals($match_id, 0);
			$data['team_2_score'] = $this->shared->get_number_of_goals($match_id, 1);

			global $wpdb;
			$table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
			$safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE match_id = %d ", $match_id);
			$match_obj = $wpdb->get_row($safe_sql);

			switch(intval($match_obj->additional_score_mode, 10)) {

				case 0:
					$data['team_1_first_leg_score'] = 0;
					$data['team_2_first_leg_score'] = 0;
					break;

				case 1:
					$data['team_1_first_leg_score'] = $match_obj->team_1_first_leg_score;
					$data['team_2_first_leg_score'] = $match_obj->team_2_first_leg_score;
					break;

				case 2:
					$data['team_1_first_leg_score'] = $data['team_1_score'] + $match_obj->team_1_first_leg_score;
					$data['team_2_first_leg_score'] = $data['team_2_score'] + $match_obj->team_2_first_leg_score;
					break;

			}

			$data['score_hash'] = hash('sha512', json_encode($data['team_1_score'] . $data['team_2_score'] . $data['team_1_first_leg_score'] . $data['team_2_first_leg_score']));

			//events
			$data['events'] = [];

			global $wpdb;
			$table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
			$safe_sql        = $wpdb->prepare("SELECT * FROM $table_name WHERE match_id = %d ORDER BY minute ASC, event_id ASC", $match_id);
			$event_a = $wpdb->get_results($safe_sql, ARRAY_A);

			foreach($event_a as $key => $event){

				$event['event_icon'] = esc_url($this->shared->get_event_icon($event['event_id']));
				$event['description'] = stripslashes($event['description']);
				$event['additional_information'] = stripslashes($event['additional_information']);

				$data['events'][] = $event;

			}

			$data['events_hash'] = hash('sha512', json_encode($event_a));

			$result_data[$match_id] = $data;

		}

        echo json_encode($result_data);
		die();

	}

}