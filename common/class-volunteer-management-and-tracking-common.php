<?php

/**
 * The common functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the common stylesheet and JavaScript.
 *
 */
class Volunteer_Management_And_Tracking_Common {

	/**
	 * The ID of this plugin.
	 *
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 */
	private $version;
	
	/*
	 * Name => [type, label, required] for each common user field
	 */
	private $wp_required_user_fields = [
	    'user_login' => [
	        'type' => 'text',
	        'label' => 'Username',
	        'required' => true,
	    ],
	    'email' => [
	        'type' => 'email',
	        'label' => 'Email',
	        'required' => true,
	    ],
	];
	
	/*
	 * Name => [type, label, required] for each common user field
	 */
	private $common_user_fields = [
	    'first_name' => [
	        'type' => 'text',
	        'label' => 'First Name',
	        'required' => true,
	    ],
	    'last_name' => [
	        'type' => 'text',
	        'label' => 'Last Name',
	        'required' => false,
	    ],
	];
	
	/**
	 * Name => [type, label, required] for each volunteer user field
	 *
	 */
	private $volunteer_user_fields = [
	    '_vmat_volunteer_type' => [
	        'type' => 'array',
	        'label' => 'Volunteer Type',
	        'required' => false,
	    ],
	    '_vmat_phone_cell' => [
	        'type' => 'text', 
	        'label' => 'Phone (cell]', 
	        'required' => false,
	    ],
	    '_vmat_phone_landline' => [
	        'type' => 'text', 
	        'label' => 'Phone (landline]', 
	        'required' => false,
	    ],
	    '_vmat_address_street' => [
	        'type' => 'text', 
	        'label' => 'Street Address', 
	        'required' => false,
	    ],
	    '_vmat_address_city' => [
	        'type' => 'text', 
	        'label' => 'City', 
	        'required' => false,
	    ],
	    '_vmat_address_zipcode' => [
	        'type' => 'text', 
	        'label' => 'Zip Code', 
	        'required' => false,
	    ],
	    '_vmat_volunteer_skillsets' => [
	        'type' => 'array', 
	        'label' => 'Skillsets', 
	        'required' => false,
	    ],
	    '_vmat_volunteer_interests' => [
	        'type' => 'array', 
	        'label' => 'Interests', 
	        'required' => false,
	    ],
	];

