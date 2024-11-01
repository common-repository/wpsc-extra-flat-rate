<?php

class WPSC_Settings_Tab_Extra_Flat_Rate extends WPSC_Settings_Tab

{

public function display() { 
	global $wpdb;
	$layers = get_option( 'flatrate_layers', array() );
	$get_md_flat_rate = get_option( 'md_flat_rate', array() );
	$final_array = array();
	$current_user = wp_get_current_user();
	wp_enqueue_style( 'wp-jquery-ui-dialog' );
	if (!get_option('wp_ecommerce_plugin_notice_shown')) {
		echo '<div id="wp_ecommerce_dialog" title="Basic dialog"><p>Subscribe for latest plugin update and get notified when we update our plugin and launch new products for free! </p> <p><input type="text" id="txt_user_sub_wp_ecommerce" class="regular-text" name="txt_user_sub_wp_ecommerce" value="'.$current_user->user_email.'"></p></div>';
	}

	?>
	
<h3>Add Extra Flat Rate Items</h3>


<table class="md_wc_extra_cost wc_input_table sortable widefat">
   
        <tr>
            <th width="4%" class="sort">&nbsp;</th>
            <th width="8%">Extra&nbsp;Cost&nbsp;Country&nbsp;Code&nbsp;
                <span class="tips"></span>
            </th>
            <th width="40%">Extra&nbsp;Cost&nbsp;name&nbsp;
                <span class="tips"></span>
            </th>
            <th width="48%">Extra&nbsp;Cost&nbsp;value&nbsp;
                <span class="tips"></span>
            </th>
        </tr>
         <tr class="new">
            <td width="4%" class="sort"></td>
            <td class="name" width="8%">
         
                <input type="text" name="md_extra_cost_country_code[]" >
                </td>
                <td class="name" width="40%">
                    <input type="text"  name="md_extra_cost_name[]">
                    </td>
                    <td class="rate" width="48%">
                    <input type="number" step="any" min="0"   placeholder="0" name="md_extra_cost_value[]">
 
                       <a href="javascript:void(0);"  class="button md_button_add plus insert">Insert row</a>
                        </td>
                       
                    </tr> 
   	   <?php if ( ! empty( $get_md_flat_rate[0] ) ){
   	   	foreach( $get_md_flat_rate[0] as $key_rate => $val ){
   	   		if (!empty($get_md_flat_rate[0][$key_rate]['country']) 
   	   		&& !empty($get_md_flat_rate[0][$key_rate]['label_name']) && !empty($get_md_flat_rate[0][$key_rate]['cost_value'])) {
   	   		?>
        <tr class="new">
            <td width="4%" class="sort"></td>
            <td class="name" width="8%">
         
                <input type="text" value="<?php echo esc_attr( $get_md_flat_rate[0][$key_rate]['country'] ); ?>"  name="md_extra_cost_country_code[]" >
                </td>
                <td class="name" width="40%">
                    <input type="text" value="<?php echo esc_attr($get_md_flat_rate[0][$key_rate]['label_name'] ); ?>" name="md_extra_cost_name[]">
                    </td>
                    <td class="rate" width="48%">
   <input type="number" step="any" min="0"   value="<?php echo esc_attr( $get_md_flat_rate[0][$key_rate]['cost_value'] ); ?>" placeholder="0" name="md_extra_cost_value[]">
  <a href="javascript:void(0);" id="md_button_remove">Remove</a>
                  
                        </td>
                       
                    </tr>
              <?php }
   	   	}
   	   } ?>
                        
             
            </table>
<?php 
}
public function callback_submit_options() {
	
	$temp = array();
	$mergearray = array();
	

	$md_extra_cost_country = sanitize_text_field( $_POST['md_extra_cost_country_code'] );
	$md_extra_cost_name = sanitize_text_field( $_POST['md_extra_cost_name'] );
	$md_extra_cost_value = sanitize_text_field ( $_POST['md_extra_cost_value'] );

	$length = count($md_extra_cost_country);
	
	for ($i = 0; $i < $length; $i++) {
	  $temp['country'] = $md_extra_cost_country[$i];
	  $temp['label_name'] = $md_extra_cost_name[$i];
	  $temp['cost_value'] = $md_extra_cost_value[$i];
	  
	  array_push($mergearray,$temp);
	}

	update_option('md_flat_rate',array($mergearray));
	

	
}

}