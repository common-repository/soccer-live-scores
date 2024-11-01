<?php

if ( !current_user_can( 'edit_posts' ) )  {
    wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'soccer-live-scores') );
}

?>

<!-- process data -->

<?php

//Initialize variables -------------------------------------------------------------------------------------------------
$dismissible_notice_a = [];

//Preliminary operations -----------------------------------------------------------------------------------------------
global $wpdb;

//Sanitization ---------------------------------------------------------------------------------------------

//Actions
$data['edit_id'] = isset($_GET['edit_id']) ? intval($_GET['edit_id'], 10) : null;
$data['delete_id'] = isset($_POST['delete_id']) ? intval($_POST['delete_id'], 10) : null;
$data['clone_id'] = isset($_POST['clone_id']) ? intval($_POST['clone_id'], 10) : null;
$data['update_id']    = isset( $_POST['update_id'] ) ? intval( $_POST['update_id'], 10 ) : null;
$data['form_submitted']    = isset( $_POST['form_submitted'] ) ? intval( $_POST['form_submitted'], 10 ) : null;

//Filter and search data
$data['s'] = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null;

//Form data
$data['match_id'] = isset($_POST['update_id']) ? intval($_POST['update_id'], 10) : null;
$data['name'] = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : null;
$data['team_1_name'] = isset($_POST['team_1_name']) ? sanitize_text_field($_POST['team_1_name']) : null;
$data['team_1_logo'] = isset($_POST['team_1_logo']) ? esc_url_raw($_POST['team_1_logo']) : null;
$data['team_2_name'] = isset($_POST['team_2_name']) ? sanitize_text_field($_POST['team_2_name']) : null;
$data['team_2_logo'] = isset($_POST['team_2_logo']) ? esc_url_raw($_POST['team_2_logo']) : null;
$data['additional_score_mode'] = isset($_POST['additional_score_mode']) ? intval($_POST['additional_score_mode'], 10) : null;
$data['team_1_first_leg_score'] = isset($_POST['team_1_first_leg_score']) ? intval($_POST['team_1_first_leg_score'], 10) : null;
$data['team_2_first_leg_score'] = isset($_POST['team_2_first_leg_score']) ? intval($_POST['team_2_first_leg_score'], 10) : null;
$data['live'] = isset($_POST['live']) ? intval($_POST['live'], 10) : null;

//Validation -----------------------------------------------------------------------------------------------

if( !is_null( $data['update_id'] ) or !is_null($data['form_submitted']) ) {

	//validation on "name"
	if ( mb_strlen( trim( $data['name'] ) ) === 0 or mb_strlen( trim( $data['name'] ) ) > 255 ) {
		$dismissible_notice_a[] = [
			'message' => __( 'Please enter a valid value in the "Name" field.', 'soccer-live-scores'),
			'class' => 'error'
		];
		$invalid_data         = true;
	}

    //validation on "team_1_name"
    if ( mb_strlen( trim( $data['team_1_name'] ) ) === 0 or mb_strlen( trim( $data['team_1_name'] ) ) > 255 ) {
        $dismissible_notice_a[] = [
            'message' => __( 'Please enter a valid value in the "Team 1 Name" field.', 'soccer-live-scores'),
            'class' => 'error'
        ];
        $invalid_data         = true;
    }

    //validation on "team_2_name"
    if ( mb_strlen( trim( $data['team_2_name'] ) ) === 0 or mb_strlen( trim( $data['team_2_name'] ) ) > 255 ) {
        $dismissible_notice_a[] = [
            'message' => __( 'Please enter a valid value in the "Team 2 Name" field.', 'soccer-live-scores'),
            'class' => 'error'
        ];
        $invalid_data         = true;
    }

	//validation on "team_1_first_leg_score"
	if ( mb_strlen( trim( $data['team_1_first_leg_score'] ) ) > 2 ) {
		$dismissible_notice_a[] = [
			'message' => __( 'Please enter a valid value in the "Team 1 First Leg Score" field.', 'soccer-live-scores'),
			'class' => 'error'
		];
		$invalid_data         = true;
	}

	//validation on "team_2_first_leg_score"
	if ( mb_strlen( trim( $data['team_2_first_leg_score'] ) ) > 2 ) {
		$dismissible_notice_a[] = [
			'message' => __( 'Please enter a valid value in the "Team 2 First Leg Score" field.', 'soccer-live-scores'),
			'class' => 'error'
		];
		$invalid_data         = true;
	}

}

