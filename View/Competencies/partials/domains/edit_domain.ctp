<?php

$this->request->data['KnowledgeDomain']['title'] = $data_title =  html_entity_decode(html_entity_decode($domain_data['KnowledgeDomain']['title'] ,ENT_QUOTES, "UTF-8"));
$this->request->data['KnowledgeDomain']['description'] = $data_desc = html_entity_decode(html_entity_decode($domain_data['KnowledgeDomain']['description'] ,ENT_QUOTES, "UTF-8"));
$this->request->data['KnowledgeDomain']['image'] = $domain_data['KnowledgeDomain']['image'];
$this->request->data['KnowledgeDomain']['id'] = $data_id = $domain_data['KnowledgeDomain']['id'];

$data_title = addslashes($data_title);
$data_desc = addslashes($data_desc);

?>
			<div class="modal-header">
				<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Edit Domain</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs" id="add_tabs">
					<li class="active">
						<a data-toggle="tab" data-type="details" class="active skilltab tab-detail" data-target="#tab_main" href="#tab_main" aria-expanded="true" >Details</a>
					</li>

					<li class="">
						<a data-toggle="tab" data-tab="domain_link"  data-type="links" id="linksTab" data-target="#tab_links" href="#tab_links" aria-expanded="false" class="skilltab <?php if( !isset($this->request->data['KnowledgeDomain']['id']) || empty($this->request->data['KnowledgeDomain']['id']) ){ ?> not-active1 <?php } ?> tab-link" >Links</a>
					</li>

					<li class="">
						<a data-toggle="tab" data-tab="domain_file" data-type="files" id="filesTab" data-target="#tab_files" href="#tab_files" aria-expanded="false" class="skilltab <?php if( !isset($this->request->data['KnowledgeDomain']['id']) || empty($this->request->data['KnowledgeDomain']['id']) ){ ?> not-active1 <?php } ?> tab-file" >Files</a>
						<input type="hidden" name="domain_id" value="<?php echo $this->request->data['KnowledgeDomain']['id']; ?>" >
					</li>

					<li class="">
						<a data-toggle="tab" data-tab="domain_keyword" data-type="keywords" id="filesTab" data-target="#tab_keywords" href="#tab_keywords" aria-expanded="false" class="skilltab tab-file" >Keywords</a>
					</li>

				</ul>

				<div class="tab-content">
				<div class="success-msg"></div>

                 <div class="tab-pane fade active in"  id="tab_main" >
					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'domain_add', 'url'=>'manage_domains', 'enctype'=>'multipart/form-data' )); ?>

						<div class="row">
							<div class="form-group">
							  <label for="UserUser" class="col-lg-2 control-label">Name: <sup class="text-danger">*</sup></label>
							  <div class="col-lg-10">
								  <div class="form-control-skill">
								<?php echo $this->Form->input('KnowledgeDomain.title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>
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
												<input type="file" name="data[KnowledgeDomain][image]" class="form-control upload" id="domain_image" placeholder="Upload File" accept=".png, .jpg, .jpeg">
												<span class="up-text"  style="">Upload a file</span>
                                            </span>
                                            <div class="input-group-addon remove-image not-shown tipText" title="Clear Image">
                                                <i class="clearblackicon"></i>
                                               <i class="fa fa-spinner fa-spin file-uploading"></i>
                                            </div>
                                        </div>

									  </div>

									<div class="show_images">
									<?php if( isset($this->request->data['KnowledgeDomain']['image']) && !empty($this->request->data['KnowledgeDomain']['image']) && isset($this->request->data['KnowledgeDomain']['id']) && !empty($this->request->data['KnowledgeDomain']['id']) ){ ?>
										<span class="main_image"><?php echo $this->request->data['KnowledgeDomain']['image'];?></span><i data-type="domain" data-id="<?php echo $this->request->data['KnowledgeDomain']['id'];?>" class="clearredicon remove_image tipText" title="Remove Now"></i>
									<?php } ?>
									</div>

							  </div>
							</div>

							<div class="col-lg-12">
							<div class="form-group">
							  <label for="UserUser" class="control-label">Description:</label>
							  <div class="form-control-skill">
								<?php echo $this->Form->input('KnowledgeDomain.description', array('type' => 'textarea', 'label' => false, 'div' => false, 'class' => 'form-control', 'rows'=> 5, 'cols'=>35, 'maxlength'=>500)); ?>
								<label class="error  text-red"></label>
							  </div>
							 </div>
							</div>
						</div>

					<input type="hidden" name="data[KnowledgeDomain][id]" value="<?php echo $this->request->data['KnowledgeDomain']['id']; ?>" >
					<?php echo $this->Form->end(); ?>
				 </div>


				<div class="tab-pane fade" id="tab_links" >
					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'domain_links_add', 'url'=>'manage_domain_links', 'enctype'=>'multipart/form-data' )); ?>

							<div class="row">
									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Web Link: <sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										  <div class="form-control-skill">
										<?php echo $this->Form->input('DomainLink.web_link', array('type' => 'url', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'placeholder'=> '', 'autocomplete'=>'off' )); ?>
										<label class="error  text-red"></label>
									  </div>
										</div>
									</div>
									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Title: <sup class="text-danger">*</sup></label>
										<div class="col-lg-10">
											<div class="form-control-skill">
										  <div class="title-group-w">
											<?php echo $this->Form->input('DomainLink.link_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'maxlength'=>50, 'autocomplete'=>'off' )); ?>
											<button type="button" class="btn outline-btn-s submit_domain_links">Add</button>
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

					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'domain_files_add', 'url'=>'manage_domain_files', 'enctype'=>'multipart/form-data' ));

					?>
							<input type="hidden" name="data[DomainFile][domain_id]" value="<?php echo $this->request->data['KnowledgeDomain']['id']; ?>" >

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
												<input type="file" name="data[DomainFile][upload_file]" class="form-control upload" id="domain_files" placeholder="Upload File">
												<span class="up-text" >Upload a file</span>
                                            </span>
                                            <div class="input-group-addon remove-image not-shown tipText" title="Clear File Name">
                                                <i class="clearblackicon"></i>
                                               <i class="fa fa-spinner fa-spin file-uploading"></i>
                                            </div>
                                        </div>
                                            <label class="error  text-red" style="display: table;"></label>
                                            </div>
									  </div>
									</div>

									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Title:<sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										  <div class="form-control-skill">
										  <div class="title-group-w">
										<?php echo $this->Form->input('DomainFile.file_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>

											<button type="button" class="btn outline-btn-s submit_domain_files">Add</button>
										  </div>
										  <label class="error  text-red" style="display: table;"></label>
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

					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'keywords_add', 'url'=>'manage_domain_keywords', 'enctype'=>'multipart/form-data' )); ?>
							<input type="hidden" name="data[Keyword][item_id]" value="<?php echo $this->request->data['KnowledgeDomain']['id']; ?>" >
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
				<button type="button" class="btn btn-primary submit_domain submit_data" >Save</button>
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

	.add-skill-popup-cont .nav-tabs {
	    padding: 0px 15px 15px 0px !important;
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

	$("#add_tabs").on('show.bs.tab', function(e){
		// $('.error').text('')
	})

	$("form#domain_add, form#domain_links_add, form#domain_files_add,  form#keywords_add").submit(function(){
		return false;
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
					console.log(response)
					if(response.success){
						console.log(response)
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
			description: $('textarea[name="data[KnowledgeDomain][description]"]').val() || '',
			filename: '',
		},
		link: {},
		file: {},
		keyword:{}
	};
	setTimeout(function(){
		$.temp_data.main.description = $('textarea[name="data[KnowledgeDomain][description]"]').val();
	}, 300)

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
		if( $.trim($("[name='data[Keyword][keyword]']").val()) == '' ){
			$("[name='data[Keyword][keyword]']").parents('.form-control-skill:first').find('.error').text('Keyword is required')
			return;
		}


		$.checkKeywords( $.trim($("[name='data[Keyword][keyword]").val()),item_id, 'domain').done(function(respose){

			if( respose.success == 'false' ){
				$("[name='data[Keyword][keyword]']").parents('.form-control-skill:first').find('.error').text('Please add a unique Keyword')
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
				});

			}

		})
		$('input[name="data[Keyword][keyword]"]').focus();
	});

	$.link_counter = 0;
	$('.submit_domain_links').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('');
		var error = false;
		if($.trim($("[name='data[DomainLink][web_link]']").val()) == ''){
			$("[name='data[DomainLink][web_link]']").parent().find('.error').text('Link is required')
			error = true;
		}
		if( $.trim($("[name='data[DomainLink][web_link]']").val()) != '' && $.isValidUrl($.trim($("[name='data[DomainLink][web_link]']").val())) == -1 ){
			$("[name='data[DomainLink][web_link]']").parent().find('.error').text('Missing a protocol, hostname or filename')
			error = true;
		}
		if($.trim($("[name='data[DomainLink][link_name]']").val()) == ''){
			$("[name='data[DomainLink][link_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		var wlink = $("[name='data[DomainLink][web_link]']").val();
		var input_link = wlink;
		if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
				input_link = 'http://'+wlink;
		}
		$.temp_data['link'][$.link_counter] = {
			title: $("[name='data[DomainLink][link_name]").val(),
			url: input_link
		}

		$.link_counter++;
		$("[name='data[DomainLink][link_name]").val('');
		$("[name='data[DomainLink][web_link]']").val('');

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
	$('.submit_domain_files').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('')
		var error = false;
		if($.trim($("[name='data[DomainFile][upload_file]']").val()) == ''){
			$("[name='data[DomainFile][upload_file]']").parents('.form-group').find('.error').text('File Name is required')
			error = true;
		}
		if($.trim($("[name='data[DomainFile][file_name]']").val()) == ''){
			$("[name='data[DomainFile][file_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		$.temp_data['file'][$.file_counter] = {
			title: $("[name='data[DomainFile][file_name]']").val(),
			filename: $.tempfile
		}
		$.file_counter++;
		$("[name='data[DomainFile][file_name]']").val('');
		$("#domain_files").val('');
		$("#domain_files").parent().find('.up-text').text("Upload a file");


		var $ul = $('.popup-skill-files ul');
		//$ul.empty();
		$('.post-added', $ul).remove();
		$.each($.temp_data.file, function(index, el) {
			var $li = $('<li class="post-added">');
			var $span1 = $('<span class="list-text">').text(el.title).appendTo($li);
			var $span2 = $('<span class="list-icon">').appendTo($li);
			var $anc_link = $('<a href="'+$js_config.base_url+'competencies/download_temp_files/'+el.filename+'" class="tipText" title="Download File" download>').appendTo($li);
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
						console.log(response)
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

	$('.submit_domain').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('')
		if($.trim($("[name='data[KnowledgeDomain][title]']").val()) == ''){
			$("[name='data[KnowledgeDomain][title]']").parent().find('.error').text('Name is required')
			$('#add_tabs a[href="#tab_main"]').tab('show');
			return;
		}

		if($.temp_detail_file != '') {
			$.temp_data.main['filename'] = $.temp_detail_file;
		}
		// console.log($.temp_data)

		$.ajax({
			url: $js_config.base_url + 'competencies/save_domain_data',
			type: 'POST',
			dataType: 'json',
			data: $.temp_data,
			success: function(response){

				if(response.success){
					$.modal_target = true;
					$.add_type = 'domain';
					$.updatedata = 'edit';
					$.temp_detail_file = '';
					$.tempfile = '';
					$('#modal_create_skills').modal('hide');
				}
			}
		})
	})

	$("#domain_image").change(function() {
		// $(this).parent().find('.clearImage').show();
		if(this.files[0] !== undefined){
			$(this).parents(".add-skill-t").find(".submit_domain").prop('disabled', true);
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

			var $form = $("#domain_add");
			var formData = new FormData($form[0]),
				$fileInput = $(this),
				file = $fileInput[0].files[0];

			var filePath = file.name;
			var file_ext = filePath.substr(filePath.lastIndexOf('.')+1,filePath.length);

			if( $fileInput.val() !== "" && file !== undefined && $.inArray(file_ext, ['jpg','jpeg','png']) == -1  ){
				$("form#domain_add").find(".remove-image").trigger('click');
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
								$fileInput.parents(".add-skill-t").find(".submit_domain").prop('disabled', false);
							},250);
							$.temp_detail_file = response.content
							$.temp_data.main['filename'] = $.temp_detail_file;
						}
					}
				})
			}
		}
	})

	$("#domain_files").change(function() {
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
			$(this).parents('.input-group').find('.remove-image').removeClass('not-shown');

			/*if( file.name.length > 45 ){
			  var fileNameString =start_and_end(name);
				 $upText.html(fileNameString)// + ', ' + filesize.toFixed(2) + 'MB'
			} else {
				$upText.html(name)// + ', ' + filesize.toFixed(2) + 'MB'
			}*/
			//

			var $form = $("#domain_files_add");
			$form.find(".submit_domain_files").prop('disabled', true);
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
							$form.find(".submit_domain_files").prop('disabled', false);
							$.tempfile = response.content
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
		if($(this).parents('.input-group').find('input[type="file"]').is($('#domain_image'))) {
			$.temp_data.main['filename'] = '';
			$.temp_detail_file = '';
		}
	});


	;($.get_pre_keywords = function(){

		var data = { 'id': '<?php echo $data_id; ?>','dtype': 'domain' };

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

						result +='<li><span class="list-text">'+v['Keyword']['keyword']+'</span> <span class="list-icon"> <a class="remove_keywords tipText" data-id="'+v['Keyword']['id']+'"  data-type="domain" title="Remove Now" ><i class="clearblackicon"></i></a> </span></li>';

					})
					$(".popup-skill-keywords").find('ul').html(result);
				}
			}
		})
	})();

	($.get_pre_links = function(){

		var data = { 'id': '<?php echo $data_id; ?>','dtype': 'domain' };

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

						var wlink = v['DomainLink']['web_link'];
						var nwlink = '';
						if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
								nwlink = 'http://'+wlink;
						}

						result +='<li><span class="list-text">'+v['DomainLink']['link_name']+'</span> <span class="list-icon"> <a href="'+nwlink+'"  target="_blank" class="tipText open-link" title="Open Link"  ><i class="openlinkicon"></i></a> <a class="remove_link tipText" data-id="'+v['DomainLink']['id']+'"  data-type="domain" title="Remove Link"    ><i class="clearblackicon"></i></a> </span></li>';


					})
					$(".popup-skill-links").find('ul').html(result);
				}
			}
		})
	})();

	($.get_pre_files = function(){

		var data = { 'id': '<?php echo $data_id; ?>','dtype': 'domain' };

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

						result +='<li class="pre-added"><span class="list-text">'+v['DomainFile']['file_name']+'</span> <span class="list-icon"> <a href="'+$js_config.base_url+'competencies/download_files/'+v['DomainFile']['id']+'/domain" data-type="domain" class="tipText" title="Download File" download><i class="downloadblackicon"></i></a> <a class="remove_file tipText" title="Remove Now"  data-id="'+v['DomainFile']['id']+'"  data-type="domain" ><i class="clearblackicon"></i></a> </span></li>';

					})
					$(".popup-skill-files").find('ul').html(result);
				}
			}
		})
	})();



	$('body').delegate('input[name="data[KnowledgeDomain][title]"]', 'keyup focus', function(event){
		event.preventDefault();
		$.temp_data.main['name'] = $(this).val();
	});

	$('body').delegate('input[name="data[KnowledgeDomain][title]"]', 'blur', function(event){
		$(this).parent().find('.skill_title_err').text('');
	});

	$('body').delegate('textarea[name="data[KnowledgeDomain][description]"]', 'keyup focus', function(event){
		event.preventDefault();
		$.temp_data.main['description'] = $(this).val();
	});

	$('body').delegate('textarea[name="data[KnowledgeDomain][description]"]', 'blur', function(event){
		$(this).parent().find('.skill_description_err').text('');
	});


	$('body').delegate('input[name="data[DomainLink][web_link]"]', 'blur', function(event){
		$(this).parent().find('.error').text('');
	});

	$('body').delegate('input[name="data[DomainLink][link_name]"]', 'blur', function(event){
		$(this).parents('.form-group:first').find('.error').text('');
	});


	$('body').delegate('input[name="data[DomainFile][file_name]"]', 'blur', function(event){
		$(this).parent().next('.file_title_err').text('');
	});

	$('.info-icon').tooltip({
		html: true,
		container: 'body',
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none;">150px by 150px Recommended</div></div>'
	});

	$('.up-icon').off('click').on('click', function(event){
		$(this).parents('.docupload-sec:first').find('input[type=file]').trigger('click');
	});
})
</script>