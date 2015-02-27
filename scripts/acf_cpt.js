/* **********************************************
     Begin acf_cpt.js
********************************************** *//*
*  acf_cpt
*
*  All javascript needed for ACF CPT to work
*
*  @type	awesome
*  @date	October 25, 2013
*
*  @param	N/A
*  @return	N/A

*/

jQuery(function($){
	$(document).on('change','#custom_post_template', function(){
		// console.log(acf.screen);
		// acf.screen.cpt_template = $(this).val();
		// $(document).trigger("acf/update_field_groups");
		// console.log(acf.screen);

		console.log($(this).val());
		$.ajax({
			url : '/wp-admin/admin-ajax.php',
			type: 'POST',
			data : {
				action: 'acf/post/get_field_groups',
				cpt_template: $(this).val(),
				post_id: $('#post_ID').val()
			},
			success: function(data){
				console.log(data);
			}
		})
	});
});