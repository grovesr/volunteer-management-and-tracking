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
	    'vmat_is_volunteer' => [
	        'type' => 'boolean', 
	        'label' => 'Volunteer',
	        'required' => false,
	    ],
	    'vmat_phone_cell' => [
	        'type' => 'text', 
	        'label' => 'Phone (cell]', 
	        'required' => false,
	    ],
	    'vmat_phone_landline' => [
	        'type' => 'text', 
	        'label' => 'Phone (landline]', 
	        'required' => false,
	    ],
	    'vmat_address_street' => [
	        'type' => 'text', 
	        'label' => 'Street Address', 
	        'required' => false,
	    ],
	    'vmat_address_city' => [
	        'type' => 'text', 
	        'label' => 'City', 
	        'required' => false,
	    ],
	    'vmat_address_zipcode' => [
	        'type' => 'text', 
	        'label' => 'Zip Code', 
	        'required' => false,
	    ],
	    'vmat_volunteer_skillsets' => [
	        'type' => 'array', 
	        'label' => 'Skillsets', 
	        'required' => false,
	    ],
	    'vmat_volunteer_interests' => [
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
	
	private function  boolean_choice_field($args) {
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
	
	private function text_input_field($args) {
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
    	        $page .= '*';
    	    }
    	    $page .= '</label>';
	    } else {
	        $page .= '<th>' .
    	    __($label, 'vmattd');
    	    if ( $required ) 
    	    { 
    	        $page .= '*';
    	    }
    	    $page .= '</th>';
    	    $page .= '<td>';
	    }
	    
	    $page .= '<input type="text"
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
	
	private function multiselect_children_of_category_fields($args) {
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
	    $ms_var_name = 'vmat_volunteer_' . strtolower($category);
	    if ( array_key_exists( 'value', $args ) ) {
	        $ms_var = array_map(strval, $args['value']);
	    } else {
	        $ms_var = [];
	        /*
	         * retain selection values in case of repost due to error
	         */
	        if ( array_key_exists( $ms_var_name, $_POST ) ) {
	            $ms_var = $_POST[$ms_var_name];
	            $ms_var = array_map(strval, $ms_var);
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
	    $is_volunteer = false;
	    if ( array_key_exists( 'vmat_is_volunteer', $_POST ) ) {
	        // re-POST due to a form submission error
	        $is_volunteer = $_POST['vmat_is_volunteer'];
	        if ( $is_volunteer ) {
	            $values = $this->get_volunteer_values_from_post( $_POST );
	        } else {
	            $values = $this->get_default_volunteer_values();
	        }
	    } elseif ( $user ) {
	        // request to populate fields from a volunteer
	        $values = $this->get_volunteer_values_from_umeta($user);
	        $is_volunteer = $values['vmat_is_volunteer'];
	    } else {
	        // not a re-POST and not a request to populate fields from a volunteer
	        $values = $this->get_default_volunteer_values();
	    }
	    if ( $values['vmat_is_volunteer'] ) {
	        $reg_fields_display = 'show';
	    }
	    if ( $section_type == 'table' ) {
	        $page .= '<table class="form-table" role="presentation">';
	    }
	    foreach ( $this->volunteer_user_fields as $field => $aspects ) {
	        if ( $field == 'vmat_is_volunteer' ) {
	            $page .= '<' . $section . '>';
	        } else {
	            $page .= '<' . $section .  ' class="vmat-registration-fields"
	                                         style="display:' . $reg_fields_display . '">';
	        }
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
	     * display user's commonb meta values
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
	        $page .= '<' . $section . '>';
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
		
	private function volunteer_registration_errors($errors) {
	    /*
	     * Check for errors on volunteer user registration
	     */
	    $is_volunteer = false;
	    if ( array_key_exists( 'vmat_is_volunteer', $_POST ) ) {
	        $is_volunteer = $_POST['vmat_is_volunteer'];
	    }
	    if ( $is_volunteer ) {
	        /*
	         * add error checking here
	         */
	    }
	    return $errors;
	}
	
	private function common_registration_errors($errors) {
	    /*
	     * Check for errors on user registration
	     */
	    if( empty( $_POST['first_name'] ) ) {
	        $errors->add( 'vmat_first_name_error', __( '<strong>ERROR</strong>: Please enter your first name.', 'vmattd' ) );
	    }
	    $is_volunteer = false;
	    if ( array_key_exists( 'vmat_is_volunteer', $_POST ) ) {
	        $is_volunteer = $_POST['vmat_is_volunteer'];
	    }
	    if ( $is_volunteer ) {
	        /*
	         * add error checking here
	         */
	    }
	    return $errors;
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
	
	public function render_common_fields_div() {
	    echo $this->render_common_fields('div');
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
	
	public function update_volunteer_user_meta($user_id) {
	    /*
	     * Updae the meta data for a volunteer user
	     */
	    if ( ! current_user_can( 'edit_user', $user_id ) ) {
	        return false;
	    }
	    $is_volunteer = false;
	    if ( array_key_exists( 'vmat_is_volunteer', $_POST ) ) {
	        $is_volunteer = boolval($_POST['vmat_is_volunteer']);
	    }
	    foreach ( $this->volunteer_user_fields as $option => $aspects ) {
	        if ( array_key_exists( $option, $_POST ) ) {
	            if ( $is_volunteer ) {
	                $option_value = $_POST[$option];
	                if ( $aspects['type'] == 'boolean' ) {
	                    update_user_meta( $user_id, $option, boolval($option_value) );
	                } elseif ( $aspects['type'] == 'text' ) {
	                    update_user_meta( $user_id, $option, strval( $option_value ) );
	                } elseif ( $aspects['type'] == 'array' ) {
	                    $selections = array_map(strval, $option_value);
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
	
	private function add_volunteer_user_role( $user_id ) {
	    $wpuser = get_user_by('id', $user_id);
	    if ( $wpuser ) {
	        $is_volunteer = boolval(get_the_author_meta( 'vmat_is_volunteer', $wpuser->ID ));
	        if ( $is_volunteer ) {
	            if ( $wpuser ) {
	                if( ! in_array('volunteer', $wpuser->roles)) {
	                    $wpuser->add_role('volunteer');
	                }
	            }
	        } else {
	            // remove the volunteer role if it exists
	            if( in_array('volunteer', $wpuser->roles)) {
	                $wpuser->remove_role('volunteer');
	            }
	        }
	    }
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