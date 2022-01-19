<?php
$this->request->data['Subject']['title'] = $data_title = html_entity_decode(html_entity_decode($subject_data['Subject']['title'] ,ENT_QUOTES, "UTF-8"));
$this->request->data['Subject']['description'] = $data_desc = html_entity_decode(html_entity_decode($subject_data['Subject']['description'] ,ENT_QUOTES, "UTF-8"));
$this->request->data['Subject']['image'] = $subject_data['Subject']['image'];
$this->request->data['Subject']['id'] = $data_id = $subject_data['Subject']['id'];

$data_title = addslashes($data_title);
$data_desc = addslashes($data_desc);
?>
			<div class="modal-header">
				<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Edit Subject</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs" id="add_tabs">
					<li class="active">
						<a data-toggle="tab" data-type="details" class="active skilltab" data-target="#tab_main" href="#tab_main" aria-expanded="true" >Details</a>
					</li>

					<li class="">
						<a data-toggle="tab" data-tab="subject_link"  data-type="links" id="linksTab" data-target="#tab_links" href="#tab_links" aria-expanded="false" class="skilltab " >Links</a>
					</li>

					<li class="">
						<a data-toggle="tab" data-tab="subject_file" data-type="files" id="filesTab" data-target="#tab_files" href="#tab_files" aria-expanded="false" class="skilltab " >Files</a>
						<input type="hidden" name="subject_id" value="<?php echo $this->request->data['Subject']['id']; ?>" >
					</li>

					<li class="">
						<a data-toggle="tab" data-tab="subject_keyword" data-type="keywords" id="filesTab" data-target="#tab_keywords" href="#tab_keywords" aria-expanded="false" class="skilltab tab-file" >Keywords</a>
					</li>

				</ul>

				<div class="tab-content">
				<div class="success-msg"></div>

                 <div class="tab-pane fade active in"  id="tab_main" >
					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'subject_add', 'url'=>'manage_subjects', 'enctype'=>'multipart/form-data' )); ?>

						<div class="row">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-2 control-label">Name: <sup class="text-danger">*</sup></label>
							  <div class="col-lg-10">
								  <div class="form-control-skill">
								<?php echo $this->Form->input('Subject.title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>
								<label class="error  text-red"></label>
							  </div>
								</div>
							</div>

							<div class="form-group">
							  <label for="UserUser" class="col-lg-2 control-label">Image: <i class="info-icon" title="150px by 150px Recommended"></i></label>
							  	<div class="col-lg-10">
								  	<div class="ski-close">
								  		<div class="input-group docupload-sec">
                                            <div class="input-group-addon up-icon">
                                                <i class="uploadblackicon"></i>
                                            </div>
                                            <span title="" class="docUpload icon_btn">
												<input type="file" name="data[Subject][image]" class="form-control upload" id="subject_image" placeholder="Upload File" accept=".png, .jpg, .jpeg">
												<span class="up-text" >Upload a file</span>
                                            </span>
                                            <div class="input-group-addon remove-image not-shown tipText" title="Clear Image">
                                                <i class="clearblackicon"></i>
                                               <i class="fa fa-spinner fa-spin file-uploading"></i>
                                            </div>
                                        </div>

									</div>


									<div class="show_images">
									<?php if( isset($this->request->data['Subject']['image']) && !empty($this->request->data['Subject']['image']) && isset($this->request->data['Subject']['id']) && !empty($this->request->data['Subject']['id']) ){ ?>
										<span class="main_image"><?php echo $this->request->data['Subject']['image'];?></span><i data-type="subject" data-id="<?php echo $this->request->data['Subject']['id'];?>" class="clearredicon remove_image tipText" title="Remove Now"></i>
									<?php } ?>
									</div>

							  </div>
							</div>
							<div class="col-lg-12">
							<div class="form-group">
							  <label for="UserUser" class="control-label">Description:</label>
								<div class="form-control-skill">
									<?php echo $this->Form->input('Subject.description', array('type' => 'textarea', 'label' => false, 'div' => false, 'class' => 'form-control', 'rows'=> 5, 'cols'=>35, 'maxlength'=>500)); ?>
									<label class="error  text-red"></label>
								</div>
							 </div>
							</div>
						</div>

					<input type="hidden" name="data[Subject][id]" value="<?php echo $this->request->data['Subject']['id']; ?>" >
					<?php echo $this->Form->end(); ?>
				 </div>


				<div class="tab-pane fade" id="tab_links" >
					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'subject_links_add', 'url'=>'manage_subject_links', 'enctype'=>'multipart/form-data' )); ?>

							<div class="row">
									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Web Link: <sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										  <div class="form-control-skill">
										<?php echo $this->Form->input('SubjectLink.web_link', array('type' => 'url', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'placeholder'=> '', 'autocomplete'=>'off' )); ?>
										<label class="error  text-red"></label>
									  </div>
										</div>
									</div>
									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Title: <sup class="text-danger">*</sup></label>
										<div class="col-lg-10">
											<div class="form-control-skill">
										  <div class="title-group-w">
											<?php echo $this->Form->input('SubjectLink.link_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'maxlength'=>50, 'autocomplete'=>'off' )); ?>
											<button type="button" class="btn outline-btn-s submit_subject_links ">Add</button>
										  </div>
										  <label class="error  text-red"></label>
										</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="popup-skill-list popup-skill-links">
											<ul></ul>
										</div>
									</div>
							</div>
					<?php echo $this->Form->end(); ?>
				</div>



				<div class="tab-pane fade "  id="tab_files" >

					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'subject_files_add', 'url'=>'manage_subject_files', 'enctype'=>'multipart/form-data' )); ?>
							<input type="hidden" name="data[SubjectFile][subject_id]" value="<?php echo $this->request->data['Subject']['id']; ?>" >
							<div class="row">

									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">File Name:<sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										   	<div class="ski-close">
										   		<div class="input-group docupload-sec">
		                                            <div class="input-group-addon up-icon">
		                                                <i class="uploadblackicon"></i>
		                                            </div>
		                                            <span title="" class="docUpload icon_btn">
														<input type="file" name="data[SubjectFile][upload_file]" class="form-control upload" id="subject_files" placeholder="Upload File">
														<span class="up-text" >Upload a file</span>
		                                            </span>
		                                            <div class="input-group-addon remove-image not-shown tipText" title="Clear File Name">
		                                                <i class="clearblackicon"></i>
                                               			<i class="fa fa-spinner fa-spin file-uploading"></i>
		                                            </div>
		                                        </div>
											  	<label class="error text-red" style="display: table;"></label>
											<!-- <?php //echo $this->Form->input('SubjectFile.upload_file', array('type' => 'file', 'label' => false, 'id'=>"subject_files", 'div' => false, 'class' => 'form-control')); ?>
											  <a onclick="reset($('#subject_files'))" class="close-a tipText clearImage" title="Clear File Name"> <i class="clearblackicon"></i></a>
											  <label class="error  text-red"></label> -->
										   </div>
									  </div>
									</div>

									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Title:<sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										  <div class="form-control-skill">
										  <div class="title-group-w">
										<?php echo $this->Form->input('SubjectFile.file_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>
										<button type="button" class="btn outline-btn-s submit_subject_files " >Add</button>
										  </div>
										  <label class="error  text-red"></label>
									  </div>
										</div>
									</div>
								<div class="col-lg-12">
									<div class="popup-skill-list popup-skill-files">
										<ul></ul>
									</div>
								</div>
							</div>
					<?php echo $this->Form->end(); ?>
				</div>

				<div class="tab-pane fade "  id="tab_keywords" >

					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'keywords_add', 'url'=>'manage_subject_keywords', 'enctype'=>'multipart/form-data' )); ?>
							<input type="hidden" name="data[Keyword][item_id]" value="<?php echo $this->request->data['Subject']['id']; ?>" >
							<div class="row">

									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Keyword:<sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										  <div class="form-control-skill">
										  <div class="title-group-w">
										<?php echo $this->Form->input('Keyword.keyword', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>
											<button type="button" class="btn outline-btn-s submit_keywords">Add</button>
										  </div>
										 <label class="error   text-red" style="display: table;"></label>
									  </div>
										</div>
									</div>
								<div class="col-lg-12">
									<div class="popup-skill-list popup-skill-keywords">
										<ul></ul>
									</div>
								</div>
							</div>
					<?php echo $this->Form->end(); ?>
				</div>

			</div>
			</div>
			<div class="modal-footer clearfix">
				<span class="submitmsg"></span>
				<button type="button" class="btn btn-primary submit_subject submit_data" >Save</button>
				<button type="button" id="discard" class="btn outline-btn-s cancel-add" data-dismiss="modal">Cancel</button>

			</div>
<style>
	.error  {
	    font-size: 11px;
	    font-weight: 500;
		margin: 0;
	}
	.success-msg{
		display:none;
	}
	.remove_link,.remove_file,.remove_keywords{
		cursor:pointer;
	}
	.add-skill-t .nav-tabs>li:first-child {
	    padding-left: 0 !important;
	}
	.remove-image {
		cursor: pointer;
	}
	.remove-image.not-shown {
		display: none;
	}
	.file-uploading, .remove-image {
		display: none;
	}
</style>
<script>
$(function(){

	$('body').on("click", ".tab-file", function(e) {
		$that = $(this);
		var dtype = $that.data('type');
		setTimeout(function(){
			if( dtype == 'keywords'){
				$('input[name="data[Keyword][keyword]"]').focus();
			}
		},300)
	});

	$("form#subject_add, form#subject_links_add, form#subject_files_add ,  form#keywords_add").submit(function(){
		return false;
	})

	$("#add_tabs").on('show.bs.tab', function(e){
		// $('.error').text('')
	})
	$('.cancel-add').off('click').on('click', function(event) {
		var delete_files = [];
		if($.temp_data.main.filename != ''){
			delete_files.push($.temp_data.main.filename);
		}

		$.each($.temp_data.file, function(index, el) {
			if(el.filename != ''){
				delete_files.push(el.filename);
			}
		})
		//remove temp uploaded files
		if(delete_files.length > 0){
			$.ajax({
				url: $js_config.base_url + 'competencies/delete_temp_files',
				type: 'POST',
				dataType: 'json',
				data: {files: delete_files},
				success: function(response){
					if(response.success){
					}
				}
			})
		}
	})

	var temp_dir = $js_config.web_root + 'competency_temp_files/'
	$.temp_detail_file = '';
	$.tempfile = '';
	$.temp_data = {
		main: {
			id: '<?php echo $data_id; ?>',
			name: '<?php echo $data_title; ?>',
			description: $('textarea[name="data[Subject][description]"]').val() || '',
			filename: '',
		},
		link: {},
		file: {},
		keyword:{}
	};

	var typingTimer;//timer identifier
	var doneTypingInterval = 300;  //time in ms
	$('input[name="data[Keyword][keyword]"]').keypress(function (e) {
		clearTimeout(typingTimer);
  		//user is "finished typing," do something
  		typingTimer = setTimeout($.proxy(function(){
			  if (e.which == 13) {
				  if( $.trim($("[name='data[Keyword][keyword]']").val()) != '' && $.trim($("[name='data[Keyword][keyword]']").val()).length > 0 ){
						$('.submit_keywords').trigger('click');
				  }
			  }
		 },this), doneTypingInterval);
	});

	$.keyword_counter = 0;
	$('.submit_keywords').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('');
		var error = false;
		var item_id = $("[name='data[Keyword][item_id]']").val();
		if( $.trim($("[name='data[Keyword][keyword]']").val()) == '' && $.trim($("[name='data[Keyword][keyword]']").val()).length == 0 ){
			$("[name='data[Keyword][keyword]']").parents('.form-control-skill:first').find('.error').text('Keyword is required')
			return;
		}

		$.checkKeywords($.trim($("[name='data[Keyword][keyword]").val()),item_id,'subject').done(function(respose){

			if( respose.success == 'false' ){

				$("[name='data[Keyword][keyword]']").parents('.form-control-skill:first').find('.error').text('Please add a unique Keyword');
				return;

			} else {

				if($.temp_data['keyword']){
					var newKeyword = $.trim($("[name='data[Keyword][keyword]").val());
					$.each($.temp_data['keyword'], function(index, el) {
						if(el.keyword == newKeyword){
							$("[name='data[Keyword][keyword]']").parents('.form-control-skill:first').find('.error').text('Please add a unique Keyword');
							error = true;
						}
					});
				}

				if(error){
					return;
				}

				$.temp_data['keyword'][$.keyword_counter] = {
					keyword: $.trim($("[name='data[Keyword][keyword]").val())
				}
				$.keyword_counter++;
				$("[name='data[Keyword][keyword]").val('');

				var $ul = $('.popup-skill-keywords ul');
				//$ul.empty();
				$('li.post-added', $ul).remove();
				$.each($.temp_data.keyword, function(index, el) {
					if(el.keyword.length >0){
					var $li = $('<li class="post-added">');
					var $span1 = $('<span class="list-text">').text(el.keyword).appendTo($li);
					var $span2 = $('<span class="list-icon">').appendTo($li);
					var $anc_del = $('<a class="tipText" title="Remove Now" data-id="'+index+'" >').appendTo($li);
					var $anc_del_icon = $('<i class="clearblackicon"></i>').appendTo($anc_del);
					$anc_del.on('click', function(event) {
						event.preventDefault();
						var key = $(this).data('id');
						delete $.temp_data.keyword[key];
						$(this).parents('li:first').remove();
						$('.tooltip').hide();
					});
					$li.prependTo($ul);
					}else{

						delete $.temp_data.keyword[index];
					}

				})
			}
		})
		$('input[name="data[Keyword][keyword]"]').focus();
	});

	$.link_counter = 0;
	$('.submit_subject_links').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('');
		var error = false;
		if($.trim($("[name='data[SubjectLink][web_link]']").val()) == ''){
			$("[name='data[SubjectLink][web_link]']").parent().find('.error').text('Link is required')
			error = true;
		}
		if( $.trim($("[name='data[SubjectLink][web_link]']").val()) != '' && $.isValidUrl($.trim($("[name='data[SubjectLink][web_link]']").val())) == -1 ){
			$("[name='data[SubjectLink][web_link]']").parent().find('.error').text('Missing a protocol, hostname or filename')
			error = true;
		}
		if($.trim($("[name='data[SubjectLink][link_name]']").val()) == ''){
			$("[name='data[SubjectLink][link_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		var wlink = $("[name='data[SubjectLink][web_link]']").val();
		var input_link = wlink;
		if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
				input_link = 'http://'+wlink;
		}
		$.temp_data['link'][$.link_counter] = {
			title: $("[name='data[SubjectLink][link_name]").val(),
			url: input_link
		}

		$.link_counter++;
		$("[name='data[SubjectLink][link_name]").val('');
		$("[name='data[SubjectLink][web_link]']").val('');

		var $ul = $('.popup-skill-links ul');
		//$ul.empty();
		$('li.post-added', $ul).remove();

		$.each($.temp_data.link, function(index, el) {

			var $li = $('<li class="post-added">');
			var $span1 = $('<span class="list-text">').text(el.title).appendTo($li);
			var $span2 = $('<span class="list-icon">').appendTo($li);
			var $anc_link = $('<a href="'+el.url+'" target="_blank" class="tipText open-link" title="Open Link">').appendTo($li);
			var $anc_link_icon = $('<i class="openlinkicon"></i>').appendTo($anc_link);
			var $anc_del = $('<a class="tipText" title="Remove Link" data-id="'+index+'" >').appendTo($li);
			var $anc_del_icon = $('<i class="clearblackicon"></i>').appendTo($anc_del);
			$anc_del.on('click', function(event) {
				event.preventDefault();
				var key = $(this).data('id');
				delete $.temp_data.link[key];
				$(this).parents('li:first').remove();
				$('.tooltip').hide();
			});
			$li.prependTo($ul);
		});
	})

	$.file_counter = 0;
	$('.submit_subject_files').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('')
		var error = false;
		if($.trim($("[name='data[SubjectFile][upload_file]']").val()) == ''){
			$("[name='data[SubjectFile][upload_file]']").parents('.form-group').find('.error').text('File Name is required')
			error = true;
		}
		if($.trim($("[name='data[SubjectFile][file_name]']").val()) == ''){
			$("[name='data[SubjectFile][file_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		$.temp_data['file'][$.file_counter] = {
			title: $("[name='data[SubjectFile][file_name]']").val(),
			filename: $.tempfile
		}
		$.file_counter++;
		$("[name='data[SubjectFile][file_name]']").val('');
		$("#subject_files").val('');
		$("#subject_files").parent().find('.up-text').text("Upload a file");


		var $ul = $('.popup-skill-files ul');
		//$ul.empty();
		$('.post-added', $ul).remove();
		$.each($.temp_data.file, function(index, el) {
			var $li = $('<li class="post-added">');
			var $span1 = $('<span class="list-text">').text(el.title).appendTo($li);
			var $span2 = $('<span class="list-icon">').appendTo($li);
			var $anc_link = $('<a href="'+$js_config.base_url+'competencies/download_temp_files/'+el.filename+'"  class="tipText" title="Download File" download>').appendTo($li);
			var $anc_link_icon = $('<i class="downloadblackicon"></i>').appendTo($anc_link);
			var $anc_del = $('<a class="tipText" title="Remove Now" data-id="'+index+'" data-file="'+el.filename+'">').appendTo($li);
			var $anc_del_icon = $('<i class="clearblackicon"></i>').appendTo($anc_del);
			$anc_del.on('click', function(event) {
				event.preventDefault();
				var key = $(this).data('id');
				var filename = $(this).data('file');
				var $parent = $(this).parents('li:first');

				$.ajax({
					url: $js_config.base_url + 'competencies/temp_remove_file',
					type: 'POST',
					dataType: 'json',
					data: {file: filename},
					success: function(response){
						if(response.success){
							delete $.temp_data.file[key];
							$parent.remove()
							$('.tooltip').hide();
						}
					}
				})

			});
			$li.prependTo($ul);
		});

	})

	$('.submit_subject').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('')
		if($.trim($("[name='data[Subject][title]']").val()) == ''){
			$("[name='data[Subject][title]']").parent().find('.error').text('Name is required')
			$('#add_tabs a[href="#tab_main"]').tab('show');
			return;
		}

		if($.temp_detail_file != '') {
			$.temp_data.main['filename'] = $.temp_detail_file;
		}

		$.ajax({
			url: $js_config.base_url + 'competencies/save_subject_data',
			type: 'POST',
			dataType: 'json',
			data: $.temp_data,
			success: function(response){

				if(response.success){
					$.modal_target = true;
					$.add_type = 'subject';
					$.updatedata = 'edit';
					$.temp_detail_file = '';
					$.tempfile = '';
					$('#modal_create_skills').modal('hide');
				}
			}
		})
	})

	$("#subject_image").change(function() {
		// $(this).parent().find('.clearImage').show();
		if(this.files[0] !== undefined){

			$(this).parents(".add-skill-t").find(".submit_subject").prop('disabled', true);
			var $parent = $(this).parents('.ski-close:first');
			$.file_uploading(false, $parent);
		   	var file = this.files[0],
	            name = file.name,
	            size = file.size,
	            type = file.type,
	            $upText = $(this).parent().find('.up-text');

	        var filesize = file.size / 1048576;
			name = newStr = name.replace(/-/g, "");
			$upText.html(name);
			// $(this).parents('.input-group').find('.remove-image').removeClass('not-shown');

			var $form = $("#subject_add");
			var formData = new FormData($form[0]),
				$fileInput = $(this),
				file = $fileInput[0].files[0];

			var filePath = file.name;
			var file_ext = filePath.substr(filePath.lastIndexOf('.')+1,filePath.length);

			if( $fileInput.val() !== "" && file !== undefined && $.inArray(file_ext, ['jpg','jpeg','png']) == -1  ){
				$("form#subject_add").find(".remove-image").trigger('click');
				return;
			}

			if ($fileInput.val() !== "" && file !== undefined) {
				var name = file.name.replace(/-/g, ''),
					size = file.size,
					type = file.type;
				formData.append('file_name', $fileInput[0].files[0], $fileInput[0].files[0]['name'].replace(/-/g, ''));

				$.ajax({
					url: $js_config.base_url + 'competencies/temp_save_file',
					type:'POST',
					dataType:'json',
					data: formData,
					global: false,
					cache: false,
					contentType: false,
					processData: false,
					success:function(response){
						if(response.success){
							$.file_uploading(true, $parent);
							setTimeout(function(){
								$form.parents(".add-skill-t").find(".submit_subject").prop('disabled', false);
							},250);
							$.temp_detail_file = response.content
							$.temp_data.main['filename'] = $.temp_detail_file;
						}
					}
				})
			}
		}
	})

	$("#subject_files").change(function() {
	   	$(this).parent().find('.clearImage').show();
	   	if(this.files[0] !== undefined){
			var $parent = $(this).parents('.ski-close:first');
			$.file_uploading(false, $parent);
		   	var file = this.files[0],
	            name = file.name,
	            size = file.size,
	            type = file.type,
	            $upText = $(this).parent().find('.up-text');

	        var filesize = file.size / 1048576;
			name = newStr = name.replace(/-/g, "");
			$upText.html(name);
			// $(this).parents('.input-group').find('.remove-image').removeClass('not-shown');

			/*if( file.name.length > 45 ){
			  var fileNameString =start_and_end(name);
				 $upText.html(fileNameString)// + ', ' + filesize.toFixed(2) + 'MB'
			} else {
				$upText.html(name)// + ', ' + filesize.toFixed(2) + 'MB'
			}*/
			//

			var $form = $("#subject_files_add");
			$form.find(".submit_subject_files").prop('disabled', true);
			var formData = new FormData($form[0]),
				$fileInput = $(this),
				file = $fileInput[0].files[0];

			if ($fileInput.val() !== "" && file !== undefined) {
				var name = file.name.replace(/-/g, ''),
					size = file.size,
					type = file.type;
				formData.append('file_name', $fileInput[0].files[0], $fileInput[0].files[0]['name'].replace(/-/g, ''));

				$.ajax({
					url: $js_config.base_url + 'competencies/temp_save_file',
					type:'POST',
					dataType:'json',
					data: formData,
					global: false,
					cache: false,
					contentType: false,
					processData: false,
					complete: function() {
					},
					success:function(response){
						if(response.success){
							$.file_uploading(true, $parent);
							$form.find(".submit_subject_files").prop('disabled', false);
							$.tempfile = response.content;
						}
					}
				})
			}
		}
	});

	$('.remove-image').off('click').on('click', function(event) {
		$(this).parents('.input-group').find('input[type="file"]').val('');
		$(this).parents('.input-group').find('.up-text').text('Upload a file');
		$(this).addClass('not-shown');
	})

	;($.get_pre_keywords = function(){

		var data = { 'id': '<?php echo $data_id; ?>','dtype': 'subject' };

		$.ajax({
			url: $js_config.base_url+'competencies/getkeywords',
			type:'POST',
			dataType:'json',
			data: data,
			global: false,
			success:function(response){
				if( response.success ){
					var result = '';
					$.each(response.content, function(k, v) {

						result +='<li><span class="list-text">'+v['Keyword']['keyword']+'</span> <span class="list-icon"> <a class="remove_keywords tipText" data-id="'+v['Keyword']['id']+'"  data-type="subject" title="Remove Now" ><i class="clearblackicon"></i></a> </span></li>';

					})
					$(".popup-skill-keywords").find('ul').html(result);
				}
			}
		})
	})();

	;($.get_pre_links = function(){

		var data = { 'id': '<?php echo $data_id; ?>','dtype': 'subject' };

		$.ajax({
			url: $js_config.base_url+'competencies/getSkillLinks',
			type:'POST',
			dataType:'json',
			data: data,
			global: false,
			success:function(response){
				if( response.success ){
					var result = '';
					$.each(response.content, function(k, v) {

						var wlink = v['SubjectLink']['web_link'];
						var nwlink = '';
						if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
								nwlink = 'http://'+wlink;
						}

						result +='<li><span class="list-text">'+v['SubjectLink']['link_name']+'</span> <span class="list-icon"> <a href="'+nwlink+'" target="_blank" class="tipText open-link" title="Open Link" ><i class="openlinkicon"></i></a> <a class="remove_link tipText" data-id="'+v['SubjectLink']['id']+'" data-type="subject" title="Remove Link"  ><i class="clearblackicon"></i></a> </span></li>';


					})
					$(".popup-skill-links").find('ul').html(result);
				}
			}
		})
	})()

	;($.get_pre_files = function(){

			var data = { 'id': '<?php echo $data_id; ?>','dtype': 'subject' };

			$.ajax({
				url: $js_config.base_url+'competencies/getSkillFiles',
				type:'POST',
				dataType:'json',
				data: data,
				global: false,
				success:function(response){
					if( response.success ){
						var result = '';
						$.each(response.content, function(k, v) {
								result +='<li class="pre-added"><span class="list-text">'+v['SubjectFile']['file_name']+'</span> <span class="list-icon"> <a href="'+$js_config.base_url+'competencies/download_files/'+v['SubjectFile']['id']+'/subject" data-type="subject" class="tipText" title="Download File" download ><i class="downloadblackicon"></i></a> <a class="remove_file tipText" title="Remove Now" data-id="'+v['SubjectFile']['id']+'"  data-type="subject"><i class="clearblackicon"></i></a> </span></li>';

						})
						$(".popup-skill-files").find('ul').html(result);
					}
				}
			})
	})()



	$("#subject_image, #subject_files").change(function() {
	   $(this).parent().find('.clearImage').addClass('tipText').show();
	});
	$('body').delegate('input[name="data[Subject][title]"]', 'keyup focus', function(event){
		$(this).parent().find('.error').text('');
		$.temp_data.main['name'] = $(this).val();
	});
	$('body').delegate('textarea[name="data[Subject][description]"]', 'keyup focus', function(event){
		$(this).parent().find('.error').text('');
		$.temp_data.main['description'] = $(this).val();
	});
	$('body').delegate('input[name="data[Subject][title]"]', 'blur', function(event){
		$(this).parent().find('.error').text('');
	});
	$('body').delegate('textarea[name="data[Subject][description]"]', 'blur', function(event){
		$(this).parent().find('.error').text('');
	});
	$('body').delegate('input[name="data[SubjectLink][link_name]"]', 'blur', function(event){
		$(this).parents('.form-group:first').find('.error').text('');
	});
	$('body').delegate('input[name="data[SubjectFile][upload_file]"]', 'blur', function(event){
		$(this).parents('.form-group:first').find('.error').text('');
	});
	$('body').delegate('input[name="data[SubjectFile][file_name]"]', 'blur', function(event){
		$(this).parents('.form-group:first').find('.error').text('');
	});

	$('.info-icon').tooltip({
		html: true,
		container: 'body',
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none;">150px by 150px Recommended</div></div>'
	});

	$('.up-icon').off('click').on('click', function(event){
		$(this).parents('.docupload-sec:first').find('input[type=file]').trigger('click');
	});

});
</script>