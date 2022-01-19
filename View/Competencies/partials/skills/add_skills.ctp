<?php //echo $this->Html->script('jquery.validate', array('inline' => true)); ?>
<?php
	$buttonText = 'Add';
	$titleText = 'Add';
	$this->request->data['Skill']['title'] = '';
	$this->request->data['Skill']['description'] = '';
	$this->request->data['Skill']['image'] = '';
	$this->request->data['Skill']['id'] = '';

	if( isset($skill_data) && !empty($skill_data) ){
		$buttonText = 'Save';
		$titleText = 'Edit';
		$this->request->data['Skill']['title'] = $skill_data['Skill']['title'];
		$this->request->data['Skill']['description'] = $skill_data['Skill']['description'];
		$this->request->data['Skill']['image'] = $skill_data['Skill']['image'];
		$this->request->data['Skill']['id'] = $skill_data['Skill']['id'];

	}

?>
			<div class="modal-header">
				<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $titleText;?> Skill</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs" id="add_tabs" >
					<li class="active">
						<a data-toggle="tab" data-type="details" class="active skilltab" data-target="#tab_main" href="#tab_main" aria-expanded="true" >Details</a>
					</li>

					<li class="">
						<a data-toggle="tab" data-tab="skill_link"  data-type="links" id="linksTab" data-target="#tab_links" href="#tab_links" aria-expanded="false" class="skilltab " >Links</a>
					</li>

					<li class="">
						<a data-toggle="tab" data-tab="skill_file" data-type="files" id="filesTab" data-target="#tab_files" href="#tab_files" aria-expanded="false" class="skilltab " >Files</a>
						<input type="hidden" name="skill_id" value="<?php echo $this->request->data['Skill']['id']; ?>" >
					</li>
					<li class="">
						<a data-toggle="tab" data-tab="skill_keyword" data-type="keywords" id="keywordsTab" data-target="#tab_keywords" href="#tab_keywords" aria-expanded="false" class="skilltab tab-file" >Keywords</a>
					</li>

				</ul>

				<div class="tab-content">
				<div class="success-msg"></div>

                 <div class="tab-pane fade active in"  id="tab_main" >
					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'skill_add', 'url'=>'manage_skills', 'enctype'=>'multipart/form-data' )); ?>

						<div class="row">
							<div class="form-group" >
							  <label for="UserUser" class="col-lg-2 control-label">Name: <sup class="text-danger">*</sup></label>
							  <div class="col-lg-10">
								<div class="form-control-skill">
								<?php echo $this->Form->input('Skill.title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>
								<label class="error  text-red"></label>
								</div>
							  </div>
							</div>

							<div class="form-group ">
							  <label for="UserUser" class="col-lg-2 control-label">Image: <i class="info-icon" title="150px by 150px Recommended"></i></label>
							  <div class="col-lg-10">
									<div class="ski-close">
										<div class="input-group docupload-sec">
                                            <div class="input-group-addon up-icon">
                                                <i class="uploadblackicon"></i>
                                            </div>
                                            <span title="" class="docUpload icon_btn">
												<input type="file" name="data[Skill][image]" class="form-control upload" id="skill_image" placeholder="Click to upload a file" accept=".png, .jpg, .jpeg">
												<span class="up-text" >Click to upload a file</span>
                                            </span>
											<label class="error text-red"></label>
                                            <div class="input-group-addon remove-image not-shown tipText" title="Clear Image" >
                                                <i class="clearblackicon"></i>
                                               <i class="fa fa-spinner fa-spin file-uploading"></i>
                                            </div>
                                        </div>

									</div>
									<div class="show_images">
									<?php if( isset($this->request->data['Skill']['image']) && !empty($this->request->data['Skill']['image']) && isset($this->request->data['Skill']['id']) && !empty($this->request->data['Skill']['id']) ){ ?>
										<span class="main_image"><?php echo $this->request->data['Skill']['image'];?></span><i data-type="skill" data-id="<?php echo $this->request->data['Skill']['id'];?>" class="clearredicon remove_image tipText" title="Remove Now"></i>
									<?php } ?>
									</div>
							  </div>
							</div>

							<div class="col-lg-12">
							<div class="form-group">
								<label for="UserUser" class="control-label">Description:</label>
								<div class="form-control-skill">
									<?php echo $this->Form->input('Skill.description', array('type' => 'textarea', 'label' => false, 'div' => false, 'class' => 'form-control', 'rows'=> 5, 'cols'=>35, 'maxlength'=>500)); ?>
									<label class="error  text-red"></label>
								</div>
							 </div>
							</div>
						</div>

					<input type="hidden" name="data[Skill][id]" value="<?php echo $this->request->data['Skill']['id']; ?>" >
					<?php echo $this->Form->end(); ?>
				 </div>


				<div class="tab-pane fade" id="tab_links" >
					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'links_add', 'url'=>'manage_skill_links', 'enctype'=>'multipart/form-data' )); ?>

							<div class="row">
									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Web Link: <sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										<div class="form-control-skill">
										<?php echo $this->Form->input('SkillLink.web_link', array('type' => 'url', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'placeholder'=> '', 'autocomplete'=>'off' )); ?>
										<label class="error   text-red"></label>
									   </div>
									  </div>
									</div>
									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Title: <sup class="text-danger">*</sup></label>
										<div class="col-lg-10">
											<div class="form-control-skill">
										  <div class="title-group-w">
											<?php echo $this->Form->input('SkillLink.link_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'maxlength'=>50, 'autocomplete'=>'off' )); ?>
											<button type="button" class="btn outline-btn-s submit_links">Add</button>
										  </div>
										  <label class="error   text-red"></label>
										</div></div>
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

					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'files_add', 'url'=>'manage_skill_files', 'enctype'=>'multipart/form-data' )); ?>
							<input type="hidden" name="data[SkillFile][skill_id]" value="<?php echo $this->request->data['Skill']['id']; ?>" >
							<div class="row">

									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">File Name:<sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										   <div class="ski-close">
											  	<div class="input-group docupload-sec ">
		                                            <div class="input-group-addon up-icon">
		                                                <i class="uploadblackicon"></i>
		                                            </div>
		                                            <span title="" class="docUpload icon_btn">
														<input type="file" name="data[SkillFile][upload_file]" class="form-control upload" id="skill_files" placeholder="Click to upload a file">
														<span class="up-text" >Click to upload a file</span>
		                                            </span>
		                                            <div class="input-group-addon remove-image not-shown tipText" title="Clear File Name">
		                                                <i class="clearblackicon"></i>
                                               			<i class="fa fa-spinner fa-spin file-uploading"></i>
		                                            </div>
		                                        </div>
											  	<label class="error   text-red" style="display: table;"></label>

											<!-- <?php //echo $this->Form->input('SkillFile.upload_file', array('type' => 'file', 'label' => false, 'id'=>"skill_files", 'div' => false, 'class' => 'form-control')); ?>
											  <a onclick="reset($('#skill_files'))" class="close-a tipText clearImage" title="Clear File Name"> <i class="clearblackicon"></i></a> -->

										  </div>
									  </div>
									</div>

									<div class="form-group">
									  <label for="UserUser" class="col-lg-2 control-label">Title:<sup class="text-danger">*</sup></label>
									  <div class="col-lg-10">
										  <div class="form-control-skill">
										  <div class="title-group-w">
										<?php echo $this->Form->input('SkillFile.file_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>
											<button type="button" class="btn outline-btn-s submit_files">Add</button>
										  </div>
										  <label class="error   text-red"></label>
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

					<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'keywords_add', 'url'=>'manage_skill_keywords', 'enctype'=>'multipart/form-data' )); ?>
							<input type="hidden" name="data[Keyword][item_id]" value="<?php echo $this->request->data['Skill']['id']; ?>" >
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
				<button type="button" class="btn btn-primary submit_skill submit_data"><?php echo $buttonText;?></button>
				<button type="button" id="discard" class="btn outline-btn-s cancel-add" data-dismiss="modal">Cancel</button>

			</div>
