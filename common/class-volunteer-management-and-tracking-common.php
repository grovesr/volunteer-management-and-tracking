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
	
	public function  boolean_choice_field($args) {
	    /*
	     * display a boolean choice field
	     */
	    
	    $var_name = $args['option_name'];
	    $label = $args['label'];
	    $var_value = false;
	    /*
	     * retain selection values in case of repost due to error
	     */
	    if ( ! empty($_POST[$var_name]) ) {
	        $var_value = boolval($_POST[$var_name]);
	    }
	    ?>
	    <label for="<?php echo esc_attr($var_name)?>"><?php _e($label, 'vmattd') ?></label>
		<input type="checkbox"
		       id="<?php echo esc_attr($var_name)?>"
		       name="<?php echo esc_attr($var_name)?>"
		       <?php if ( $var_value ) {
		            echo "checked";
		       }
		       ?>
		       />
	    <?php
	    
	}
	
	public function text_input_field($args) {
	    /*
	     * display a text input field
	     */
	    
	    $var_name = $args['option_name'];
	    $label = $args['label'];
	    $required = false;
	    if( in_array('required', $args) ) {
	       $required = boolval($args['required']);
	    }
	    $var_value = '';
	    /*
	     * retain selection values in case of repost due to error
	     */
	    if ( ! empty($_POST[$var_name]) ) {
	        $var_value = strval($_POST[$var_name]);
	    }
	    
	    if ( ! empty($_POST['vmat_is_volunteer']) ) {
	        $is_volunteer = boolval($_POST['vmat_is_volunteer']);
	    }
	    if ( ! $is_volunteer ) {
	        $var_value = '';
	    }
	    
	    ?>
	    <label for="<?php echo esc_attr($var_name)?>"><?php
	    _e($label, 'vmattd');
	    if ( $required ) 
	    { 
	        echo '*';
	    }
	    ?></label>
		<input type="text"
		       id="<?php echo esc_attr($var_name)?>"
		       name="<?php echo esc_attr($var_name)?>"
		       value="<?php echo $var_value?>"
		       />
	    <?php
	    
	}
	
	public function multiselect_children_of_category_registration_fields($category='Skillsets') {
	    /*
	     * Display a multiselect populated from children of the passed-in $category
	     * The $category is derived from the post taxonomy 'category'
	     */
	    
	    /*
	     * retain selection values in case of repost due to error
	     */
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
	            ?>
    	        <fieldset>
    	        	<legend>Choose your <?php echo $category?></legend>
    	        <?php
    	        foreach ($subcategories as $subcategory) {
    	            
    	            ?>
    	            	<div>
                        	<input type="checkbox" 
                        	       id="<?php echo "vmat-" . esc_attr( $subcategory->slug ); ?>" 
                        	       name="<?php echo $ms_var_name ?>[]" 
                        	       value="<?php echo esc_attr( $subcategory->name ); ?>"
                        	       <?php if ( in_array($subcategory->name, $ms_var) ) {
                			         echo "checked";
                			       }?>
                			       />
                        	<label for="<?php echo "vmat-" . esc_attr( $subcategory->slug ); ?>"><?php echo esc_html( __($subcategory->name, 'vmattd') ); ?></label>
                        </div>
    	            <?php
        	        }
        	        ?>
    	        </fieldset>
    	        <?php
	        } // if ( count($subcategories)
	   } // if ($category_id)
	} // multiselect_children_of_category_registration_fields
    
	public function registration_fields() {
	    /*
	     * display user registration form with extra fields for volunteers
	     */
	    
	    $is_volunteer = false;
	    /*
	     * retain selection values in case of repost due to error
	     */
	    if ( ! empty($_POST['vmat_is_volunteer']) ) {
	        $is_volunteer = boolval($_POST['vmat_is_volunteer']);
	    }
	    ?>
	    <div>
	    <?php
	    echo $this->boolean_choice_field(['option_name' => 'vmat_is_volunteer',
	                                      'label' => 'Volunteer',
	                                     ]);
	    ?>
	    </div>
	    <div id="vmat_registration_fields"
    		 style="display:<?php
	            	if ( $is_volunteer ) {
	            	  echo "show";
	            	} else {
	            	    echo "none";
	            	}
	            	?>"
	    >
	    <div>
	    <?php
	    echo $this->text_input_field(['option_name' => 'vmat_first_name',
                                      'label' => 'First Name',
	                                  'required' => true,
                                     ]);
	    ?>
	    </div>
	    <div>
	    <?php
	    echo $this->text_input_field(['option_name' => 'vmat_last_name',
                                      'label' => 'Last Name'
                                     ]);
	    ?>
	    </div>
	    <div>
	    <?php
	    echo $this->text_input_field(['option_name' => 'vmat_phone_cell',
                                      'label' => 'Phone (cell)'
                                     ]);
	    ?>
	    </div>
	    <div>
	    <?php
	    echo $this->text_input_field(['option_name' => 'vmat_phone_landline',
                                      'label' => 'Phone (landline)'
                                     ]);
	    ?>
	    </div>
	    <div>
	    <?php
	    echo $this->text_input_field(['option_name' => 'vmat_address_street',
                                      'label' => 'Street Address'
                                     ]);
	    ?>
	    </div>
	    <div>
	    <?php
	    echo $this->text_input_field(['option_name' => 'vmat_address_city',
                                      'label' => 'City'
                                     ]);
	    ?>
	    </div>
	    <div>
	    <?php
	    echo $this->text_input_field(['option_name' => 'vmat_address_zipcode',
                                      'label' => 'Zip Code'
                                     ]);
	    ?>
	    </div>
	    <div>
	    <?php
	    echo $this->multiselect_children_of_category_registration_fields('Skillsets');
	    ?>
	    </div>
	    <div>
	    <?php
	    echo $this->multiselect_children_of_category_registration_fields('Interests');
        ?>
        </div>
        </div>
        <?php
	}
	
	public function registration_errors($errors) {
	    /*
	     * Check for errors on volunteer user registration
	     */
	    
	    if ( empty( $_POST['vmat_first_name'] ) ) {
	        $errors->add( 'vmat_first_name_error', __( '<strong>ERROR</strong>: Please enter your first name.', 'vmattd' ) );
	    }
	    return $errors;
	}
	
	public function user_register($user_id) {
	    /*
	     * Register the new volunteer user
	     */
	    if ( ! empty( $_POST['vmat_first_name'] ) ) {
	        update_user_meta( $user_id, 'first_name', strval( $_POST['vmat_first_name'] ) );
	    }
	    if ( ! empty( $_POST['vmat_last_name'] ) ) {
	        update_user_meta( $user_id, 'last_name', strval( $_POST['vmat_last_name'] ) );
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