	/**
	 * Initialize the class and set its properties.
	 *
	 * param      string    $plugin_name       The name of the plugin.
	 * param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	private function get_volunteer_values_from_post($post) {
	    $values = [];
	    foreach ( $this->volunteer_user_fields as $field => $aspects ) {
	        if ( $aspects['type'] == 'boolean' ) {
	            $default = false;
	        } elseif ($aspects['type'] == 'text' ) {
	            $default = '';
	        } elseif ($aspects['type'] == 'array') {
	            $default = [];
	        }
	        $values[$field] = $default;
	        if ( ! empty( $post[$field] ) ) {
	            $values[$field] = $post[$field];
	        }
	    }
	    return $values;
	}
	
	private function get_common_values_from_post($post) {
	    $values = [];
	    foreach ( $this->common_user_fields as $field => $aspects ) {
	        if ( $aspects['type'] == 'boolean' ) {
	            $default = false;
	        } elseif ($aspects['type'] == 'text' ) {
	            $default = '';
	        } elseif ($aspects['type'] == 'array') {
	            $default = [];
	        }
	        $values[$field] = $default;
	        if ( ! empty( $post[$field] ) ) {
	            $values[$field] = $post[$field];
	        }
	    }
	    return $values;
	}
	
	private function get_default_volunteer_values() {
	    $values = [];
	    foreach ( $this->volunteer_user_fields as $field => $aspects ) {
	        if ( $aspects['type'] == 'boolean' ) {
	            $default = false;
	        } elseif ($aspects['type'] == 'text' ) {
	            $default = '';
	        } elseif ($aspects['type'] == 'array') {
	            $default = [];
	        }
	        $values[$field] = $default;
	    }
	    return $values;
	}
	
	private function get_default_common_values() {
	    $values = [];
	    foreach ( $this->common_user_fields as $field => $aspects ) {
	        if ( $aspects['type'] == 'boolean' ) {
	            $default = false;
	        } elseif ($aspects['type'] == 'text' ) {
	            $default = '';
	        } elseif ($aspects['type'] == 'array') {
	            $default = [];
	        }
	        $values[$field] = $default;
	    }
	    return $values;
	}

	private function get_default_wp_required_values() {
	    $values = [];
	    foreach ( $this->wp_required_user_fields as $field => $aspects ) {
	        if ( $aspects['type'] == 'boolean' ) {
	            $default = false;
	        } elseif ($aspects['type'] == 'text' ||
	            $aspects['type'] == 'email') {
	            $default = '';
	        } elseif ($aspects['type'] == 'array') {
	            $default = [];
	        }
	        $values[$field] = $default;
	    }
	    return $values;
	}
	
	private function get_volunteer_values_from_umeta($user) {
	    $values = [];
	    foreach ( $this->volunteer_user_fields as $field => $aspects ) {
	        if ( $aspects['type'] == 'boolean' ) {
	            $default = false;
	        } elseif ($aspects['type'] == 'text' ) {
	            $default = '';
	        } elseif ($aspects['type'] == 'array') {
	            $default = [];
	        }
	        $values[$field] = $default;
	        $value = get_the_author_meta( $field, $user->ID );
	        if ( ! empty( $value ) ) {
	            $values[$field] = $value;
	        }
	    }
	    return $values;
	}
	
	private function get_common_values_from_user($user) {
	    $values = [];
	    foreach ( $this->common_user_fields as $field => $aspects ) {
	        if ( $aspects['type'] == 'boolean' ) {
	            $default = false;
	        } elseif ($aspects['type'] == 'text' ) {
	            $default = '';
	        } elseif ($aspects['type'] == 'array') {
	            $default = [];
	        }
	        $values[$field] = $default;
	        $value = $user->{ $field };
	        if ( ! empty( $value ) ) {
	            $values[$field] = $value;
	        }
	    }
	    return $values;
	}
	
	private function get_wp_required_values_from_user($user) {
	    $values = [];
	    foreach ( $this->wp_required_user_fields as $field => $aspects ) {
	        if ( $aspects['type'] == 'boolean' ) {
	            $default = false;
	        } elseif ($aspects['type'] == 'text' ) {
	            $default = '';
	        } elseif ($aspects['type'] == 'array') {
	            $default = [];
	        }
	        $values[$field] = $default;
	        $user_field = $field;
	        if ( $field == 'email' ) {
	            $user_field = 'user_email';
	        }
	        $value = $user->{ $user_field };
	        if ( ! empty( $value ) ) {
	            $values[$field] = $value;
	        }
	    }
	    return $values;
	}
    
	private function render_volunteer_fields($section_type, $user=null) {
	    /*
	     * display user's volunteer meta values
	     * $section_type switches between <div> based and <table> based layouts
	     * if $user passed in populate fields with user's meta information
	     */
	    $page = '';
	    $reg_fields_display = 'none';
	    if ( $section_type == 'table' ) {
	        $section = 'tr';
	    } else {
	        $section = 'div';
	    }
	    if( $user ) {
	        // request to populate fields from a volunteer
	        $values = $this->get_volunteer_values_from_umeta($user);
	    } else {
	        $values = $this->get_volunteer_values_from_post( $_POST );
	    }
	    $reg_fields_display = 'show';
	    if ( $section_type == 'table' ) {
	        $page .= '<table class="form-table" role="presentation">';
	    }
	    foreach ( $this->volunteer_user_fields as $field => $aspects ) {
	        $field_class = '';
	        if ( $aspects['type'] == 'text' ||
	            $aspects['type'] == 'email' ) {
	                $field_class='form-field';
	            }
            $page .= '<' . $section .  ' class="vmat-registration-fields ' . $field_class . '"
                                         style="display:' . $reg_fields_display . '">';
	        if ( $aspects['type'] == 'boolean' ) {
	            $page .= $this->boolean_choice_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'value' => $values[$field],
	                'required' => $aspects['required'],
	            ]);
	        } elseif ( $aspects['type'] == 'text' ) {
	            $page .= $this->text_input_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'value' => $values[$field],
	                'required' => $aspects['required']
	            ]);
	        } elseif ( $aspects['type'] == 'array' ) {
	            $category_id = get_cat_ID( $aspects['label'] );
	            if ($category_id) {
	                $subcategories = get_terms( array(
	                    'taxonomy' => 'category',
	                    'child_of' => $category_id,
	                    'hide_empty' => false,)
	                    );
	                if ( count ( $subcategories ) ) {
	                    $page .= $this->multiselect_children_of_category_fields(
	                        ['category' => $aspects['label'],
	                            'subcategories' => $subcategories,
	                            'type' => $section_type,
	                            'value' => $values[$field],
	                        ]);
	                }
	            } else if ( $field == '_vmat_volunteer_type' ) {
	                // this is not a category
	                ob_start();
	                $this->link_posts_to_user_pulldown( substr( $field, 1 ) , $section_type, $user );
	                $page .= ob_get_clean();
	            }
	        }
	        $page .= '</' . $section . '>';
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '</table>';
	    }
	    return $page;
	}
		
	private function render_common_fields($section_type) {
	    /*
	     * display user's common meta values
	     * $section_type switches between <div> based and <table> based layouts
	     * if $user passed in populate fields with user's meta information
	     */
	    
	    $page = '';
	    if ( $section_type == 'table' ) {
	        $section = 'tr';
	    } else {
	        $section = 'div';
	    }
	    $values = $this->get_common_values_from_post($_POST);
	    if ( $section_type == 'table' ) {
	        $page .= '<table class="form-table" role="presentation">';
	    }
	    foreach ( $this->common_user_fields as $field => $aspects ) {
	        $field_class = '';
	        if ( $aspects['type'] == 'text' ||
	            $aspects['type'] == 'email' ) {
	                $field_class='form-field';
	            }
	        $page .= '<' . $section . ' class="' . $field_class . '">';
	        if ( $aspects['type'] == 'boolean' ) {
	            $page .= $this->boolean_choice_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'value' => $values[$field],
	                'required' => $aspects['required'],
	            ]);
	        } elseif ( $aspects['type'] == 'text' ) {
	            $page .= $this->text_input_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'value' => $values[$field],
	                'required' => $aspects['required']
	            ]);
	        } elseif ( $aspects['type'] == 'array' ) {
	            $page .= $this->multiselect_children_of_category_registration_fields(
	                ['category' => $aspects['label'],
	                    'type' => $section_type,
	                    'value' => $values[$field],
	                ]);
	        }
	        $page .= '</' . $section . '>';
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '</table>';
	    }
	    return $page;
	}
	
	private function render_volunteer_fields_for_ajax($section_type, $user=null ) {
	    /*
	     * display user's volunteer meta values
	     * $section_type switches between <div> based and <table> based layouts
	     */
	    $page = '';
	    $reg_fields_display = 'none';
	    if ( $section_type == 'table' ) {
	        $section = 'tr';
	    } else {
	        $section = 'div';
	    }
	    $is_volunteer = true;
	    if( $user ) {
	        $values = $this->get_volunteer_values_from_umeta($user);
	    } else {
	        $values = $this->get_default_volunteer_values();
	    }
	    if ( $is_volunteer ) {
	        $reg_fields_display = 'show';
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '<table class="form-table" role="presentation">';
	    }
	    foreach ( $this->volunteer_user_fields as $field => $aspects ) {
	        $field_class = '';
	        if ( $aspects['type'] == 'text' ||
	            $aspects['type'] == 'email' ) {
	                $field_class='form-field';
	            }
            $page .= '<' . $section .  ' class="vmat-registration-fields ' . $field_class . '"
                                         style="display:' . $reg_fields_display . '">';
	        if ( $aspects['type'] == 'boolean' ) {
	            $page .= $this->boolean_choice_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'value' => $values[$field],
	                'required' => $aspects['required'],
	            ]);
	        } elseif ( $aspects['type'] == 'text' ||
	                   $aspects['type'] == 'email' ) {
	            $page .= $this->text_input_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'input_type' => $aspects['type'],
	                'value' => $values[$field],
	                'required' => $aspects['required']
	            ]);
	        } elseif ( $aspects['type'] == 'array' ) {
	            $category_id = get_cat_ID( $aspects['label'] );
	            if ($category_id) {
	                $subcategories = get_terms( array(
	                    'taxonomy' => 'category',
	                    'child_of' => $category_id,
	                    'hide_empty' => false,)
	                    );
	                if ( count ( $subcategories ) ) {
	                    $page .= $this->multiselect_children_of_category_fields(
	                        ['category' => $aspects['label'],
	                            'subcategories' => $subcategories,
	                            'type' => $section_type,
	                            'value' => $values[$field],
	                        ]);
	                }
	            } else if ( $field == '_vmat_volunteer_type' ) {
	                // this is not a category
	                ob_start();
	                $this->link_posts_to_user_pulldown( substr( $field, 1 ), $section_type, $user );
	                $page .= ob_get_clean();
	            }
	        }
	        $page .= '</' . $section . '>';
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '</table>';
	    }
	    return $page;
	}
	
	private function render_wp_required_fields_for_ajax( $section_type, $user=null ) {
	    /*
	     * display user's WP required meta values
	     * $section_type switches between <div> based and <table> based layouts
	     * if $user passed in populate fields with user's meta information
	     */
	    
	    $page = '';
	    if ( $section_type == 'table' ) {
	        $section = 'tr';
	    } else {
	        $section = 'div';
	    }
	    if( $user ) {
	        $values = $this->get_wp_required_values_from_user($user);
	    } else {
	        $values = $this->get_default_wp_required_values();
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '<table class="form-table" role="presentation">';
	    }
	    foreach ( $this->wp_required_user_fields as $field => $aspects ) {
	        $field_class = '';
	        if ( $aspects['type'] == 'text' ||
	            $aspects['type'] == 'email' ) {
	            $field_class='form-field';
	        }
	        $page .= '<' . $section . ' class="' . $field_class . '">';
	        if ( $aspects['type'] == 'boolean' ) {
	            $page .= $this->boolean_choice_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'value' => $values[$field],
	                'required' => $aspects['required'],
	            ]);
	        } elseif ( $aspects['type'] == 'text' ||
	                   $aspects['type'] == 'email' ) {
	            $page .= $this->text_input_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'input_type' => $aspects['type'],
	                'value' => $values[$field],
	                'required' => $aspects['required']
	            ]);
	        } elseif ( $aspects['type'] == 'array' ) {
	            $page .= $this->multiselect_children_of_category_registration_fields(
	                ['category' => $aspects['label'],
	                    'type' => $section_type,
	                    'value' => $values[$field],
	                ]);
	        }
	        $page .= '</' . $section . '>';
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '</table>';
	    }
	    return $page;
	}
	
	private function render_common_fields_for_ajax( $section_type, $user=null ) {
	    /*
	     * display user's common meta values
	     * $section_type switches between <div> based and <table> based layouts
	     * if $user passed in populate fields with user's meta information
	     */
	    
	    $page = '';
	    if ( $section_type == 'table' ) {
	        $section = 'tr';
	    } else {
	        $section = 'div';
	    }
	    if( $user ) {
	        $values = $this->get_common_values_from_user($user);
	    } else {
	        $values = $this->get_default_common_values();
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '<table class="form-table" role="presentation">';
	    }
	    foreach ( $this->common_user_fields as $field => $aspects ) {
	        $field_class = '';
	        if ( $aspects['type'] == 'text' ||
	            $aspects['type'] == 'email' ) {
	                $field_class='form-field';
	            }
	        $page .= '<' . $section . ' class="' . $field_class . '">';
	        if ( $aspects['type'] == 'boolean' ) {
	            $page .= $this->boolean_choice_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'value' => $values[$field],
	                'required' => $aspects['required'],
	            ]);
	        } elseif ( $aspects['type'] == 'text' ||
	                   $aspects['type'] == 'email' ) {
	            $page .= $this->text_input_field(['option_name' => $field,
	                'label' => $aspects['label'],
	                'type' => $section_type,
	                'input_type' => $aspects['type'],
	                'value' => $values[$field],
	                'required' => $aspects['required']
	            ]);
	        } elseif ( $aspects['type'] == 'array' ) {
	            $page .= $this->multiselect_children_of_category_registration_fields(
	                ['category' => $aspects['label'],
	                    'type' => $section_type,
	                    'value' => $values[$field],
	                ]);
	        }
	        $page .= '</' . $section . '>';
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '</table>';
	    }
	    return $page;
	}
		
	private function volunteer_registration_errors($errors) {
	    /*
	     * Check for errors on volunteer user registration
	     */
        /*
         * add error checking here
         */
	    return $errors;
	}
	
	private function common_registration_errors($errors) {
	    /*
	     * Check for errors on user registration
	     */
	    if( empty( $_POST['first_name'] ) ) {
	        $errors->add( 'vmat_first_name_error', __( '<strong>ERROR</strong>: Please enter your first name.', 'vmattd' ) );
	    }
        /*
         * add error checking here
         */
	    return $errors;
	}
	
	public function add_volunteer_user_role( $user_id ) {
        $wpuser = get_user_by('id', $user_id);
        if ( $wpuser ) {
            if ( $wpuser ) {
                if( ! in_array('volunteer', $wpuser->roles)) {
                    $wpuser->add_role('volunteer');
                }
            }
        }
	}
	
	public function add_volunteer_to_em_event( $em_event_id, $user_id ) {
	   global $wpdb;
	   $sql .= 'SELECT post_id FROM ' . EM_EVENTS_TABLE . ' WHERE event_id=%d';
	   $event_id = $wpdb->get_var( $wpdb->prepare( $sql, $em_event_id ) );
	   return $this->add_volunteer_to_event( $event_id, $user_id );
	}
	
	public function add_volunteer_to_event( $event_id, $user_id ) {
	    global $vmat_plugin;
	    $hours = null;
	    if( $event_id > 0 && $user_id ) {
	        $args = array(
	            'author' => $user_id,
	            'post_type' => 'vmat_hours',
	            'post_status' => 'publish',
	            'meta_query' => array(
	                array(
	                    'key' => '_event_id',
	                    'value' => $event_id,
	                )
	            )
	        );
	        $hours = new WP_Query( $args );
	        if( ! $hours->posts ) {
	            // hours are not already assigned to the event
	            $event_data = $vmat_plugin->get_common()->get_event_data( $event_id );
	            $args = array(
	                    'author' => $user_id,
	                    'post_type' => 'vmat_hours',
	                    'post_status' => 'publish',
	                    'post_title' => $event_data['title'],
	                    'meta_input' => array(
	                        '_event_id' => $event_id ,
	                        '_volunteer_start_date' => $event_data['iso_start_date'],
	                        '_hours_per_day' => $event_data['hours_per_day'],
	                        '_volunteer_num_days' => 0,
	                        '_approved' => false,
	                    )
	                );
	            // Create a new vmat_hours post with the appropriate default information
	            $hours = wp_insert_post( $args );
	        }
	    }
	    return $hours;
	}
	
	public function link_posts_to_user_pulldown( $type='vmat_organization', $section_type='div', $user=null ) {
	    /*
	     * Generic function to create a list for selecting multiple $type
	     * CPT posts to a user
	     */
	    if ( ! $type ) {
	        return false;
	    }
	    global $vmat_plugin;
	    $posts_to_link = array();
	    $selected_posts_to_link = array();
	    $unselected_posts_to_link = array();
	    foreach ( $vmat_plugin->get_common()->get_post_type($type)->posts as $post_to_link ) {
	        if ( $user && in_array( absint($post_to_link->ID), get_post_meta( $user->ID, '_' . $type , true ) ) ) {
	            $selected_posts_to_link[] = array(
	                'id' => $post_to_link->ID,
	                'name' => __($post_to_link->post_title, 'vmattd')
	            );
	        } else {
	            $unselected_posts_to_link[] = array(
	                'id' => $post_to_link->ID,
	                'name' => __($post_to_link->post_title, 'vmattd')
	            );
	        }
	    }
	    foreach( $selected_posts_to_link as $post_to_link ) {
	        $posts_to_link[] = $post_to_link;
	    }
	    foreach( $unselected_posts_to_link as $post_to_link ) {
	        $posts_to_link[] = $post_to_link;
	    }
	    if ( $section_type != 'div') {?>
	        <th><?php _e( 'Choose Volunteer Type', 'vmattd' ); ?></th>
	        <td><?php
	    }
	    ?>
	    <fieldset>
	    <?php
	    if ( $section_type == 'div' ) {?>
	        <legend><?php _e( 'Choose your type', 'vmattd' ); ?></legend>
	        <?php
	    }?>
	    <div id="<?php echo $type; ?>_checklist">
	    <?php
	    foreach( $posts_to_link as $post_to_link ){
	        ?>
            <label>
            <input type="checkbox" name="<?php echo '_' . $type; ?>[]" 
            value="<?php echo $post_to_link['id']; ?>" 
            <?php if ( $user && in_array( absint($post_to_link['id']), get_user_meta( $user->ID, '_' . $type , true ) ) ) { echo 'checked="checked"';} ?> /> 
            <?php echo $post_to_link['name'] ?>
            </label><br />          
            <?php
        }
        ?>
        </div>
		</fieldset>
		<?php
        if ( $section_type != 'div' ) {?>
            </td>
            <?php
        }
	}
	
	public function  boolean_choice_field($args) {
	    /*
	     * display a boolean choice field
	     */
	    
	    $page = '';
	    $var_name = $args['option_name'];
	    $label = $args['label'];
	    $type = 'div';
	    $var_value = false;
	    if ( ! empty( $args['type'] ) ) {
	        $type = $args['type'];
	    }
	    if ( array_key_exists( 'value', $args ) ) {
	        $var_value = boolval($args['value']);
	    } else {
	        $var_value = false;
	        /*
	         * retain selection values in case of repost due to error
	         */
	        if ( array_key_exists( $var_name, $_POST ) ) {
	            $var_value = boolval($_POST[$var_name]);
	        }
	    }
	    if ( $type == 'div' ) {
	        $page .= '<label for="' . esc_attr($var_name) . '">' . __($label, 'vmattd') . '</label>';
	    } else {
	        $page .= '<th>' . __($label, 'vmattd') . '</th>';
	        $page .= '<td>';
	    }
	    $page .= '<input type="checkbox"
		       id="' . esc_attr($var_name) . '"
		       name="' . esc_attr($var_name) . '" ';
	    if ( $var_value ) {
	        $page .= "checked";
	    }
	    $page .= '/>';
	    if ( $type != 'div' ) {
	        $page .= '</td>';
	    }
	    return $page;
	}
	
	public function text_input_field($args) {
	    /*
	     * display a text input field
	     */
	    $page = '';
	    $var_name = $args['option_name'];
	    
	    $label = $args['label'];
	    $type = 'div';
	    if ( ! empty( $args['type'] ) ) {
	        $type = $args['type'];
	    }
	    $required = false;
	    if( ! empty( $args['required'] ) ) {
	        $required = boolval($args['required']);
	    }
	    if ( array_key_exists( 'value', $args ) ) {
	        $var_value = strval($args['value']);
	    } else {
	        $var_value = '';
	        /*
	         * retain selection values in case of repost due to error
	         */
	        if ( array_key_exists( $var_name, $_POST ) ) {
	            $var_value = strval($_POST[$var_name]);
	        }
	    }
	    if ( $type == 'div' ) {
	        $page .= '<label for="' . esc_attr($var_name) . '">' .
	   	        __($label, 'vmattd');
	   	        if ( $required )
	   	        {
	   	            $page .= '<span class="description"> (required)</span>';
	   	        }
	   	        $page .= '</label>';
	    } else {
	        $page .= '<th>' .
	   	        __($label, 'vmattd');
	   	        if ( $required )
	   	        {
	   	            $page .= '<span class="description"> (required)</span>';
	   	        }
	   	        $page .= '</th>';
	   	        $page .= '<td>';
	    }
	    
	    $page .= '<input class="input vmat-form-field" type="' . $args['input_type'] . '"
		       id="' . esc_attr($var_name) . '"
		       name="' . esc_attr($var_name) . '"
		       value="' . $var_value . '" ';
	    if ( $required ) {
	        $page .= 'required';
	    }
	    $page .= '/>';
	    if ( $type != 'div' ) {
	        $page .= '</td>';
	    }
	    return $page;
	}
	
	public function multiselect_children_of_category_fields($args) {
	    /*
	     * Display a multiselect populated from children of the passed-in $category
	     * The $category is derived from the post taxonomy 'category'
	     */
	    
	    /*
	     * retain selection values in c !ase of repost due to error
	     */
	    $category = $args['category'];
	    $subcategories = $args['subcategories'];
	    $type = 'div';
	    if ( ! empty( $args['type'] ) ) {
	        $type = $args['type'];
	    }
	    $page = '';
	    $ms_var_name = '_vmat_volunteer_' . strtolower($category);
	    if ( array_key_exists( 'value', $args ) ) {
	        $ms_var = array_map('strval', $args['value']);
	    } else {
	        $ms_var = [];
	        /*
	         * retain selection values in case of repost due to error
	         */
	        if ( array_key_exists( $ms_var_name, $_POST ) ) {
	            $ms_var = $_POST[$ms_var_name];
	            $ms_var = array_map('strval', $ms_var);
	        }
	    }
	    if ( $type != 'div') {
	        $page .= '<th>Choose your ' . $category . '</th>';
	        $page .= '<td>';
	    }
	    $page .= '<fieldset>';
	    if ( $type == 'div' ) {
	        $page .= '<legend>Choose your ' . $category . '</legend>';
	    }
	    foreach ($subcategories as $subcategory) {
	        $page .= '<div>';
	        $page .= '<input type="checkbox"
                	       id="' . 'vmat-' . esc_attr( $subcategory->slug ) . '"
                	       name="' . $ms_var_name . '[]"
                	       value="' . esc_attr( $subcategory->name ) . '" ';
	        if ( in_array($subcategory->name, $ms_var) ) {
	            $page .= "checked";
	        }
	        $page .= '/>';
	        $page .= '<label for="vmat-' . esc_attr( $subcategory->slug ) . '">' . esc_html( __($subcategory->name, 'vmattd') ) . '</label>';
	        $page .= '</div>';
	    }
	    $page .= '</fieldset>';
	    if ( $type != 'div' ) {
	        $page .= '</td>';
	    }
	    return $page;
	} // multiselect_children_of_category_fields
	
	public function get_post_type( $type='vmat_organization' ) {
	    $args = array(
	        'post_type' => $type,
	        'nopaging' => true,
	        'orderby' => 'post_title',
	        'order' => 'ASC'
	    );
	    return new WP_Query( $args );
	}
	
	public function get_volunteers_added_to_event( $args=array() ) {
	    global $vmat_plugin;
	    $event = $args['event'];
	    $ev_args = array(
	        'event_id' => $event->ID,
	        'event_volunteers_search' => $args['event_volunteers_search'],
	    );
	    $event_volunteers = $vmat_plugin->get_common()->get_event_volunteers( $ev_args );
	    $event_volunteer_ids = array();
	    foreach ( $event_volunteers as $event_volunteer ) {
	        $event_volunteer_ids[] = $event_volunteer['WP_User']->ID;
	    }
	    if ( empty( $event_volunteer_ids ) ) {
	        $event_volunteer_ids[] = 0;
	    }
	    $user_query_args = array(
	        'count_total' => true,
	        'include' => $event_volunteer_ids,
	        'paged' => absint( $args['evpno'] ),
	        'number' => absint( $args['posts_per_page'] ),
	        'orderby' => 'display_name',
	    );
	    return new WP_User_Query( $user_query_args );
	}
	
	public function get_volunteers_not_added_to_event( $args=array() ) {
	    // find the volunteers that are already associated with this event, nopaging=true the results
	    global $vmat_plugin;
	    $event = $args['event'];
	    $ev_args = array(
	        'event_id' => $event->ID,
	        'nopaging' => true,
	        'paged' => 1,
	        'fields' => 'ids'
	    );
	    $ev_query = $vmat_plugin->get_common()->get_event_volunteers( $ev_args );
	    $event_volunteer_ids = $ev_query->results;
	    // exclude the volunteers associated with this event from the list
	    $volunteer_args = array(
	        'role' => 'volunteer',
	        'exclude' => $event_volunteer_ids,
	    );
	    $search = '';
	    if ( array_key_exists( 'volunteers_search', $args ) ) {
	        $search = $args['volunteers_search'];
	    }
	    $vmat_org = 0;
	    if ( array_key_exists( 'vmat_org', $args ) ) {
	        $vmat_org = $args['vmat_org'];
	    }
	    $volunteers = get_users( $volunteer_args );
	    $volunteers = $vmat_plugin->get_common()->filter_volunteers( $volunteers, $search, $vmat_org );
	    
	    $volunteer_ids = array();
	    foreach ( $volunteers as $volunteer ) {
	        $volunteer_ids[] = $volunteer['WP_User']->ID;
	    }
	    if ( empty( $volunteer_ids ) ) {
	        $volunteer_ids[] = 0;
	    }
	    $user_query_args = array(
	        'count_total' => true,
	        'include' => $volunteer_ids,
	        'paged' => absint( $args['vpno'] ),
	        'number' => absint( $args['posts_per_page'] ),
	        'orderby' => 'display_name',
	    );
	    return new WP_User_Query( $user_query_args );
	}
	
	public function get_volunteer_hours( $args=array() ) {
	    // find the hours that are already associated with this volunteer, nopaging=true the results
	    $volunteer = $args['volunteer'];
	    $search = '';
	    if ( array_key_exists( 'manage_volunteer_search', $args ) ) {
	        $search = $args['manage_volunteer_search'];
	    }
	    $hours_args = array(
	        'count_total' => true,
	        'post_type' => 'vmat_hours',
	        'author' => $volunteer->ID,
	        'paged' => $args['hpno'],
	        's' => $search,
	    );
	    $hours_query = new WP_Query( $hours_args );
	    return $hours_query;
	}
	
	public function filter_volunteers( $volunteers, $volunteers_search='', $vmat_org=0 ) {

	    $volunteers = array_map( function ( $a ) { return array('WP_User'=>$a);}, $volunteers);
	    foreach( $volunteers as $key=>$user ) {
	        // get additional data about each user
	        $orgs = $this->get_volunteers_data( array( $user['WP_User'] ) )[$user['WP_User']->ID]['orgs'];
	        $meta_values = get_user_meta( $volunteers[$key]['WP_User']->ID );
	        $volunteers[$key]['WP_User'] = get_userdata( $volunteers[$key]['WP_User']->ID );
	        $meta_values = array_filter(
	            array_map(
	                function( $a ) {
	                    return $a[0];
	                },
	                $meta_values )
	            );
	        $volunteers[$key]['usermeta'] = $meta_values;
	        $volunteers[$key]['search'] = $volunteers_search;
	        $volunteers[$key]['orgs'] = $orgs;
	        $volunteers[$key]['vmat_org'] = $vmat_org;
	    }
	    // filter the users based on the search criteria
	    $volunteers = array_filter( $volunteers,
	        function ( $user ) {
	            $like = '/.*' . $user['search'] . '.*/i';
	            $found_org = true;
	            if( $user['vmat_org'] != 0 ) {
	               $found_org = in_array( $user['vmat_org'], $user['orgs'] );
	            }
	            return
	            $found_org && 
	            ( preg_match( $like, $user['WP_User']->display_name ) === 1 ||
	            preg_match( $like, $user['WP_User']->data->user_email ) === 1 ||
	            preg_match( $like, $user['WP_User']->data->user_login ) === 1 ||
	            preg_match( $like, $user['WP_User']->data->user_nicename ) === 1 ||
	            preg_match( $like, $user['usermeta']['first_name'] ) === 1 ||
	            preg_match( $like, $user['usermeta']['last_name'] ) === 1 ||
	            preg_match( $like, $user['usermeta']['nickname'] ) === 1 );
	        }
	        );
	    return $volunteers;
	}
	
	public function get_number_event_volunteers ( $event ) {
        $found = 0;
	    if ( $event ) {
	        $hours_args = array(
	            'post_type' => 'vmat_hours',
	            'nopaging' => true,
	            'fields' =>'ids',
	            'meta_query' => array(
	                array(
	                    'key' => '_event_id',
	                    'value' => $event->ID,
	                    'type' => 'NUMERIC',
	                    'compare' => '=',
	                ),
	            ),
	        );
	        $hours_query = new WP_Query( $hours_args );
	        $found = $hours_query->found_posts;
	    }
	    return $found;
	}
	
	public function get_event_volunteers ( $args=array() ) {
	    $event_id = $args['event_id'];
	    $fields = '';
	    if ( array_key_exists( 'fields', $args ) ) {
	        $fields = $args['fields'];
	    }
	    $search = '';
	    if ( array_key_exists( 'event_volunteers_search', $args ) ) {
	        $search = $args['event_volunteers_search'];
	    }
	    if ( $event_id ) {
	        $hours_args = array(
	            'post_type' => 'vmat_hours',
	            'nopaging' => true,
	            'meta_query' => array(
	                array(
                        'key' => '_event_id',
                        'value' => $event_id,
	                   ),
	                ),
	        );
	        $hours_query = new WP_Query( $hours_args );
	        $volunteer_ids = array();
	        foreach( $hours_query->posts as $hours_post ) {
	            $volunteer_ids[] = $hours_post->post_author;
	        }
	        $event_volunteer_args = array(
	            'include' => array(0),
	            'orderby' => 'display_name',
	        );
	        if ( $fields ) {
	            $event_volunteer_args['fields'] = $fields;
	        }
	        if ( $volunteer_ids ) {
	            $event_volunteer_args['include'] = $volunteer_ids;
	        }
	    }
	    $ev_query = new WP_User_Query( $event_volunteer_args );
	    if ( $fields ==  'ids' ) {
	        return $ev_query;
	    }
	    return $this->filter_volunteers( $ev_query->results, $search );
	}
	
	public function get_all_volunteer_hours( $volunteer ) {
	    $args = array(
	        'post_type' => 'vmat_hours',
	        'author' => $volunteer->ID,
	        'nopaging' => true,
	    );
	    $hours_query = new WP_Query( $args );
	    return $hours_query->posts;
	}
	
	public function get_event_organizations_string( $event_id ) {
	    $organizations = get_post_meta( $event_id, '_vmat_organizations', true );
	    return $this->get_organizations_string_from_array( $organizations );
	}
	
	public function get_organizations_string_from_array( $organizations ) {
	    if ( is_array( $organizations ) && count($organizations) > 0 ) {
	        // found the organizations, only select the first one for the pulldown
	        $organizations = get_posts(array(
	        'post_type' => 'vmat_organization',
	        'include' => $organizations,
	        'nopaging' =>  true
	        ));
	        if ( is_array( $organizations ) && count($organizations) > 0 ) {
	            $orgs = array();
	            foreach ( $organizations as $org ) {
	                $orgs[] = $org->post_title;
	            }
	            $orgs = implode( ',', $orgs);
	        }
	    } else {
	        $orgs = 'None';
	    }
	    return $orgs;
	}
	
	public function get_organization_funding_streams ( $organization_id=0 ) {
	    $org_funding_streams = array();
	    if ( $organization_id ) {
	        $org_funding_streams = get_post_meta( $organization_id, '_vmat_funding_streams', true);
	    }
	    return $org_funding_streams;
	}
	
	public function select_options_pulldown ( $name, $options, $selected='' ) {
	    /*
	     * $name = name of the select
	     * $options = array of option value=>name
	     * $selected = selected value, 0 if unselected
	     */
	    $output = '';
	    $output .= '<select name="' . $name . '" id="_vmat_' . $name . '" class="postform">';
	    foreach ( $options as $opt_value=>$opt_name ) {
	        $option_selected = 'selected="selected"';
	        if ( $opt_value != $selected ) {
	            $option_selected = '';
	        }
	        $output .= '<option  value="' . $opt_value . '" ' . $option_selected .'>' . $opt_name . '</option>';
	    }
	    $output .= '</select>';
	    return $output;
	}
	
	public function get_event_data( $event_id=0 ) {
	    $days = 0;
	    if ( $event_id ) {
	        $event_meta = get_post_meta( $event_id, false);
	        $end_date = date_create_from_format('Y-m-d', $event_meta['_event_end_date'][0]);
	        $start_date=date_create_from_format('Y-m-d', $event_meta['_event_start_date'][0]);
	        $start_time = date_create_from_format('H:i:s', $event_meta['_event_start_time'][0]);
	        $end_time=date_create_from_format('H:i:s', $event_meta['_event_end_time'][0]);
	        $days = $end_date->diff($start_date)->days + 1;
	        $hours_per_day = $end_time->diff($start_time)->h;
	        $organizations = get_post_meta( $event_id, '_vmat_organizations', true );
	        if( empty( $organizations ) ) {
	           $organizations = array();
	        }
	    }
	    return array( 
	        'title' => get_post_field( 'post_title', $event_id ),
	        'days' => $days, 
	        'iso_start_date' => date_format( $start_date, 'Y-m-d' ),
	        'iso_end_date' => date_format( $end_date, 'Y-m-d' ),
	        'mdY_start_date' => date_format( $start_date, 'm/d/Y' ),
	        'mdY_end_date' => date_format( $end_date, 'm/d/Y' ),
	        'start_date' => date_format( $start_date, 'M m, Y' ),
	        'end_date' => date_format( $end_date, 'M m, Y' ),
	        'iso_start_time' => date_format( $start_time, 'H:i:s' ),
	        'iso_end_time' => date_format( $end_time, 'H:i:s' ),
	        'start_time' => date_format( $start_time, 'g:i a' ),
	        'end_time' => date_format( $end_time, 'g:i a' ),
	        'hours_per_day' => $hours_per_day,
	        'organizations' => $organizations,
	    );
	}
	
	public function get_funding_stream_data( $funding_stream_id=0 ) {
	    $days = 0;
	    $result = array(
	        'description' => '',
	        'days' => 0,
	        'iso_start_date' => 'None',
	        'iso_end_date' => 'None',
	        'start_date' => 'None',
	        'end_date' => 'None',
	        'start_end_string' => 'None',
	    );
	    if ( $funding_stream_id ) {
	        $funding_stream_meta = get_post_meta( $funding_stream_id, false);
	        $result['description'] = $funding_stream_meta['_description'][0];
	        $end_date = date_create_from_format('Y-m-d', $funding_stream_meta['_funding_end_date'][0]);
	        $start_date=date_create_from_format('Y-m-d', $funding_stream_meta['_funding_start_date'][0]);
	        if( $start_date && $end_date ) {
	            $days = $end_date->diff($start_date)->days + 1;
	            $result['days'] = $days;
	            $result['iso_start_date'] = date_format( $start_date, 'Y-m-d' );
	            $result['iso_end_date'] = date_format( $end_date, 'Y-m-d' );
	            $result['start_date'] = date_format( $start_date, 'm/d/Y' );
	            $result['end_date'] = date_format( $end_date, 'm/d/Y' );
	            $result['start_end_string'] = date_format( $start_date, 'M d, Y' ) . ' - ' . date_format( $end_date, 'M d, Y' );
	        }
	    }
	    return $result;
	}
	
	public function get_organization_data( $organization_id=0 ) {
	    $result = array(
	        'description' => '',
	    );
	    if ( $organization_id ) {
	        $organization_meta = get_post_meta( $organization_id, false);
	        $result['description'] = $organization_meta['_description'][0];
	        $funding_streams = $this->get_organization_funding_streams( $organization_id );
	        $funding_streams = array_map( function( $funding_id ) {
	            $fs = get_post_field( 'post_title', $funding_id );
	            return $fs;
	        },
	        $funding_streams);
	        $fs_string = implode( ',', $funding_streams );
	        if ($fs_string == '' ) {
	            $fs_string = 'None';
	        }
	        $result['funding_streams_string'] = $fs_string;
	    }
	    return $result;
	}
	
	public function get_volunteers_data( $volunteers=array() ) {
	    $volunteer_ids = array_map( function($volunteer) {
	        return $volunteer->ID;
	    }, 
	    $volunteers);
	    $args = array(
	        'no_found_rows' => true,
	        'post_type' => 'vmat_hours',
	        'author__in' => $volunteer_ids,
	        'nopaging' => true,
	    );
	    $return = array();
	    foreach( $volunteer_ids as $volunteer_id ) {
	       $return[$volunteer_id]['orgs'] = array(); 
	       $return[$volunteer_id]['approved']=array(
	           'num_events' => 0,
	           'num_hours' => 0,
	           'num_days' => 0,
	       );
	       $return[$volunteer_id]['unapproved']=array(
	           'num_events' => 0,
	           'num_hours' => 0,
	           'num_days' => 0,
	       );
	    }
	    
	    $volunteers_hours_query = new WP_Query( $args );
	    foreach( $volunteers_hours_query->posts as $hour) {
	        $volunteer_id = $hour->post_author;
	        $organizations = get_post_meta(  get_post_meta( $hour->ID, '_event_id', true ), '_vmat_organizations', true );
	        $approved = absint( get_post_meta( $hour->ID, '_approved', true ) );
	        $num_days = absint( get_post_meta( $hour->ID, '_volunteer_num_days', true ) );
	        $num_hours = absint( get_post_meta( $hour->ID, '_hours_per_day', true ) );
	        if( $approved == 1 ) {
	            $return[$volunteer_id]['approved']['num_days'] = $return[$volunteer_id]['approved']['num_days'] + $num_days;
	            $return[$volunteer_id]['approved']['num_hours'] = $return[$volunteer_id]['approved']['num_hours'] + $num_days * $num_hours;
	            if( ($num_hours * $num_days) > 0 ) {
	                $return[$volunteer_id]['approved']['num_events'] = $return[$volunteer_id]['approved']['num_events'] + 1;
	                if($organizations){
	                    $return[$volunteer_id]['orgs'] = array_unique( array_merge( $return[$volunteer_id]['orgs'], $organizations ) );
	                }
	            }
	        } else {
	            $return[$volunteer_id]['unapproved']['num_days'] = $return[$volunteer_id]['unapproved']['num_days'] + $num_days;
	            $return[$volunteer_id]['unapproved']['num_hours'] = $return[$volunteer_id]['unapproved']['num_hours'] + $num_days * $num_hours;
	            if( ($num_hours * $num_days) > 0 ) {
	                $return[$volunteer_id]['unapproved']['num_events'] = $return[$volunteer_id]['unapproved']['num_events'] + 1;
	                if($organizations){
	                    $return[$volunteer_id]['orgs'] = array_unique( array_merge( $return[$volunteer_id]['orgs'], $organizations ) );
	                }
	            }
	        }
	    }
	    return $return;
	}
	
	public function admin_paginate( $num_items, $page, $max_num_pages, $page_link='', $page_id='pno' ) {
	    if ($page > 2 ) {
	        $first_page_button = ' <a class="first-page button" href="' . $page_link . '&' . $page_id . '=' . 1 . '"><span class="screen-reader-text">First page</span><span aria-hidden="true">&laquo;</span></a>';
	    } else {
	        $first_page_button = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
	    }
	    if ($page > 1 ) {
	        $previous_page_button = ' <a class="prev-page button" href="' . $page_link . '&' . $page_id . '=' . absint($page - 1) .'"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">&lsaquo;</span></a>';
	    } else {
	        $previous_page_button = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
	    }
	    if ($max_num_pages > 1 ) {
	        $selected_page = '<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" type="text" name="' . $page_id . '" value="' . $page . '" size="2" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">' . $max_num_pages . '</span></span></span>';
	    }
	    if ($page < $max_num_pages ) {
	        $next_page_button = ' <a class="next-page button" href="' . $page_link . '&' . $page_id . '=' . absint($page + 1) .'"><span class="screen-reader-text">Next page</span><span aria-hidden="true">&rsaquo;</span></a>';
	    } else {
	        $next_page_button = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
	    }
	    if ($page < $max_num_pages - 1 ) {
	        $last_page_button = ' <a class="last-page button" href="' . $page_link . '&' . $page_id. '=' . $max_num_pages .'"><span class="screen-reader-text">Last page</span><span aria-hidden="true">&raquo;</span></a>';
	    } else {
	        $last_page_button = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
	    }
	    $output = '';
	    $output .= '<div class="tablenav-pages"><span class="displaying-num">' . $num_items . ' items</span>';
	    $output .= '<span class="pagination-links">';
	    if ( $max_num_pages > 1 ) {
	        $output .= $first_page_button;
	        $output .= $previous_page_button;
	        $output .= $selected_page;
	        $output .= $next_page_button;
	        $output .= $last_page_button;
	    }
	    $output .= '</span>';
	    $output .= '</div>';
	    return $output;
	}
	
	public function ajax_admin_paginate( $num_items, $page, $max_num_pages, $page_name='vpno', $ajax_args=array() ) {
	    $ajax_data_attributes = array();
	    foreach( $ajax_args as $key => $value ) {
	        $ajax_data_attributes[] = $key . ':' . $value;
	    }
	    $ajax_data_attributes = implode( ',', $ajax_data_attributes );
	    if ($page > 2 ) {
	        $ajax_data_attributes_first = $ajax_data_attributes . ',' . $page_name . ':' . 1;
	        $first_page_button = ' <span class="first-page button vmat-ajax-paginate" ajax_data_attributes="' . $ajax_data_attributes_first . '"><span class="screen-reader-text">First page</span><span aria-hidden="true">&laquo;</span></span>';
	    } else {
	        $first_page_button = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
	    }
	    if ($page > 1 ) {
	        $ajax_data_attributes_previous = $ajax_data_attributes . ',' . $page_name . ':' . absint($page - 1);
	        $previous_page_button = ' <span class="prev-page button vmat-ajax-paginate" ajax_data_attributes="' . $ajax_data_attributes_previous . '"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">&lsaquo;</span></span>';
	    } else {
	        $previous_page_button = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
	    }
	    if ($max_num_pages > 1 ) {
	        $ajax_data_attributes_this_page = $ajax_data_attributes . ',page_name:' . $page_name; 
	        $selected_page = '<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input type="number" min="1" max="' . $max_num_pages . '" class="current-page vmat-ajax-paginate" id="current-page-selector" type="text" ajax_data_attributes="' . $ajax_data_attributes_this_page . '" name="' . $page_name . '" value="' . $page . '" size="2" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">' . $max_num_pages . '</span></span></span>';
	    }
	    if ($page < $max_num_pages ) {
	        $ajax_data_attributes_next = $ajax_data_attributes . ',' . $page_name . ':' . absint($page + 1);
	        $next_page_button = ' <span class="next-page button vmat-ajax-paginate" ajax_data_attributes="' . $ajax_data_attributes_next . '"><span class="screen-reader-text">Next page</span><span aria-hidden="true">&rsaquo;</span></span>';
	    } else {
	        $next_page_button = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
	    }
	    if ($page < $max_num_pages - 1 ) {
	        $ajax_data_attributes_last = $ajax_data_attributes .  ',' . $page_name . ':' . $max_num_pages;
	        $last_page_button = ' <span class="last-page button vmat-ajax-paginate" ajax_data_attributes="' . $ajax_data_attributes_last . '"><span class="screen-reader-text">Last page</span><span aria-hidden="true">&raquo;</span></span>';
	    } else {
	        $last_page_button = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
	    }
	    $output = '';
	    $output .= '<div class="tablenav-pages"><span class="displaying-num">' . $num_items . ' items</span>';
	    $output .= '<span class="pagination-links">';
	    if ( $max_num_pages > 1 ) {
	        $output .= $first_page_button;
	        $output .= $previous_page_button;
	        $output .= $selected_page;
	        $output .= $next_page_button;
	        $output .= $last_page_button;
	    }
	    $output .= '</span>';
	    $output .= '</div>';
	    return $output;
	}
	
	public function event_display( $event ) {
	    /*
	     * return a grid row for an event
	     * $event = event post object
	     */
	    global $wpdb;
	    $private = '';
	    if( $event->post_status === 'private' ) {
	        $private = ' - <span class="post-state">Private</span>';
	    }
	    
	    $location_id = get_post_meta( $event->ID, '_location_id', true);
	    $event_data = $this->get_event_data( $event->ID );
	    $event_start_date = $event_data['start_date'];
	    $event_end_date = $event_data['end_date'];
	    if ( $event_start_date == $event_end_date ) {
	        $event_end_date = '';
	    } else {
	        $event_end_date = ' - ' . $event_end_date;
	    }
	    $event_start_time = $event_data['start_time'];
	    $event_end_time = $event_data['end_time'];
	    if ( $event_start_time == $event_end_time ) {
	        $event_end_time = '';
	    } else {
	        $event_end_time = ' - ' . $event_end_time;
	    }
	    $event_edit_href = admin_url( 'post.php');
	    $event_edit_href = add_query_arg( 'post', $event->ID, $event_edit_href );
	    $event_edit_href = add_query_arg( 'action', 'edit', $event_edit_href );
	    $location = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . EM_LOCATIONS_TABLE . ' WHERE location_id=%s', $location_id ), ARRAY_A);
	    // get event sponsoring organizations
	    $orgs = $this->get_event_organizations_string( $event->ID );
	    $output = '<div id="vmat_event_display_admin">';
	    $output .= '<table class="widefat">';
	    $output .= '<thead>';
	    $output .= '<tr>';
	    $output .= '<th>';
	    $output .= 'Event';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Sponsoring Organization';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Volunteers';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Location';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Date &amp; Time';
	    $output .= '</th>';
	    $output .= '</tr>';
	    $output .= '</thead>';
	    $output .= '<tr>';
	    $output .= '<td>';
	    $output .= '<strong>'. $event->post_title . '</strong>';
	    $output .= $private;
	    $output .= '<div>';
	    $output .= '<a id="vmat_event_' . $event->ID . '" href="' . $event_edit_href . '">' . __('Edit', 'vmattd') . '</a>';
	    $output .= '</div>';
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= '<strong>' . $orgs . '</strong>';
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $this->get_number_event_volunteers( $event );
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= '<strong>' . $location['location_name'] . '</strong>';
	    $output .= '<br />';
	    $output .= $location['location_address'] . ' - ' . $location['location_town'];
	    $output .= '</td>';
	    $output .= '<td>'; // dates and time
	    $output .= $event_start_date . $event_end_date . '<br />';
	    $output .= $event_start_time . $event_end_time;
	    $output .= '</td>';
	    $output .= '</tr>';
	    $output .= '</table>';
	    $output .= '</div>';
	    return $output;
	}
	
	public function volunteer_display( $volunteer ) {
	    /*
	     * return a grid row for a volunteer
	     * $event = event post object
	     */
	    $output = '<div id="vmat_volunteer_display_admin">';
	    $output .= '<table class="widefat">';
	    $output .= '<thead>';
	    $output .= '<tr>';
	    $output .= '<th>';
	    $output .= 'Volunteer';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Email';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Orgs.';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Events Vol. (apprvd)';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Days Vol. (apprvd)';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Hours. Vol. (apprvd)';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Events Vol. (not apprvd)';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Days Vol. (not apprvd)';
	    $output .= '</th>';
	    $output .= '<th>';
	    $output .= 'Hours. Vol. (not apprvd)';
	    $output .= '</th>';
	    $output .= '</tr>';
	    $output .= '</thead>';
	    $output .= $this->volunteer_row( $volunteer, $this->get_volunteers_data( array( $volunteer ) )[$volunteer->ID] );
	    $output .= '</table>';
	    $output .= '</div>';
	    return $output;
	}
	
	public function volunteer_row( $volunteer, $volunteer_data, $submit_url='', $alternate='' ) {
	    
	    $orgs = $volunteer_data['orgs'];
	    $approved_events = $volunteer_data['approved']['num_events'];
	    $approved_hours = $volunteer_data['approved']['num_hours'];
	    $approved_days = $volunteer_data['approved']['num_days'];
	    $unapproved_events = $volunteer_data['unapproved']['num_events'];
	    $unapproved_hours = $volunteer_data['unapproved']['num_hours'];
	    $unapproved_days = $volunteer_data['unapproved']['num_days'];
	    $display_name = $volunteer->first_name . ' ' . $volunteer->last_name;
	    if( $display_name == '' ) {
	        $display_name = $volunteer->data->user_login;
	    }
	    $output = '<tr class="' . $alternate . '" id="volunteer_' . $volunteer->ID . '">';
	    if( $submit_url != '' ) {
	        $output .= '<th class="check-column">';
	        $output .= '<input type="checkbox" id="vmat_manage_volunteer_cb_' . $volunteer->ID . '" name="manage_volunteers_checked[]">';
	        $output .= '</th>';
	    }
	    $output .= '<td>';
	    if( $submit_url != '' ) {
	        $output .= '<a class="row-title" href="' . $submit_url . '">' . $display_name . '</a>';
	    } else {
	        $output .= '<strong>'. $volunteer->first_name . ' ' . $volunteer->last_name . '</strong>';
	        $output .= '<div class="row-actions">';
	        $output .= '<span class="vmat-link vmat-quick-link" id="show_update_volunteer_form" title="Edit volunteer">' . __('Edit', 'vmattd') . '</span>';
	        $output .= '</div>';
	    }
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $volunteer->data->user_email;
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $this->get_organizations_string_from_array( $orgs );
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $approved_events;
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $approved_days;
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $approved_hours;
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $unapproved_events;
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $unapproved_days;
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= $unapproved_hours;
	    $output .= '</td>';
	    $output .= '</tr>';
	    return $output;
	   
	}
	
	public function event_row( $event, $this_page_url, $alternate='' ) {
	    /* 
	     * return a table row for an event
	     * $event = event post object
	     * $this_page_url = url to the page that will allow take event_id as an arg
	     */
	    global $wpdb;
	    $private = '';
	    if( $event->post_status === 'private' ) {
	        $private = ' - <span class="post-state">Private</span>';
	    }
	    $location_id = get_post_meta( $event->ID, '_location_id', true);
	    $event_data = $this->get_event_data( $event->ID );
	    $event_start_date = $event_data['start_date'];
	    $event_end_date = $event_data['end_date'];
	    if ( $event_start_date == $event_end_date ) {
	        $event_end_date = '';
	    } else {
	        $event_end_date = ' - ' . $event_end_date;
	    }
	    $event_start_time = $event_data['start_time'];
	    $event_end_time = $event_data['end_time'];
	    if ( $event_start_time == $event_end_time ) {
	        $event_end_time = '';
	    } else {
	        $event_end_time = ' - ' . $event_end_time;
	    }
	    $submit_url = add_query_arg( 'event_id', $event->ID, $this_page_url );
	    $event_edit_href = admin_url( 'post.php');
	    $event_edit_href = add_query_arg( 'post', $event->ID, $event_edit_href );
	    $event_edit_href = add_query_arg( 'action', 'edit', $event_edit_href );
	    $location = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . EM_LOCATIONS_TABLE . ' WHERE location_id=%s', $location_id ), ARRAY_A);
	    // get event sponsoring organizations
	    $orgs = $this->get_event_organizations_string( $event->ID );
	    $output = '<tr class="event ' . $alternate . '" id="event_' . $event->ID . '">';
	    $output .= '<td>';
	    $output .= '<strong>';
	    $output .= '<a class="row-title" href="' . $submit_url . '">' . $event->post_title . '</a>';
	    $output .= $private;
	    $output .= '</strong>';
	    $output .= '<div class="row-actions">';
	    $output .= '<a id="vmat_event_' . $event->ID . '" href="' . $event_edit_href . '"><span class="vmat-quick-link">' . __('Edit', 'vmattd') . '</span></a>';
	    $output .= '</div>';
	    $output .= '</td>';
	    $output .= '<td><b>';
	    $output .= $orgs;
	    $output .= '</b></td>';
	    $output .= '<td>';
	    $output .= $this->get_number_event_volunteers( $event );
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= '<b>';
	    $output .= $location['location_name'];
	    $output .= '</b><br />';
	    $output .= $location['location_address'] . ' - ' . $location['location_town'];
	    $output .= '</td>';
	    $output .= '<td>'; // dates and time
	    $output .= $event_start_date . $event_end_date . '<br />';
	    $output .= $event_start_time . $event_end_time;
	    $output .= '</td>';
	    return $output;
	}
	
	public function manage_volunteer_event_row( $volunteer, $event, $alternate='' ) {
	    /*
	     * return a table row for an event
	     * $event = event post object
	     * $this_page_url = url to the page that will allow take event_id as an arg
	     */
	    global $wpdb;
	    $location_id = get_post_meta( $event->ID, '_location_id', true);
	    $event_data = $this->get_event_data( $event->ID );
	    $event_start_date = $event_data['iso_start_date'];
	    $event_end_date = $event_data['iso_end_date'];
	    if ( $event_start_date == $event_end_date ) {
	        $event_end_date = '';
	    } else {
	        $event_end_date = ' - ' . $event_end_date;
	    }
	    $event_start_time = $event_data['iso_start_time'];
	    $event_end_time = $event_data['iso_end_time'];
	    if ( $event_start_time == $event_end_time ) {
	        $event_end_time = '';
	    } else {
	        $event_end_time = ' - ' . $event_end_time;
	    }
	    $location = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . EM_LOCATIONS_TABLE . ' WHERE location_id=%s', $location_id ), ARRAY_A);
	    // get event sponsoring organizations
	    $orgs = $this->get_event_organizations_string( $event->ID );
	    $output = '<tr class="event ' . $alternate . '" id="event_' . $event->ID . '">';
	    $output .= '<td>';
	    $output .= '<strong>';
	    $output .= '<span class="vmat-link" data_action="add" event_id="' . $event->ID . '"' .
            	    ' volunteer_id="' . $volunteer->ID . '" title="Add Event">' . __( $event->post_title, 'vmattd') . '</span>';
	    $output .= '</strong>';
	    $output .= '</td>';
	    $output .= '<td><b>';
	    $output .= $orgs;
	    $output .= '</b></td>';
	    $output .= '<td>';
	    $output .= $this->get_number_event_volunteers( $event );
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= '<b>';
	    $output .= $location['location_name'];
	    $output .= '</b><br />';
	    $output .= $location['location_address'] . ' - ' . $location['location_town'];
	    $output .= '</td>';
	    $output .= '<td>'; // dates and time
	    $output .= $event_start_date . $event_end_date . '<br />';
	    $output .= $event_start_time . $event_end_time;
	    $output .= '</td>';
	    return $output;
	}
	
	public function manage_volunteers_row( $volunteer, $volunteer_data, $this_page_url='#', $alternate='' ) {
	    /*
	     * return a table row for a manage volunteers table
	     * $volunteer = volunteer user object
	     * $this_page_url = url to the page that will allow take event_id as an arg
	     */
	    $submit_url = add_query_arg( 'volunteer_id', $volunteer->ID, $this_page_url );
	    $volunteer_edit_href = admin_url( 'user-edit.php');
	    $volunteer_edit_href = add_query_arg( 'user_id', $volunteer->ID, $volunteer_edit_href );
	    return $this->volunteer_row( $volunteer, $volunteer_data, $submit_url, $alternate );
	}
	
	public function volunteer_hour_row( $hour, $alternate='' ) {
	    /*
	     * return a table row for a volunteer
	     * $volunteer = volunteer user object
	     * $this_page_url = url to the page that will allow take event_id as an arg
	     */
	    $hour_post = $hour['WP_Post'];
	    $hour_meta = $hour['postmeta'];
	    $event = $hour['event'];
	    $event_data = $this->get_event_data( $event->ID );
	    $event_manage_participation_href = admin_url('admin.php');
	    $event_manage_participation_href = add_query_arg( array(
	        'page' => 'vmat_admin_volunteer_participation',
	        'event_id' => $event->ID,
	        ),
	        $event_manage_participation_href
	    );
	    $output = '<tr class="' . $alternate . '" id="hour_' . $hour_post->ID . '">';
	    $output .= '<th class="check-column">';
	    $output .= '<input type="checkbox" id="vmat_hour_cb_' . $hour_post->ID . '" name="hours_checked[]">';
	    $output .= '</th>';
	    $output .= '<td>';
	    $output .= '<strong>';
	    $output .= $event->post_title;
	    $output .= '</strong>';
	    $output .= '<div class="row-actions">';
	    $output .= '<span class="vmat-link vmat-quick-link" data_action="remove" hour_id="' . $hour_post->ID . 
	   	            '" volunteer_id="' . $hour_post->post_author . '" id="vmat_selected_hour_remove_' . $hour_post->ID . 
	                '" title="Remove hours" data_action="remove">' . __(' Remove', 'vmattd') . '</span>';
   	    $output .= '&nbsp;|&nbsp;';
   	    $output .= '<span class="vmat-link vmat-quick-link" data_action="save" hour_id="' . $hour_post->ID . 
   	   	           '" volunteer_id="' . $hour_post->post_author . '" id="vmat_selected_hour_save_' . $hour_post->ID . 
   	               '" title="Save volunteer hours" data_action="save">' . __('Save', 'vmattd') . '</span>';
   	    $output .= '</span>';
   	    $output .= '&nbsp;|&nbsp;';
   	    $output .= '<a id="vmat_hour_' . $hour_post->ID . '" href="' . $event_manage_participation_href . 
   	               '"><span class="vmat-quick-link">' . __('Manage Particip.', 'vmattd') . '</span></a>';
   	    $output .= '</span>';
   	    $output .= '</div>';
   	    $output .= '</td>';
   	    $output .= '<td>';
   	    $output .= '<input class="vmat-check-before-save" data_name="hours_per_day" type="number" size="3" min="0" max="24" id="vmat_hours_per_day_' . $hour_post->post_author . '_' .
   	    $hour_post->ID . '" value="' . $hour_meta['_hours_per_day'] . '" required>';
   	    $output .= '</td>';
   	    $output .= '<td>';
   	    $output .= '<input class="vmat-check-before-save" data_name="volunteer_start_date" min="' . $event_data['iso_start_date'] .
   	    '" max="' . $event_data['iso_end_date'] . '" type="text" size="6" id="vmat_start_date_' .
   	    $hour_post->post_author . '_' . $hour_post->ID . '" value="' . $hour_meta['_volunteer_start_date'] . '" required>';
   	    $output .= '</td>';
   	    $output .= '<td>';
   	    $output .= '<input class="vmat-check-before-save" data_name="volunteer_days" type="number" size="3" min="0" max="' . $event_data['days'] . 
   	   	           '" id="vmat_days_' . $hour_post->post_author . '_' . $hour_post->ID . '" value="' . $hour_meta['_volunteer_num_days'] . '" required>';
   	    $output .= '</td>';
   	    $output .= '<td class="vmat-check-column">';
   	    $checked = '';
   	    if ( $hour_meta['_approved'] ) {
   	        $checked = 'checked';
   	    }
   	    $output .= '<input class="vmat-check-before-save" data_name="approved" ' .
   	   	    'id="vmat_hour_approved_' . $hour_post->post_author . '_' . $hour_post->ID . '" type="checkbox" ' . $checked . '/>';
   	    $output .= '</td>';
   	    return $output;
	}
	
	public function volunteer_participation_volunteers_row( $volunteer, $alternate='' ) {
	    /*
	     * return a table row for a manage volunteers table
	     * $volunteer = volunteer user object
	     * $this_page_url = url to the page that will allow take event_id as an arg
	     */
	    $volunteer_edit_href = admin_url('admin.php');
	    $volunteer_edit_href = add_query_arg( array(
	        'page' => 'vmat_admin_manage_volunteers',
	        'volunteer_id' => $volunteer->ID,
	        ),
	        $volunteer_edit_href
	    );
	    $display_name = $volunteer->first_name . ' ' . $volunteer->last_name;
	    if( $display_name == '' ) {
	        $display_name = $volunteer->data->user_login;
	    }
	    $output = '<tr class="' . $alternate . '" id="volunteer_' . $volunteer->ID . '">';
	    $output .= '<th class="check-column">';
	    $output .= '<input type="checkbox" id="vmat_volunteer_cb_' . $volunteer->ID . '" name="volunteers_checked[]">';
	    $output .= '</th>';
	    $output .= '<td>';
	    $output .= '<strong>';
	    $output .= $display_name;
	    $output .= '</strong>';
	    $output .= '<div class="row-actions">';
	    $output .= '<a id="vmat_volunteer_' . $volunteer->ID . '" title="' . __( 'Manage Volunteer', 'vmattd' ) . 
	               '" href="' . $volunteer_edit_href . '"><span class="vmat-quick-link">' . __('Manage Vol.', 'vmattd') . '</span></a>';
	    $output .= '</span>';
	    $output .= '&nbsp;|&nbsp;';
	    $output .= '<span class="vmat-link vmat-quick-link" data_action="add" volunteer_id="' . $volunteer->ID . 
	   	           '" id="vmat_selected_volunteer_' . $volunteer->ID . '" title="Add volunteer">' . __(' Add', 'vmattd') . '&nbsp;&raquo;</span>';
   	    $output .= '</div>';
   	    $output .= '</td>';
	    $output .= '<td>'; // dates and time
	    $output .= $volunteer->user_email;
	    $output .= '</td>';
	    return $output;
	}
	
	public function volunteer_participation_event_volunteer_row( $volunteer, $event_id, $alternate='' ) {
	    /*
	     * return a table row for a volunteer in the event volunteers table
	     * $volunteer = volunteer user object
	     * $this_page_url = url to the page that will allow take event_id as an arg
	     */
	    $hours_args = array(
	        'post_type' => 'vmat_hours',
	        'author' => $volunteer->ID,
	        'meta_query' => array(
	            array(
	                'key' => '_event_id',
	                'value' => $event_id,
	            ),
	        ),
	    );
	    $meta_query = new WP_Query( $hours_args );
	    $hours_meta = get_post_meta( $meta_query->post->ID ); 
	    $event_data = $this->get_event_data( $event_id );
	    $volunteer_edit_href = admin_url('admin.php');
	    $volunteer_edit_href = add_query_arg( array(
	        'page' => 'vmat_admin_manage_volunteers',
	        'volunteer_id' => $volunteer->ID
	       ),
	        $volunteer_edit_href
	    );
	    $display_name = $volunteer->first_name . ' ' . $volunteer->last_name;
	    if( $display_name == '' ) {
	        $display_name = $volunteer->data->user_login;
	    }
	    $days = $event_data['days'];
	    $output = '<tr class="' . $alternate . '" id="event_volunteer_' . $volunteer->ID . '">';
	    $output .= '<th class="check-column">';
	    $output .= '<input type="checkbox" id="vmat_event_volunteer_cb_' . $volunteer->ID . '" name="event_volunteers_checked[]">';
	    $output .= '</th>';
	    $output .= '<td>';
	    $output .= '<strong>';
	    $output .= $display_name;
	    $output .= '</strong>';
	    $output .= '<div class="row-actions">';
	    $output .= '<span>';
	    $output .= '<span class="vmat-link vmat-quick-link" data_action="remove" volunteer_id="' . $volunteer->ID . '" id="vmat_selected_event_volunteer_remove_' . 
	               $volunteer->ID . '" title="Remove volunteer">&laquo;' . __(' Remove', 'vmattd') . '</span>';
	    $output .= '&nbsp;|&nbsp;';
	    $output .= '<span class="vmat-link vmat-quick-link" data_action="save" volunteer_id="' . $volunteer->ID . '" id="vmat_selected_event_volunteer_save_' . 
	               $volunteer->ID . '" title="Save volunteer hours">' . __('Save', 'vmattd') . '</span>';
	    $output .= '</span>';
	    $output .= '&nbsp;|&nbsp;';
	    $output .= '<span class="vmat-link vmat-quick-link" data_action="default" volunteer_id="' . $volunteer->ID . '" id="vmat_selected_event_volunteer_default_' .
	   	    $volunteer->ID . '" title="Set to event defaults">' . __('Default', 'vmattd') . '</span>';
   	    $output .= '</span>';
   	    $output .= '&nbsp;|&nbsp;';
   	    $output .= '<a id="vmat_volunteer_' . $volunteer->ID . '" title="' . __( 'Manage Volunteer', 'vmattd' ) . '" href="' . $volunteer_edit_href . '"><span class="vmat-quick-link">' . __('Manage Vol.', 'vmattd') . '</span></a>';
   	    $output .= '</span>';
	    $output .= '</div>';
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= '<input class="vmat-check-before-save" data_name="hours_per_day" type="number" size="3" min="0" max="24" id="vmat_hours_per_day_' . $event_id . '_' . 
	               $volunteer->ID . '" value="' . $hours_meta['_hours_per_day'][0] . '" required>';
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= '<input class="vmat-check-before-save" data_name="volunteer_start_date" min="' . $event_data['iso_start_date'] . 
	   	            '" max="' . $event_data['iso_end_date'] . '" size="6" type="text" id="vmat_start_date_' . 
	   	           $event_id . '_' . $volunteer->ID . '" value="' . $hours_meta['_volunteer_start_date'][0] . '" required>';
	    $output .= '</td>';
	    $output .= '<td>';
	    $output .= '<input class="vmat-check-before-save" data_name="volunteer_days" type="number" size="3" min="0" max="' . $days . '" id="vmat_days_' . $event_id . '_' . 
	               $volunteer->ID . '" value="' . $hours_meta['_volunteer_num_days'][0] . '" required>';
	    $output .= '</td>';
	    $output .= '<td class="vmat-check-column">';
	    $checked = '';
	    if ( $hours_meta['_approved'][0] ) {
	        $checked = 'checked';
	    }
	    $output .= '<input class="vmat-check-before-save" data_name="approved" ' .
	   	           'id="vmat_hours_approved_' . $event_id . '_' . $volunteer->ID . '" type="checkbox" ' . $checked . '/>';
	    $output .= '</td>';
	    return $output;
	}
	
	public function update_volunteer_profile_fields( $user_id ) {
	    $this->update_volunteer_user_meta($user_id);
	}
	
	public function render_volunteer_fields_div() {
	    echo $this->render_volunteer_fields('div');
	}
	
	public function render_volunteer_fields_form_table() {
	    echo $this->render_volunteer_fields('table');
	}
	
	public function render_volunteer_fields_for_ajax_div( $volunteer=null ) {
	    echo $this->render_volunteer_fields_for_ajax('div',  $volunteer );
	}
	
	public function render_volunteer_fields_for_ajax_form_table( $volunteer=null ) {
	    echo $this->render_volunteer_fields_for_ajax('table',  $volunteer );
	}
	
	public function render_wp_required_fields_for_ajax_div( $volunteer=null ) {
	    echo $this->render_wp_required_fields_for_ajax('div',  $volunteer );
	}
	
	public function render_wp_required_fields_for_ajax_form_table( $volunteer=null ) {
	    echo $this->render_wp_required_fields_for_ajax('table',  $volunteer );
	}
	
	public function render_common_fields_div() {
	    echo $this->render_common_fields('div');
	}
	
	public function render_common_fields_form_table() {
	    echo $this->render_common_fields('table');
	}
	
	public function render_common_fields_for_ajax_div( $volunteer=null ) {
	    echo $this->render_common_fields_for_ajax('div',  $volunteer );
	}
	
	public function render_common_fields_for_ajax_form_table( $volunteer=null ) {
	    echo $this->render_common_fields_for_ajax('table',  $volunteer );
	}
	
	public function render_populated_volunteer_fields_div( $user ) {
	    echo $this->render_volunteer_fields('div', $user);
	}
	
	public function render_populated_volunteer_fields_form_table( $user ) {
	    echo $this->render_volunteer_fields('table', $user);
	}
	
	public function volunteer_registration_errors_filter($errors) {
	   return $this->volunteer_registration_errors($errors);
	}
	
	public function common_registration_errors_filter($errors) {
	    return $this->common_registration_errors($errors);
	}
	
	public function volunteer_registration_errors_action($errors) {
	    $this->volunteer_registration_errors($errors);
	}
	
	public function common_registration_errors_action($errors) {
	    $this->common_registration_errors($errors);
	}
	
	public function validate_date($date, $format = 'm-d-Y') {
	    // Create the format date
	    $d = DateTime::createFromFormat($format, $date);
	    // Return the comparison
	    return $d && $d->format($format) === $date;
	}
	
	public function validate_month($month, $format = 'M' ) {
	    return $this->validate_date( $month, $format );
	}
	
	public function validate_input( $input=array() ) {
	    /*
	     * expect an input with the following format
	     * $input['val'] : value
	     * $input['type'] : type if input value ('text','number','checkbox', ...)
	     * $input['selector'] : CSS selector of input element
	     * $input['min'] : minimum value (may be null depending on type) 
	     * $input['max'] : maximum value (may be null depending on type) 
	     */
	    $message = '';
	    switch( $input['type'] ) {
	        case 'text':
	            break;
	        case 'number':
	            if( ! $this->check_numeric_input( $input ) ) {
	                $message = 'invalid input. Type should be "' . $input['type'] . '" and range=[' . $input['min'] . '-' . $input['max'] . ']';
	            }
	            break;
	        case 'date':
	            break;
	        case 'checkbox':
	            break;
	        default:
	    }
	    return $message;
	}
	
	public function check_numeric_input( $input=array() ) {
	    /*
	     * expect an input with the following format
	     * $input['val'] : value
	     * $input['type'] : type if input value ('text','number','checkbox', ...)
	     * $input['selector'] : CSS selector of input element
	     * $input['min'] : minimum value (may be null depending on type)
	     * $input['max'] : maximum value (may be null depending on type)
	     */
	    $success = false;
	    if ( is_numeric($input['val'] ) ) {
	        if ( $input['min'] > $input['val'] || $input['max'] < $input['val'] ) {
	            $success = false;
	        }
	        $success = true;
	    }
	    return $success;
	}
	
	public function var_from_get( $key, $default='' ) {
	    if ( array_key_exists( $key, $_GET) ) {
	        return $_GET[$key];
	    }
	    return $default;
	}
	
	public function update_volunteer_user_meta($user_id) {
	    /*
	     * Updae the meta data for a volunteer user
	     */
	    $is_volunteer = true;
	    foreach ( $this->volunteer_user_fields as $option => $aspects ) {
	        if ( array_key_exists( $option, $_POST ) ) {
	            if ( $is_volunteer ) {
	                $option_value = $_POST[$option];
	                if ( $aspects['type'] == 'boolean' ) {
	                    update_user_meta( $user_id, $option, boolval($option_value) );
	                } elseif ( $aspects['type'] == 'text' ) {
	                    update_user_meta( $user_id, $option, strval( $option_value ) );
	                } elseif ( $aspects['type'] == 'array' ) {
	                    $selections = array_map( 'strval', $option_value);
	                    update_user_meta( $user_id, $option, $selections );
	                }
	            } else {
	                delete_user_meta( $user_id, $option ); 
	            }
	        } else {
	            delete_user_meta( $user_id, $option );
	        }
	    }
	    foreach ( $this->common_user_fields as $option => $aspects ) {
	        if ( array_key_exists( $option, $_POST ) ) {
	            if ( $is_volunteer ) {
	                $option_value = $_POST[$option];
	                if ( $aspects['type'] == 'boolean' ) {
	                    update_user_meta( $user_id, $option, boolval($option_value) );
	                } elseif ( $aspects['type'] == 'text' ) {
	                    update_user_meta( $user_id, $option, strval( $option_value ) );
	                } elseif ( $aspects['type'] == 'array' ) {
	                    $selections = array_map( 'strval', $option_value);
	                    update_user_meta( $user_id, $option, $selections );
	                }
	            } else {
	                delete_user_meta( $user_id, $option );
	            }
	        } else {
	            delete_user_meta( $user_id, $option );
	        }
	    }
	    $this->add_volunteer_user_role($user_id);
	}
	
	public function register_hours_post_type() {
	    register_post_type('vmat_hours',
	        array(
	            'labels'      => array(
	                'name'          => __('Hours', 'vmattd'),
	                'singular_name' => __('Hours', 'vmattd'),
	            ),
	            'capability_type' => array( 'vmat_hour', 'vmat_hours' ),
	            'map_meta_cap' => true,
	            'public'      => false,
	            'show_ui' => false,
	            'has_archive' => false,
	        )
	    );
	}
	
	public function register_funding_stream_post_type() {
	    register_post_type('vmat_funding_stream',
	        array(
	            'labels'      => array(
	                'name'          => __('Funding Streams', 'vmattd'),
	                'singular_name' => __('Funding Stream', 'vmattd'),
	                'edit_item' => __('Edit Funding Stream', 'vmattd'),
	                'add_new_item' => __('Add New Funding Stream', 'vmattd'),
	                'add_new' => _x('Add New Funding Stream', 'vmattd'),
	                'search_items' => __( 'Search', 'vmattd' ),
	                'not_found' => __( 'Not Found', 'vmattd' ),
	                'item_published' => __('Funding Stream Published', 'vmattd'),
	                'item_updated' => __('Funding Stream Updated', 'vmattd'),
	            ),
	            'capability_type' => 'vmat_funding_stream',
	            'map_meta_cap' => true,
	            'public'      => false,
	            'supports' => array('title'),
	            'show_in_menu' => 'vmat_admin_main',
	            'show_ui' => true,
	            'has_archive' => false,
	        )
	        );
	}
	
	public function register_volunteer_type_post_type() {
	    register_post_type('vmat_volunteer_type',
	        array(
	            'labels'      => array(
	                'name'          => __('Volunteer Types', 'vmattd'),
	                'singular_name' => __('Volunteer Type', 'vmattd'),
	                'edit_item' => __('Edit Volunteer Type', 'vmattd'),
	                'add_new_item' => __('Add New Volunteer Type', 'vmattd'),
	                'add_new' => _x('Add New Volunteer Type', 'vmattd'),
	                'search_items' => __( 'Search', 'vmattd' ),
	                'not_found' => __( 'Not Found', 'vmattd' ),
	                'item_published' => __('Volunteer Type Published', 'vmattd'),
	                'item_updated' => __('Volunteer Type Updated', 'vmattd'),
	            ),
	            //'capability_type' => 'vmat_volunteer_type',
	            'public'      => false,
	            'supports' => array('title'),
	            'show_in_menu' => 'vmat_admin_main',
	            'show_ui' => true,
	            'has_archive' => false,
	        )
	        );
	}
	
	public function register_organization_post_type() {
	    register_post_type('vmat_organization',
	        array(
	            'labels'      => array(
	                'name'          => __('Organizations', 'vmattd'),
	                'singular_name' => __('Organization', 'vmattd'),
	                'edit_item' => __('Edit Organization', 'vmattd'),
	                'add_new_item' => __('Add New Organization', 'vmattd'),
	                'add_new' => _x('Add New Organization', 'vmattd'),
	                'search_items' => __( 'Search', 'vmattd' ),
	                'not_found' => __( 'Not Found', 'vmattd' ),
	                'item_published' => __('Organization Published', 'vmattd'),
	                'item_updated' => __('Organization Updated', 'vmattd'),
	            ),
	            //'capability_type' => 'vmat_organization',
	            'public'      => false,
	            'supports' => array('title' ),
	            'show_in_menu' => 'vmat_admin_main',
	            'show_ui' => true,
	            'has_archive' => false,
	        )
	        );
	}
	
	public function get_plugin_name() {
	    return $this->plugin_name;
	}
	
	public function get_version() {
	    return $this->version;
	}
	
	public function get_volunteer_fields() {
	    return $this->volunteer_user_fields;
	}
	    
}
