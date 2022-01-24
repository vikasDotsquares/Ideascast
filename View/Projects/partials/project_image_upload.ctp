<style>
	.docUpload {
		max-width: 365px;
		overflow: hidden;
		padding: 5px;
		position: relative;
		width: 100%;
	}
	.docUpload input.upload {
		cursor: pointer;
		font-size: 20px;
		margin: 0;
		opacity: 0;
		filter: alpha(opacity=0);
		padding: 0;
		position: absolute;
		right: 0;
		top: 0;
		width: 100%;
	}


	.fileUpload {
		position: relative;
		overflow: hidden;
		margin: 10px;
	}
	.fileUpload input.upload {
		position: absolute;
		top: 0;
		right: 0;
		margin: 0;
		padding: 0;
		font-size: 20px;
		cursor: pointer;
		opacity: 0;
		filter: alpha(opacity=0);

	}
	.upload-group i {
	    cursor: default !important;
	}
	.upload-group input {
	    visibility: hidden;
	}
</style>

	<?php echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'project_image_upload'), 'class' => 'form-bordered', 'id' => 'modelFormProjectImage')); ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 3px 0 0;"><span aria-hidden="true">&times;</span></button>
			<h3 class="modal-title" id="myModalLabel">Project Image</h3>
		</div>

		<!-- POPUP MODAL BODY -->
		<div class="modal-body">
			<input type="hidden" value="<?php echo $project_id; ?>" name="data[Project][id]" id="project_id" />
			<div class="form-group row" >
				<label class="col-sm-2 form-control-label">Note: </label>
				<div class="col-sm-10">
					Please upload image with minimum dimension ("750px * 150px")
				</div>
			</div>

			<div class="form-group row" >
				<label class="col-sm-3 form-control-label" style="margin: 5px 0 0 0">Project Image: </label>
				<div class="col-sm-9">

					<div class="input-group upload-group">
						<div class="input-group-addon">
							<i class="fa fa-upload"></i>
						</div>
						<span class="docUpload icon_btn bg-white noborder-radius " >
							<input type="file" placeholder="Upload File" id="doc_file" class="form-control upload" name="data[Project][image_file]">
							<span style="display: block; min-height: 23px;" id="upText" class="text-blue">Upload file</span>
						</span>
					</div>
					<span style="display: block;" id="" class="image_error text-red"></span>
				</div>
			</div>
			<div class="form-group clearfix" id="project_image_wrapper" style="text-align: center;" >
			<?php
				$project_image = $data['Project']['image_file'];
				if( !empty($project_image) && file_exists(PROJECT_IMAGE_PATH . $project_image)){
			?>
				<div class="col-sm-12">
					<?php
						$project_image = SITEURL . PROJECT_IMAGE_PATH . $project_image;
						// echo $this->Html->image( $project_image, ['class' => 'pull-right', 'alt' => 'Project Image' ] );
						echo $this->Image->resize( $data['Project']['image_file'], 780, 150, array(), 100);
					?>
				</div>
				<div class="col-sm-12">
					<a class="btn btn-danger btn-sm pull-right margin-top <?php if($center == 2){ ?> remove_center_image <?php } ?>" <?php if($center == 1){ ?> id="remove_image" <?php } ?>>Remove</a>
				</div>
			<?php }else{
			?>
			<b style="font-size: 20px; font-weight: 600;"><!-- Upload Project Image --></b>
			<?php
					//echo $this->Image->resize( 'noimage.jpg', 750, 150, array("style" => ' '), 100);
			} ?>
			</div>
		</div>

		<!-- POPUP MODAL FOOTER -->
		<div class="modal-footer">
			 <button type="button" id="upload_image" class="btn btn-success">Upload</button>
			 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
		</div>
	<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
$(function(){

	/* $('#upload_model_box').on('hidden.bs.modal', function () {

			$(this).removeData('bs.modal');
			$(this).find('.modal-content').html('')

	}); */

	// $("#uploadBtn").on('change', function (e) {
		// $("#uploadedFile").text(this.value);
	// });

	$("#doc_file").on('change', function (e) {
		var $form = $("#modelFormProjectImage"),
			$fileInput = $form.find("#doc_file"),
			file = $fileInput[0].files[0];
		var name = '';
		if ($fileInput.val() !== "" && file !== undefined) {
			name = file.name;
		}

		$("#upText").text(name);
	});
	$('#upText').on('click', function(event) {
		event.preventDefault();
		$("#doc_file").trigger('click')
	});

	$("body").delegate("#remove_image", "click", function(event){
		event.preventDefault();

		var $pidInput = $("#modelFormProjectImage").find("#project_id"),
			project_id = $pidInput.val();
		$.ajax({
			type: 'POST',
			dataType: 'JSON',
			data: $.param({ 'project_id': project_id }),
			url: $js_config.base_url + 'projects/remove_project_image/' + project_id,
			global: false,
			success: function (response) {
				if(response.success) {
					$("#project_image_wrapper").slideUp(300, function() {
						$(this).remove()
					})
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({ 'project_id': project_id }),
						url: $js_config.base_url + 'projects/get_project_image/' + project_id,
						global: false,
						success: function (response) {
							$("#image_file_"+project_id).hide().html(response).fadeIn(500)
						},
					});
				}
			}
		});
	})


})
</script>