//update ---------------------------------------------------------------
if( !is_null($data['update_id']) and !isset($invalid_data) ){

    //update the database
    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
    $safe_sql = $wpdb->prepare("UPDATE $table_name SET 
         name = %s,
        team_1_name = %s,
        team_1_logo = %s,
        team_2_name = %s,
        team_2_logo = %s,
        additional_score_mode = %d,
        team_1_first_leg_score = %d,
        team_2_first_leg_score = %d,
        live = %d
        WHERE match_id = %d",
        $data['name'],
        $data['team_1_name'],
        $data['team_1_logo'],
        $data['team_2_name'],
        $data['team_2_logo'],
	    $data['additional_score_mode'],
	    $data['team_1_first_leg_score'],
	    $data['team_2_first_leg_score'],
	    $data['live'],
        $data['match_id'] );

    $query_result = $wpdb->query( $safe_sql );

    if($query_result !== false){
        $dismissible_notice_a[] = [
            'message' => __('The match has been successfully updated.', 'soccer-live-scores'),
            'class' => 'updated'
        ];
    }

}else{

    //add ------------------------------------------------------------------
    if( !is_null($data['form_submitted']) and !isset($invalid_data) ){

        //insert into the database
        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
        $safe_sql = $wpdb->prepare("INSERT INTO $table_name SET 
            name = %s,
            team_1_name = %s,
            team_1_logo = %s,
            team_2_name = %s,
            team_2_logo = %s,
            additional_score_mode = %d,
            team_1_first_leg_score = %d,
            team_2_first_leg_score = %d,
            live = %d",
            $data['name'],
	        $data['team_1_name'],
	        $data['team_1_logo'],
	        $data['team_2_name'],
	        $data['team_2_logo'],
	        $data['additional_score_mode'],
	        $data['team_1_first_leg_score'],
	        $data['team_2_first_leg_score'],
	        $data['live'],
        );

        $query_result = $wpdb->query( $safe_sql );

        if($query_result !== false){
            $dismissible_notice_a[] = [
                'message' => __('The match has been successfully added.', 'soccer-live-scores'),
                'class' => 'updated'
            ];
        }

    }

}

//delete an item
if( !is_null($data['delete_id']) ){

    //delete this layout only if it's not used by any formation and it's not a default layout.
    if( $this->match_is_used($data['delete_id']) ){

        $dismissible_notice_a[] = [
            'message' => __("This match is associated with one or more events and can't be deleted.", 'soccer-live-scores'),
            'class' => 'error'
        ];

    }else{

        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
        $safe_sql = $wpdb->prepare("DELETE FROM $table_name WHERE match_id = %d ", $data['delete_id']);
        $query_result = $wpdb->query( $safe_sql );

        if($query_result !== false){
            $dismissible_notice_a[] = [
                'message' => __('The match has been successfully deleted.', 'soccer-live-scores'),
                'class' => 'updated'
            ];
        }

    }

}

//clone a table
if (!is_null($data['clone_id'])) {

    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
    $wpdb->query("CREATE TEMPORARY TABLE tmptable_1 SELECT * FROM $table_name WHERE match_id = " . $data['clone_id']);
    $wpdb->query("UPDATE tmptable_1 SET match_id = NULL");
    $wpdb->query("INSERT INTO $table_name SELECT * FROM tmptable_1");
    $wpdb->query("DROP TEMPORARY TABLE IF EXISTS tmptable_1");

}

//get the match data
if(!is_null($data['edit_id'])){

    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
    $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE match_id = %d ", $data['edit_id']);
    $match_obj = $wpdb->get_row($safe_sql);

}


?>

<!-- output -->

