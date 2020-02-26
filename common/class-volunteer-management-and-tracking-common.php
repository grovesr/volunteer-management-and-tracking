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
	    if ( ! empty( $args['value'] ) ) {
	        $var_value = boolval($args['value']);
	    } else {
	        $var_value = false;
	        /*
	         * retain selection values in case of repost due to error
	         */
	        if ( ! empty($_POST[$var_name]) ) {
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
	    if ( ! empty( $args['value'] ) ) {
	        $var_value = strval($args['value']);
	    } else {
	        $var_value = '';
	        /*
	         * retain selection values in case of repost due to error
	         */
	        if ( ! empty($_POST[$var_name]) ) {
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
	
	private function multiselect_children_of_category_registration_fields($args) {
	    /*
	     * Display a multiselect populated from children of the passed-in $category
	     * The $category is derived from the post taxonomy 'category'
	     */
	    
	    /*
	     * retain selection values in case of repost due to error
	     */
	    $category = $args['category'];
	    $type = 'div';
	    if ( ! empty( $args['type'] ) ) {
	        $type = $args['type'];
	    }
	    $page = '';
	    $ms_var_name = 'vmat_volunteer_' . strtolower($category);
	    if ( ! empty($_POST[$ms_var_name]) ) {
	        $ms_var = $_POST[$ms_var_name];
	        $ms_var = array_map(strval, $ms_var);
	    }
	    if ( ! empty($_POST['vmat_is_volunteer']) ) {
	        $is_volunteer = boolval($_POST['vmat_is_volunteer']);
	    }
	    if ( ! $is_volunteer ) {
	        $ms_var = array([]);
	    }
	    $category_id = get_cat_ID($category);
	    if ($category_id) {
	        $subcategories = get_terms( array(
	            'taxonomy' => 'category',
	            'child_of' => $category_id,
	            'hide_empty' => false,)
	            );
	        if ( count($subcategories) ) {
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
	        } // if ( count($subcategories)
	   } // if ($category_id)
	   return $page;
	} // multiselect_children_of_category_registration_fields
    
	private function registration_fields($type) {
	    /*
	     * display user registration form with extra fields for volunteers
	     * $type switches between <div> based and <table> based layouts
	     */
	    
	    $page = '';
	    $is_volunteer = false;
	    $reg_fields_display = 'none';
	    if ( $type == 'table' ) {
	        $section = 'tr';
	    } else {
	        $section = 'div';
	    }
	    /*
	     * retain selection values in case of repost due to error
	     */
	    if ( ! empty($_POST['vmat_is_volunteer']) ) {
	        $is_volunteer = boolval($_POST['vmat_is_volunteer']);
	    }
	    if ( $is_volunteer ) {
	        $reg_fields_display = 'show';
	    }
	    if ( $type == 'table' ) {
	        $page .= '<table class="form-table" role="presentation">';
	    }
	    $page .= '<' . $section . '>';
	    $page .= $this->boolean_choice_field(['option_name' => 'vmat_is_volunteer',
	                                      'label' => 'Volunteer',
	                                      'type' => $type,
	                                      'value' => $is_volunteer,
	                                     ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields" 
                                    style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'first_name',
                                      'label' => 'First Name',
	                                  'type' => $type,
	                                  'required' => true,
                                     ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section .  ' class="vmat-registration-fields"
	                                 style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'last_name',
                            	      'label' => 'Last Name',
                            	      'type' => $type,
                            	      ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_phone_cell',
                                      'label' => 'Phone (cell)',
	                                  'type' => $type,
                                     ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_phone_landline',
                                      'label' => 'Phone (landline)',
	                                  'type' => $type,
                                     ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_address_street',
                                      'label' => 'Street Address',
	                                  'type' => $type,
                                     ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_address_city',
                                      'label' => 'City',
	                                  'type' => $type,
                                     ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_address_zipcode',
                                      'label' => 'Zip Code',
	                                  'type' => $type,
                                     ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->multiselect_children_of_category_registration_fields(['category' => 'Skillsets',
	                                                                          'type' => $type,
	                                                                          ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->multiselect_children_of_category_registration_fields(['category' => 'Interests',
                                                                        	  'type' => $type,
                                                                        	 ]);
	    $page .= '</' . $section .'>';
        if ( $type == 'table' ) {
            $page .= '</table>';
	    }
	    return $page;
	}
	
	private function populate_registration_fields($user, $type) {
	    /*
	     * display user registration form with extra fields for volunteers
	     * $type switches between <div> based and <table> based layouts
	     */
	    
	    $page = '';
	    $reg_fields_display = 'none';
	    $is_volunteer = false;
	    if ( $type == 'table' ) {
	        $section = 'tr';
	    } else {
	        $section = 'div';
	    }
	    
	    /*
	     * Get the values from the $user meta 
	     */
	    $capabilities = get_the_author_meta( 'wp_capabilities', $user->ID );
	    if ( ! empty( $capabilities['Volunteer'] ) ) {
	        $is_volunteer = $capabilities['Volunteer'];
	    }
	    
	    /*
	     * retain selection values in case of repost due to error
	     */
	    if ( ! empty($_POST['vmat_is_volunteer']) ) {
	        $is_volunteer = boolval($_POST['vmat_is_volunteer']);
	    }
	    if ( $is_volunteer ) {
	        $reg_fields_display = 'show';
	    }
	    if ( $type == 'table' ) {
	        $page .= '<table class="form-table" role="presentation">';
	    }
	    $page .= '<' . $section . '>';
	    $page .= $this->boolean_choice_field(['option_name' => 'vmat_is_volunteer',
	        'label' => 'Volunteer',
	        'type' => $type,
	        'value' => $is_volunteer,
	    ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
                                    style="display:' . $reg_fields_display . '>';
	    $page .= $this->text_input_field(['option_name' => 'vmat_first_name',
	        'label' => 'First Name',
	        'type' => $type,
	        'required' => true,
	    ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section .  ' class="vmat-registration-fields"
	                                 style="display:' . $reg_fields_display . '>';
	    $page .= $this->text_input_field(['option_name' => 'vmat_last_name',
	        'label' => 'Last Name',
	        'type' => $type,
	    ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_phone_cell',
	        'label' => 'Phone (cell)',
	        'type' => $type,
	    ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_phone_landline',
	        'label' => 'Phone (landline)',
	        'type' => $type,
	    ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_address_street',
	        'label' => 'Street Address',
	        'type' => $type,
	    ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_address_city',
	        'label' => 'City',
	        'type' => $type,
	    ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->text_input_field(['option_name' => 'vmat_address_zipcode',
	        'label' => 'Zip Code',
	        'type' => $type,
	    ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->multiselect_children_of_category_registration_fields(['category' => 'Skillsets',
                                                                	          'type' => $type,
                                                                	         ]);
	    $page .= '</' . $section . '>';
	    $page .= '<' . $section . ' class="vmat-registration-fields"
	                                style="display:' . $reg_fields_display . '">';
	    $page .= $this->multiselect_children_of_category_registration_fields(['category' => 'Interests',
                                                                        	  'type' => $type,
                                                                        	 ]);
	    $page .= '</' . $section .'>';
	    if ( $type == 'table' ) {
	        $page .= '</table>';
	    }
	    return $page;
	}
	
	private function registration_errors($errors) {
	    /*
	     * Check for errors on volunteer user registration
	     */
	    
	    if ( empty( $_POST['first_name'] ) ) {
	        $errors->add( 'vmat_first_name_error', __( '<strong>ERROR</strong>: Please enter your first name.', 'vmattd' ) );
	    }
	    return $errors;
	}
	
	public function registration_fields_div() {
	    echo $this->registration_fields('div');
	}
	
	public function registration_fields_form_table() {
	    echo $this->registration_fields('table');
	}
	
	public function populate_registration_fields_div( $user ) {
	    echo $this->populate_registration_fields($user, 'div');
	}
	
	public function populate_registration_fields_form_table( $user ) {
	    echo $this->populate_registration_fields($user, 'table');
	}
	
	public function registration_errors_filter($errors) {
	   return $this->registration_errors($errors);
	}
	
	public function registration_errors_action($errors) {
	    $this->registration_errors($errors);
	}
	
	public function user_register($user_id) {
	    /*
	     * Register the new volunteer user
	     */
	    if ( ! empty( $_POST['first_name'] ) ) {
	        update_user_meta( $user_id, 'first_name', strval( $_POST['first_name'] ) );
	    }
	    if ( ! empty( $_POST['last_name'] ) ) {
	        update_user_meta( $user_id, 'last_name', strval( $_POST['last_name'] ) );
	    }
	    if ( ! empty( $_POST['vmat_phone_cell'] ) ) {
	        update_user_meta( $user_id, 'phone_cell', strval( $_POST['vmat_phone_cell'] ) );
	    }
	    if ( ! empty( $_POST['vmat_phone_landline'] ) ) {
	        update_user_meta( $user_id, 'phone_landline', strval( $_POST['vmat_phone_landline'] ) );
	    }
	    if ( ! empty( $_POST['vmat_address_street'] ) ) {
	        update_user_meta( $user_id, 'address_street', strval( $_POST['vmat_address_street'] ) );
	    }
	    if ( ! empty( $_POST['vmat_address_city'] ) ) {
	        update_user_meta( $user_id, 'address_city', strval( $_POST['vmat_address_city'] ) );
	    }
	    if ( ! empty( $_POST['vmat_address_zipcode'] ) ) {
	        update_user_meta( $user_id, 'address_zipcode', strval( $_POST['vmat_address_zipcode'] ) );
	    }
	    if ( ! empty( $_POST['vmat_volunteer_skillsets'] ) ) {
	        $skillset = array_map(strval, $_POST['vmat_volunteer_skillsets']);
	        update_user_meta( $user_id, 'volunteer_skillsets', $skillset );
	    }
	    if ( ! empty( $_POST['volunteer_interests'] ) ) {
	        $interests = array_map(strval, $_POST['vmat_volunteer_interests']);
	        update_user_meta( $user_id, 'volunteer_interests', $interests );
	    }
	    if ( ! empty( $_POST['vmat_is_volunteer'] ) && boolval($_POST[ 'vmat_is_volunteer'] )) {
	        $wpuser = get_user_by('id', $user_id);
	        if ( $wpuser ) {
	            $wpuser->set_role('Volunteer');
	        }
	    }
	    
	}
	    
}