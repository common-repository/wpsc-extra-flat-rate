<?php
/*
* Plugin Name:       WordPress e Commerce Extra Flat Rate Shipping
* Plugin URI:        http://www.multidots.com/
* Description:     	 WordPress eCommerce Extra Flat Rate Shipping plugin allows store admin to add/create new flat rate options in your WP eCommerce site.
* Version:           1.0.5
* Author:            Multidots
* Author URI:        http://www.multidots.com/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       wpsc-extra-flat-rate.php
* Domain Path:       /languages
*/


class wpe_flatrate {
		
	public function __construct(){   
	}
	var $internal_name, $name;

	/**
	 * Constructor
	 *
	 * @return boolean Always returns true.
	 */
	function wpe_flatrate() {
		add_action( 'wpsc_register_settings_tabs', array($this,'md_plugin_settings_tabs'),10,1);
		add_action( 'wpsc_load_settings_tab_class', array($this,'md_plugin_settings_tabs_class'),10,1);
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
		add_action( 'wp_head',  array($this,'own_script'),10 );
		
		add_action('admin_init', array($this,'welcome_wp_eCommerce_flate_rate_screen_do_activation_redirect' ) );
      	add_action('admin_menu', array($this,'welcome_pages_screen_wp_eCommerce_flate_rate' )) ;
        add_action('wp_eCommerce_extra_flate_rate_about', array($this, 'wp_eCommerce_extra_flate_rate_about'));
       	add_action('admin_print_footer_scripts',  array($this,'wp_eCommerce_flate_rate_pointers_footer') );
        add_action('admin_menu', array($this, 'welcome_screen_wp_eCommerce_flate_rate_remove_menus'), 999 );
        
		
		add_action( 'wp_ajax_get_country_code',  array($this,'wpe_get_country_code') );
		add_action( 'wp_ajax_nopriv_get_country_code',  array($this,'wpe_get_country_code') );
		
		add_action( 'wp_ajax_hide_subscribe_wp_ecommerce',array($this,'hide_subscribe_wp_ecommercefn') );
		
		register_activation_hook(__FILE__, array($this, 'activate_wpe_flatrate'));
		register_deactivation_hook(__FILE__, array($this, 'deactivate_wpe_flatrate'));
		$this->internal_name = "wpe_extra_flatrate";
		$this->name= __( "Extra Flat Rate", 'wpsc' );
		$this->is_external=false;
		return true;
	}
	
	/**
	 * Function for welcome screen 
	 * 
	 */
	
	public function welcome_wp_eCommerce_flate_rate_screen_do_activation_redirect (){ 
		
		if (!get_transient('_wp_eCommerce_extra_flate_rate_welcome_screen')) {
			return;
		}
		
		// Delete the redirect transient
		delete_transient('_wp_eCommerce_extra_flate_rate_welcome_screen');

		// if activating from network, or bulk
		if (is_network_admin() || isset($_GET['activate-multi'])) {
			return;
		}
		// Redirect to extra cost welcome  page
		wp_safe_redirect(add_query_arg(array('page' => 'wp-eCommerce-extra-flate-rate&tab=about'), admin_url('index.php')));
    
	
	} 
	
	public function welcome_pages_screen_wp_eCommerce_flate_rate () { 
		add_dashboard_page(
		'WP-eCommerce-Extra-Flat-Rate-Shipping Dashboard', 'WP eCommerce Extra Flat Rate Shipping Dashboard', 'read', 'wp-eCommerce-extra-flate-rate', array(&$this, 'welcome_screen_content_wp_eCommerce_extra_flate_rate')
		);
		
	}
	
	//function for remove welcome page deshboard menu
	
	public function welcome_screen_wp_eCommerce_flate_rate_remove_menus ( ){ 
		remove_submenu_page( 'index.php', 'wp-eCommerce-extra-flate-rate' );
	} 
	
	//function for welcome secreen page 
	
