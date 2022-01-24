<style>
.error-message {
	/* display: inline-block; */
}
.participant-boxes{
	display: block;
	margin: 0 0 5px;
	width: 100%;
}
#frm_objective_project .form-group {
	margin-bottom: 10px;
}
.btn-white {
	background-color: #FFFFFF;
	border-color: #dedede;
	color: #333;
}
.inset-shadow {
	box-shadow: 3px 3px 4px 1px #999 inset !important;
}

</style>

<?php

?>

<script type="text/javascript" >


$(function(){

	$("body").delegate("#summary_dashboard", 'click', function (event) {
		event.preventDefault();
		$(this).removeData('bs.tooltip');
		$('.tooltip').remove()
		$(this).removeClass('tipText');

		location.href = $js_config.base_url + 'projects/objectives';

	})

	$("body").delegate("ul.element-icons-list li span.btn:not(.blocked)", 'click', function (event) {

		var $span = $(this),
			data = $span.data(),
			URL = data.remote;

		if (URL.trim() !== '') {
			window.location.href = URL
		}
	})

	$.submit_filter = function(project_id, aligned_id, rag_status,program_id) {

		var project_id = project_id || 0,
			program_id = program_id || 0,
			aligned_id = aligned_id || 0;


		var box_urls = {
				filtered_data: $js_config.base_url + 'projects/filtered_data'
			},
			url_params = {project_id: project_id, aligned_id: aligned_id, rag_status: rag_status,program_id: program_id };


			$.ajax({
				url: $js_config.base_url + 'projects/filtered_data',
				type: "POST",
				data: $.param(url_params),
				global: false,
				success: function (response) {
					$(document).find( ".objective_details1" ).empty().append(response);
				}
			})
		return;
	}

	var project_id = 0;
	var aligned_id = 0;
	var rag_status = 0;
	var program_id = 0;

	<?php if( isset($project_id) && !empty($project_id) ) { ?>
			project_id = '<?php echo $project_id; ?>';
	<?php } ?>
	<?php if( isset($aligned_id) && !empty($aligned_id) ) { ?>
			aligned_id = '<?php echo $aligned_id; ?>';
	<?php } ?>
	<?php if( isset($rag_status) && !empty($rag_status) ) { ?>
			rag_status = '<?php echo $rag_status; ?>';
	<?php } ?>
	<?php if( isset($program_id) && !empty($program_id) ) { ?>
			program_id = '<?php echo $program_id; ?>';
	<?php } ?>
	setTimeout(function(){
		$.submit_filter(project_id, aligned_id, rag_status,program_id);
		// $('#submit_filter').trigger('click')
	}, 1200)
})
</script>
<div class="objective_details1"></div>
