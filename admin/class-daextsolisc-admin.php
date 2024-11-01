<?php

/*
 * this class should be used to work with the administrative side of wordpress
 */

class Daextsolisc_Admin {

	protected static $instance = null;
	private $shared = null;

    private $screen_id_matches = null;
    private $screen_id_events = null;
    private $screen_id_event_types = null;
    private $screen_id_help = null;
    private $screen_id_soccer_engine = null;
	private $screen_id_pro_version = null;
	private $screen_id_options = null;
	private $menu_options = null;

	private function __construct() {

		//assign an instance of the plugin info
		$this->shared = Daextsolisc_Shared::get_instance();

		//Load admin stylesheets and JavaScript
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		//Add the admin menu
		add_action( 'admin_menu', array( $this, 'me_add_admin_menu' ) );

		//Load the options API registrations and callbacks
		add_action( 'admin_init', array( $this, 'op_register_options' ) );

		//this hook is triggered during the creation of a new blog
		add_action( 'wpmu_new_blog', array( $this, 'new_blog_create_options_and_tables' ), 10, 6 );

		//this hook is triggered during the deletion of a blog
		add_action( 'delete_blog', array( $this, 'delete_blog_delete_options' ), 10, 1 );

		//Require and instantiate the class used to register the menu options
		require_once( $this->shared->get( 'dir' ) . 'admin/inc/class-daextsolisc-menu-options.php' );
		$this->menu_options = new Daextsolisc_Menu_Options( $this->shared );

	}

	/*
	 * return an instance of this class
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/*
	 * Enqueue admin specific styles.
	 */
	public function enqueue_admin_styles() {

		$screen = get_current_screen();

        //menu matches
        if ($screen->id == $this->screen_id_matches) {

            //Select2
            wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2',
                $this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/css/select2.min.css', array(),
                $this->shared->get( 'ver' ) );
            wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2-custom',
                $this->shared->get( 'url' ) . 'admin/assets/css/select2-custom.css', array(),
                $this->shared->get( 'ver' ) );

