<style type="text/css">
	.project-document-popup .error.text-red {
		display: block;
	}
</style>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Project Documents</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body  popup-select-icon project-document-popup clearfix">
		<div class="common-tab-sec view-skills-tab">
	        <ul class="nav nav-tabs tab-list" id="doc_tabs">
                <li class="active"> <a data-toggle="tab" class="active slevels" href="#projdocuments" aria-expanded="true">Documents</a> </li>
	            <li class="shistoryMain"> <a data-toggle="tab" class="shistory" href="#projupload" aria-expanded="true">Upload</a> </li>
	        </ul>
	        <div class="tab-content">
	            <div id="projdocuments" class="tab-pane fade active in">
	                <div class="projdocuments-list"></div>
	            </div>
	            <div id="projupload" class="tab-pane fade">
	            	<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'detail_add', 'url'=>'manage_files', 'enctype'=>'multipart/form-data' )); ?>
	            	<?php echo $this->Form->input('ProjectDocument.project_id', array('type' => 'hidden', 'value' => $project_id)); ?>
					<div class="row">
						<div class="form-group">
						  	<label for="" class="col-lg-2 control-label">Document:</label>
						  	<div class="col-lg-10">
							  	<div class="ski-close">
								  	<div class="input-group docupload-sec">
				                        <div class="input-group-addon up-icon">
				                            <i class="uploadblackicon"></i>
				                        </div>
				                        <span title="" class="docUpload icon_btn">
											<input type="file" name="data[ProjectDocument][filename]" class="form-control upload" id="pd_image" placeholder="Upload File">
											<span class="up-text">Click to upload a file</span>
				                        </span>
				                        <div class="input-group-addon remove-image not-shown tipText" title="Clear File Name">
				                            <i class="clearblackicon"></i>
				                           <i class="fa fa-spinner fa-spin file-uploading"></i>
				                        </div>
				                    </div>
				                    <label class="error text-red" ></label>
				                </div>
						  	</div>
						</div>
						<div class="form-group">
	                        <label for="" class="col-lg-2 control-label">Title: </label>
	                        <div class="col-lg-10">
	                            <input class="form-control" placeholder="50 characters" type="text" id="doc_title" name="data[ProjectDocument][title]" autocomplete="off" maxlength="50">
	                            <span class="error text-red"></span>
	                        </div>
	                    </div>
						<div class="form-group">
	                        <label for="" class="col-lg-2 control-label">Summary: </label>
	                        <div class="col-lg-10">
	                            <input class="form-control" placeholder="50 characters" type="text" id="doc_summary" name="data[ProjectDocument][summary]" autocomplete="off" maxlength="50">
	                            <span class="error text-red"></span>
	                        </div>
	                    </div>

						<div class="form-group visiblesharers-field">
	                        <label for="" class="col-lg-2 control-label"> </label>
	                        <div class="col-lg-10">
	                            <input type="checkbox" value="1" id="visible_sharers"> <label for="visible_sharers">Visible to Sharers</label>
	                        </div>
	                    </div>
					</div>
					<?php echo $this->Form->end(); ?>
	            </div>
	        </div>
	    </div>
	</div>
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button"  class="btn btn-success submit-doc" style="display: none;">Upload</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>

