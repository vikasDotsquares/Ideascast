<style>
.parent_template_list label { float:left;}
.multi_select_template h3{ text-align :left;}
.error{ font-size:11px; color:#f00; font-weight: 600; }
	
.add-update-modal .modal-header .close.close-skill {
    margin-top: 9px;
}	
.add-update-modal .control-label {
    font-size: 14px;
    font-weight: 700;
}	
	
	
</style>
<?php
$current_user_id = $this->Session->read('Auth.User.id');
//echo $this->Form->create('TemplateReview', array('url' => array('controller' => 'projects', 'action' => 'save_review'), 'class' => 'form-bordered', 'id' => 'modelFormTemplateReview'));
// pr($ownerprojects);
?>
<div class="convert-project-Workspace">
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close close-skill" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Convert Workspace to Knowledge Template</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<div class="form-body">
			<div class="form-group">
			<select class="form-control ownerproject" id="all_owner_projects">
			<?php $disabled = "disabled='disabled'";
				if(isset($ownerprojects) && !empty($ownerprojects)){

			?>
				<option value="0">Select Project</option>
			<?php foreach($ownerprojects as $pidlist => $owlists){?>
				<option value="<?php echo $pidlist;?>"><?php echo $owlists;?></option>
			<?php }
				} else {
			?>
				<option>No Projects</option>
			<?php } ?>
			</select>
			</div>
			<div class="form-group">
			<select class="form-control ownerprojectworkspace" id="all_owner_project_workspace" name="data[Workspace][id]" disabled >
				<option>No Workspace</option>
			</select>
			</div>
			<hr>
			<div class="form-group" >
			<label for="data[TemplateRelation][title]" class="control-label">Knowledge Template Title:</label>
			<input type="text" class="form-control addremove" disabled name="data[TemplateRelation][title]" placeholder="max chars allowed 50" id="template_title" >
			<label class="tmptTitle error"></label>
			<h6 class="pull-right" id="count_message_title"></h6>
			<span class="error text-red chars_left"> </span>
			</div>
			<div class="form-group addremove" disabled >
			<label for="data[TemplateRelation][description]" class="control-label">Description:</label>

			<textarea class="form-control addremove" disabled name="data[TemplateRelation][description]" placeholder="max chars allowed 500" rows="3" id="template_description" ></textarea>
			<label class="tmptDescription error"></label>
			<h6 class="pull-right" id="count_message"></h6>
			<span class="error text-red chars_left"> </span>
			</div>
			<div class="form-group">
			<div class="row ">
				<div class="col-sm-8 convert-destination">
				<label class="control-label">Select Target Folder:</label>
				<select class="form-control addremove" size="5" id="destination_id" disabled name="data[TemplateRelation][template_category_id]" multiple="true" >

					<?php
					if( isset($template_categories) && !empty($template_categories) ){
						foreach($template_categories as $tempCat){
					?>
						<option value="<?php echo $tempCat['TemplateCategory']['id'];?>"><?php echo $tempCat['TemplateCategory']['title'];?></option>
					<?php }
					} ?>
				</select>
			</div>
			  <div class="col-sm-4 include_documents_fild">
				<div class="add_template_documents">
					<input <?php echo $disabled;?> id="include_documents" name="data[TemplateRelation][include_documents]" class="fancy_input" type="checkbox" checked="checked" >
					<label class="fancy_label" for="include_documents">Include Documents</label>
				</div>
				<div class="wsp_template_import">
					<input <?php echo $disabled;?> id="wsp_imported_for" name="data[TemplateRelation][wsp_imported]" class="fancy_input" type="checkbox" checked="checked" >
					<label class="fancy_label" for="wsp_imported_for">Add Background</label>
				</div>
              </div>
			</div>
			</div>

		</div>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button" id="submit_convert" disabled class="btn btn-success submitwptemplate">Convert</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
	</div>
<?php //echo $this->Form->end(); ?>
<script>
$(function(){

	$('.ownerprojectworkspace').on('change', function(event) {
		$('[name="data[TemplateRelation][title]"]').val($('option:selected', $(this)).text())
		var id = $(this).val();
		var href = $js_config.base_url + 'templates/get_workspace_detail';
		$.ajax({
					url: href,
					type: "POST",
					crossDomain: false,
					data: $.param({workspace_id:id}),
					global: false,
					success: function (response) {
						 $('#template_description').val(response);
						//$(".ownerprojectworkspace").html(response);
					}
		})
	});
		$('#count_message_title').hide();
		$('#count_message').hide();

		// counter for template description
		var text_max = 500;
		//$('#count_message').html(text_max + ' remaining');
		$('#template_description').keyup(function() {
		  var text_length = $('#template_description').val().length;
		  var text_remaining = text_max - text_length;

		  /* if( text_length > text_max ){
			$('#count_message').html("You have reached the maximum number of characters.").css("color","red");
			 $(this).val($(this).val().substring(0, text_length-1));

		  } else {
			$('#count_message').html(text_remaining + ' remaining').css("color","#555");
		  } */
		  		var $ts = $(this)
		if($(this).val().length > text_max){
			$(this).val($(this).val().substr(0, text_max));
		}



		var remaining = text_max -  $(this).val().length;
		$('#template_description').parents('.form-group').find('.chars_left').html("Char 500 , <strong>" +$(this).val().length+ "</strong> characters entered.");
		if(remaining <= 10)
		{
			$(this).next().css("color","red");
		}
		else
		{
			$(this).next().css("color","red");
		}



		  $(".tmptDescription").text('');

		});

		// counter for template title
		var text_max_title = 50;
		//$('#count_message_title').html(text_max_title + ' remaining');
		$('#template_title').keyup(function() {
		  var text_length_title = $('#template_title').val().length;
		  var text_remaining_title = text_max_title - text_length_title;

			/* if( text_length_title > text_max_title ){
				$('#count_message_title').html("You have reached the maximum number of characters.").css("color","red");
				 $(this).val($(this).val().substring(0, text_length_title-1));
			} else {
				$('#count_message_title').html(text_remaining_title + ' remaining').css("color","#555");
			} */

		var $ts = $(this)
		if($(this).val().length > text_max_title){
			$(this).val($(this).val().substr(0, text_max_title));
		}
		var remaining = text_max_title -  $(this).val().length;
		$('#template_title').parents('.form-group').find('.chars_left').html("Char 50 , <strong>" +$(this).val().length+ "</strong> characters entered.");
		if(remaining <= 10)
		{
			$(this).next().css("color","red");
		}
		else
		{
			$(this).next().css("color","red");
		}



			$(".tmptTitle").text('');
		});

		$(document).on("change", "#all_owner_projects", function(){
			console.log($(this).val());
			if( $(this).val() == 0 ){
				$("#all_owner_project_workspace").html('<option value="0">Select Workspace</option>');
				$("#all_owner_project_workspace").prop("disabled", true);
				$(".addremove").prop("disabled", true);
				$("#submit_convert").prop("disabled", true);
				$('#count_message_title').hide();
				$('#count_message').hide();
				$('#template_title').val('');
				$('#template_description').val('');
				$("#destination_id option:selected").removeAttr("selected");

				$("#include_documents").prop("disabled", true);
				$("#wsp_imported_for").prop("disabled", true);
			} else {
				$("#all_owner_project_workspace").html('<option value="0">Select Workspace</option>');
				$("#all_owner_project_workspace").prop("disabled", false);

				$(".addremove").prop("disabled", true);
				$("#submit_convert").prop("disabled", true);
				$('#count_message_title').hide();
				$('#count_message').hide();
				$('#template_title').val('');
				$('#template_description').val('');
				$("#destination_id option:selected").removeAttr("selected");

				$("#include_documents").prop("disabled", false);
				$("#wsp_imported_for").prop("disabled", false);

			}

		});

		$(document).on("change", "#all_owner_project_workspace", function(){

			if( $(this).val() == 0 ){
				$(".addremove").prop("disabled", true);
				$("#submit_convert").prop("disabled", true);
				$('#count_message').hide();
				$('#template_title').val('');
				$('#template_description').val('');
				$("#destination_id option:selected").removeAttr("selected");


			} else {
				$(".addremove").prop("disabled", false);
				$('#count_message_title').show();
				$('#count_message').show();
			}

		});

		$(document).on("change", "#destination_id", function(){

			if( $(this).val() ){
				$("#submit_convert").prop("disabled", false);
			} else {
				$("#submit_convert").prop("disabled", true);
			}

		});

})

</script>


