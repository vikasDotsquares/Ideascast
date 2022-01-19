<style type="text/css">
	.multiselect-container.dropdown-menu li:not(.multiselect-group) a label.checkbox {
	    padding: 6px 20px 6px 40px;
	    width: 100%;
	}
	.multiselect-container.dropdown-menu > li:not(.multiselect-group) {
	    display: inline-block;
	    width: 100% !important;
	    float: left;
	}
	.multi_select_template .modal-title {
		display: block;
	    overflow: hidden;
	    text-overflow: ellipsis;
	    white-space: nowrap;
	}
</style>
<?php 

	echo $this->Form->create('Template', array('url' => array('controller' => 'templates', 'action' => 'select_multi_template'), 'class' => 'form-bordered', 'id' => 'modelFormMultiTemplate')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header multi_select_template">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel"> <?php echo strip_tags($project_detail['Project']['title']); ?> </h3>
	</div>

		<?php 
if(isset($templates) && !empty($templates)) {
	usort($templates, function($a, $b) {
	    return $a['TemplateRelation']['title'] > $b['TemplateRelation']['title'];
	});
		 ?>
	<!-- POPUP MODAL BODY -->
	<div class="modal-body clearfix">
		<!-- <div class="loading-rays"></div> -->
		<?php if(isset($project_id) && !empty($project_id) ){
					echo $this->Form->input('project_id', [ 'type' => 'hidden', 'value' => $project_id ] );
			} ?>
		<div class="col-sm-12 clearfix nopadding" style="margin-bottom: 10px;">
			<!-- <span class="error-message text-danger non-admin">Moving functionality is only available for self created templates.</span> -->
		</div>
		<div class="form-group parent_template_list">
			<label>Add Knowledge Templates to Project:</label>
			<select name="data[Template][template_list][]" id="template_list" multiple="multiple" class="form-control">
			<?php 
			foreach ($templates as $key => $value) {
			 ?>
			<option value="<?php echo $value['TemplateRelation']['id']; ?>" data-admin='<?php echo ($value['TemplateRelation']['user_id'] == $this->Session->read('Auth.User.id')) ? true : false; ?>'><?php echo strip_tags($value['TemplateRelation']['title']); ?></option>
			<?php 
			} 
			 ?>
			 </select>
			<span class="error-message text-danger" ></span>
		</div>
	</div>
        

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer"> 
		 <button type="submit"  class="btn btn-success process">Publish to Project</button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
 <?php }else{ ?>
 	<div class="modal-body clearfix"></div>
 	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">  
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
		<?php } ?>

		<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
	$(function() {
	    // TEMPLATES MULTISELECT BOX INITIALIZATION
	    $.template_list = $('#template_list').multiselect({
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '85%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'template_list',
	        includeSelectAllOption: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search Knowledge Templates',
	        enableCaseInsensitiveFiltering: true,
	        nonSelectedText: 'Select Knowledge Templates',
	        templates: {
                button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><span class="multiselect-selected-text"></span> <span class="arrow fa fa-arrow-down"></span></button>',
                ul: '<ul class="multiselect-container dropdown-menu"></ul>',
                filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
                li: '<li><a tabindex="0"><label></label></a></li>',
                divider: '<li class="multiselect-item divider"></li>',
                liGroup: '<li class="multiselect-item multiselect-group"><label></label></li>'
            },
	        onChange: function(option, checked) { },
	        onDropdownHidden: function(option, closed, select) { }
	    });
 

	    $('.process').on('click', function(event){
	    	event.preventDefault();

	    	var $this = $(this),
	    		$form = $this.closest('form'),
	    		params = $form.serializeArray(),
	    		template_list = $('#template_list');

	    	$form.find('.error-message').html('');
	    	if( template_list.val() == '' || template_list.val() === undefined  || template_list.val() === null ) {
	    		template_list.parents('.parent_template_list').find('.error-message').html('At least one template is required')
	    		return;
	    	}

	    	$.ajax({
	            url: $js_config.base_url + 'templates/select_multi_template',
	            type: "POST",
	            data: $.param(params),
	            dataType: "JSON",
	            global: true,
	            success: function(response) {
	                if(response.success) {
	                	$('#modal_multi_templates').modal('hide');
	                	location.href = $js_config.base_url +  'projects/index/' + $js_config.project_id
	                }
	            }
	        })

	    })
 

	})
</script> 