<style>
	.error {
	    font-size: 11px;
	    font-weight: 500;
	    margin: 0;
	}
	.success-msg{
		display:none;
	}
	a .clearblackicon{
		cursor:pointer;
	}
	.remove_link,.remove_file{
		cursor:pointer;
	}
	.add-skill-t .nav-tabs>li:first-child {
	    padding-left: 0 !important;
	}
	/* .up-text {
		display: block;
		min-height: 22px;
		text-align: left;
		padding: 3px 0 0 0;
	} */
	.remove-image {
		cursor: pointer;
	}
	.remove-image.not-shown {
		display: none;
	}

	.tipText {
		text-transform: none !important;
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

	$('input[name="data[Skill][title]"]').focus();

	$("form#skill_add, form#links_add, form#files_add, form#keywords_add").submit(function(){
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
			name: '',
			description: '',
			filename: '',
		},
		link: {},
		file: {},
		keyword: {}
	};

	$.link_counter = 0;
	$('.submit_links').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('');
		var error = false;
		if($.trim($("[name='data[SkillLink][web_link]']").val()) == ''){
			$("[name='data[SkillLink][web_link]']").parent().find('.error').text('Web Link is required')
			error = true;
		}
		if( $.trim($("[name='data[SkillLink][web_link]']").val()) != '' && $.isValidUrl($.trim($("[name='data[SkillLink][web_link]']").val())) == -1 ){
			$("[name='data[SkillLink][web_link]']").parent().find('.error').text('Missing a protocol, hostname or filename')
			error = true;
		}

		if($.trim($("[name='data[SkillLink][link_name]']").val()) == ''){
			$("[name='data[SkillLink][link_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}

		if(error){
			return;
		}


		var wlink = $("[name='data[SkillLink][web_link]']").val();
		var input_link = wlink;
		if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
				input_link = 'http://'+wlink;
		}
		$.temp_data['link'][$.link_counter] = {
			title: $("[name='data[SkillLink][link_name]").val(),
			url: input_link
		}

		$.link_counter++;
		$("[name='data[SkillLink][link_name]").val('');
		$("[name='data[SkillLink][web_link]']").val('');

		var $ul = $('.popup-skill-links ul');
		$ul.empty();
		$.each($.temp_data.link, function(index, el) {

			var $li = $('<li>');
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

		if($.trim($("[name='data[Keyword][keyword]']").val()) == ''){
			$("[name='data[Keyword][keyword]']").parents('.form-control-skill:first').find('.error').text('Keyword is required');
			return;
		}

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
			keyword: $("[name='data[Keyword][keyword]").val()
		}
		$.keyword_counter++;

		$("[name='data[Keyword][keyword]").val('');

			var $ul = $('.popup-skill-keywords ul');
			$ul.empty();
			$.each($.temp_data.keyword, function(index, el) {
				if(el.keyword.length >0){
				var $li = $('<li>');
				var $span1 = $('<span class="list-text">').text(el.keyword).appendTo($li);
				var $anc_del = $('<a class="removekeyword tipText" title="Remove Now" data-id="'+index+'" >').appendTo($li);
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

		$('input[name="data[Keyword][keyword]"]').focus();
	})



	$.file_counter = 0;
	$('.submit_files').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('')
		var error = false;

		if($.trim($("[name='data[SkillFile][upload_file]']").val()) == ''){
			$("[name='data[SkillFile][upload_file]']").parents('.form-group').find('.error').text('File Name is required')
			error = true;
		}
		if($.trim($("[name='data[SkillFile][file_name]']").val()) == ''){
			$("[name='data[SkillFile][file_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		$.temp_data['file'][$.file_counter] = {
			title: $("[name='data[SkillFile][file_name]']").val(),
			filename: $.tempfile
		}
		$.file_counter++;
		$("[name='data[SkillFile][file_name]']").val('');
		$("#skill_files").val('');
		$("#skill_files").parent().find('.up-text').text("Click to upload a file");


		var $ul = $('.popup-skill-files ul');
		$ul.empty();
		$.each($.temp_data.file, function(index, el) {
			var $li = $('<li>');
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

	$('.submit_skill').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('')
		if($.trim($("[name='data[Skill][title]']").val()) == ''){
			$("[name='data[Skill][title]']").parent().find('.error').text('Name is required');
			$('#add_tabs a[href="#tab_main"]').tab('show');
			return;
		}
		var $this = $(this);
		$this.prop('disabled', true);

		$.temp_data.main['filename'] = $.temp_detail_file;

		$.ajax({
			url: $js_config.base_url + 'competencies/save_skill_data',
			type: 'POST',
			dataType: 'json',
			data: $.temp_data,
			success: function(response){

				if(response.success){
					$this.prop('disabled', false);
					$.modal_target = true;
					$.updatedata = 'add';
					$.add_type = 'skill';
					$.temp_detail_file = '';
					$.tempfile = '';
					$('.search-box[data-type="skills"]').val('').trigger('keyup');
					$('#modal_create_skills').modal('hide');
				}
			}
		})
	})

	$("#skill_image").change(function() {
		if(this.files[0] !== undefined){
			$(this).parents(".add-skill-t").find(".submit_skill").prop('disabled', true);
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

			var $form = $("#skill_add");
			var formData = new FormData($form[0]),
				$fileInput = $(this),
				file = $fileInput[0].files[0];

			var filePath = file.name;
			var file_ext = filePath.substr(filePath.lastIndexOf('.')+1,filePath.length);

			if( $fileInput.val() !== "" && file !== undefined && $.inArray(file_ext, ['jpg','jpeg','png']) == -1  ){

				$("form#skill_add").find(".remove-image").trigger('click');
				return;

			}


			if ($fileInput.val() !== "" && file !== undefined ) {

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
								$form.parents(".add-skill-t").find(".submit_skill").prop('disabled', false);
							},250);
							$.temp_detail_file = response.content
							$.temp_data.main['filename'] = $.temp_detail_file;
						}
					}
				})
			}
		}
	})

	$("#skill_files").change(function() {
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

			var $form = $("#files_add");
			$form.find(".submit_files").prop('disabled', true);
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
							$form.find(".submit_files").prop('disabled', false);
							$.tempfile = response.content;
						}
					}
				})
			}
		}
	});

	$('.remove-image').off('click').on('click', function(event) {
		$(this).parents('.input-group').find('input[type="file"]').val('');
		$(this).parents('.input-group').find('.up-text').text('Click to upload a file');
		$(this).addClass('not-shown');

		if($(this).parents('.input-group').find('input[type="file"]').is($('#skill_image'))) {
			$.temp_data.main['filename'] = '';
			$.temp_detail_file = '';
		}
	})







	$('body').delegate('input[name="data[Skill][title]"]', 'keyup focus', function(event){
		$.temp_data.main['name'] = $(this).val();
	});

	$('body').delegate('input[name="data[Skill][image]"]', 'keyup focus', function(event){
		$(this).parents('.docupload-sec').find('.error').text('');
	});

	$('body').delegate('input[name="data[Skill][title]"]', 'blur', function(event){
		$(this).parent().find('.error').text('');
	});

	$('body').delegate('textarea[name="data[Skill][description]"]', 'keyup focus', function(event){
		$.temp_data.main['description'] = $(this).val();
		event.preventDefault();
	});

	$('body').delegate('textarea[name="data[Skill][description]"]', 'blur', function(event){
		$(this).parent().find('.error').text('');
	});

	$('body').delegate('input[name="data[SkillLink][web_link]"]', 'blur', function(event){
		$(this).parent().find('.error').text('');
	});

	$('body').delegate('input[name="data[SkillLink][link_name]"]', 'blur', function(event){
		$(this).parents('.form-group:first').find('.error').text('');
	});

	$('body').delegate('input[name="data[SkillFile][file_name]"]', 'blur', function(event){
		$(this).parent().find('.file_title_err').text('');
	});

	$('body').delegate('input[name="data[SkillFile][upload_file]"]', 'blur', function(event){
		$(this).parents('.form-group:first').find('.error').text('');
	});

	$("#skill_image, #skill_files").change(function() {
	   $(this).parent().find('.clearImage').show();
	});

	$('.up-icon').off('click').on('click', function(event){
		$(this).parents('.docupload-sec:first').find('input[type=file]').trigger('click');
	});


	$('.info-icon').tooltip({
		html: true,
		template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none;"></div></div>',
		container: 'body'
	});

	$('.up-icon').off('click').on('click', function(event){
		$(this).parents('.docupload-sec:first').find('input[type=file]').trigger('click');
	});

})

</script>