<?php

//menu formations view

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
$data['event_id'] = isset($_POST['update_id']) ? intval($_POST['update_id'], 10) : null;
$data['match_id'] = isset($_POST['match_id']) ? intval($_POST['match_id'], 10) : null;
$data['team'] = isset($_POST['team']) ? intval($_POST['team'], 10) : null;
$data['minute'] = isset($_POST['minute']) ? intval($_POST['minute'], 10) : null;
$data['description'] = isset($_POST['description']) ? sanitize_text_field($_POST['description']) : null;
$data['additional_information'] = isset($_POST['additional_information']) ? sanitize_text_field($_POST['additional_information']) : null;
$data['event_type_id'] = isset($_POST['event_type_id']) ? intval($_POST['event_type_id'], 10) : null;

//Validation -----------------------------------------------------------------------------------------------

if( !is_null( $data['update_id'] ) or !is_null($data['form_submitted']) ) {

	//validation on "minute"
	if ( mb_strlen( trim( $data['minute'] ) ) > 3 ) {
		$dismissible_notice_a[] = [
			'message' => __( 'Please enter a valid value in the "Minute" field.', 'soccer-live-scores'),
			'class' => 'error'
		];
		$invalid_data         = true;
	}

    //validation on "description"
    if ( mb_strlen( trim( $data['description'] ) ) === 0 or mb_strlen( trim( $data['description'] ) ) > 255 ) {
        $dismissible_notice_a[] = [
            'message' => __( 'Please enter a valid value in the "Description" field.', 'soccer-live-scores'),
            'class' => 'error'
        ];
        $invalid_data         = true;
    }

	//validation on "additional_information"
	if ( mb_strlen( trim( $data['additional_information'] ) ) > 255 ) {
		$dismissible_notice_a[] = [
			'message' => __( 'Please enter a valid value in the "Additional Information" field.', 'soccer-live-scores'),
			'class' => 'error'
		];
		$invalid_data         = true;
	}

}

//update ---------------------------------------------------------------
if( !is_null($data['update_id']) and !isset($invalid_data) ){

    //update the database
    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
    $safe_sql = $wpdb->prepare("UPDATE $table_name SET 
            match_id = %d,
            team = %d,
            minute = %s,
            description = %s,
            additional_information = %s,
            event_type_id = %s
        WHERE event_id = %d",
        $data['match_id'],
        $data['team'],
        $data['minute'],
        $data['description'],
	    $data['additional_information'],
        $data['event_type_id'],
        $data['event_id'],
    );

    $query_result = $wpdb->query( $safe_sql );

    if($query_result !== false){
        $dismissible_notice_a[] = [
            'message' => __('The event has been successfully updated.', 'soccer-live-scores'),
            'class' => 'updated'
        ];
    }

}else{

    //add ------------------------------------------------------------------
    if( !is_null($data['form_submitted']) and !isset($invalid_data) ){

        //insert into the database
        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
        $safe_sql = $wpdb->prepare("INSERT INTO $table_name SET 
            match_id = %d,
            team = %d,
            minute = %s,
            description = %s,
            additional_information = %s,
            event_type_id = %s",
            $data['match_id'],
            $data['team'],
            $data['minute'],
            $data['description'],
            $data['additional_information'],
            $data['event_type_id'],
        );

        $query_result = $wpdb->query( $safe_sql );

        if($query_result !== false){
            $dismissible_notice_a[] = [
                'message' => __('The event has been successfully added.', 'soccer-live-scores'),
                'class' => 'updated'
            ];
        }

    }

}

//delete an item
if( !is_null($data['delete_id']) ){

    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
    $safe_sql = $wpdb->prepare("DELETE FROM $table_name WHERE event_id = %d ", $data['delete_id']);
    $query_result = $wpdb->query( $safe_sql );

    if($query_result !== false){
        $dismissible_notice_a[] = [
            'message' => __('The event has been successfully deleted.', 'soccer-live-scores'),
            'class' => 'updated'
        ];
    }

}

