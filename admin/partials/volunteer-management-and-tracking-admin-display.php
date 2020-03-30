<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 */

function vmat_get_events_admin( $args=array() ) {
    global $vmat_plugin;
    $event = $args['event'];
    $orgs = $args['orgs'];
    $event_select_prefix = ' Select ';
    if ( $event ) {
        $event_select_prefix = 'Selected ';
        $event_data = $vmat_plugin->get_common()->get_event_data( $event->ID );
    }
    ?>
    <div class="row">
        <div class="col">
        	<form id='events-filter' method="get">
        		<input type="hidden" name="page" value="vmat_admin_hours">
        		<input type="hidden" name="form_id" value="events-filter">
        		<?php
        		if ( $event ) {
        		?>
        		<div class="row">
        			<div class="col">
        				<?php
        				echo '<a class="button" href="' . add_query_arg( 'page', 'vmat_admin_hours', admin_url( 'admin.php' ) ) . '">' . __('Select another event', 'vmattd') . '</a>';
        				?>
            		</div><!-- col1 select another event if one is already selected -->
            	</div><!-- row -->
            	<?php 
        		}
        		?>
        		<div class="row">
        		    <div class="col">
                		<h2><?php _e( $event_select_prefix . 'Event:', 'vmattd' ); ?></h2>
                	</div><!-- Event header -->
                	<?php
                	if ( $event ) {?>
            			<div class="col">
            				<h2><?php
            				
            				    _e( 'Sponsoring Organization(s):', 'vmattd' );
            				    ?>
            				</h2>
            			</div><!-- Organization header -->
            			<?php
                	}
                	?>
        		</div><!-- row -->
        		<div class="row">
        			<div class="col">
        				<?php
        			if ( $event ) {
        			    echo '<h2>';
        			    _e($event->post_title . '<br />( duration ' . $event_data['days'] . ' days)', 'vmattd');
        			    echo '</h2>';
        			} else {
        			    vmat_events_table( $args );
        			}
        				?>
        			</div><!--  Selected event name or event selection table -->
        			<?php
            			if ( $event ) {
            			    ?>
                			<div class="col">
                				<h2>
                    			    <?php _e($orgs, 'vmattd'); ?>
                    			</h2>
                			</div><!-- Selected organization -->
                			<?php
            			}
            			?>
        		</div><!-- row -->
          	</form><!-- form -->
      	</div>
  	</div>
	<?php
}

function vmat_manage_volunteers_admin( $args=array() ) {
    $event = $args['event'];
    ?>
    <div class="row">
        <div class="col">
        	<form id='volunteers-filter' method="get">
        		<input type="hidden" name="page" value="vmat_admin_hours">
            	<input type="hidden" name="form_id" value="volunteers-filter">
            	<input type="hidden" name="event_id" value="<?php echo $event->ID; ?>">
        		<div class="row">
        			<div class="col-lg-4">
        				<h2><?php _e( 'Add Volunteers:', 'vmattd' ); ?></h2>
        				<?php
        			    vmat_volunteers_table( $args );
        				?>
        			</div><!--  volunteer selection table -->
        			<div class="col">
        				<h2><?php _e( 'Manage Event Volunteer Hours:', 'vmattd' ); ?></h2>
        				<?php
        			    vmat_event_volunteers_table( $args );
        				?>
        			</div><!--  manage hours table -->
    		</div><!-- row -->
    		</form>
      	</div>
  	</div>
	<?php
}

