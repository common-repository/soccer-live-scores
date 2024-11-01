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
$data['id'] = isset($_POST['update_id']) ? intval($_POST['update_id'], 10) : null;
$data['name'] = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : null;
$data['icon'] = isset($_POST['icon']) ? esc_url_raw($_POST['icon']) : null;
$data['goal'] = isset($_POST['goal']) ? intval($_POST['goal'], 10) : null;

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

	//validation on "goal"
	if ( mb_strlen( trim( $data['goal'] ) ) > 2 ) {
		$dismissible_notice_a[] = [
			'message' => __( 'Please enter a valid value in the "Goal" field.', 'soccer-live-scores'),
			'class' => 'error'
		];
		$invalid_data         = true;
	}

}

//update ---------------------------------------------------------------
if( !is_null($data['update_id']) and !isset($invalid_data) ){

    //update the database
    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event_type";
    $safe_sql = $wpdb->prepare("UPDATE $table_name SET 
            name = %s,
            icon = %s,
             goal = %d
        WHERE event_type_id = %d",
        $data['name'],
        $data['icon'],
        $data['goal'],
        $data['id']
    );

    $query_result = $wpdb->query( $safe_sql );

    if($query_result !== false){
        $dismissible_notice_a[] = [
            'message' => __('The event type has been successfully updated.', 'soccer-live-scores'),
            'class' => 'updated'
        ];
    }

}else{

    //add ------------------------------------------------------------------
    if( !is_null($data['form_submitted']) and !isset($invalid_data) ){

        //insert into the database
        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event_type";
        $safe_sql = $wpdb->prepare("INSERT INTO $table_name SET 
            name = %s,
            icon = %s,
             goal = %d",
            $data['name'],
            $data['icon'],
            $data['goal'],
        );

        $query_result = $wpdb->query( $safe_sql );

        if($query_result !== false){
            $dismissible_notice_a[] = [
                'message' => __('The event type has been successfully added.', 'soccer-live-scores'),
                'class' => 'updated'
            ];
        }

    }

}

//delete an item
if( !is_null($data['delete_id']) ){

    //delete this event type only if it's not used in any event.
    if( $this->event_type_is_used($data['delete_id']) ){

        $dismissible_notice_a[] = [
            'message' => __("This event type is associated with one or more events and can't be deleted.", 'soccer-live-scores'),
            'class' => 'error'
        ];

    }else{

        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event_type";
        $safe_sql = $wpdb->prepare("DELETE FROM $table_name WHERE event_type_id = %d ", $data['delete_id']);
        $query_result = $wpdb->query( $safe_sql );

        if($query_result !== false){
            $dismissible_notice_a[] = [
                'message' => __('The event type has been successfully deleted.', 'soccer-live-scores'),
                'class' => 'updated'
            ];
        }

    }

}

//clone a table
if (!is_null($data['clone_id'])) {

    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event_type";
    $wpdb->query("CREATE TEMPORARY TABLE tmptable_1 SELECT * FROM $table_name WHERE event_type_id = " . $data['clone_id']);
    $wpdb->query("UPDATE tmptable_1 SET event_type_id = NULL");
    $wpdb->query("INSERT INTO $table_name SELECT * FROM tmptable_1");
    $wpdb->query("DROP TEMPORARY TABLE IF EXISTS tmptable_1");

}

//get the event data
if(!is_null($data['edit_id'])){

    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_event_type";
    $safe_sql = $wpdb->prepare("SELECT * FROM $table_name WHERE event_type_id = %d ", $data['edit_id']);
    $event_type_obj = $wpdb->get_row($safe_sql);

}


?>

<!-- output -->

