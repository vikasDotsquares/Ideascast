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
	.remove-image {
		cursor: pointer;
	}



	.ski-close {
	    position: relative;
	    padding-bottom: 0;
	}
	.docupload-sec {
	    display: flex;
	    width: 100%;
	}
	.docupload-sec .input-group-addon {
	    border-color: #ccc;
	    min-width: 40px;
	    text-align: center;
	    height: 34px;
	    line-height: 32px;
	    padding: 0px 5px;
	    display: flex;
	    align-items: center;
	    justify-content: center;
	}
	.docupload-sec .icon_btn {
	    border-color: #ccc;
	    border-radius: 0;
	    padding: 0 5px;
	    height: 34px;
	    background-color: #fff;
	    color: #444;
	    max-width: 100%;
	    cursor: pointer;
	}

	.docUpload {
	    line-height: 20px;
	}
	.docupload-sec .icon_btn .up-text {
	    display: block;
	    text-align: left;
	    text-overflow: ellipsis;
	    overflow: hidden;
	    white-space: nowrap;
	    line-height: 32px;
	}
	.docupload-sec .input-group-addon.remove-image {
	    border-left: none;
	}
	.ski-close label.error {
	    position: absolute;
	    bottom: -14px;
	    left: 0;
	    margin: 0;
	    font-size: 11px;
	    font-weight: normal;
	}
	.remove-image.not-shown {
	    display: none;
	}
	.file-uploading, .remove-image {
	    display: none;
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
				<label class="col-sm-3 form-control-label">Note: </label>
				<div class="col-sm-9">
					Minimum 750px by 150px required
				</div>
			</div>

			<div class="form-group row" >
				<label class="col-sm-3 form-control-label" style="margin: 5px 0 0 0">Project Image: </label>
				<div class="col-sm-9">
					<div class="ski-close">
						  	<div class="input-group docupload-sec">
                                <div class="input-group-addon up-icon">
                                    <i class="uploadblackicon"></i>
                                </div>
                                <span title="" class="docUpload icon_btn">
									<input type="file" name="data[Project][image_file]" class="form-control upload" id="doc_file" placeholder="Upload File" accept=".png, .jpg, .jpeg">
									<span class="up-text"  style="">Click to upload a file</span>
                                </span>
                                <div class="input-group-addon remove-image not-shown tipText" title="Clear Image">
                                    <i class="clearblackicon"></i>
                                   	<i class="fa fa-spinner fa-spin file-uploading"></i>
                                </div>
								<label class="image_error error text-red"></label>
                            </div>
					  	</div>
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
						echo $this->Image->resize( $data['Project']['image_file'], 780, 150, array(), 100);
					?>
				</div>
				<?php } ?>
			</div>

		</div>
		<!-- POPUP MODAL FOOTER -->
		<div class="modal-footer">
			 <button type="button" id="upload_image" class="btn btn-success">Upload</button>
			 <?php $project_image = $data['Project']['image_file'];  ?>
			 <button type="button" class="btn btn-success " id="remove_image" <?php if( empty($project_image) || !file_exists(PROJECT_IMAGE_PATH . $project_image)){ ?> disabled <?php } ?> >Remove</button>
			 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
		</div>
	<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
$(function(){


	$.file_uploading = function(flag, parent) {
		parent.find('.remove-image').removeClass('not-shown');

		//uploaded
		if(flag) {
			parent.find('.clearblackicon').show();
			parent.find('.file-uploading').hide();
			$('.submit_data').prop('disabled', false);
		}
		// uploading
		else{
			parent.find('.clearblackicon').hide();
			parent.find('.file-uploading').show();
			$('.submit_data').prop('disabled', true);
		}
	}


	$("#doc_file").on('change', function (e) {
		$("#upload_image").prop('disabled', true);
		var $form = $("#modelFormProjectImage");

		$.file_uploading(true, $form);
		var file = this.files[0],
			name = file.name,
            size = file.size,
            type = file.type,
            $upText = $(this).parent().find('.up-text');


		$upText.html(name);
		$("#upload_image").prop('disabled', false);

	});
	$('#upText').on('click', function(event) {
		event.preventDefault();
		$("#doc_file").trigger('click');
	});

	$("#remove_image").off('click').on( "click", function(event){
		event.preventDefault();
		$.image_uploaded = true;
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
					$('#upload_model_box').modal('hide');
				}
			}
		});
	})

	$("#upload_image").off('click').on( "click", function(event){
		event.preventDefault();

		var $t = $(this),
			$form = $("#modelFormProjectImage"),
			formData = new FormData($form[0]),
			$fileInput = $form.find("#doc_file"),
			file = $fileInput[0].files[0],
			$pidInput = $form.find("#project_id"),
			project_id = $pidInput.val(),
			url = $js_config.base_url + "projects/image_upload/" + project_id;

		if ($fileInput.val() !== "" && file !== undefined) {
			var name = file.name,
			size = file.size,
			type = file.type;

			formData.append('image_file', $fileInput[0].files[0]);
		}
		$t.prop('disabled', true);

		if ( $fileInput.val() !== "" ) {
			$.image_uploaded = true;
			$.ajax({
				type: 'POST',
				dataType: "JSON",
				url: url,
				data: formData,
				global: true,
				cache: false,
				contentType: false,
				processData: false,
				success: function (response) {
					$t.prop('disabled', false);
					if (!response.success) {
						$('.image_error').text(response.msg);
					}
					else{
						$('#upload_model_box').modal('hide');
					}
				}
			});
		}
		else {
			$('.image_error').text('Please select a file.')
		}
	})

		$('.remove-image').off('click').on('click', function(event) {
			$(this).parents('.ski-close').find('input[type="file"]').val('');
			$(this).parents('.ski-close').find('.up-text').text('Click to upload a file');
			$(this).addClass('not-shown');
			$(this).parents('.ski-close').find('.error').text('');
		})

})
</script>