	public function welcome_screen_content_wp_eCommerce_extra_flate_rate ( ) {  
			$current_user = wp_get_current_user(); 
			if(!empty( $_GET['page'] ) && $_GET['page'] === 'wp-eCommerce-extra-flate-rate') {
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
			}
		
		?>
		<style type="text/css"> 
		.free_plugin {margin-bottom: 20px;}.paid_plugin {margin-bottom: 20px;}
		.paid_plugin h3 {border-bottom: 1px solid #ccc;padding-bottom: 20px;}
		.free_plugin h3 {padding-bottom: 20px;border-bottom: 1px solid #ccc;}
		.free_plugin {margin-bottom: 20px;}
		.paid_plugin {margin-bottom: 20px;}
		.paid_plugin h3 {border-bottom: 1px solid #ccc;padding-bottom: 20px;}
		.free_plugin h3 {padding-bottom: 20px;border-bottom: 1px solid #ccc;}
		.plug-containter {width: 100%;display: inline-block;margin-left: 20px;}
		.plug-containter .contain-section {width: 25%;display: inline-block;margin-top: 30px;}
		.plug-containter .contain-section .contain-img {width: 30%;display: inline-block;}
		.plug-containter .contain-section .contain-title {width: 50%;display: inline-block;vertical-align: middle;margin-left: 10px;}
		.plug-containter .contain-section .contain-title a {text-decoration: none;line-height: 20px;font-weight: bold;}
		.version_logo_img {position: absolute;right: 0;top: 0;}
		
		</style>
		<div class="wrap about-wrap">
            <h1 style="font-size: 2.1em;"><?php printf(__('Welcome to WP eCommerce Extra Flat Rate Shipping', 'wpsc-extra-flat-rate')); ?></h1>

            <div class="about-text woocommerce-about-text">
        <?php
        $message = '';
        printf(__('%s WP eCommerce Extra Flat Rate Shipping plugin allows store admin to add/create new flat rate options in your WP eCommerce site.', 'wpsc-extra-flat-rate'), $message);
        ?>
                <img class="version_logo_img" src="<?php echo plugin_dir_url(__FILE__) . 'images/wpsc-extra-flat-rate.png'; ?>">
            </div>

        <?php
        $setting_tabs_wc = apply_filters('WP_eCommerce_extra_flate_rate_setting_tab', array("about" => "Overview", "other_plugins" => "Checkout our other plugins" ));
        $current_tab_wc = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
        $aboutpage = isset($_GET['page'])
        ?>
            <h2 id="woo-extra-cost-tab-wrapper" class="nav-tab-wrapper">
            <?php
            foreach ($setting_tabs_wc as $name => $label)
            echo '<a  href="' . home_url('wp-admin/index.php?page=wp-eCommerce-extra-flate-rate&tab=' . $name) . '" class="nav-tab ' . ( $current_tab_wc == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            ?>
            </h2>
                <?php
                foreach ($setting_tabs_wc as $setting_tabkey_wc => $setting_tabvalue) {
                	switch ($setting_tabkey_wc) {
                		case $current_tab_wc:
                			do_action('wp_eCommerce_extra_flate_rate_' . $current_tab_wc);
                			break;
                	}
                }
                ?>
            <hr />
            <div class="return-to-dashboard">
                <a href="<?php echo home_url('/wp-admin/options-general.php?page=wpsc-settings&tab=extra_flat_rate'); ?>"><?php _e('Go to WP eCommerce Extra Flat Rate Shipping Settings', 'wpsc-extra-flat-rate'); ?></a>
            </div>
        </div>	
		
	<?php }
	
	//function for display content for welcome page about tag 
	
	public function wp_eCommerce_extra_flate_rate_about () { ?>
		<div class="changelog">
            </br>
           	<style type="text/css">
				p.wp_eCommerce_flate_rate_overview {max-width: 100% !important;margin-left: auto;margin-right: auto;font-size: 15px;line-height: 1.5;}.wp_eCommerce_flate_rate_content_ul ul li {margin-left: 3%;list-style: initial;line-height: 23px;}
			</style>  
            <div class="changelog about-integrations">
                <div class="wc-feature feature-section col three-col">
                    <div>
                        <p class="wp_eCommerce_flate_rate_overview"><?php _e('WP eCommerce Extra Flat Rate Shipping plugin provides you an interface in WP eCommerce Extra Flat Rate Shipping Rate Shipping setting section from admin side. So admin can add Multiple Shipping options(Extra Flat Rate Shipping) or remove any existing shipping from the backend. Admin set options will be displayed from the front side. So the user can choose shipping method based on that.', 'wpsc-extra-flat-rate'); ?></p> 
                        
                         <p class="wp_eCommerce_flate_rate_overview"><?php _e('This plugin is for those users who wants to use Multiple shipping on the website. By using this plugin, You can add multiple shipping in your WPSC Extra Flat Rate Shipping website as well as you can add/remove it as per your requirement.', 'wpsc-extra-flat-rate'); ?></p>
                        
                    </div>
                </div>
            </div>
        </div>
	
	<?php } 
	
	
	
	//function for print wp-pointer notice 
	
	public function wp_eCommerce_flate_rate_pointers_footer () { 
			global $wpdb;
			$admin_pointers =  self::wp_eCommerce_flate_rate_admin_pointers();
			$data = get_option( 'Wp_eCommerce_extra_flate_rate' );	         
			
	    ?>
	    <script type="text/javascript">
	        /* <![CDATA[ */
	        <?php 
	        	if( !empty( $data ) && $data == 'yes') { } else {   
	        ?>
	        ( function($) {
	            <?php
	            foreach ( $admin_pointers as $pointer => $array ) {
	               if ( $array['active'] ) { 
	                  ?>
	            $( '<?php echo $array['anchor_id']; ?>' ).pointer( { 
	                content: '<?php echo $array['content']; ?>',
	                position: {
	                    edge: '<?php echo $array['edge']; ?>',
	                    align: '<?php echo $array['align']; ?>'
	                },
	              
	                close: function() {
	                	
	                	 $.post( ajaxurl, {
	                        pointer: '<?php echo $pointer; ?>',
	                        action: 'dismiss-wp-pointer'
	                    } );
	                } 
	            } ).pointer( 'open' );
	            <?php
	         }
	      } 
		
	      ?>
	        } )(jQuery);
	        /* ]]> */
	        <?php } ?>
	    </script>
	<?php
	} 
	
	
 function wp_eCommerce_flate_rate_admin_pointers () {
	global  $wpdb;
	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
    $version = '1_0'; // replace all periods in 1.0 with an underscore
   	$prefix = 'wp_eCommerce_flate_rate_admin_pointers' . $version . '_';
   
    $new_pointer_content = '<h3>' . __( 'Welcome to WP eCommerce Extra Flat Rate Shipping' ) . '</h3>';
    $new_pointer_content .= '<p>' . __( 'WP eCommerce Extra Flat Rate Shipping plugin allows store admin to add/create new flat rate options in your WP eCommerce site. ' ) . '</p>';
	
    update_option('Wp_eCommerce_extra_flate_rate','yes');
    
    return array(
        $prefix . 'wp_eCommerce_flate_rate_admin_pointers' => array(
            'content' => $new_pointer_content,
            'anchor_id' => '#menu-settings',
            'edge' => 'left',
            'align' => 'left',
            'active' => ( ! in_array( $prefix . 'wp_eCommerce_flate_rate_admin_pointers', $dismissed ) )
        )
    );
}
	
	
	
     public function hide_subscribe_wp_ecommercefn() {
     	global $wpdb;
    	$email_id= $_POST['email_id'];
    	update_option('wp_ecommerce_plugin_notice_shown', 'true');
    }
	

	/**
	 * Returns i18n-ized name of shipping module.
	 *
	 * @return string
	 */
	function getName() {
		return $this->name;
	}

	public function activate_wpe_flatrate() { 
		global $wpdb,$woocommerce;
		set_transient( '_wp_eCommerce_extra_flate_rate_welcome_screen', true, 30 );
	}
	public function deactivate_wpe_flatrate() {
	
	}
	public function md_plugin_settings_tabs_class( $page_instance ) {

		$current_tab_id = $page_instance->get_current_tab_id();
		if ( in_array( $current_tab_id, array( 'extra_flat_rate' ) ) ){

			require_once ('includes/WPSC_Settings_Tab_Extra_Flat_Rate.php');
		}
	}
	
	

	public function enqueue_scripts() {

		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script( 'wpe-extra-flat-rate', plugin_dir_url( __FILE__ ) . 'includes/wpecustom.js',false );
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );

	}
	public function md_plugin_settings_tabs($settings_page)
	{
	
		$settings_page->register_tab('extra_flat_rate','Extra Flat Rate');
	}

	public function wpe_get_country_code() {
		$country_code = $_POST['ccode'];
		$get_flat_rate = get_option( 'md_flat_rate', array() );
		$get_rate = get_option( 'flatrate_layers', array() );
		$tmp_arrray = array();
		$temp_layer = array();
		foreach ($get_flat_rate[0] as $key_flat=>$val_flat) {
			if (!empty($get_flat_rate[0][$key_flat]['label_name'])) {
				if ($get_flat_rate[0][$key_flat]['country'] == $country_code) {
					if (array_key_exists($get_flat_rate[0][$key_flat]['label_name'], $get_rate)) {
						$temp_layer[$get_flat_rate[0][$key_flat]['label_name']] = $get_flat_rate[0][$key_flat]['cost_value'];
					}
				}else {
					$temp_layer[$get_flat_rate[0][$key_flat]['label_name']] = $get_rate[$get_flat_rate[0][$key_flat]['label_name']];
				}
			}
		}
		ksort($temp_layer);
		update_option('fixedrate_layers_tmp',$temp_layer);
	}

	public function own_script() {?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#shipping_list_item_wpe_extra_flatrate").remove();
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		jQuery('#current_country').on('change', function() {

			var data = {
			'action': 'get_country_code',
			'ccode': this.value
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {

			});




		});

		jQuery('form').each(function(){
			var cmdcode = jQuery(this).find('input[name="cmd"]').val();
			var bncode = jQuery(this).find('input[name="bn"]').val();

			if (cmdcode && bncode) {
				jQuery('input[name="bn"]').val("Multidots_SP");
			}else if ((cmdcode) && (!bncode )) {
				jQuery(this).find('input[name="cmd"]').after("<input type='hidden' name='bn' value='Multidots_SP' />");
			}


		});

	});
	</script>	
	<?php }


	/**
	 * Returns internal name of shipping module.
	 *
	 * @return string
	 */	
	function getInternalName() {
		return $this->internal_name;
	}

	/**
	 * generates row of table rate fields
	 */
	private function output_row( $key = '', $shipping = '' ) {

	}

	/**
	 * Returns HTML settings form. Should be a collection of <tr> elements containing two columns.
	 *
	 * @return string HTML snippet.
	 */
	function getForm() {

	}

	/**
	 * Saves shipping module settings.
	 *
	 * @return boolean Always returns true.
	 */
	function submit_form() {
		if ( ! isset( $_POST['md_extra_cost_name'] ) || ! isset( $_POST['md_extra_cost_value'] ) )
		return false;


		$country_code = (array) $_POST['md_extra_cost_country_code'];
		$layers = (array) $_POST['md_extra_cost_name'];
		$shippings = (array) $_POST['md_extra_cost_value'];
		$new_layer = array();
		if ( $shippings != '' ) {
			foreach ( $shippings as $key => $price ) {
				if ( ! is_numeric( $key ) || ! is_numeric( $price ) )
				continue;
				if ($country_code[$key] =='*') {
					$new_layer[ $layers[ $key ] ] = $price;
				}else {
					$new_layer[ $layers[ $key ] ] = 0;
				}
			}
		}

		// Sort the data before it goes into the database. Makes the UI make more sense
		krsort( $new_layer );
		update_option( 'flatrate_layers', $new_layer );
		return true;
	}
        function remove_data() {
		if ( ! isset( $_POST['md_extra_cost_name'] ) || ! isset( $_POST['md_extra_cost_value'] ) )
		return false;


		$country_code = (array) $_POST['md_extra_cost_country_code'];
		$layers = (array) $_POST['md_extra_cost_name'];
		$shippings = (array) $_POST['md_extra_cost_value'];
		$new_layer = array();
		if ( $shippings != '' ) {
			foreach ( $shippings as $key => $price ) {
				if ( ! is_numeric( $key ) || ! is_numeric( $price ) )
				continue;
				if ($country_code[$key] =='*') {
					$new_layer[ $layers[ $key ] ] = $price;
				}else {
					$new_layer[ $layers[ $key ] ] = 0;
				}
			}
		}

		// Sort the data before it goes into the database. Makes the UI make more sense
		krsort( $new_layer );
		delete_option( 'flatrate_layers', $new_layer );
		return true;
	}
        

	/**
	 * returns shipping quotes using this shipping module.
	 *
	 * @return array collection of rates applicable.
	 */
	function getQuote() {

		global $wpdb, $wpsc_cart;
		$md_flat_rate = array();
		$md_flat_rate = get_option('md_flat_rate',true);
		$md_flat_rate = maybe_unserialize($md_flat_rate);

		if ( wpsc_get_customer_meta( 'nzshpcart' ) ) {
			$shopping_cart = wpsc_get_customer_meta( 'nzshpcart' );
		}
		if ( is_object( $wpsc_cart ) ) {
			$price = $wpsc_cart->calculate_subtotal( true );
		}

		$layers = get_option( 'fixedrate_layers_tmp' );

		if ($layers != '') {

			// At some point we should probably remove this as the sorting should be
			// done when we save the data to the database. But need to leave it here
			// for people who have non-sorted settings in their database
			krsort( $layers );
			return $layers;


		}
	}

	/**
	 * calculates shipping price for an individual cart item.
	 *
	 * @param object $cart_item (reference)
	 * @return float price of shipping for the item.
	 */
	function get_item_shipping( &$cart_item ) {

		global $wpdb, $wpsc_cart;

		$unit_price = $cart_item->unit_price;
		$quantity = $cart_item->quantity;
		$weight = $cart_item->weight;
		$product_id = $cart_item->product_id;

		$uses_billing_address = false;
		foreach ( $cart_item->category_id_list as $category_id ) {
			$uses_billing_address = (bool) wpsc_get_categorymeta( $category_id, 'uses_billing_address' );
			if ( $uses_billing_address === true ) {
				break; /// just one true value is sufficient
			}
		}

		if ( is_numeric( $product_id ) && ( get_option( 'do_not_use_shipping' ) != 1 ) ) {
			if ( $uses_billing_address == true ) {
				$country_code = $wpsc_cart->selected_country;
			} else {
				$country_code = $wpsc_cart->delivery_country;
			}

			if ( $cart_item->uses_shipping == true ) {
				//if the item has shipping
				$additional_shipping = '';
				if ( isset( $cart_item->meta[0]['shipping'] ) ) {
					$shipping_values = $cart_item->meta[0]['shipping'];
				}
				if ( isset( $shipping_values['local'] ) && $country_code == get_option( 'base_country' ) ) {
					$additional_shipping = $shipping_values['local'];
				} else {
					if ( isset( $shipping_values['international'] ) ) {
						$additional_shipping = $shipping_values['international'];
					}
				}
				$shipping = $quantity * $additional_shipping;
			} else {
				//if the item does not have shipping
				$shipping = 0;
			}
		} else {
			//if the item is invalid or all items do not have shipping
			$shipping = 0;
		}
		return $shipping;
	}
}
$wpe_flatrate = new wpe_flatrate();
$wpsc_shipping_modules[$wpe_flatrate->getInternalName()] = $wpe_flatrate;
?>