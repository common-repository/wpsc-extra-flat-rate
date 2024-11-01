jQuery(document).ready(function(){
    "use strict";
	jQuery(".md_button_add").click(function(){
		jQuery(".md_wc_extra_cost").append('<tr class="new"><td width="4%" class="sort"></td><td class="name" width="8%"><input type="text" name="md_extra_cost_country_code[]"></td><td class="name" width="40%"><input type="text" name="md_extra_cost_name[]"></td><td class="rate" width="48%"><input type="number" step="any" min="0" placeholder="0" name="md_extra_cost_value[]"><a href="javascript:void(0);" id="md_button_remove">Remove</a></td></tr>');
	});
	jQuery(".md_wc_extra_cost").on('click','#md_button_remove',function(){
		jQuery('#md_button_remove').parent().parent().remove();
	});
});