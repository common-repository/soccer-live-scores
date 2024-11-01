<?php

/*
 * this class should be used to stores properties and methods shared by the
 * admin and public side of wordpress
 */

class Daextsolisc_Shared {

	//regex
	public $font_family_regex = '/^([A-Za-z0-9-\'", ]*)$/';

	protected static $instance = null;

	private $data = array();

	private function __construct() {

		//Set plugin textdomain
		load_plugin_textdomain( 'soccer-live-scores', false, 'soccer-live-scores/lang/' );

		$this->data['slug'] = 'daextsolisc';
		$this->data['ver']  = '1.05';
		$this->data['dir']  = substr( plugin_dir_path( __FILE__ ), 0, - 7 );
		$this->data['url']  = substr( plugin_dir_url( __FILE__ ), 0, - 7 );

        //Here are stored the plugin option with the related default values
        $this->data['options'] = [

            //Database Version -----------------------------------------------------------------------------------------
            $this->get('slug') . "_database_version" => "0",

            //General --------------------------------------------------------------------------------------------------
            $this->get('slug') . '_database_version' => "0",
            $this->get('slug') . '_text_primary_color' => "#424242",
            $this->get('slug') . '_text_secondary_color' => "#828282",
            $this->get('slug') . '_separator_color' => "#f2f2f2",
            $this->get('slug') . '_font_family' => "'Roboto', sans-serif",
            $this->get('slug') . '_google_fonts' => "https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap",
            $this->get('slug') . '_update_time' => "10",
            $this->get('slug') . '_responsive_breakpoint' => "768",
            $this->get('slug') . '_top_margin' => "20",
            $this->get('slug') . '_bottom_margin' => "20",
        ];

	}

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	//retrieve data
	public function get( $index ) {
		return $this->data[ $index ];
	}

	/**
     * Given the match id and the team (team 1 or team 2) this function returns the number of goals scored by this team.
     *
	 * @param $match_id
	 * @param $team
	 *
	 * @return int
	 */
    public function get_number_of_goals($match_id, $team){

	    $goals = 0;

        global $wpdb;
        $table_name = $wpdb->prefix . $this->get('slug') . "_event";
        $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE match_id = %d", $match_id);
        $event_a = $wpdb->get_results($safe_sql, ARRAY_A);

        foreach($event_a as $key => $event){

            if(intval($event['team'], 10) === $team){

                //get event type of the event
                $table_name = $wpdb->prefix . $this->get('slug') . "_event_type";
                $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE event_type_id = %d", $event['event_type_id']);
                $event_type_obj = $wpdb->get_row($safe_sql);

                $goals = $goals + $event_type_obj->goal;

            }

        }

        return $goals;

    }

	/**
     * Get the name of the team based on the provided $match_id and $team_slot
     *
	 * @param $match_id
	 * @param $team_slot
	 *
	 * @return mixed
	 */
    public function get_team_name($match_id, $team_slot){

	    global $wpdb;
        $table_name = $wpdb->prefix . $this->get('slug') . "_match";
        $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE match_id = %d", $match_id);
        $match_obj = $wpdb->get_row($safe_sql);

        return stripslashes($match_obj->{'team_' . ($team_slot+1) . '_name'});

    }

	/**
	 * Get the name of a match based on the provided $match_id.
	 *
	 * @param $event_type_id
	 *
	 * @return mixed
	 */
	public function get_match_name($match_id){

		global $wpdb;
		$table_name = $wpdb->prefix . $this->get('slug') . "_match";
		$safe_sql = $wpdb->prepare("SELECT name FROM $table_name WHERE match_id = %d", $match_id);
		$match_id = $wpdb->get_row($safe_sql);

		return stripslashes($match_id->name);

	}

	/**
     * Get the name of the event type based on the provided $event_type_id.
     *
	 * @param $event_type_id
	 *
	 * @return mixed
	 */
    public function get_event_type_name($event_type_id){

        global $wpdb;
        $table_name = $wpdb->prefix . $this->get('slug') . "_event_type";
        $safe_sql = $wpdb->prepare("SELECT name FROM $table_name WHERE event_type_id = %d", $event_type_id);
        $event_type_obj = $wpdb->get_row($safe_sql);

        return stripslashes($event_type_obj->name);

    }

	/**
     * Get the icon associated with the event based on the provided $event_id.
     *
	 * @param $event_id
	 *
	 * @return mixed
	 */
    public function get_event_icon($event_id){

        //get the event
        global $wpdb;
        $table_name = $wpdb->prefix . $this->get('slug') . "_event";
        $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE event_id = %d ", $event_id);
        $event_obj = $wpdb->get_row($safe_sql);

        //get the event type
        $table_name = $wpdb->prefix . $this->get('slug') . "_event_type";
        $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE event_type_id = %d ", $event_obj->event_type_id);
        $event_type_obj = $wpdb->get_row($safe_sql);

        //return the icon from the event type
        return $event_type_obj->icon;

    }

	/**
	 * Returns the total number of event types.
	 *
	 * @return int
	 */
    public function get_number_of_event_types(){

	    global $wpdb;
	    $table_name  = $wpdb->prefix . $this->get('slug') . "_event_type";
	    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

	    return intval($total_items, 10);

    }

	/**
	 * Returns the total number of matches.
	 *
	 * @return int
	 */
	public function get_number_of_matches(){

		global $wpdb;
		$table_name  = $wpdb->prefix . $this->get('slug') . "_match";
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

		return intval($total_items, 10);

	}

}