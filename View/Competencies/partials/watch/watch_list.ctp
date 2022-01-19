<?php //pr($watches); ?>
<style type="text/css">
	a.reset-watch.disabled {
	    pointer-events: none;
	    opacity: 0.5;
	}
</style>
<?php if(isset($watches) && !empty($watches)){ ?>
	<?php foreach ($watches as $key => $value) { ?>
	<div class="popup-watch-row" data-id="<?php echo $value['ucw']['id']; ?>">
		<div class="popup-watch-col popup-watch-col1"><?php echo htmlentities($value['ucw']['name'], ENT_QUOTES, "UTF-8"); ?></div>
		<div class="popup-watch-col popup-watch-col2">
			<a href="#" class="tipText view-watch" title="View"><i class="view-icon"></i></a>
			<a href="#" class="tipText reset-watch <?php if(!isset($value['ucw']['last_run']) || empty($value['ucw']['last_run'])){ ?>disabled<?php } ?>" title="Reset"><i class="resetblack"></i></a>
			<a href="#" class="tipText delete-watch" title="Delete"><i class="deleteblack"></i></a>
		</div>
	</div>
<?php } ?>
<?php }else{ ?>
	<div class="no-sec-data-found">No watches</div>
<?php } ?>

<script type="text/javascript">
	$(()=>{
		$(".view-watch").off('click').on('click', function(event) {
			event.preventDefault();
			var $parent = $(this).parents('.popup-watch-row:first')
			var id = $parent.data('id');
			$.ajax({
	            url: $js_config.base_url + 'competencies/watch_list',
	            type: 'POST',
	            dataType: 'json',
	            data: {id: id},
	            success: function(response){
	            	if(response.success){
	            		var data = JSON.parse(response.content);
	            		// CLEAR ALL DD VALUES BEFORE FILL DESIRED
	            		$('#watch_skill, #watch_skill_level, #watch_skill_exp, #watch_subject, #watch_subject_level, #watch_subject_exp, #watch_domain, #watch_domain_level, #watch_domain_exp').val([]).multiselect('clearSelection').multiselect('refresh');
	                	if(data.skills){ // IF ANY SKILL DATA SELECTED
	                		$('#watch_skill').val(data.skills).multiselect('refresh');
	                		$('#watch_skill_level').val(data.skill_levels).multiselect('refresh');
	                		$('#watch_skill_exp').val(data.skill_exps).multiselect('refresh');
	                	}
	                	if(data.subjects){ // IF ANY SUBJECT DATA SELECTED
	                		$('#watch_subject').val(data.subjects).multiselect('refresh');
	                		$('#watch_subject_level').val(data.subject_levels).multiselect('refresh');
	                		$('#watch_subject_exp').val(data.subject_exps).multiselect('refresh');
	                	}
	                	if(data.domains){ // IF ANY DOMAIN DATA SELECTED
	                		$('#watch_domain').val(data.domains).multiselect('refresh');
	                		$('#watch_domain_level').val(data.domain_levels).multiselect('refresh');
	                		$('#watch_domain_exp').val(data.domain_exps).multiselect('refresh');
	                	}
	                	// IF ANY OF THE SKILL, SUBJECT OR DOMAIN DATA SELECTED THEN HIDE THE MODAL AND SHOW THE RESULT
	                	if(data.skills || data.subjects || data.domains){
	                		$('#competency_tabs a[href="#tab_watch"]').tab('show');
	                		$('#watch_showbtn').prop('disabled', false).trigger('click');
	                		$('#modal_set_watch').modal('hide');
	                	}
	            	}
	            }
	        })
		});
		$(".delete-watch").off('click').on('click', function(event) {
			event.preventDefault();
			var $parent = $(this).parents('.popup-watch-row:first')
			var id = $parent.data('id');
			$.ajax({
	            url: $js_config.base_url + 'competencies/delete_watch',
	            type: 'POST',
	            dataType: 'json',
	            data: {id: id},
	            success: function(response){
	                if(response.success){
	                	$parent.slideUp('fast', function(){
	                		$(this).remove();
	                		if(('.popup-watch-row', $(".popup-watch-list-data")).length <= 1){
	                			$.watch_list();
	                		}
	                	})
	                }
	            }
	        })
		});

		$(".reset-watch").off('click').on('click', function(event) {
			event.preventDefault();
			var $parent = $(this).parents('.popup-watch-row:first')
			var id = $parent.data('id');
			$.ajax({
	            url: $js_config.base_url + 'competencies/reset_watch',
	            type: 'POST',
	            dataType: 'json',
	            data: {id: id},
	            context: this,
	            success: function(response){
	                if(response.success){
	                	$(this).addClass('disabled');
	                }
	            }
	        })
		});
	})
</script>


