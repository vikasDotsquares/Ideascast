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
	input.fancy_input[type="radio"][disabled="disabled"]+label.fancy_label:hover {
		filter: hue-rotate(180deg);
	    background-position: left 0 !important;
	    cursor: not-allowed !important;
	}
	input.fancy_input[type="checkbox"][disabled="disabled"]+label.fancy_label:hover {
	    cursor: not-allowed !important;
	}
	.non-admin {
		display: none;
    	margin-bottom: 10px;
	}
	#template_categories option:first-child{
	    font-weight:bold;
	}
</style>
<?php 
if(isset($templates) && !empty($templates)) {
	usort($templates, function($a, $b) {
	    return $a['TemplateRelation']['title'] > $b['TemplateRelation']['title'];
	});
	echo $this->Form->create('Template', array('url' => array('controller' => 'templates', 'action' => 'copy_move_templates', $template_cat_id), 'class' => 'form-bordered', 'id' => 'modelFormMoveCopyTemplate')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel"> <?php echo($category_detail['TemplateCategory']['title']); ?> </h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body clearfix">
		<div class="col-sm-12 clearfix nopadding" style="margin-bottom: 10px;">
			<!-- <span class="error-message text-danger non-admin">Moving functionality is only available for self created templates.</span> -->
		</div>
		<div class="form-group parent_template_list">
			<select name="data[Template][template_list][]" id="template_list" multiple="multiple" class="form-control">
			<?php 
			foreach ($templates as $key => $value) {
			 ?>
			<option value="<?php echo $value['TemplateRelation']['id']; ?>" data-admin='<?php echo ($value['TemplateRelation']['user_id'] == $this->Session->read('Auth.User.id')) ? true : false; ?>'><?php echo htmlentities($value['TemplateRelation']['title'],ENT_QUOTES, "UTF-8"); ?></option>
			<?php 
			} 
			 ?>
			 </select>
			<span class="error-message text-danger" ></span>
		</div>

		<div class="col-sm-12 clearfix nopadding">
			<div class="col-sm-8 clearfix nopadding-left parent_template_categories">
				<!-- <label class="custom-dropdown" style="width: 100%;"> -->
				<?php 
					echo $this->Form->input('template_categories', array(
						'options' 		=> $template_categories,
						'empty' 		=> 'Select Target Folder',
						'type'			=> 'select',
						'size'			=> 5,
						'style'			=> 'width: 100%',
						'label' 		=> false,
						'div' 			=> false,
						'id' 			=> 'template_categories',
						'class' 		=> 'gray',
						'placeholder' 	=> "Select Target Folder"
					));
				 ?>
				<!-- </label> -->
				<span class="error-message text-danger" ></span>
			</div>
			<div class="col-sm-4 clearfix nopadding-right">
				<div class="options copy-move">
					<input type="radio" id="option_move" name="data[Template][option_copy_move]" class="fancy_input rdo-copy-move" value="1" checked="checked">
					<label  class="fancy_label text-black tipText" style="font-weight: 700" for="option_move">Move</label>
					<input type="radio" id="option_copy" name="data[Template][option_copy_move]" class="fancy_input rdo-copy-move" value="2" >
					<label class="fancy_label text-black" style="font-weight: 700" for="option_copy">Copy</label>
				</div>
				<div class="options copy-reviews">
					<input type="checkbox" id="option_review" name="data[Template][option_review]" class="fancy_input" value="1" >
					<label class="fancy_label text-black" style="font-weight: 700" for="option_review">Remove Reviews</label>
				</div>
				<div class="options copy-likes">
					<input type="checkbox" id="option_likes" name="data[Template][option_likes]" class="fancy_input" value="1" >
					<label class="fancy_label text-black" style="font-weight: 700" for="option_likes">Remove Likes</label>
				</div>
				<div class="options copy-docs">
					<input type="checkbox" id="option_dots" name="data[Template][option_dots]" class="fancy_input" value="1" >
					<label class="fancy_label text-black" style="font-weight: 700" for="option_dots">Remove Documents</label>
				</div>
			</div>
		</div>
		
	</div>
        

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="submit"  class="btn btn-success process">Submit</button>
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>

		<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
	$(function() {
	
	

        
	    // USER'S MULTISELECT BOX INITIALIZATION
	    $.template_list = $('#template_list').multiselect({
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '85%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'template_list',
	        includeSelectAllOption: true,
	        // enableHTML: true,
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
	        onChange: function(option, checked) {
	            // console.log($(this))
	        },
	        onDropdownHidden: function(option, closed, select) {

	            var templates = $('#template_list').val();
	            // console.log(templates);
	            if(templates != null ) {
		            var nonAdmin = false; 
		            $.each(templates, function(i, v){
		            	var data = $('#template_list option[value='+v+']').data('admin');
		            	if(!data){
		            		$('#option_move').prop('checked', false).removeAttr('checked').addClass('disabled').attr('disabled','disabled');
		            		$('#option_copy').prop('checked', true);

		            		$('[for=option_move]').attr('data-original-title','Only Author Can Move');
		            		nonAdmin = true;
		            		// $('.non-admin').show();		            	
		            	}
		            })
		            if(!nonAdmin) {
		            	$('#option_move').removeClass('disabled').removeAttr('disabled');
	            		// $('.non-admin').hide();
	            		$('[for=option_move]').removeAttr('data-original-title');
		            }
		        }
		        else {
		        	$('#option_move').removeClass('disabled').removeAttr('disabled');
		        }

	        }
	    });

	    /*$('.rdo-copy-move').on('change', function(event){
	    	
	    	if($(this).val() == 1) {
	    		$('#option_review').prop('checked', true).attr('disabled', 'disabled')
	    		$('#option_likes').prop('checked', true).attr('disabled', 'disabled')
	    	}
	    	else{
	    		$('#option_review').prop('checked', false).removeAttr('disabled')
	    		$('#option_likes').prop('checked', false).removeAttr('disabled')
	    	}
	    })*/

	    $('.process').on('click', function(event){
	    	event.preventDefault();

	    	var $this = $(this),
	    		$form = $this.closest('form'),
	    		params = $form.serializeArray(),
	    		template_list = $('#template_list'),
	    		template_categories = $('#template_categories');

	    	$form.find('.error-message').html('');
	    	if( template_list.val() == '' || template_list.val() === undefined  || template_list.val() === null ) {
	    		template_list.parents('.parent_template_list').find('.error-message').html('At least one template is required')
	    		return;
	    	}
	    	if( template_categories.val() == '' || template_categories.val() === undefined  || template_categories.val() === null) {
	    		template_categories.parents('.parent_template_categories').find('.error-message').html('Target Folder is required');
	    		return;
	    	}

	    	$.ajax({
	            url: $js_config.base_url + 'templates/copy_move_templates',
	            type: "POST",
	            data: $.param(params),
	            dataType: "JSON",
	            global: true,
	            success: function(response) {
	                if(response.success) {
	                	$('#modal_manage_templates').modal('hide');
	                	// if($('#option_move').prop('checked')){
	                		location.reload();
	                	// }
	                }
	            }
	        })

	    })
 

	})
</script> 
<?php } ?>