function vmat_events_table( $args ) {
    /*
     * Display an events table
     * 
     */
    global $vmat_plugin;
    $events_query = $args['events'];
    $found_posts = $events_query->found_posts;
    $page = $args['epno'];
    $max_num_pages = $events_query->max_num_pages;
    //$posts_per_page = ceil($found_posts/$max_num_pages);
    $vmat_org = $args['vmat_org'];
    $scope = $args['scope'];
    $search = $args['events_search'];
    $this_page_url = admin_url() . 'admin.php';
    $this_page_url = add_query_arg( 'page', 'vmat_admin_hours', $this_page_url );
    $organizations = array(
        0 => __('View all organizations', 'vmattd'),
    );
    foreach ( $vmat_plugin->get_common()->get_post_type('vmat_organization')->posts as $org ) {
        $organizations[$org->ID] = __($org->post_title, 'vmattd');
    }
    $em_scopes = em_get_scopes();
    $org_pulldown = $vmat_plugin->get_common()->select_options_pulldown(
        'vmat_org',
        $organizations,
        $vmat_org
        );
    $scope_pulldown = $vmat_plugin->get_common()->select_options_pulldown(
        'scope',
        $em_scopes,
        $scope
        );
    $page_name = 'epno';
    $ajax_args = array(
        'admin_page' => 'vmat_admin_hours',
        'scope' => $scope,
        'vmat_org' => $vmat_org,
        'events_search' => $search,
        'posts_per_page' => $args['posts_per_page'],
    );
    $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
        $found_posts,
        $page,
        $max_num_pages,
        $page_name,
        $ajax_args
        );
    ?>
     <form id='events-filter' method="get">
    	<input type="hidden" name="page" value="vmat_admin_hours">
    	<input type="hidden" name="form_id" value="events-filter">
        <div>
        	<p class="search-box">
        	<label class="screen-reader-text" for="post-search-input"><?php _e('Search Events:', 'vmattd')?></label>
        	<input type="text" id="event-search-input" name="events_search" value="<?php 
        	if ( ! empty( $search) ) {
        	    echo $search;
        	} else {
        	    echo '';
        	}
        	?>">&nbsp;
        	</input>
        	<button class="button" name="submit_button" type="submit" value="search_events"><?php _e('Search Events', 'vmattd'); ?></button>
        	</p>
        	<div class="alignleft">
        	<?php
        	echo $org_pulldown;
        	?>
    		</div>
    		<div class="alignleft">
    		<?php 
    		echo $scope_pulldown;
    		?>
    		</div>
    		<div class="alignleft">
    			<button class="button" name="submit_button" type="submit" value="filter_events"><?php _e('Filter', 'vmattd'); ?></button>
    		</div>
    		<div class="tablenav">
    			<?php
    			echo $table_nav;
    			?>
    			<br class="clear"/>
    		</div>
    		<table class="widefat events-table">
    			<thead>
    				<tr>
    					<th><?php _e( 'Name', 'vmattd' ); ?></th>
    					<th><?php _e( 'Orgs', 'vmattd' ); ?></th>
    					<th><?php _e( 'Volunteers', 'vmattd' ); ?></th>
    					<th><?php _e( 'Location', 'vmattd' ); ?></th>
    					<th><?php _e('Date and time', 'vmattd' ); ?></th>
    				</tr>
    			</thead>
    			<tbody>
    			<?php 
    			if ( $events_query->posts ) {
    			    $alternate = 'alternate';
    			    foreach ( $events_query->posts as $event ) {
    			        echo $vmat_plugin->get_common()->event_row(
    			            $event,
    			            $this_page_url,
    			            $alternate
    			            );
    			        if ( empty( $alternate ) ) {
    			            $alternate = 'alternate';
    			        } else {
    			            $alternate = '';
    			        }
    			    }
    			} else {
    			    echo '<tr><td>';
    			    _e( 'No events found', 'vmattd');
    			    echo '</td></tr>';
    			}
    			?>
    			</tbody>
    		</table>
        </div>
    </form>
    <?php
}

function vmat_volunteers_table( $args ) {
    /*
     * Display a volunteers table
     *
     */
    global $vmat_plugin;
    $event = $args['event'];
    $user_query = $args['volunteers'];
    $volunteers = $user_query->results;
    $found_users = $user_query->total_users;
    $page = $args['vpno'];
    $evpno = $args['evpno'];
    $max_num_pages = ceil( $found_users / $args['posts_per_page'] );
    $search = $args['volunteers_search'];
    
    $page_name = 'vpno';
    $ajax_args = array(
        'event_id' => $event->ID,
        'evpno' => $evpno,
        'volunteers_search' => $search,
        'event_volunteers_search' => $args['event_volunteers_search'],
        'admin_page' => 'vmat_admin_hours',
        'posts_per_page' => $args['posts_per_page'],
    );
    $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
        $found_users,
        $page,
        $max_num_pages,
        $page_name,
        $ajax_args
        );
    ?>
	<div class="row">
		<div class="col-md-auto search-box">
        	<label class="screen-reader-text" for="volunteer-search-input"><?php _e('Search', 'vmattd')?></label>
        	<input type="text" id="post-search-input" name="volunteers_search" value="<?php 
        	if ( ! empty( $search) ) {
        	    echo $search;
        	} else {
        	    echo '';
        	}
        	?>">&nbsp;
        	</input>
        	<button class="button" name="submit_button" type="submit" value="search_volunteers"><?php _e('Search', 'vmattd'); ?></button>
		</div>
	</div>
    <div class="row">
    	<div class="col">
    		<div class="alignleft actions bulkactions">
				<button id="do_volunteers_bulk_action" class="button action" type="button" value="bulk_add_volunteers_to_event" disabled><?php _e( 'Bulk Add Hours ', 'vmattd')?>&raquo;</button>
			</div>
    	</div>
    </div>
    <div class="row">
    	<div class="col">
    		<div class="tablenav alignright">
    			<?php
    			echo $table_nav;
    			?>
    			<br class="clear"/>
    		</div>
    	</div>
    </div>
    <div class="row">
    	<div class="col">
    		<table class="widefat" id="vmat_volunteers_table">
    			<thead>
    				<tr>
    					<td class="manage-column column-cb check-column"><input id="vmat_volunteers_select_all" type=checkbox></td>
    					<td class="manage-column"><?php _e( 'User', 'vmattd' ); ?></td>
    					<td class="manage-column"><?php _e( 'Email', 'vmattd' ); ?></td>
    				</tr>
    			</thead>
    			<tbody>
    			<?php 
    			if ( $volunteers ) {
    			    $alternate = 'alternate';
    			    foreach ( $volunteers as $volunteer ) {
    			        echo $vmat_plugin->get_common()->volunteer_row( $volunteer, $alternate );
    			        if ( empty( $alternate ) ) {
    			            $alternate = 'alternate';
    			        } else {
    			            $alternate = '';
    			        }
    			    }
    			} else {
    			    echo '<tr><td>';
    			    _e( 'No volunteers found', 'vmattd');
    			    echo '</td></tr>';
    			}
    			?>
    			</tbody>
    		</table>
    	</div>
    </div>
    <?php
}