//clone a table
if (!is_null($data['clone_id'])) {

    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
    $wpdb->query("CREATE TEMPORARY TABLE tmptable_1 SELECT * FROM $table_name WHERE event_id = " . $data['clone_id']);
    $wpdb->query("UPDATE tmptable_1 SET event_id = NULL");
    $wpdb->query("INSERT INTO $table_name SELECT * FROM tmptable_1");
    $wpdb->query("DROP TEMPORARY TABLE IF EXISTS tmptable_1");

}

//get the event data
if(!is_null($data['edit_id'])){

    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event";
    $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE event_id = %d ", $data['edit_id']);
    $event_obj = $wpdb->get_row($safe_sql);

}


?>

<!-- output -->

<div class="wrap">

    <div id="daext-header-wrapper" class="daext-clearfix">

        <h2><?php esc_html_e('Soccer Live Scores - Events', 'soccer-live-scores'); ?></h2>

        <form action="admin.php" method="get" id="daext-search-form">

            <input type="hidden" name="page" value="daextsolisc-events">

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

        <?php

        $blocking_conditions = false;

        //Verify if there is at least one event type
        if($this->shared->get_number_of_event_types() === 0){

	        $blocking_conditions = true;

	        echo '<p>' . esc_html__( 'Please add at least one event type with the', 'soccer-live-scores' ) .
	             ' <a href="' . get_admin_url() . 'admin.php?page=daextsolisc-event-types">' .
	             esc_html__( 'Event Types', 'soccer-live-scores' ) .
	             '</a> ' . esc_html__( 'menu.', 'soccer-live-scores' ) . '</p>';

        }

        //Verify if there is at least one match
        if($this->shared->get_number_of_matches() === 0){

	        $blocking_conditions = true;

	        echo '<p>' . esc_html__( 'Please add at least one match type with the', 'soccer-live-scores' ) .
	             ' <a href="' . get_admin_url() . 'admin.php?page=daextsolisc-matches">' .
	             esc_html__( 'Matches', 'soccer-live-scores' ) .
	             '</a> ' . esc_html__( 'menu.', 'soccer-live-scores' ) . '</p>';

        }

        ?>

        <?php if(!$blocking_conditions) : ?>

	        <?php $this->dismissible_notice($dismissible_notice_a); ?>

            <!-- table -->

	        <?php

	        //create the query part used to filter the results when a search is performed
	        if (!is_null($data['s']) and mb_strlen(trim($data['s'])) > 0) {

		        //create the query part used to filter the results when a search is performed
		        $filter = $wpdb->prepare('WHERE (description LIKE %s)',
			        '%' . $data['s'] . '%');

	        }else{
		        $filter = '';
	        }

	        //retrieve the total number of formations
	        global $wpdb;
	        $table_name=$wpdb->prefix . $this->shared->get('slug') . "_event";
	        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $filter");

	        //Initialize the pagination class
	        require_once( $this->shared->get('dir') . '/admin/inc/class-daextsolisc-pagination.php' );
	        $pag = new Daextsolisc_Pagination();
	        $pag->set_total_items( $total_items );//Set the total number of items
	        $pag->set_record_per_page( 10 ); //Set records per page
	        $pag->set_target_page( "admin.php?page=" . $this->shared->get('slug') . "-events" );//Set target page
	        $pag->set_current_page();//set the current page number from $_GET

	        ?>

            <!-- Query the database -->
	        <?php
	        $query_limit = $pag->query_limit();
	        $results = $wpdb->get_results("SELECT * FROM $table_name $filter ORDER BY event_id DESC $query_limit ", ARRAY_A); ?>

	        <?php if( count($results) > 0 ) : ?>

                <div class="daext-items-container">

                    <!-- list of tables -->
                    <table class="daext-items">
                        <thead>
                        <tr>
                            <th>
                                <div><?php esc_html_e( 'Event ID', 'soccer-live-scores'); ?></div>
                                <div class="help-icon"
                                     title="<?php esc_attr_e( 'The ID of the event.', 'soccer-live-scores'); ?>"></div>
                            </th>
                            <th>
                                <div><?php esc_html_e( 'Match', 'soccer-live-scores'); ?></div>
                                <div class="help-icon"
                                     title="<?php esc_attr_e( 'The match of the event.', 'soccer-live-scores'); ?>"></div>
                            </th>
                            <th>
                                <div><?php esc_html_e( 'Team', 'soccer-live-scores'); ?></div>
                                <div class="help-icon"
                                     title="<?php esc_attr_e( 'The team of the event.', 'soccer-live-scores'); ?>"></div>
                            </th>
                            <th>
                                <div><?php esc_html_e( 'Minute', 'soccer-live-scores'); ?></div>
                                <div class="help-icon" title="<?php esc_attr_e( 'The minute of the event.', 'soccer-live-scores'); ?>"></div>
                            </th>
                            <th>
                                <div><?php esc_html_e( 'Description', 'soccer-live-scores'); ?></div>
                                <div class="help-icon"
                                     title="<?php esc_attr_e( 'The description of the event.', 'soccer-live-scores'); ?>"></div>
                            </th>
                            <th>
                                <div><?php esc_html_e( 'Event Type', 'soccer-live-scores'); ?></div>
                                <div class="help-icon"
                                     title="<?php esc_attr_e( 'The event type.', 'soccer-live-scores'); ?>"></div>
                            </th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

				        <?php foreach($results as $result) : ?>
                            <tr>
                                <td><?php echo intval($result['event_id'], 10); ?></td>
                                <td><?php echo esc_html($this->shared->get_match_name($result['match_id'])); ?></td>
                                <td><?php echo esc_html($this->shared->get_team_name($result['match_id'], $result['team'])); ?></td>
                                <td><?php echo intval($result['minute'], 10); ?></td>
                                <td><?php echo esc_html(stripslashes($result['description'])); ?></td>
                                <td><?php echo esc_html($this->shared->get_event_type_name($result['event_type_id'])); ?></td>
                                <td class="icons-container">
                                    <form method="POST"
                                          action="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-events">
                                        <input type="hidden" name="clone_id" value="<?php echo intval($result['event_id'], 10); ?>">
                                        <input class="menu-icon clone help-icon" type="submit" value="">
                                    </form>
                                    <a class="menu-icon edit" href="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-events&edit_id=<?php echo intval($result['event_id'], 10); ?>"></a>
                                    <form id="form-delete-<?php echo intval($result['event_id'], 10); ?>" method="POST" action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-events">
                                        <input type="hidden" value="<?php echo intval($result['event_id'], 10); ?>" name="delete_id" >
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

            <form method="POST" action="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-events" >

                <input type="hidden" value="1" name="form_submitted">

                <div class="daext-form-container">

			        <?php if(!is_null($data['edit_id'])) : ?>

                        <!-- Edit an event -->

                        <div class="daext-form-title"><?php esc_html_e('Edit Event', 'soccer-live-scores'); ?> <?php echo intval($event_obj->event_id, 10); ?></div>

                        <table class="daext-form daext-form-table">

                            <input type="hidden" name="update_id" value="<?php echo intval($event_obj->event_id, 10); ?>" />

                            <!-- Match -->
                            <tr>
                                <th scope="row"><label for="match"><?php esc_html_e('Match', 'soccer-live-scores'); ?></label></th>
                                <td>
							        <?php

							        $html = '<select id="match-id" name="match_id" class="daext-display-none">';

							        global $wpdb;
							        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
							        $sql        = "SELECT match_id, name FROM $table_name ORDER BY match_id DESC";
							        $match_a = $wpdb->get_results($sql, ARRAY_A);

							        foreach ($match_a as $key => $match) {
								        $html .= '<option value="' . intval($match['match_id'], 10) . '"' . selected($event_obj->match_id,
										        $match['match_id'],
										        false) . '>' . esc_html(stripslashes($match['name'])) . '</option>';
							        }

							        $html .= '</select>';
							        $html .= '<div class="help-icon" title="' . esc_attr__('The match of the event.', 'soccer-live-scores') . '"></div>';

							        echo $html;

							        ?>
                                </td>
                            </tr>

                            <!-- Team -->
                            <tr>
                                <th scope="row"><label for="team"><?php esc_html_e('Match', 'soccer-live-scores'); ?></label></th>
                                <td>
                                    <select id="team" name="team" class="daext-display-none">
                                        <option value="0" <?php echo selected( $event_obj->team, 0 ); ?>><?php esc_html_e('Team 1', 'soccer-live-scores'); ?></option>
                                        <option value="1" <?php echo selected( $event_obj->team, 1 ); ?>><?php esc_html_e('Team 2', 'soccer-live-scores'); ?></option>
                                    </select>
                                    <div class="help-icon" title="<?php esc_attr_e('The team of the event.', 'soccer-live-scores'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Minute -->
                            <tr valign="top">
                                <th><label for="minute"><?php esc_html_e('Minute', 'soccer-live-scores'); ?></label></th>
                                <td>
                                    <input value="<?php echo esc_attr(stripslashes($event_obj->minute));?>" type="text"
                                           id="minute" maxlength="3" size="30" name="minute"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The minute of the event.', 'soccer-live-scores'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Description -->
                            <tr valign="top">
                                <th><label for="description"><?php esc_html_e('Description', 'soccer-live-scores'); ?></label></th>
                                <td>
                                    <input value="<?php echo esc_attr(stripslashes($event_obj->description));?>" type="text"
                                           id="description" maxlength="255" size="30" name="description"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The description of the event.', 'soccer-live-scores'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Additional Information -->
                            <tr valign="top">
                                <th><label for="description"><?php esc_html_e('Additional Information', 'soccer-live-scores'); ?></label></th>
                                <td>
                                    <input value="<?php echo esc_attr(stripslashes($event_obj->additional_information));?>" type="text"
                                           id="description" maxlength="255" size="30" name="additional_information"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('Additional information about the event.', 'soccer-live-scores'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Event Type -->
                            <tr>
                                <th scope="row"><label for="event-type"><?php esc_html_e('Event Type', 'soccer-live-scores'); ?></label></th>
                                <td>
							        <?php

							        $html = '<select id="event-type-id" name="event_type_id" class="daext-display-none">';

							        global $wpdb;
							        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event_type";
							        $sql        = "SELECT event_type_id, name FROM $table_name ORDER BY event_type_id DESC";
							        $event_type_a = $wpdb->get_results($sql, ARRAY_A);

							        foreach ($event_type_a as $key => $event_type) {
								        $html .= '<option value="' . intval($event_type['event_type_id'], 10) . '"' . selected($event_obj->event_type_id,
										        $event_type['event_type_id'],
										        false) . '>' . esc_html(stripslashes($event_type['name'])) . '</option>';
							        }

							        $html .= '</select>';
							        $html .= '<div class="help-icon" title="' . esc_attr__('The event type.', 'soccer-live-scores') . '"></div>';

							        echo $html;

							        ?>
                                </td>
                            </tr>

                        </table>

                        <!-- submit button -->
                        <div class="daext-form-action">
                            <input class="button" type="submit" value="<?php esc_attr_e('Update Event', 'soccer-live-scores'); ?>" >
                            <input id="cancel" class="button" type="submit" value="<?php esc_attr_e('Cancel', 'soccer-live-scores'); ?>">
                        </div>

			        <?php else : ?>

                        <!-- Create new event -->

                        <div class="daext-form-title"><?php esc_html_e('Create New Event', 'soccer-live-scores'); ?></div>

                        <table class="daext-form daext-form-table">

                            <!-- Match -->
                            <tr>
                                <th scope="row"><label for="match"><?php esc_html_e('Match', 'soccer-live-scores'); ?></label></th>
                                <td>
							        <?php

							        $html = '<select id="match-id" name="match_id" class="daext-display-none">';

							        global $wpdb;
							        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_match";
							        $sql        = "SELECT match_id, name FROM $table_name ORDER BY match_id DESC";
							        $match_a = $wpdb->get_results($sql, ARRAY_A);

							        foreach ($match_a as $key => $match) {
								        $html .= '<option value="' . intval($match['match_id'], 10) . '">' . esc_html(stripslashes($match['name'])) . '</option>';
							        }

							        $html .= '</select>';
							        $html .= '<div class="help-icon" title="' . esc_attr__('The match of the event.', 'soccer-live-scores') . '"></div>';

							        echo $html;

							        ?>
                                </td>
                            </tr>

                            <!-- Team -->
                            <tr>
                                <th scope="row"><label for="team"><?php esc_html_e('Team', 'soccer-live-scores'); ?></label></th>
                                <td>
                                    <select id="team" name="team" class="daext-display-none">
                                        <option value="0"><?php esc_html_e('Team 1', 'soccer-live-scores'); ?></option>
                                        <option value="1"><?php esc_html_e('Team 2', 'soccer-live-scores'); ?></option>
                                    </select>
                                    <div class="help-icon" title="<?php esc_attr_e('The team of the event.', 'soccer-live-scores'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Minute -->
                            <tr valign="top">
                                <th><label for="minute"><?php esc_html_e('Minute', 'soccer-live-scores'); ?></label></th>
                                <td>
                                    <input type="text"
                                           id="minute" maxlength="3" size="30" name="minute"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The minute of the event.', 'soccer-live-scores'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Description -->
                            <tr valign="top">
                                <th><label for="description"><?php esc_html_e('Description', 'soccer-live-scores'); ?></label></th>
                                <td>
                                    <input type="text"
                                           id="description" maxlength="255" size="30" name="description"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The description of the event.', 'soccer-live-scores'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Additional Information -->
                            <tr valign="top">
                                <th><label for="description"><?php esc_html_e('Additional Information', 'soccer-live-scores'); ?></label></th>
                                <td>
                                    <input type="text"
                                           id="description" maxlength="255" size="30" name="additional_information"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('Additional information about the event.', 'soccer-live-scores'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Event Type -->
                            <tr>
                                <th scope="row"><label for="event-type"><?php esc_html_e('Event Type', 'soccer-live-scores'); ?></label></th>
                                <td>
							        <?php

							        $html = '<select id="event-type-id" name="event_type_id" class="daext-display-none">';

							        global $wpdb;
							        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event_type";
							        $sql        = "SELECT event_type_id, name FROM $table_name ORDER BY event_type_id DESC";
							        $event_type_a = $wpdb->get_results($sql, ARRAY_A);

							        foreach ($event_type_a as $key => $event_type) {
								        $html .= '<option value="' . intval($event_type['event_type_id'], 10) . '">' . esc_html(stripslashes($event_type['name'])) . '</option>';
							        }

							        $html .= '</select>';
							        $html .= '<div class="help-icon" title="' . esc_attr__('The event type.', 'soccer-live-scores') . '"></div>';

							        echo $html;

							        ?>
                                </td>
                            </tr>

                        </table>

                        <!-- submit button -->
                        <div class="daext-form-action">
                            <input class="button" type="submit" value="<?php esc_attr_e('Add Event', 'soccer-live-scores'); ?>" >
                        </div>

			        <?php endif; ?>

                </div>

            </form>

        <?php endif; ?>

    </div>

</div>

<!-- Dialog Confirm -->
<div id="dialog-confirm" title="<?php esc_attr_e('Delete the event?', 'soccer-live-scores'); ?>" class="display-none">
    <p><?php esc_attr_e('This event will be permanently deleted and cannot be recovered. Are you sure?', 'soccer-live-scores'); ?></p>
</div>