<script type="text/javascript">
	$(function(){
		$(".projdocuments-list").slimScroll({height: 244, alwaysVisible: true, width: '100%'});

		$("#doc_tabs").on('shown.bs.tab', function (e) {
            if($(this).find('li.active a').is('.shistory')){
            	$(".submit-doc").show();
            }
            else{
            	$(".submit-doc").hide();
            }
		})

		var project_id = '<?php echo $project_id; ?>';

		($.document_list = function(){
			$.ajax({
				url: $js_config.base_url + 'projects/project_document_list/' + project_id,
				type: 'POST',
				data: {},
				success:function(response){
					$('.projdocuments-list').html(response);
					$('.projdocuments-list-right').removeClass('stopped');
				}
			})
		})();

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

		$("#pd_image").off('change').on('change', function() {
			if(this.files[0] !== undefined){
				var $parent = $(this).parents('.ski-close:first');
				var $fileInput = $(this);
				var file = this.files[0];
				var fnme = file.name;
				var fnme = fnme.substr(0, fnme.lastIndexOf('.')) || fnme;
				var regex = /^[A-Za-z0-9\s-_()]+$/
				$("#pd_image").parents('.ski-close').find('.error').text('');
				if(regex.test(fnme) == false) {
					$("#pd_image").parents('.ski-close').find('.error').text('Image filename contains unsupported characters');
					$("#pd_image").val('');
					return;
				}

		        var name = file.name,
		            size = file.size,
		            type = file.type,
		            $upText = $(this).parent().find('.up-text');
		        var filesize = file.size / 1048576;

				if(filesize.toFixed(2) > 20) {
					$("#pd_image").parents('.ski-close').find('.error').text('File must be less than 20MB');
					$("#pd_image").val('');
					return;
				}

				$upText.html(name);
				$.file_uploading(true, $parent);

			}
		})

		$('.remove-image').off('click').on('click', function(event) {
			$(this).parents('.ski-close').find('input[type="file"]').val('');
			$(this).parents('.ski-close').find('.up-text').text('Click to upload a file');
			$(this).addClass('not-shown');
		})

		$('#doc_title, #doc_summary').off('keyup').on('keyup', function(event) {
			$(this).parent().find('.error').html('');
		})

		$('.submit-doc').off('click').on('click', function(event) {
			event.preventDefault();
			var file = $("#pd_image")[0].files[0];
			var fnme = (file) ? file.name : '';
			$('.error').html('');

			var error = false;
			if($("#pd_image").val() == ''){
				$("#pd_image").parents('.ski-close').find('.error').text('Document is required');
				error = true;
			}

			if($('#doc_title').val() == '' || $('#doc_title').val() == undefined){
				$('#doc_title').parent().find('.error').html('Title is required');
				error = true;
			}
			if($('#doc_summary').val() == '' || $('#doc_summary').val() == undefined){
				$('#doc_summary').parent().find('.error').html('Summary is required');
				error = true;
			}

			if(error) return;

			var $form = $("#detail_add");
			var formData = new FormData($form[0]),
				$fileInput = $("#pd_image"),
				file = $fileInput[0].files[0];


			var $parent = $("#pd_image").parents('.ski-close:first');
			$.file_uploading(false, $parent);

			var filePath = file.name;
			var file_ext = filePath.substr(filePath.lastIndexOf('.')+1,filePath.length);

			$parent.find('.error').text('');

			if ($fileInput.val() !== "" && file !== undefined) {
				$(this).prop('disabled', true);
				var name = file.name.replace(/-/g, ''),
					size = file.size,
					type = file.type;
				formData.append('filename', $fileInput[0].files[0], $fileInput[0].files[0]['name'].replace(/-/g, ''));
				formData.append('title', $('#doc_title').val() );
				formData.append('summary', $('#doc_summary').val() );
				formData.append('is_sharers', ($('#visible_sharers').prop('checked')) ? 1 : 0 );

				$.ajax({
					url: $js_config.base_url + 'projects/upload_project_doc',
					type:'POST',
					dataType:'json',
					data: formData,
					global: false,
					cache: false,
					contentType: false,
					processData: false,
					success:function(response){
						if(response.success){
							$.docs_uploaded = true;
							$.file_uploading(true, $parent);
							$.document_list();
							$(".submit-doc").prop('disabled', false);
							$('#doc_title, #doc_summary').val('');
							$('.remove-image').trigger('click');
							$('#visible_sharers').prop('checked', false);
							$('#doc_tabs a.slevels').tab('show');
						}
					}
				})
			}
		});

	})
</script>