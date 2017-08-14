<?php
// Set up settings defaults
register_activation_hook(__FILE__, 'twaic_set_options');
if( !function_exists( 'twaic_activate' ) ) {
	//echo 1;die();
function twaic_set_options(){
	$defaults = array(
		'theme'	=>	'theme1',
		'client_id' => '',
		'client_secret'	=> '',
		'access_token'	=> '',
		'instagram_user_id'	=> '',
		'before_wrapper' => '<ul>',
		'after_wrapper' => '</ul>',
		'before_image_listing'	=> '<li>',
		'after_image_listing'	=> '</li>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
		'before_caption' => '<p>',
		'after_caption' => '</p>',
		'image_count' => '20',
		'image_size' => 'standard_resolution',
		'buffer_hours' => '1',
		'show_user_details' => '1',
	);
	add_option('twaic_settings', $defaults);
}
}
// Clean up on uninstall
register_deactivation_hook(__FILE__, 'twaic_deactivate');
function twaic_deactivate(){
	delete_option('twaic_settings');
}


// Render the settings page
class twaic_settings_page {
	// Holds the values to be used in the fields callbacks
	private $options;
			
	// Start up
	public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
	}
			
	// Add settings page
	public function add_plugin_page() {
		add_menu_page( 'Advanced Instagram Carousel', 'AI Carousel', 'administrator', 'twaic', array($this,'create_admin_page'), plugins_url('asset/images/icon20x20.png',__FILE__ ) );
		
	}
			
	// Options page callback
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'twaic_settings' );
		if(!$this->options){
			twaic_set_options ();
			$this->options = get_option( 'twaic_settings' );
		}
		?>
		<div class="wrap">
		<h2>Advanced Instagram Carousel Settings</h2>
        
        
					 
				<form method="post" action="options.php">
				<?php
						settings_fields( 'twaic_settings' );   
						do_settings_sections( 'twaic-settings' );
						submit_button(); 
				?>
				</form>
		</div>
		<?php
	}
			
	// Register and add settings
	public function page_init() {		
		register_setting(
				'twaic_settings', // Option group
				'twaic_settings', // Option name
				array( $this, 'sanitize' ) // Sanitize
		);
		
        // Sections
		add_settings_section(
				'twaic_instagram_settings', // ID
				'Instagram Settings', // Title
				array( $this, 'twaic_instagram_settings_header' ), // Callback
				'twaic-settings' // Page
		);
		
		add_settings_section(
				'twaic_settings_setup', // ID
				 'Carousel Advance Settings', // Title
				array( $this, 'twaic_settings_setup' ), // Callback
				'twaic-settings' // Page
		);
		add_settings_section(
				'twaic_settings_markup', // ID
				'Custom Markup', // Title
				array( $this, 'twaic_settings_markup_header' ), // Callback
				'twaic-settings' // Page
		);
        
		// Behaviour Fields
		add_settings_field(
				'client_id', // ID
				'Client ID', // Title
				array( $this, 'client_id_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_instagram_settings' // Section
		);
		add_settings_field(
				'client_secret', // ID
				'Client Secret', // Title
				array( $this, 'client_secret_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_instagram_settings' // Section
		);
		add_settings_field(
				'access_token', // ID
				'Access Token', // Title
				array( $this, 'access_token_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_instagram_settings' // Section
		);
		add_settings_field(
				'instagram_user_id', // ID
				'Instagram User ID', // Title
				array( $this, 'instagram_user_id_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_instagram_settings' // Section
		);
		add_settings_field(
				'showcontrols', // ID
				'Show Slide Controls?', // Title 
				array( $this, 'showcontrols_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_behaviour' // Section		   
		);
		add_settings_field(
				'showindicator', // ID
				'Show Slide Indicator?', // Title 
				array( $this, 'showindicator_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_behaviour' // Section		   
		);
		add_settings_field(
				'orderby', // ID
				__('Order Slides By', 'twaic-settings'), // Title 
				array( $this, 'orderby_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_behaviour' // Section		   
		);
		add_settings_field(
				'order', // ID
				__('Ordering Direction', 'twaic-settings'), // Title 
				array( $this, 'order_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_behaviour' // Section		   
		);
		add_settings_field(
				'category', // ID
				__('Restrict to Category', 'twaic-settings'), // Title 
				array( $this, 'category_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_behaviour' // Section		   
		);
        
        // Carousel Setup Section
		
		add_settings_field(
				'image_size', // ID
				__('Image Size', 'twaic-settings'), // Title 
				array( $this, 'image_size_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_setup' // Section		   
		);
		add_settings_field(
				'theme', // ID
				__('Theme', 'twaic-settings'), // Title 
				array( $this, 'theme_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_setup' // Section		   
		);
		add_settings_field(
				'show_user_details', // ID
				__('Show User Details', 'twaic-settings'), // Title 
				array( $this, 'show_user_details_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_setup' // Section		   
		);
		

        
        // Markup Section
	
		
		add_settings_field(
				'before_wrapper', // ID
				__('HTML before wrapper', 'twaic-settings'), // Title
				array( $this, 'before_wrapper_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_markup' // Section
		);
		add_settings_field(
				'after_wrapper', // ID
				__('HTML after wrapper', 'twaic-settings'), // Title
				array( $this, 'after_wrapper_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_markup' // Section
		);
		add_settings_field(
				'before_title', // ID
				__('HTML before title', 'twaic-settings'), // Title
				array( $this, 'before_title_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_markup' // Section
		);
		add_settings_field(
				'after_title', // ID
				__('HTML after title', 'twaic-settings'), // Title
				array( $this, 'after_title_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_markup' // Section
		);
		add_settings_field(
				'before_caption', // ID
				__('HTML before caption text', 'twaic-settings'), // Title
				array( $this, 'before_caption_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_markup' // Section
		);
		add_settings_field(
				'after_caption', // ID
				__('HTML after caption text', 'twaic-settings'), // Title
				array( $this, 'after_caption_callback' ), // Callback
				'twaic-settings', // Page
				'twaic_settings_markup' // Section
		);
		
			 
	}
			
	// Sanitize each setting field as needed -  @param array $input Contains all settings fields as array keys
	public function sanitize( $input ) {
		$new_input = array();
		foreach($input as $key => $var){
			if($key == 'twbs' || $key == 'interval' || $key == 'background_images_height'){
				$new_input[$key] = absint( $input[$key] );
			} else if ($key == 'link_button_before' || $key == 'link_button_after' || $key == 'before_title' || $key == 'after_title' || $key == 'before_caption' || $key == 'after_caption' || $key == 'after_wrapper' || $key == 'before_wrapper'){
				$new_input[$key] = $input[$key]; // Don't sanitise these, meant to be html!
			} else { 
				$new_input[$key] = sanitize_text_field( $input[$key] );
			}
		}
		return $new_input;
	}
			
	// Print the Section text
	public function twaic_instagram_settings_header() {
            echo '<p>'.__('Basic setup of how each Carousel will function, what controls will show and which images will be displayed.', 'twaic-settings').'</p>';
	}
	public function twaic_settings_setup() {
            echo '<p>'.__('Change the setup of the carousel - how it functions.', 'twaic-settings').'</p>';
	}
	public function twaic_settings_link_buttons_header() {
            echo '<p>'.__('Options for using a link button instead of linking the image directly.', 'twaic-settings').'</p>';
	}
	public function twaic_settings_markup_header() {
            echo '<p>'.__('Customise which CSS classes and HTML tags the Carousel uses.', 'twaic-settings').'</p>';
	}
			
	// Callback functions - print the form inputs
    // Carousel behaviour	
	public function client_id_callback() {
			printf('<input type="text" id="client_id" name="twaic_settings[client_id]" value="%s" size="50" />',
					isset( $this->options['client_id'] ) ? esc_attr( $this->options['client_id']) : '');
            echo '<p class="description">'.__('Follow our tutorial <a href="http://www.thelogicalcoder.com/how-to-register-your-app-in-instagram/" target="_blank">http://www.thelogicalcoder.com/how-to-register-your-app-in-instagram/</a>').'</p>';
	}
	public function client_secret_callback() {
			printf('<input type="text" id="client_secret" name="twaic_settings[client_secret]" value="%s" size="50" />',
					isset( $this->options['client_secret'] ) ? esc_attr( $this->options['client_secret']) : '');
            echo '<p class="description">'.__('Follow our tutorial <a href="http://www.thelogicalcoder.com/how-to-register-your-app-in-instagram/" target="_blank">http://www.thelogicalcoder.com/how-to-register-your-app-in-instagram/</a>').'</p>';
	}
	public function access_token_callback() {
			printf('<input type="text" id="access_token" name="twaic_settings[access_token]" value="%s" size="50" />',
					isset( $this->options['access_token'] ) ? esc_attr( $this->options['access_token']) : '');
			echo '<p class="description">'.__('Use this URL if using in Localhost as Redirect URI: <strong>http://www.thelogicalcoder.com/development/social/instagram/').'</strong></p>';
			echo '<p class="description">'.__('Use this URL if using in Server as Redirect URI: <strong>'.plugins_url('instagram/',__FILE__ )).'</strong></p>';
            echo '<p class="description">'.__('Generate Instagram Access Token if using in Localhost <a href="javascript:GenerateToken(0);" class="button button-primary">Generate</a>').'</p>';
			echo '<p class="description">'.__('Generate Instagram Access Token if using in Server <a href="javascript:GenerateToken(1);" class="button button-primary">Generate</a>').'</p>';
			echo '<script>function GenerateToken(_type){
				
				var _cid = document.getElementById("client_id").value; var _csr = document.getElementById("client_secret").value;
				if(_cid=="" || _csr==""){alert("Please Fill valid Client ID and Client Secret"); return;}
				if(_type==0){
					window.open("http://www.thelogicalcoder.com/development/social/instagram/generator.php?clientid="+_cid+"&clientsecret="+_csr);
				}
				else if(_type==1){
					window.open("'.plugins_url('instagram/generator.php?clientid="+_cid+"&clientsecret="+_csr',__FILE__ ).');
				}
			}</script>';
					
	}
	public function instagram_user_id_callback() {
			printf('<input type="text" id="instagram_user_id" name="twaic_settings[instagram_user_id]" value="%s" size="20" />',
					isset( $this->options['instagram_user_id'] ) ? esc_attr( $this->options['instagram_user_id']) : '');
            echo '<p class="description">'.__('Generate Instagram Access Token First').'</p>';
	}
	public function showcaption_callback() {
		if(isset( $this->options['showcaption'] ) && $this->options['showcaption'] == 'false'){
			$twaic_showcaption_t = '';
			$twaic_showcaption_f = ' selected="selected"';
		} else {
			$twaic_showcaption_t = ' selected="selected"';
			$twaic_showcaption_f = '';
		}
		echo '<select id="showcaption" name="twaic_settings[showcaption]">
			<option value="true"'.$twaic_showcaption_t.'>'.__('Show', 'twaic-settings').'</option>
			<option value="false"'.$twaic_showcaption_f.'>'.__('Hide', 'twaic-settings').'</option>
		</select>';
	}
	public function showcontrols_callback() {
		if(isset( $this->options['showcontrols'] ) && $this->options['showcontrols'] == 'false'){
			$twaic_showcontrols_t = '';
			$twaic_showcontrols_f = ' selected="selected"';
			$twaic_showcontrols_c = '';
		} else if(isset( $this->options['showcontrols'] ) && $this->options['showcontrols'] == 'true'){
			$twaic_showcontrols_t = ' selected="selected"';
			$twaic_showcontrols_f = '';
			$twaic_showcontrols_c = '';
		} else if(isset( $this->options['showcontrols'] ) && $this->options['showcontrols'] == 'custom'){
			$twaic_showcontrols_t = '';
			$twaic_showcontrols_f = '';
			$twaic_showcontrols_c = ' selected="selected"';
		}
		echo '<select id="showcontrols" name="twaic_settings[showcontrols]">
			<option value="true"'.$twaic_showcontrols_t.'>'.__('Show', 'twaic-settings').'</option>
			<option value="false"'.$twaic_showcontrols_f.'>'.__('Hide', 'twaic-settings').'</option>
			<option value="custom"'.$twaic_showcontrols_c.'>'.__('Custom', 'twaic-settings').'</option>
		</select>';
	}
	public function showindicator_callback() {
		if(isset( $this->options['showindicator'] ) && $this->options['showindicator'] == 'false'){
			$twaic_showindicator_t = '';
			$twaic_showindicator_f = ' selected="selected"';
			$twaic_showindicator_c = '';
		} else if(isset( $this->options['showindicator'] ) && $this->options['showindicator'] == 'true'){
			$twaic_showindicator_t = ' selected="selected"';
			$twaic_showindicator_f = '';
			$twaic_showindicator_c = '';
		} else if(isset( $this->options['showindicator'] ) && $this->options['showindicator'] == 'numbered'){
			$twaic_showindicator_t = '';
			$twaic_showindicator_f = '';
			$twaic_showindicator_c = ' selected="selected"';
		}
		echo '<select id="showindicator" name="twaic_settings[showindicator]">
			<option value="true"'.$twaic_showindicator_t.'>Bullet Indicators</option>
			<option value="false"'.$twaic_showindicator_f.'>Hide Indicators</option>
			<option value="numbered"'.$twaic_showindicator_c.'>Numbered Indicators</option>
		</select>';
	}
	public function orderby_callback() {
		$orderby_options = array (
			'menu_order' => __('Menu order, as set in Carousel overview page', 'twaic-settings'),
			'date' => __('Date slide was published', 'twaic-settings'),
			'rand' => __('Random ordering', 'twaic-settings'),
			'title' => __('Slide title', 'twaic-settings')	  
		);
		echo '<select id="orderby" name="twaic_settings[orderby]">';
		foreach($orderby_options as $val => $option){
			echo '<option value="'.$val.'"';
			if(isset( $this->options['orderby'] ) && $this->options['orderby'] == $val){
				echo ' selected="selected"';
			}
			echo ">$option</option>";
		}
		echo '</select>';
	}
	public function order_callback() {
		if(isset( $this->options['order'] ) && $this->options['order'] == 'DESC'){
			$twaic_showcontrols_a = '';
			$twaic_showcontrols_d = ' selected="selected"';
		} else {
			$twaic_showcontrols_a = ' selected="selected"';
			$twaic_showcontrols_d = '';
		}
		echo '<select id="order" name="twaic_settings[order]">
			<option value="ASC"'.$twaic_showcontrols_a.'>'.__('Ascending', 'twaic-settings').'</option>
			<option value="DESC"'.$twaic_showcontrols_d.'>'.__('Decending', 'twaic-settings').'</option>
		</select>';
	}
	public function category_callback() {
		$cats = get_terms('twaic_category');
		echo '<select id="orderby" name="twaic_settings[category]">
			<option value="">'.__('All Categories', 'twaic-settings').'</option>';
		foreach($cats as $cat){
			echo '<option value="'.$cat->name.'"';
			if(isset( $this->options['category'] ) && $this->options['category'] == $cat->name){
				echo ' selected="selected"';
			}
			echo ">".$cat->name."</option>";
		}
		echo '</select>';
	}
	
    // Setup Section
	
	public function image_size_callback() {
		$image_sizes = get_intermediate_image_sizes();
		echo '<select id="image_size" name="twaic_settings[image_size]">
			<option value="standard_resolution"';
			if(isset( $this->options['image_size'] ) && $this->options['image_size'] == 'standard_resolution'){
				echo ' selected="selected"';
			}
			echo '>Standard Resolution (640X640)</option>';
			echo '<option value="low_resolution"';
			if(isset( $this->options['image_size'] ) && $this->options['image_size'] == 'low_resolution'){
				echo ' selected="selected"';
			}
			echo '>Low Resolution (320X320)</option>';
			echo '<option value="thumbnail"';
			if(isset( $this->options['image_size'] ) && $this->options['image_size'] == 'thumbnail'){
				echo ' selected="selected"';
			}
			echo '>Thumbnail (150X150)</option>';
			
		
		echo '</select>';
        echo '<p class="description">'.__("If your need small image, you can use a smaller image size to increase page speed.", 'twaic-settings').'</p>';
	}
	public function theme_callback() {
		
		echo '<select id="theme" name="twaic_settings[theme]">
			<option value="theme1"';
			if(isset( $this->options['theme'] ) && $this->options['theme'] == 'theme1'){
				echo ' selected="selected"';
			}
			echo '>Theme 1</option>';
			echo '<option value="theme2"';
			if(isset( $this->options['theme'] ) && $this->options['theme'] == 'theme2'){
				echo ' selected="selected"';
			}
			echo '>Theme 2 (Coming Soon)</option>';
			echo '<option value="theme3"';
			if(isset( $this->options['theme'] ) && $this->options['theme'] == 'theme3'){
				echo ' selected="selected"';
			}
			echo '>Theme 3 (Coming Soon)</option>';
			
		
		echo '</select>';
        echo '<p class="description">'.__("Choose your desire design.", 'twaic-settings').'</p>';
	}
	public function show_user_details_callback(){
		echo '<select id="show_user_details" name="twaic_settings[show_user_details]">
			<option value="1"';
			if(isset( $this->options['show_user_details'] ) && $this->options['show_user_details'] == '1'){
				echo ' selected="selected"';
			}
			echo '>Yes</option>';
			echo '<option value="0"';
			if(isset( $this->options['show_user_details'] ) && $this->options['show_user_details'] == '0'){
				echo ' selected="selected"';
			}
			echo '>No</option>';
		
		echo '</select>';
        echo '<p class="description">'.__("Want to show user info?", 'twaic-settings').'</p>';
	}
	// Markup Section 
	public function before_wrapper_callback(){
		printf('<input type="text" id="before_wrapper" name="twaic_settings[before_wrapper]" value="%s" size="15" />',
					isset( $this->options['before_wrapper'] ) ? esc_attr( $this->options['before_wrapper']) : '<ul>');
		
	}
	public function after_wrapper_callback(){
		printf('<input type="text" id="after_wrapper" name="twaic_settings[after_wrapper]" value="%s" size="15" />',
					isset( $this->options['after_wrapper'] ) ? esc_attr( $this->options['after_wrapper']) : '</ul>');
		
	}
	public function before_title_callback() {
			printf('<input type="text" id="before_title" name="twaic_settings[before_title]" value="%s" size="15" />',
					isset( $this->options['before_title'] ) ? esc_attr( $this->options['before_title']) : '<h4>');
	}
	public function after_title_callback() {
			printf('<input type="text" id="after_title" name="cptbc_settings[after_title]" value="%s" size="15" />',
					isset( $this->options['after_title'] ) ? esc_attr( $this->options['after_title']) : '</h4>');
	}
	public function before_caption_callback() {
			printf('<input type="text" id="before_caption" name="cptbc_settings[before_caption]" value="%s" size="15" />',
					isset( $this->options['before_caption'] ) ? esc_attr( $this->options['before_caption']) : '<p>');
	}
	public function after_caption_callback() {
			printf('<input type="text" id="after_caption" name="cptbc_settings[after_caption]" value="%s" size="15" />',
					isset( $this->options['after_caption'] ) ? esc_attr( $this->options['after_caption']) : '</p>');
	}
	
	
}

if( is_admin() ){
		$twaic_settings_page = new twaic_settings_page();
}

// Add settings link on plugin page
function twaic_settings_link ($links) { 
	$settings_link = '<a href="admin.php?page=twaic">'.__('Settings', 'twaic-settings').'</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
$twaic_plugin = TWAIC_PLUGIN_BASENAME; 
add_filter("plugin_action_links_$twaic_plugin", 'twaic_settings_link' );


function twaic_msg() {
	$option = get_option( 'twaic_settings' );
	if(!isset($option['access_token']) || $option['access_token']==""){
		echo '<div class="notice notice-success is-dismissible">
		<h3>Advanced Instagram Carousel</h3>
		<p class="message">Configure Instagram Access Token first. <a href="admin.php?page=twaic">Settings</a></p>
		<p class="message">Follow our tutorial <a href="http://www.thelogicalcoder.com/how-to-register-your-app-in-instagram/" target="_blank">http://www.thelogicalcoder.com/how-to-register-your-app-in-instagram/</a></p>
	</div>';
	}
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'twaic_msg' );