function vmat_event_volunteers_table( $args ) {
    /*
     * Display a volunteers hours management table
     *
     */
    global $vmat_plugin;
    $event = $args['event'];
    $event_data = $vmat_plugin->get_common()->get_event_data( $event->ID );
    $ev_query = $args['event_volunteers'];
    $volunteers = $ev_query->results;
    $found_users = $ev_query->total_users;
    $page = $args['evpno'];
    $vpno = $args['vpno'];
    $max_num_pages = ceil( $found_users / $args['posts_per_page'] );
    $search = $args['event_volunteers_search'];
    $page_name = 'evpno';
    $ajax_args = array(
        'event_id' => $event->ID,
        'vpno' => $vpno,
        'admin_page' => 'vmat_admin_hours',
        'volunteers_search' => $args['volunteers_search'],
        'event_volunteers_search' => $args['event_volunteers_search'],
        'posts_per_page' => $args['posts_per_page'],
    );
    $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
        $found_users,
        $page,
        $max_num_pages,
        $page_name,
        $ajax_args
        );
    ?>
	<div class="row">
		<div class="col-md-auto search-box">
        	<label class="screen-reader-text" for="post-search-input"><?php _e('Search', 'vmattd')?></label>
        	<input type="text" id="event-volunteer-search-input" name="event_volunteers_search" value="<?php 
        	if ( ! empty( $search) ) {
        	    echo $search;
        	} else {
        	    echo '';
        	}
        	?>">&nbsp;
        	</input>
        	<button class="button" name="submit_button" type="submit" value="search_event_volunteers"><?php _e('Search', 'vmattd'); ?></button>
		</div>
	</div>
    <div class="row">
    	<div class="col">
    		<div class="alignleft actions bulkactions">
				<label for="event-volunteers-bulk-action-selector-top" class="screen-reader-text">' <?php _e( 'Select bulk action', 'vmattd' ); ?>'</label>
				<select name="event_volunteers_bulk_action" id="event-volunteers-bulk-action-selector">
                	<option value="-1">Bulk Actions</option>
                	<option value="default" ><?php _e( 'Set Default Hours', 'vmattd' ); ?></option>
                	<option value="approve" ><?php _e( 'Approve Hours', 'vmattd' ); ?></option>
                	<option value="remove" ><?php _e( 'Remove Hours', 'vmattd' ); ?></option>
                	<option value="save" ><?php _e( 'Save Changes', 'vmattd' ); ?></option>
                </select>
				<button id="do_event_volunteers_bulk_action" class="button action" type="button" value="bulk_event_volunteers_action" disabled><?php _e( 'Apply', 'vmattd')?></button>
			</div>
    	</div>
    </div>
    <div class="row">
    	<div class="col">
    		<div id="event_volunteers_added_status">&nbsp;</div>
    	</div>
    	<div class="col">
    		<div class="tablenav alignright">
    			<?php
    			echo $table_nav;
    			?>
    			<br class="clear"/>
    		</div>
    	</div>
    </div>
    <div class="row">
    	<div class="col">
    		<table class="widefat" id="vmat_event_volunteers_table">
    			<thead>
    				<tr>
    					<td class="manage-column column-cb check-column"><input id="vmat_event_volunteers_select_all" type=checkbox></td>
    					<td class="manage-column"><?php _e('User', 'vmattd' );?></td>
    					<td class="manage-column"><?php _e('Hours/Day', 'vmattd' );?></td>
    					<td class="manage-column"><?php _e('Start', 'vmattd' );?></td>
    					<td class="manage-column"><?php _e('Vol. Days (' . $event_data['days'] . ')', 'vmattd' );?></td>
    					<td class="manage-column"><?php _e('Approved', 'vmattd' );?></td>
    				</tr>
    			</thead>
    			<tbody>
    			<?php 
    			if ( $volunteers ) {
    			    $alternate = 'alternate';
    			    foreach ( $volunteers as $volunteer ) {
    			        echo $vmat_plugin->get_common()->event_volunteer_row( $volunteer, $event->ID, $alternate );
    			        if ( empty( $alternate ) ) {
    			            $alternate = 'alternate';
    			        } else {
    			            $alternate = '';
    			        }
    			    }
    			} else {
    			    echo '<tr><td>';
    			    _e( 'No event volunteers found', 'vmattd');
    			    echo '</td></tr>';
    			}
    			?>
    			</tbody>
    		</table>
    	</div>
    </div>
    <?php
}