<div class="wrap">

    <div id="daext-header-wrapper" class="daext-clearfix">

        <h2><?php esc_html_e('Soccer Live Scores - Matches', 'soccer-live-scores'); ?></h2>

        <form action="admin.php" method="get" id="daext-search-form">

            <input type="hidden" name="page" value="daextsolisc-matches">

            <p><?php esc_html_e('Perform your Search', 'soccer-live-scores'); ?></p>

            <?php
            if ( ! is_null( $data['s'] ) and mb_strlen( trim( $data['s'] ) ) > 0 ) {
                $search_string = $data['s'];
            } else {
                $search_string = '';
            }

            ?>

            <input type="text" name="s"
                   value="<?php echo esc_attr(stripslashes($search_string)); ?>" autocomplete="off" maxlength="255">
            <input type="submit" value="">

        </form>

    </div>

    <div id="daext-menu-wrapper">

        <?php $this->dismissible_notice($dismissible_notice_a); ?>

        <!-- table -->

        <?php

        //create the query part used to filter the results when a search is performed
        if (!is_null($data['s']) and mb_strlen(trim($data['s'])) > 0) {

            //create the query part used to filter the results when a search is performed
            $filter = $wpdb->prepare('WHERE (name LIKE %s)',
                '%' . $data['s'] . '%');

        }else{
            $filter = '';
        }

        //retrieve the total number of matches
        global $wpdb;
        $table_name=$wpdb->prefix . $this->shared->get('slug') . "_match";
        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $filter");

        //Initialize the pagination class
        require_once( $this->shared->get('dir') . '/admin/inc/class-daextsolisc-pagination.php' );
        $pag = new Daextsolisc_Pagination();
        $pag->set_total_items( $total_items );//Set the total number of items
        $pag->set_record_per_page( 10 ); //Set records per page
        $pag->set_target_page( "admin.php?page=" . $this->shared->get('slug') . "-matches" );//Set target page
        $pag->set_current_page();//set the current page number from $_GET

        ?>

        <!-- Query the database -->
        <?php
        $query_limit = $pag->query_limit();
        $results = $wpdb->get_results("SELECT * FROM $table_name $filter ORDER BY match_id DESC $query_limit ", ARRAY_A); ?>

        <?php if( count($results) > 0 ) : ?>

            <div class="daext-items-container">

                <!-- list of tables -->
                <table class="daext-items">
                    <thead>
                    <tr>
                        <th>
                            <div><?php esc_html_e( 'Match ID', 'soccer-live-scores'); ?></div>
                            <div class="help-icon" title="<?php esc_attr_e( 'The ID of the match.', 'soccer-live-scores'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_html_e( 'Shortcode', 'soccer-live-scores'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e( 'The shortcode of the match.', 'soccer-live-scores'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_html_e( 'Name', 'soccer-live-scores'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e( 'The name of the match.', 'soccer-live-scores'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_html_e( 'Team 1', 'soccer-live-scores'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e( 'The name of team 1.', 'soccer-live-scores'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_html_e( 'Team 2', 'soccer-live-scores'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e( 'The name of team 2.', 'soccer-live-scores'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_html_e( 'Live', 'soccer-live-scores'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e( 'The live status of the match.', 'soccer-live-scores'); ?>"></div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach($results as $result) : ?>
                        <tr>
                            <td><?php echo intval($result['match_id'], 10); ?></td>
                            <td>[soccer-live-scores id="<?php echo intval($result['match_id'], 10); ?>"]</td>
                            <td><?php echo esc_html(stripslashes($result['name'])); ?></td>
                            <td><?php echo esc_html(stripslashes($result['team_1_name'])); ?></td>
                            <td><?php echo esc_html(stripslashes($result['team_2_name'])); ?></td>
                            <td><?php echo intval($result['live'], 10) === 0 ? esc_html__('No', 'soccer-live-scores') : esc_html__('Yes', 'soccer-live-scores'); ?></td>
                            <td class="icons-container">
                                <form method="POST"
                                      action="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-matches">
                                    <input type="hidden" name="clone_id" value="<?php echo intval($result['match_id'], 10); ?>">
                                    <input class="menu-icon clone help-icon" type="submit" value="">
                                </form>
                                <a class="menu-icon edit" href="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-matches&edit_id=<?php echo intval($result['match_id'], 10); ?>"></a>
                                <form id="form-delete-<?php echo intval($result['match_id'], 10); ?>" method="POST" action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-matches">
                                    <input type="hidden" value="<?php echo intval($result['match_id'], 10); ?>" name="delete_id" >
                                    <input class="menu-icon delete" type="submit" value="">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- Display the pagination -->
            <?php if($pag->total_items > 0) : ?>
                <div class="daext-tablenav daext-clearfix">
                    <div class="daext-tablenav-pages">
                        <span class="daext-displaying-num"><?php echo $pag->total_items; ?> <?php esc_html_e('items', 'soccer-live-scores'); ?></span>
                        <?php $pag->show(); ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else : ?>

            <?php

            if (mb_strlen(trim($filter)) > 0) {
                echo '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('There are no results that match your filter.', 'soccer-live-scores') . '</p></div>';
            }

            ?>

        <?php endif; ?>

        <form method="POST" action="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-matches" >

            <input type="hidden" value="1" name="form_submitted">

            <div class="daext-form-container">

                <?php if(!is_null($data['edit_id'])) : ?>

                    <!-- Edit a match -->

                    <div class="daext-form-title"><?php esc_html_e('Edit Match', 'soccer-live-scores'); ?> <?php echo intval($match_obj->match_id, 10); ?></div>

                    <table class="daext-form daext-form-table">

                        <input type="hidden" name="update_id" value="<?php echo intval($match_obj->match_id, 10); ?>" />

                        <!-- Name -->
                        <tr valign="top">
                            <th><label for="name"><?php esc_html_e('Name', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($match_obj->name)); ?>" type="text"
                                       id="name" maxlength="255" size="30" name="name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('A short description of the match. E.g. "Real Madrid vs Juventus"', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>
                        
                        <!-- Team 1 Name -->
                        <tr valign="top">
                            <th><label for="team-1-name"><?php esc_html_e('Team 1', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($match_obj->team_1_name)); ?>" type="text"
                                       id="team-1-name" maxlength="100" size="30" name="team_1_name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The name of team 1.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 1 Logo -->
                        <tr>
                            <th scope="row"><label for="team-1-logo"><?php esc_html_e('Team 1 Logo', 'soccer-live-scores'); ?></label></th>
                            <td>

                                <div class="image-uploader">
                                    <img class="selected-image" src="<?php echo esc_url($match_obj->team_1_logo); ?>" <?php echo strlen(trim($match_obj->team_1_logo)) === 0 ? 'style="display: none;"' : ''; ?>>
                                    <input value="<?php echo esc_url($match_obj->team_1_logo); ?>" type="hidden" id="team_1-logo" maxlength="1000" name="team_1_logo">
                                    <a class="button_add_media" data-set-remove="set" data-set="<?php esc_attr_e('Set image', 'soccer-live-scores'); ?>" data-remove="<?php esc_attr_e('Remove Image', 'soccer-live-scores'); ?>"><?php echo strlen(trim($match_obj->team_1_logo)) === 0 ? esc_html__('Set image', 'soccer-live-scores') : esc_html__('Remove Image', 'soccer-live-scores'); ?></a>
                                    <p class="description"><?php esc_html_e('Select an image that represents this team', 'soccer-live-scores'); ?>.</p>
                                </div>

                            </td>
                        </tr>

                        <!-- Team 2 Name -->
                        <tr valign="top">
                            <th><label for="team-2-name"><?php esc_html_e('Team 2', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($match_obj->team_2_name)); ?>" type="text"
                                       id="team-2-name" maxlength="100" size="30" name="team_2_name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The name of team 2.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 2 Logo -->
                        <tr>
                            <th scope="row"><label for="team-2-logo"><?php esc_html_e('Team 2 Logo', 'soccer-live-scores'); ?></label></th>
                            <td>

                                <div class="image-uploader">
                                    <img class="selected-image" src="<?php echo esc_url($match_obj->team_2_logo); ?>" <?php echo strlen(trim($match_obj->team_2_logo)) === 0 ? 'style="display: none;"' : ''; ?>>
                                    <input value="<?php echo esc_url($match_obj->team_2_logo); ?>" type="hidden" id="team-2-logo" maxlength="2000" name="team_2_logo">
                                    <a class="button_add_media" data-set-remove="<?php echo strlen(trim($match_obj->team_2_logo)) === 0 ? 'set' : 'remove'; ?>" data-set="<?php esc_attr_e('Set image', 'soccer-live-scores'); ?>" data-remove="<?php esc_attr_e('Remove Image', 'soccer-live-scores'); ?>"><?php echo strlen(trim($match_obj->team_2_logo)) === 0 ? esc_html__('Set image', 'soccer-live-scores') : esc_html__('Remove Image', 'soccer-live-scores'); ?></a>
                                    <p class="description"><?php esc_html_e('Select an image that represents this team', 'soccer-live-scores'); ?>.</p>
                                </div>

                            </td>
                        </tr>

                        <tr class="group-trigger" data-trigger-target="additional-score">
                            <th scope="row" class="group-title"><?php esc_attr_e( 'Additional Score', 'soccer-live-scores' ); ?></th>
                            <td>
                                <div class="expand-icon"></div>
                            </td>
                        </tr>

                        <!-- Additional Score Mode -->
                        <tr valign="top" class="additional-score">
                            <th scope="row"><label for="additional-score-mode"><?php esc_html_e('Additional Score Mode', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <select id="additional-score-mode" name="additional_score_mode">
                                    <option value="0" <?php echo selected( $match_obj->additional_score_mode, 0 ); ?>><?php esc_html_e('Disabled', 'soccer-live-scores'); ?></option>
                                    <option value="1" <?php echo selected( $match_obj->additional_score_mode, 1 ); ?>><?php esc_html_e('First Leg', 'soccer-live-scores'); ?></option>
                                    <option value="2" <?php echo selected( $match_obj->additional_score_mode, 2 ); ?>><?php esc_html_e('Aggregate', 'soccer-live-scores'); ?></option>
                                </select>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('Select "Disabled" to hide the additional score section, "First Leg" to display the result of the first leg, or "Aggregate" to display the aggregate result.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 1 First Leg Score -->
                        <tr valign="top" class="additional-score">
                            <th><label for="team-1-first-leg-score"><?php esc_html_e('Team 1 First Leg Score', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($match_obj->team_1_first_leg_score)); ?>" type="text"
                                       id="team-1-first-leg-score" maxlength="2" size="30" name="team_1_first_leg_score"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The score of team 1 in the first leg match.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 2 First Score -->
                        <tr valign="top" class="additional-score">
                            <th><label for="team-2-first-leg-score"><?php esc_html_e('Team 2 First Leg Score', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($match_obj->team_2_first_leg_score)); ?>" type="text"
                                       id="team-2-first-leg-score" maxlength="2" size="30" name="team_2_first_leg_score"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The score of team 2 in the first leg match.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <tr class="group-trigger" data-trigger-target="advanced">
                            <th scope="row" class="group-title"><?php esc_attr_e( 'Advanced', 'soccer-live-scores' ); ?></th>
                            <td>
                                <div class="expand-icon"></div>
                            </td>
                        </tr>

                        <!-- Live -->
                        <tr valign="top" class="advanced">
                            <th scope="row"><label for="live-match"><?php esc_html_e('Live', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <select id="live" name="live">

                                    <option value="0" <?php echo selected( $match_obj->live, 0 ); ?> ><?php esc_html_e('No', 'soccer-live-scores'); ?></option>
                                    <option value="1" <?php echo selected( $match_obj->live, 1 ); ?> ><?php esc_html_e('Yes', 'soccer-live-scores'); ?></option>

                                </select>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('If you select "Yes" the match result and the match events will be updated in real time with AJAX requests.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                    </table>

                    <!-- submit button -->
                    <div class="daext-form-action">
                        <input class="button" type="submit" value="<?php esc_attr_e('Update Match', 'soccer-live-scores'); ?>" >
                        <input id="cancel" class="button" type="submit" value="<?php esc_attr_e('Cancel', 'soccer-live-scores'); ?>">
                    </div>

                <?php else : ?>

                    <!-- Create new match -->

                    <div class="daext-form-title"><?php esc_html_e('Create New Match', 'soccer-live-scores'); ?></div>

                    <table class="daext-form daext-form-table">

                        <!-- Name -->
                        <tr valign="top">
                            <th><label for="name"><?php esc_html_e('Name', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input type="text"
                                       id="name" maxlength="255" size="30" name="name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('A short description of the match. E.g. "Real Madrid vs Juventus"', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 1 Name -->
                        <tr valign="top">
                            <th><label for="team-1-name"><?php esc_html_e('Team 1', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input type="text"
                                       id="team-1-name" maxlength="100" size="30" name="team_1_name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The name of team 1.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 1 Logo -->
                        <tr>
                            <th scope="row"><label for="team-1-logo"><?php esc_html_e('Team 1 Logo', 'soccer-live-scores'); ?></label></th>
                            <td>

                                <div class="image-uploader">
                                    <img class="selected-image" src="" style="display: none">
                                    <input type="hidden" id="team-1-logo" maxlength="1000" name="team_1_logo">
                                    <a class="button_add_media" data-set-remove="set" data-set="<?php esc_attr_e('Set image', 'soccer-live-scores'); ?>" data-remove="<?php esc_attr_e('Remove Image', 'soccer-live-scores'); ?>"><?php esc_html_e('Set image', 'soccer-live-scores'); ?></a>
                                    <p class="description"><?php esc_html_e('Select an image that represents this team', 'soccer-live-scores'); ?>.</p>
                                </div>

                            </td>
                        </tr>

                        <!-- Team 2 Name -->
                        <tr valign="top">
                            <th><label for="team-2-name"><?php esc_html_e('Team 2', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input type="text"
                                       id="team-2-name" maxlength="200" size="30" name="team_2_name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The name of team 2.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 2 Logo -->
                        <tr>
                            <th scope="row"><label for="team-2-icon"><?php esc_html_e('Team 2 Logo', 'soccer-live-scores'); ?></label></th>
                            <td>

                                <div class="image-uploader">
                                    <img class="selected-image" src="" style="display: none">
                                    <input type="hidden" id="team-2-logo" maxlength="1000" name="team_2_logo">
                                    <a class="button_add_media" data-set-remove="set" data-set="<?php esc_attr_e('Set image', 'soccer-live-scores'); ?>" data-remove="<?php esc_attr_e('Remove Image', 'soccer-live-scores'); ?>"><?php esc_html_e('Set image', 'soccer-live-scores'); ?></a>
                                    <p class="description"><?php esc_html_e('Select an image that represents this team', 'soccer-live-scores'); ?>.</p>
                                </div>

                            </td>
                        </tr>

                        <tr class="group-trigger" data-trigger-target="additional-score">
                            <th scope="row" class="group-title"><?php esc_attr_e( 'Additional Score', 'soccer-live-scores' ); ?></th>
                            <td>
                                <div class="expand-icon"></div>
                            </td>
                        </tr>

                        <!-- Additional Score Mode -->
                        <tr valign="top" class="additional-score">
                            <th scope="row"><label for="additional-score-mode"><?php esc_html_e('Additional Score Mode', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <select id="additional-score-mode" name="additional_score_mode">
                                    <option value="0" ><?php esc_html_e('Disabled', 'soccer-live-scores'); ?></option>
                                    <option value="1" ><?php esc_html_e('First Leg', 'soccer-live-scores'); ?></option>
                                    <option value="2" ><?php esc_html_e('Aggregate', 'soccer-live-scores'); ?></option>
                                </select>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('Select "Disabled" to hide the additional score section, "First Leg" to display the result of the first leg, or "Aggregate" to display the aggregate result.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 1 First Leg Score -->
                        <tr valign="top" class="additional-score">
                            <th><label for="team-1-first-leg-score"><?php esc_html_e('Team 1 First Leg Score', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input type="text"
                                       id="team-1-first-leg-score" maxlength="2" size="30" name="team_1_first_leg_score"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The score of team 1 in the first leg match.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Team 2 First Leg Score -->
                        <tr valign="top" class="additional-score">
                            <th><label for="team-2-first-leg-score"><?php esc_html_e('Team 2 First Leg Score', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input type="text"
                                       id="team-2-first-leg-score" maxlength="2" size="30" name="team_2_first_leg_score"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The score of team 2 in the first leg match.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <tr class="group-trigger" data-trigger-target="advanced">
                            <th scope="row" class="group-title"><?php esc_attr_e( 'Advanced', 'soccer-live-scores' ); ?></th>
                            <td>
                                <div class="expand-icon"></div>
                            </td>
                        </tr>

                        <!-- Live -->
                        <tr valign="top" class="advanced">
                            <th scope="row"><label for="live"><?php esc_html_e('Live', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <select id="live" name="live">

                                    <option value="0" ><?php esc_html_e('No', 'soccer-live-scores'); ?></option>
                                    <option value="1" ><?php esc_html_e('Yes', 'soccer-live-scores'); ?></option>

                                </select>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('If you select "Yes" the match result and the match events will be updated in real time with AJAX requests.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                    </table>

                    <!-- submit button -->
                    <div class="daext-form-action">
                        <input class="button" type="submit" value="<?php esc_attr_e('Add Match', 'soccer-live-scores'); ?>" >
                    </div>

                <?php endif; ?>

            </div>

        </form>

    </div>

</div>

<!-- Dialog Confirm -->
<div id="dialog-confirm" title="<?php esc_attr_e('Delete the match?', 'soccer-live-scores'); ?>" class="display-none">
    <p><?php esc_attr_e('This match will be permanently deleted and cannot be recovered. Are you sure?', 'soccer-live-scores'); ?></p>
</div>