<div class="wrap">

    <div id="daext-header-wrapper" class="daext-clearfix">

        <h2><?php esc_html_e('Soccer Live Scores - Event Types', 'soccer-live-scores'); ?></h2>

        <form action="admin.php" method="get" id="daext-search-form">

            <input type="hidden" name="page" value="daextsolisc-event-types">

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

        //retrieve the total number of formations
        global $wpdb;
        $table_name=$wpdb->prefix . $this->shared->get('slug') . "_event_type";
        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $filter");

        //Initialize the pagination class
        require_once( $this->shared->get('dir') . '/admin/inc/class-daextsolisc-pagination.php' );
        $pag = new Daextsolisc_Pagination();
        $pag->set_total_items( $total_items );//Set the total number of items
        $pag->set_record_per_page( 10 ); //Set records per page
        $pag->set_target_page( "admin.php?page=" . $this->shared->get('slug') . "-event-types" );//Set target page
        $pag->set_current_page();//set the current page number from $_GET

        ?>

        <!-- Query the database -->
        <?php
        $query_limit = $pag->query_limit();
        $results = $wpdb->get_results("SELECT * FROM $table_name $filter ORDER BY event_type_id DESC $query_limit ", ARRAY_A); ?>

        <?php if( count($results) > 0 ) : ?>

            <div class="daext-items-container">

                <!-- list of tables -->
                <table class="daext-items">
                    <thead>
                    <tr>
                        <th>
                            <div><?php esc_html_e( 'Event Type ID', 'soccer-live-scores'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e( 'The ID of the event type.', 'soccer-live-scores'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_html_e( 'Name', 'soccer-live-scores'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e( 'The name of the event type.', 'soccer-live-scores'); ?>"></div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach($results as $result) : ?>
                        <tr>
                            <td><?php echo intval($result['event_type_id'], 10); ?></td>
                            <td><?php echo esc_html(stripslashes($result['name'])); ?></td>
                            <td class="icons-container">
                                <form method="POST"
                                      action="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-event-types">
                                    <input type="hidden" name="clone_id" value="<?php echo intval($result['event_type_id'], 10); ?>">
                                    <input class="menu-icon clone help-icon" type="submit" value="">
                                </form>
                                <a class="menu-icon edit" href="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-event-types&edit_id=<?php echo intval($result['event_type_id'], 10); ?>"></a>
                                <form id="form-delete-<?php echo intval($result['event_type_id'], 10); ?>" method="POST" action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-event-types">
                                    <input type="hidden" value="<?php echo intval($result['event_type_id'], 10); ?>" name="delete_id" >
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

        <form method="POST" action="admin.php?page=<?php echo esc_attr($this->shared->get('slug')); ?>-event-types" >

            <input type="hidden" value="1" name="form_submitted">

            <div class="daext-form-container">

                <?php if(!is_null($data['edit_id'])) : ?>

                    <!-- Edit an event -->

                    <div class="daext-form-title"><?php esc_html_e('Edit Event Type', 'soccer-live-scores'); ?> <?php echo intval($event_type_obj->event_type_id, 10); ?></div>

                    <table class="daext-form daext-form-table">

                        <input type="hidden" name="update_id" value="<?php echo intval($event_type_obj->event_type_id, 10); ?>" />

                        <!-- Name -->
                        <tr valign="top">
                            <th><label for="name"><?php esc_html_e('Name', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($event_type_obj->name));?>" type="text"
                                       id="name" maxlength="255" size="30" name="name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The name of the event type.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Icon -->
                        <tr>
                            <th scope="row"><label for="icon"><?php esc_html_e('Icon', 'soccer-live-scores'); ?></label></th>
                            <td>

                                <div class="image-uploader">
                                    <img class="selected-image" src="<?php echo esc_url($event_type_obj->icon); ?>" <?php echo strlen(trim($event_type_obj->icon)) === 0 ? 'style="display: none;"' : ''; ?>>
                                    <input value="<?php echo esc_url($event_type_obj->icon); ?>" type="hidden" id="icon" maxlength="1000" name="icon">
                                    <a class="button_add_media" data-set-remove="<?php echo strlen(trim($event_type_obj->icon)) === 0 ? 'set' : 'remove'; ?>" data-set="<?php esc_attr_e('Set image', 'soccer-live-scores'); ?>" data-remove="<?php esc_attr_e('Remove Image', 'soccer-live-scores'); ?>"><?php echo strlen(trim($event_type_obj->icon)) === 0 ? esc_html__('Set image', 'soccer-live-scores') : esc_html__('Remove Image', 'soccer-live-scores'); ?></a>
                                    <p class="description"><?php esc_html_e('Select an image that represents this event type', 'soccer-live-scores'); ?>.</p>
                                </div>

                            </td>
                        </tr>

                        <!-- Goal -->
                        <tr valign="top">
                            <th><label for="goal"><?php esc_html_e('Goal', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($event_type_obj->goal));?>" type="text"
                                       id="goal" maxlength="2" size="30" name="goal"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The number of goals associated with the event type. This field supports both positive and negative values.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                    </table>

                    <!-- submit button -->
                    <div class="daext-form-action">
                        <input class="button" type="submit" value="<?php esc_attr_e('Update Event Type', 'soccer-live-scores'); ?>" >
                        <input id="cancel" class="button" type="submit" value="<?php esc_attr_e('Cancel', 'soccer-live-scores'); ?>">
                    </div>

                <?php else : ?>

                    <!-- Create new event -->

                    <div class="daext-form-title"><?php esc_html_e('Create New Event Type', 'soccer-live-scores'); ?></div>

                    <table class="daext-form daext-form-table">

                        <!-- Name -->
                        <tr valign="top">
                            <th><label for="name"><?php esc_html_e('Name', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input type="text"
                                       id="name" maxlength="255" size="30" name="name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The name of the event type.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Icon -->
                        <tr>
                            <th scope="row"><label for="icon"><?php esc_html_e('Icon', 'soccer-live-scores'); ?></label></th>
                            <td>

                                <div class="image-uploader">
                                    <img class="selected-image" src="" style="display: none">
                                    <input type="hidden" id="icon" maxlength="1000" name="icon">
                                    <a class="button_add_media" data-set-remove="set" data-set="<?php esc_attr_e('Set image', 'soccer-live-scores'); ?>" data-remove="<?php esc_attr_e('Remove Image', 'soccer-live-scores'); ?>"><?php esc_html_e('Set image', 'soccer-live-scores'); ?></a>
                                    <p class="description"><?php esc_html_e('Select an image that represents this player', 'soccer-live-scores'); ?>.</p>
                                </div>

                            </td>
                        </tr>

                        <!-- Goal -->
                        <tr valign="top">
                            <th><label for="goal"><?php esc_html_e('Goal', 'soccer-live-scores'); ?></label></th>
                            <td>
                                <input value="0" type="text"
                                       id="goal" maxlength="2" size="30" name="goal"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The number of goals associated with the event type. This field supports both positive and negative values.', 'soccer-live-scores'); ?>"></div>
                            </td>
                        </tr>

                    </table>

                    <!-- submit button -->
                    <div class="daext-form-action">
                        <input class="button" type="submit" value="<?php esc_attr_e('Add Event Type', 'soccer-live-scores'); ?>" >
                    </div>

                <?php endif; ?>

            </div>

        </form>

    </div>

</div>

<!-- Dialog Confirm -->
<div id="dialog-confirm" title="<?php esc_attr_e('Delete the event type?', 'soccer-live-scores'); ?>" class="display-none">
    <p><?php esc_attr_e('This event type will be permanently deleted and cannot be recovered. Are you sure?', 'soccer-live-scores'); ?></p>
</div>