            //jQuery UI Dialog
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog-custom',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog-custom.css', array(),
                $this->shared->get('ver'));

            wp_enqueue_style('wp-color-picker');

            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip-custom', $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip-custom.css', array(), $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-framework-menu', $this->shared->get('url') . 'admin/assets/css/framework/menu.css', array(), $this->shared->get('ver'));
            wp_enqueue_style( $this->shared->get('slug') .'-menu-matches', $this->shared->get('url') . 'admin/assets/css/menu-matches.css', array(), $this->shared->get('ver') );

        }

        //menu events
        if ($screen->id == $this->screen_id_events) {

            //Select2
            wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2',
                $this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/css/select2.min.css', array(),
                $this->shared->get( 'ver' ) );
            wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2-custom',
                $this->shared->get( 'url' ) . 'admin/assets/css/select2-custom.css', array(),
                $this->shared->get( 'ver' ) );

            //jQuery UI Dialog
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog-custom',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog-custom.css', array(),
                $this->shared->get('ver'));

            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip-custom', $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip-custom.css', array(), $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-framework-menu', $this->shared->get('url') . 'admin/assets/css/framework/menu.css', array(), $this->shared->get('ver'));
            wp_enqueue_style( $this->shared->get('slug') .'-menu-events', $this->shared->get('url') . 'admin/assets/css/menu-events.css', array(), $this->shared->get('ver') );

        }

        //menu event types
        if ($screen->id == $this->screen_id_event_types) {

            //Select2
            wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2',
                $this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/css/select2.min.css', array(),
                $this->shared->get( 'ver' ) );
            wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2-custom',
                $this->shared->get( 'url' ) . 'admin/assets/css/select2-custom.css', array(),
                $this->shared->get( 'ver' ) );

            //jQuery UI Dialog
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog.css', array(),
                $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-dialog-custom',
                $this->shared->get('url') . 'admin/assets/css/jquery-ui-dialog-custom.css', array(),
                $this->shared->get('ver'));

            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip-custom', $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip-custom.css', array(), $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-framework-menu', $this->shared->get('url') . 'admin/assets/css/framework/menu.css', array(), $this->shared->get('ver'));
            wp_enqueue_style( $this->shared->get('slug') .'-menu-events', $this->shared->get('url') . 'admin/assets/css/menu-event-types.css', array(), $this->shared->get('ver') );

        }

        //Menu Help
        if ($screen->id == $this->screen_id_help) {

            wp_enqueue_style($this->shared->get('slug') . '-menu-help',
                $this->shared->get('url') . 'admin/assets/css/menu-help.css', array(), $this->shared->get('ver'));

        }

		//menu pro version
		if ($screen->id == $this->screen_id_pro_version) {
			wp_enqueue_style($this->shared->get('slug') . '-menu-pro-version',
				$this->shared->get('url') . 'admin/assets/css/menu-pro-version.css', array(), $this->shared->get('ver'));
		}

        //Menu Soccer Engine
        if ($screen->id == $this->screen_id_soccer_engine) {

            wp_enqueue_style($this->shared->get('slug') . '-menu-soccer-engine',
                $this->shared->get('url') . 'admin/assets/css/menu-soccer-engine.css', array(), $this->shared->get('ver'));

        }

        if ( $screen->id == $this->screen_id_options ) {

            //Select2
            wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2',
                $this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/css/select2.min.css', array(),
                $this->shared->get( 'ver' ) );
            wp_enqueue_style( $this->shared->get( 'slug' ) . '-select2-custom',
                $this->shared->get( 'url' ) . 'admin/assets/css/select2-custom.css', array(),
                $this->shared->get( 'ver' ) );

            wp_enqueue_style('wp-color-picker');

            wp_enqueue_style($this->shared->get('slug') . '-framework-options', $this->shared->get('url') . 'admin/assets/css/framework/options.css', array(), $this->shared->get('ver'));
            wp_enqueue_style($this->shared->get('slug') . '-jquery-ui-tooltip-custom', $this->shared->get('url') . 'admin/assets/css/jquery-ui-tooltip-custom.css', array(), $this->shared->get('ver'));

        }


	}

	/*
	 * Enqueue admin-specific JavaScript.
	 */
	public function enqueue_admin_scripts() {

        //Store the JavaScript parameters in the window.DAEXTSOLISC_PARAMETERS object
        $php_data = 'window.DAEXTSOLISC_PARAMETERS = {';
        $php_data .= 'admin_url: "' . get_admin_url() . '"';
        $php_data .= '};';

        $wp_localize_script_data = array(
            'deleteText' => esc_html__( 'Delete', 'soccer-live-scores'),
            'cancelText' => esc_html__( 'Cancel', 'soccer-live-scores'),
        );

		$screen = get_current_screen();

        //menu matches
        if ( $screen->id == $this->screen_id_matches ) {

            //Media Uploader
            wp_enqueue_media();
            wp_enqueue_script( $this->shared->get('slug') . '-media-uploader', $this->shared->get('url') . 'admin/assets/js/media-uploader.js', array('jquery'), $this->shared->get('ver') );

            //JQuery UI Tooltips
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init', $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', array('jquery'), $this->shared->get('ver'));

            //Select2
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-select2',
                $this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/js/select2.min.js', array('jquery'),
                $this->shared->get( 'ver' ) );

            //Matches Menu
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu-matches',
                $this->shared->get( 'url' ) . 'admin/assets/js/menu-matches.js',
                array( 'jquery', 'jquery-ui-dialog', 'daextsolisc-select2' ),
                $this->shared->get( 'ver' ) );

            wp_localize_script( $this->shared->get( 'slug' ) . '-menu-matches', 'objectL10n',
                $wp_localize_script_data );

            wp_add_inline_script( $this->shared->get('slug') . '-menu-matches', $php_data, 'before' );

            //Color Picker Initialization
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-wp-color-picker-init',
                $this->shared->get( 'url' ) . 'admin/assets/js/wp-color-picker-init.js',
                array( 'jquery', 'wp-color-picker' ), false, true );

        }

        //menu events
        if ( $screen->id == $this->screen_id_events ) {

            //JQuery UI Tooltips
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init', $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', array('jquery'), $this->shared->get('ver'));

            //Select2
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-select2',
                $this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/js/select2.min.js', array('jquery'),
                $this->shared->get( 'ver' ) );

            //Layouts Menu
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu-events',
                $this->shared->get( 'url' ) . 'admin/assets/js/menu-events.js',
                array( 'jquery', 'jquery-ui-dialog', 'daextsolisc-select2' ),
                $this->shared->get( 'ver' ) );
            wp_localize_script( $this->shared->get( 'slug' ) . '-menu-events', 'objectL10n',
                $wp_localize_script_data );

            wp_add_inline_script( $this->shared->get('slug') . '-menu-events', $php_data, 'before' );

        }

        //menu event types
        if ( $screen->id == $this->screen_id_event_types ) {

            //Media Uploader
            wp_enqueue_media();
            wp_enqueue_script( $this->shared->get('slug') . '-media-uploader', $this->shared->get('url') . 'admin/assets/js/media-uploader.js', array('jquery'), $this->shared->get('ver') );

            //JQuery UI Tooltips
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init', $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', array('jquery'), $this->shared->get('ver'));

            //Select2
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-select2',
                $this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/js/select2.min.js', array('jquery'),
                $this->shared->get( 'ver' ) );

            //Layouts Menu
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu-event-types',
                $this->shared->get( 'url' ) . 'admin/assets/js/menu-event-types.js',
                array( 'jquery', 'jquery-ui-dialog', 'daextsolisc-select2' ),
                $this->shared->get( 'ver' ) );
            wp_localize_script( $this->shared->get( 'slug' ) . '-menu-event-types', 'objectL10n',
                $wp_localize_script_data );

            wp_add_inline_script( $this->shared->get('slug') . '-menu-event-types', $php_data, 'before' );

        }

        //menu options
        if ( $screen->id == $this->screen_id_options ) {

            //JQuery UI Tooltips
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script($this->shared->get('slug') . '-jquery-ui-tooltip-init', $this->shared->get('url') . 'admin/assets/js/jquery-ui-tooltip-init.js', array('jquery'), $this->shared->get('ver'));

            //Select2
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-select2',
                $this->shared->get( 'url' ) . 'admin/assets/inc/select2/dist/js/select2.min.js', array('jquery'),
                $this->shared->get( 'ver' ) );

            //Color Picker Initialization
            wp_enqueue_script( $this->shared->get( 'slug' ) . '-wp-color-picker-init',
                $this->shared->get( 'url' ) . 'admin/assets/js/wp-color-picker-init.js',
                array( 'jquery', 'wp-color-picker' ), false, true );

	        //Menu Options
	        wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu-options',
		        $this->shared->get( 'url' ) . 'admin/assets/js/menu-options.js',
		        array( 'jquery' ),
		        $this->shared->get( 'ver' ) );

        }

	}

	/*
	 * plugin activation
	 */
	static public function ac_activate( $networkwide ) {

		/*
		 * delete options and tables for all the sites in the network
		 */
		if ( function_exists( 'is_multisite' ) and is_multisite() ) {

			/*
			 * if this is a "Network Activation" create the options and tables
			 * for each blog
			 */
			if ( $networkwide ) {

				//get the current blog id
				global $wpdb;
				$current_blog = $wpdb->blogid;

				//create an array with all the blog ids
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

				//iterate through all the blogs
				foreach ( $blogids as $blog_id ) {

					//swith to the iterated blog
					switch_to_blog( $blog_id );

					//create options and tables for the iterated blog
					self::ac_initialize_options();
					self::ac_create_database_tables();
					self::ac_initialize_custom_css();

				}

				//switch to the current blog
				switch_to_blog( $current_blog );

			} else {

				/*
				 * if this is not a "Network Activation" create options and
				 * tables only for the current blog
				 */
				self::ac_initialize_options();
				self::ac_create_database_tables();
				self::ac_initialize_custom_css();

			}

		} else {

			/*
			 * if this is not a multisite installation create options and
			 * tables only for the current blog
			 */
			self::ac_initialize_options();
			self::ac_create_database_tables();
			self::ac_initialize_custom_css();

		}

	}

	//create the options and tables for the newly created blog
	public function new_blog_create_options_and_tables( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		global $wpdb;

		/*
		 * if the plugin is "Network Active" create the options and tables for
		 * this new blog
		 */
		if ( is_plugin_active_for_network( 'soccer-live-scores/init.php' ) ) {

			//get the id of the current blog
			$current_blog = $wpdb->blogid;

			//switch to the blog that is being activated
			switch_to_blog( $blog_id );

			//create options for the new blog
			$this->ac_initialize_options();
            $this->ac_create_database_tables();
			$this->ac_initialize_custom_css();

			//switch to the current blog
			switch_to_blog( $current_blog );

		}

	}

	//delete options for the deleted blog
	public function delete_blog_delete_options( $blog_id ) {

		global $wpdb;

		//get the id of the current blog
		$current_blog = $wpdb->blogid;

		//switch to the blog that is being activated
		switch_to_blog( $blog_id );

		//delete options for the new blog
		$this->un_delete_options();
		$this->un_delete_database_tables();

		//switch to the current blog
		switch_to_blog( $current_blog );

	}

	/*
	 * initialize plugin options
	 */
	static private function ac_initialize_options() {

		//assign an instance of Daextsolisc_Shared
		$shared = Daextsolisc_Shared::get_instance();

		foreach ( $shared->get( 'options' ) as $key => $value ) {
			add_option( $key, $value );
		}

	}

    /*
 * create the plugin database tables
 */
    static private function ac_create_database_tables()
    {

	    //assign an instance of Daextsolisc_Shared
	    $shared = Daextsolisc_Shared::get_instance();

        global $wpdb;

        //Get the database character collate that will be appended at the end of each query
        $charset_collate = $wpdb->get_charset_collate();

        //check database version and create the database
        if (intval(get_option('daextsolisc_database_version'), 10) < 1) {

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            //create *prefix*_match
            $table_name = $wpdb->prefix . $shared->get('slug') . "_match";
            $sql = "CREATE TABLE $table_name (
              match_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              name TEXT DEFAULT '' NOT NULL,
              team_1_name TEXT DEFAULT '' NOT NULL,
              team_2_name TEXT DEFAULT '' NOT NULL,
              team_1_logo TEXT DEFAULT '' NOT NULL,
              team_2_logo TEXT DEFAULT '' NOT NULL,
              additional_score_mode TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL,
              team_1_first_leg_score TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL,
              team_2_first_leg_score TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL,
              live TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL
            ) $charset_collate";

            dbDelta($sql);

            //create *prefix*_event
            $table_name = $wpdb->prefix . $shared->get('slug') . "_event";
            $sql = "CREATE TABLE $table_name (
              event_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              team TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL,
              minute SMALLINT UNSIGNED NOT NULL,
              description TEXT DEFAULT '' NOT NULL,
              additional_information TEXT DEFAULT '' NOT NULL,
              event_type_id BIGINT UNSIGNED NOT NULL,
              match_id BIGINT UNSIGNED NOT NULL
            ) $charset_collate";

            dbDelta($sql);

            //create *prefix*_event_type
            $table_name = $wpdb->prefix . $shared->get('slug') . "_event_type";
            $sql = "CREATE TABLE $table_name (
              event_type_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              name TEXT DEFAULT '' NOT NULL,
              icon TEXT DEFAULT '' NOT NULL,
              goal TINYINT(1) DEFAULT 0 NOT NULL
            ) $charset_collate";

            dbDelta($sql);

	        //set default layouts
	        $wpdb->query( "INSERT INTO $table_name (name, icon, goal) VALUES " .
	                      "('Goal', '" . $shared->get( 'url' ) . "public/assets/img/goal.png', 1)," .
	                      "('Substitution', '" . $shared->get( 'url' ) . "public/assets/img/substitution.png', 0)," .
	                      "('Double Yellow Card', '" . $shared->get( 'url' ) . "public/assets/img/double-yellow-card.png', 0)," .
	                      "('Red Card', '" . $shared->get( 'url' ) . "public/assets/img//red-card.png', 0)," .
	                      "('Yellow Card', '" . $shared->get( 'url' ) . "public/assets/img/yellow-card.png', 0)," .
	                      "('Generic', '" . $shared->get( 'url' ) . "public/assets/img/generic.png', 0)" );

            //Update database version
            update_option($shared->get('slug') . '_database_version', "1");

        }

    }

	/*
	 * initialize the custom-[blog_id].css file
	 */
	static public function ac_initialize_custom_css(){

		//assign an instance of Daextsolisc_Shared
		$shared = Daextsolisc_Shared::get_instance();

		/*
		 * Write the custom-[blog_id].css file or die if the file can't be
		 * created or modified
		 */
		if( self::write_custom_css() === false ){
			die( esc_html__('The', 'soccer-live-scores') . ' ' . esc_html($shared->get('dir') . 'public/assets/css/custom-' . get_current_blog_id() . '.css') . ' ' . esc_html__('file should be writable by the server. Please change its permissions and try again.', 'soccer-live-scores'));
		}

	}

	/*
	 * Plugin delete.
	 */
	static public function un_delete() {

		/*
		 * Delete options for all the sites in the network.
		 */
		if ( function_exists( 'is_multisite' ) and is_multisite() ) {

			//get the current blog id
			global $wpdb;
			$current_blog = $wpdb->blogid;

			//create an array with all the blog ids
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

			//iterate through all the blogs
			foreach ( $blogids as $blog_id ) {

				//switch to the iterated blog
				switch_to_blog( $blog_id );

				//delete options for the iterated blog
				Daextsolisc_Admin::un_delete_options();
				Daextsolisc_Admin::un_delete_database_tables();

			}

			//switch to the current blog
			switch_to_blog( $current_blog );

		} else {

			/*
			 * If this is not a multisite installation delete options only for the current blog.
			 */
			Daextsolisc_Admin::un_delete_options();
			Daextsolisc_Admin::un_delete_database_tables();

		}

	}

	/*
	 * Delete plugin options.
	 */
	static public function un_delete_options() {

		//assign an instance of Daextsolisc_Shared
		$shared = Daextsolisc_Shared::get_instance();

		foreach ( $shared->get( 'options' ) as $key => $value ) {
			delete_option( $key );
		}

	}

	/*
     * delete plugin database tables
     */
	static public function un_delete_database_tables()
	{

		//assign an instance of Daextsolisc_Shared
		$shared = Daextsolisc_Shared::get_instance();

		global $wpdb;

		$table_name = $wpdb->prefix . $shared->get('slug') . "_match";
		$sql = "DROP TABLE $table_name";
		$wpdb->query($sql);

		$table_name = $wpdb->prefix . $shared->get('slug') . "_event";
		$sql = "DROP TABLE $table_name";
		$wpdb->query($sql);

		$table_name = $wpdb->prefix . $shared->get('slug') . "_event_type";
		$sql = "DROP TABLE $table_name";
		$wpdb->query($sql);

	}

	/*
	 * Register the admin menu.
	 */
	public function me_add_admin_menu() {


		//The icon in Base64 format
        $icon_base64 = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNS4yLjMsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyMCAyMCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjAgMjA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxwYXRoIGQ9Ik02LjgsOS4xTDEwLDYuOGwzLjIsMi4zTDEyLDEyLjlIOEw2LjgsOS4xeiBNMTAsMGMxLjQsMCwyLjcsMC4zLDMuOSwwLjhzMi4zLDEuMiwzLjIsMi4xYzAuOSwwLjksMS42LDEuOSwyLjEsMy4yDQoJUzIwLDguNywyMCwxMHMtMC4yLDIuNi0wLjgsMy45Yy0wLjYsMS4zLTEuMywyLjMtMi4xLDMuMmMtMC45LDAuOS0xLjksMS42LTMuMiwyLjFTMTEuMywyMCwxMCwyMHMtMi42LTAuMy0zLjktMC44UzMuOCwxOCwyLjksMTcuMQ0KCXMtMS42LTEuOS0yLjEtMy4yUzAsMTEuMywwLDEwczAuMy0yLjYsMC44LTMuOVMyLDMuOCwyLjksMi45czItMS42LDMuMi0yLjFTOC42LDAsMTAsMHogTTE2LjksMTUuMWMxLjEtMS41LDEuNy0zLjIsMS43LTUuMWwwLDANCglsLTEuMSwxbC0yLjctMi41bDAuNy0zLjZMMTcsNWMtMS4xLTEuNS0yLjYtMi42LTQuMy0zLjFsMC42LDEuNEwxMCw1TDYuOCwzLjJsMC42LTEuNEM1LjYsMi40LDQuMiwzLjQsMyw1bDEuNS0wLjFsMC43LDMuNkwyLjYsMTENCglsLTEuMS0xbDAsMGMwLDEuOSwwLjYsMy42LDEuNyw1LjFsMC4zLTEuNUw3LjEsMTRsMS42LDMuM2wtMS4zLDAuOGMwLjksMC4zLDEuOCwwLjQsMi43LDAuNHMxLjgtMC4xLDIuNy0wLjRsLTEuMy0wLjhsMS42LTMuMw0KCWwzLjYtMC40TDE2LjksMTUuMXoiLz4NCjwvc3ZnPg0K';

		//The icon in the data URI scheme
		$icon_data_uri = 'data:image/svg+xml;base64,' . $icon_base64;

		add_menu_page(
			esc_html__( 'SLS', 'soccer-live-scores' ),
			esc_html__( 'Matches', 'soccer-live-scores' ),
			'manage_options',
			$this->shared->get( 'slug' ) . '-matches',
			array( $this, 'me_display_menu_matches' ),
			$icon_data_uri
		);

        $this->screen_id_matches = add_submenu_page(
            $this->shared->get( 'slug' ) . '-matches',
            esc_html__( 'SLS - Help', 'soccer-live-scores' ),
            esc_html__( 'Matches', 'soccer-live-scores' ),
            'manage_options',
            $this->shared->get( 'slug' ) . '-matches',
            array( $this, 'me_display_menu_matches' )
        );

        $this->screen_id_events = add_submenu_page(
            $this->shared->get( 'slug' ) . '-matches',
            esc_html__( 'SLS - Events', 'soccer-live-scores' ),
            esc_html__( 'Events', 'soccer-live-scores' ),
            'manage_options',
            $this->shared->get( 'slug' ) . '-events',
            array( $this, 'me_display_menu_events' )
        );

        $this->screen_id_event_types = add_submenu_page(
            $this->shared->get( 'slug' ) . '-matches',
            esc_html__( 'SLS - Event Types', 'soccer-live-scores' ),
            esc_html__( 'Event Types', 'soccer-live-scores' ),
            'manage_options',
            $this->shared->get( 'slug' ) . '-event-types',
            array( $this, 'me_display_menu_event_types' )
        );

        $this->screen_id_help = add_submenu_page(
            $this->shared->get( 'slug' ) . '-matches',
            esc_html__( 'SLS - Help', 'soccer-live-scores' ),
            esc_html__( 'Help', 'soccer-live-scores' ),
            'manage_options',
            $this->shared->get( 'slug' ) . '-help',
            array( $this, 'me_display_menu_help' )
        );

        $this->screen_id_pro_version = add_submenu_page(
            $this->shared->get( 'slug' ) . '-matches',
            esc_html__( 'SLS - Soccer Engine', 'soccer-live-scores' ),
            esc_html__( 'Soccer Engine', 'soccer-live-scores' ),
            'manage_options',
            $this->shared->get( 'slug' ) . '-soccer-engine',
            array( $this, 'me_display_menu_soccer_engine' )
        );

		$this->screen_id_options = add_submenu_page(
			$this->shared->get( 'slug' ) . '-matches',
			esc_html__( 'LCN - Options', 'soccer-live-scores' ),
			esc_html__( 'Options', 'soccer-live-scores' ),
			'manage_options',
			$this->shared->get( 'slug' ) . '-options',
			array( $this, 'me_display_menu_options' )
		);

	}

    /*
     * includes the options view
     */
    public function me_display_menu_matches() {
        include_once( 'view/matches.php' );
    }

    /*
     * includes the events view
     */
    public function me_display_menu_events() {
        include_once( 'view/events.php' );
    }

    /*
     * includes the event types view
     */
    public function me_display_menu_event_types() {
        include_once( 'view/event_types.php' );
    }


    /*
     * includes the options view
     */
    public function me_display_menu_help() {
        include_once( 'view/help.php' );
    }

    /*
     * includes the options view
     */
    public function me_display_menu_soccer_engine() {
        include_once( 'view/soccer-engine.php' );
    }

	/*
	 * includes the options view
	 */
	public function me_display_menu_options() {
		include_once( 'view/options.php' );
	}

	/*
	 * register options
	 */
	public function op_register_options() {

		$this->menu_options->register_options();

	}

    /**
     * Echo all the dismissible notices based on the values of the $notices array.
     *
     * @param $notices
     */
    public function dismissible_notice($notices){

        foreach($notices as $key => $notice){
            echo '<div class="' . esc_attr($notice['class']) . ' settings-error notice is-dismissible below-h2"><p>' . esc_html($notice['message']) . '</p></div>';
        }

    }

    /**
     * check if the event type is used in an event
     *
     * @param int $layout_id the event type id
     * @return bool true if the event type is used, false if the event type is not used
     */
    private function event_type_is_used($event_type_id){

        global $wpdb; $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
        $safe_sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE event_type_id = %d ", $event_type_id);
        $number_of_events = $wpdb->get_var($safe_sql);

        if( $number_of_events > 0 ){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Check if the match is used in an event.
     *
     * @param int $match_id the match id
     * @return bool true if the match is used, false if the match is not used.
     */
    private function match_is_used($match_id){

        global $wpdb; $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
        $safe_sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE match_id = %d ", $match_id);
        $number_of_matches = $wpdb->get_var($safe_sql);

        if( $number_of_matches > 0 ){
            return true;
        }else{
            return false;
        }

    }

	/*
	 * Generate the custom.css file based on the values of the options and write them down in the custom.css file.
	 */
	static public function write_custom_css(){

		//assign an instance of Daextsolisc_Shared
		$shared = Daextsolisc_Shared::get_instance();

		//turn on output buffering
		ob_start();

		//text primary color
		echo ".daextsolisc-team-1-name, .daextsolisc-team-2-name, .daextsolisc-score, .daextsolisc-team-1-score, .daextsolisc-team-2-score, .daextsolisc-event-description{ color: " . esc_attr( stripslashes( get_option("daextsolisc_text_primary_color") ) ) . " !important;}";

		//text secondary color
        echo ".daextsolisc-minute{ color: " . esc_attr( stripslashes( get_option("daextsolisc_text_secondary_color") ) ) . " !important;}";
		echo ".daextsolisc-additional-information{ color: " . esc_attr( stripslashes( get_option("daextsolisc_text_secondary_color") ) ) . " !important;}";
		echo ".daextsolisc-additional-score{ color: " . esc_attr( stripslashes( get_option("daextsolisc_text_secondary_color") ) ) . " !important;}";

		//separator color
		echo ".daextsolisc-row{ border-color: " . esc_attr( stripslashes( get_option("daextsolisc_separator_color") ) ) . " !important;}";


		//font family
		echo '.daextsolisc-container *{font-family: ' . stripslashes( get_option("daextsolisc_font_family") ) . ' !important; }';

		//Responsive breakpoint
		?>
		@media (max-width: <?php echo intval(get_option('daextsolisc_responsive_breakpoint'), 10); ?>px) {

			.daextsolisc-no-logo .daextsolisc-team-1-name{
				display: block !important;
			}

			.daextsolisc-team-1-name{
				display: none !important;
			}

			.daextsolisc-no-logo .daextsolisc-team-2-name{
				display: block !important;
			}

			.daextsolisc-team-2-name{
				display: none !important;
			}

		}
		<?php

		//Top Margin
		echo ".daextsolisc-container{ margin-top: " . intval( esc_attr( stripslashes( get_option("daextsolisc_top_margin") ) ), 10 ) . "px !important;}";

		//Bottom Margin
		echo ".daextsolisc-container{ margin-bottom: " . intval( esc_attr( stripslashes( get_option("daextsolisc_bottom_margin") ) ), 10 ) . "px !important;}";

		$custom_css_string = ob_get_clean();

		return @file_put_contents( $shared->get('dir') . 'public/assets/css/custom-' . get_current_blog_id() . '.css', $custom_css_string, LOCK_EX);

	}

}