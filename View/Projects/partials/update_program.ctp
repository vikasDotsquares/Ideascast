<?php $programlist = $this->Common->list_program_update('program_name'); ?>
<div class="form-group" >
	<div class="row">
		<div class="col-md-12" id="programNamelist">
		<?php if( isset($programlist) && !empty($programlist) ){ ?>
			<label class="">Program Name:</label>
			<ul class="list-group" style="overflow-x:hidden; overflow-y:auto; max-height:250px;" >
		<?php //'modified'
			
			if( isset($programlist) && !empty($programlist) ){

				foreach($programlist as $pkey => $listprogram){
		?>


			  <li class="list-group-item d-flex justify-content-between align-items-center">
				<span class="programtitle"><?php echo htmlentities($listprogram);?></span>
					<span class="del-options">
							<i data-deleteprogramid="<?php echo $pkey;?>" class="fa fa-check accept bg-green tipText" title="Confirm Delete" ></i>
							<i class="fa fa-times reject bg-red tipText" title="Reject Delete"></i>
					</span>
					<span class="btn btn-xs btn-danger pull-right deleteprogram tipText" title="Delete">
						<i class="fa fa-trash-o" aria-hidden="true" ></i>
					</span>
				<span data-programid="<?php echo $pkey;?>" class="btn btn-xs btn-primary pull-right editprogram tipText" title="Update"><i class="fa fa-pencil " aria-hidden="true" data-programid="<?php echo $pkey;?>" ></i></span>
			  </li>


		<?php }
			} ?>
			</ul>
		<?php } else { echo "<span class='no-data-found'>No Programs</span>"; } ?>	
		</div>
		<div class="update_program_projectlist">
			<?php /* <div class="col-md-12" style="padding-top: 10px;">
				<label class="">Update Projects To Program:<br></label>
			</div>
			<div class="col-md-12 update_program_projectlist" style="padding-top: 10px;">
				<?php
					echo $this->Form->input('ProjectProgram.project_id', array(
						'options' => $allprojects,
						'type' => 'select',
						'multiple' => 'multiple',
						'label' => false,
						'div' => false,
						'class' => 'form-control aqua clear',
						'id'=>'multiple_program_project_update'
					));
				?>
				<span class="error-message-pid text-danger" ></span>
			</div>*/ ?>
		</div>
	</div>
</div>
<script>
$('#multiple_program_project_update').hide()
	setTimeout(function () {

		$('#multiple_program_project_update').multiselect({
			enableFiltering: true,
			buttonClass	: 'btn btn-default btn-sm',
			includeSelectAllOption: true,
			buttonWidth: '100%',
			checkboxName: 'data[ProjectProgram][project_id][]',
		});

		$('.span_profile').hide()
	}, 1);

	$(function(){
		// error-message-upname	
		// pgtitle
		
	    $('body').delegate("input[name='data[Program][program_name_update]']", 'keyup focus contextmenu', function(event){
	    //$('body').delegate('#pgtitle', 'keyup focus contextmenu', function(event){
	        var characters = 50;
	        event.preventDefault();
	        var $error_el = $(this).parents("li.list-group-item:first").find('.error-message-upname');
	        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
	            $.input_char_count(this, characters, $error_el);
	        }
	    })
	})
</script>
<style>
.program-manager{ padding-bottom:0 !important;}
#programNamelist .list-group{ margin-top:0;}
</style>