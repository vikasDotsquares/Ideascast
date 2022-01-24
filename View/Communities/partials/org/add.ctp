<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<div class="modal-header">
	<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Add Organization</h4>
</div>
<div class="modal-body add-deprt-popup-cont org-popup-cont">
	<ul class="nav nav-tabs" id="add_tabs">
		<li class="active">
			<a data-toggle="tab" data-type="details" class="active tab-detail" data-target="#tab_main" href="#tab_main" aria-expanded="true" >Details</a>
		</li>

		<li class="">
			<a data-toggle="tab" data-type="links" id="linksTab" data-target="#tab_links" href="#tab_links" aria-expanded="false" class="tab-link" >Links</a>
		</li>

		<li class="">
			<a data-toggle="tab" data-type="files" id="filesTab" data-target="#tab_files" href="#tab_files" aria-expanded="false" class="tab-file" >Files</a>
		</li>

		<li class="">
			<a data-toggle="tab" data-type="files" id="compTab" data-target="#tab_comp" href="#tab_comp" aria-expanded="false" class="tab-comp" >Competencies</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade active in add-location-details popup-select-icon"  id="tab_main" >
			<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'detail_add', 'url'=>'manage_files', 'enctype'=>'multipart/form-data' ));
			?>
			<div class="row">
				<!-- LEFT -->
				<div class="col-sm-6">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Name: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Organization.name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Locations: </label>
					  	<div class="form-control-skill">
					  		<?php /* echo $this->Form->input('Organization.location_id', array('type' => 'hidden', 'id' => 'org_location_id')); ?>
					  		<div class="dropdown loc-dd">
							  	<button class="btn btn-primary dropdown-toggle btn-loc" type="button" data-toggle="dropdown" aria-expanded="false">
							  		<span class="dd-text">Select Location</span>
									<span class="caret"></span>
							  	</button>
							  	<ul class="dropdown-menu">
							  		<?php if(isset($loc_list) && !empty($loc_list)){ ?>
							  		<?php foreach ($loc_list as $key => $value) { ?>
							    		<li>
							    			<a href="#" class="loc-anc" data-id="<?php echo $value['locations']['id']; ?>" data-name="<?php echo $value['locations']['name']; ?>">
							    				<span class="loc-name-list">
													<span class="loc-thumb">
														<?php if(!empty($value['locations']['image'])){ ?>
															<img src="<?php echo SITEURL . LOC_IMAGE_PATH . $value['locations']['image']; ?>" >
														<?php } ?>
													</span>
													<div class="loc-info">
														<span data-html="true" class="loc-name" ><?php echo $value['locations']['name']; ?></span>
														<div class="loc-cc-name">
															<span class="sks-city"><?php echo $value['locations']['city']; ?>,</span>
															<span class="sks-country"><?php echo $value['countries']['countryName']; ?></span>
														</div>
													</div>
												</span>
											</a>
							    		</li>
							    	<?php } ?>
							    	<?php }else{ ?>
						            	<li><a href="#">No Location</a></li>
						            <?php } ?>
							  	</ul>
							</div>
							<?php  */ echo $this->Form->input('Organization.locations', array('type' => 'select', 'options' => $locations, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'org_locations', 'multiple' => 'multiple' )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>


				</div>
				<!-- RIGHT -->
				<div class="col-sm-6">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Type: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Organization.type_id', array('type' => 'select', 'options' => $org_types, 'label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'Select Type' )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group loc-img-info">
					  	<label for="UserUser" class="control-label">Image: <i class="info-icon  tipText" title="" data-original-title="150px by 150px Recommended"></i></label>
					  	<div class="ski-close">
						  	<div class="input-group docupload-sec">
                                <div class="input-group-addon up-icon">
                                    <i class="uploadblackicon"></i>
                                </div>
                                <span title="" class="docUpload icon_btn">
									<input type="file" name="data[Organization][image]" class="form-control upload" id="main_image" placeholder="Upload File" accept=".png, .jpg, .jpeg">
									<span class="up-text"  style="">Click to upload a file</span>
                                </span>
                                <div class="input-group-addon remove-image not-shown tipText" title="Clear Image">
                                    <i class="clearblackicon"></i>
                                   <i class="fa fa-spinner fa-spin file-uploading"></i>
                                </div>
								<label class="error text-red"></label>
                            </div>
					  	</div>

					</div>

				</div>
				<div class="col-sm-12">
					<div class="form-group information-filed">
					  	<label for="UserUser" class="control-label">Information: </label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Organization.information', array('type' => 'textarea', 'rows'=> 5, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control information', 'maxlength'=>4096 )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
						  	<label for="UserUser" class="control-label col-sm-3 emaildomains-text">Email Domains: <sup class="text-danger">*</sup></label>
						  	<div class="col-sm-9">
								<div class="form-control-skill">
								<?php echo $this->Form->input('Organization.edomains', array('type' => 'select', 'options' => $email_domails, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'org_edomains', 'multiple' )); ?>
								<label class="error text-red"></label>
								</div>
					  		</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
		<div class="tab-pane fade"  id="tab_links">
			<div class="row">
				<div class="form-group">
				  <label for="UserUser" class="col-lg-2 control-label">Web Link: <sup class="text-danger">*</sup></label>
				  <div class="col-lg-10">
					  <div class="form-control-skill">
					<?php echo $this->Form->input('Organization.web_link', array('type' => 'url', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'placeholder'=> '', 'autocomplete'=>'off', 'title'=>'', 'maxlength'=>255 )); ?>
					<label class="error  text-red"></label>
				  </div>
					</div>
				</div>
				<div class="form-group">
				  <label for="UserUser" class="col-lg-2 control-label">Title: <sup class="text-danger">*</sup></label>
					<div class="col-lg-10">
						<div class="form-control-skill">
					  <div class="title-group-w">
						<?php echo $this->Form->input('Organization.link_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'maxlength'=>50, 'autocomplete'=>'off' )); ?>
						<button type="button" class="btn outline-btn-s save_links">Add</button>
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
		</div>
		<div class="tab-pane fade"  id="tab_files" >
			<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'files_add', 'url'=>'manage_files', 'enctype'=>'multipart/form-data' ));
			?>

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
									<input type="file" name="data[OrganizationFile][upload_file]" class="form-control upload" id="file_upload" placeholder="Upload File">
									<span class="up-text" >Click to upload a file</span>
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
										<?php echo $this->Form->input('OrganizationFile.file_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>

										<button type="button" class="btn outline-btn-s save_files">Add</button>
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
		<div class="tab-pane fade"  id="tab_comp" >
			<div class="row popup-select-icon">
				<div class="lo-add-competencies">
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Skills:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Organization.skill', array('type' => 'select', 'options' => $skill_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'org_skills', 'multiple' => 'multiple' )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Subjects:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Organization.subject', array('type' => 'select', 'options' => $subject_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'org_subjects', 'multiple' => 'multiple' )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Domains:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Organization.domain', array('type' => 'select', 'options' => $kd_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'org_domains', 'multiple' => 'multiple' )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
				</div></div>
		</div>
	</div>

</div>
<div class="modal-footer clearfix">
	<button type="button" class="btn btn-primary submit_data">Add</button>
	<button type="button" id="discard" class="btn outline-btn-s cancel-add" data-dismiss="modal">Cancel</button>
</div>

<style>
	.remove-image {
		cursor: pointer;
	}
	.remove-image.not-shown {
		display: none;
	}
	.file-uploading, .remove-image {
		display: none;
	}
	.loc-dd ul {
		max-height: 308px;
		overflow: auto;
		width: 100%;
	}
	.btn-loc, .btn-loc:hover, .btn-loc:active, .btn-loc:focus, .open>.dropdown-toggle.btn-primary.btn-loc {
		width: 100%;
	    background: no-repeat;
	    color: #333;
	    border-color: #ddd;
	    border-radius: 0;
	    display: flex;
	    justify-content: space-between;
	    align-items: center;
	}
	.btn-loc .dd-text {
		flex-grow: 1;
	    display: inline-block;
	    align-items: center;
	    text-overflow: ellipsis;
	    max-width: 100%;
	    overflow: hidden;
	    white-space: nowrap;
	    text-align: left;
	}
	.open ul.dropdown-menu>li a.loc-anc {
	    padding: 5px 10px;
    	line-height: 1.35;
	}
	.open ul.dropdown-menu>li a.loc-anc:hover {
	    background-color: #f6f6f6;
	    color: #333;
	    background-image: none !important;
	}
</style>

<script>
$(function(){
	$('input[name="data[Organization][name]"]').focus();
	$(".loc-anc").off('click').on('click', function(e) {
		e.preventDefault();
		$(this).parents('.loc-dd').find('.dd-text').text($(this).data('name'))
		$('#org_location_id').val($(this).data('id'))
	});

	$('input[name="data[Location][name]"]').focus();

	$("form#detail_add, form#files_add").submit(function(){
		return false;
	})
	$('.up-icon').off('click').on('click', function(event){
		$(this).parents('.docupload-sec:first').find('input[type=file]').trigger('click');
	});


	$.temp_detail_file = '';
	$.tempfile = '';
	$.temp_data = {
		main: {
			name: '',
			type: '',
			locations: '',
			filename: '',
			info: '',
			edomains: ''
		},
		link: {},
		file: {},
		competency: {
			skills: '',
			subjects: '',
			domains: '',
		}
	};

	$org_locations = $('#org_locations').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Locations',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Locations',
            onSelectAll:function(){
            	var selected = $('#org_locations').val();
                $.temp_data.main.locations = selected;
			},
			onDeselectAll:function(){
                $.temp_data.main.locations = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#org_locations').val();
                $.temp_data.main.locations = selected;
            }
        });

	$org_edomains = $('#org_edomains').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Email Domains',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Email Domains',
            onSelectAll:function(){
            	var selected = $('#org_edomains').val();
                $.temp_data.main.edomains = selected;
			},
			onDeselectAll:function(){
                $.temp_data.main.edomains = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#org_edomains').val();
                $.temp_data.main.edomains = selected;
            }
        });

	$org_skills = $('#org_skills').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Skills',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Skills',
            onSelectAll:function(){
            	var selected = $('#org_skills').val();
                $.temp_data.competency.skills = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.skills = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#org_skills').val();
                $.temp_data.competency.skills = selected;
            }
        });

	$org_subjects = $('#org_subjects').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Subjects',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Subjects',
            onSelectAll:function(){
            	var selected = $('#org_subjects').val();
                $.temp_data.competency.subjects = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.subjects = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#org_subjects').val();
                $.temp_data.competency.subjects = selected;
            }
        });

	$org_domains = $('#org_domains').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Domains',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Domains',
            onSelectAll:function(){
            	var selected = $('#org_domains').val();
                $.temp_data.competency.domains = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.domains = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#org_domains').val();
                $.temp_data.competency.domains = selected;
            }
        });

	$.link_counter = 0;
	$('.save_links').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('');
		var error = false;
		if($.trim($("[name='data[Organization][web_link]']").val()) == ''){
			$("[name='data[Organization][web_link]']").parent().find('.error').text('Link is required')
			error = true;
		}
		if( $.trim($("[name='data[Organization][web_link]']").val()) != '' && $.isValidUrl($.trim($("[name='data[Organization][web_link]']").val())) == -1 ){
			$("[name='data[Organization][web_link]']").parent().find('.error').text('Missing a protocol, hostname or filename')
			error = true;
		}
		if($.trim($("[name='data[Organization][link_name]']").val()) == ''){
			$("[name='data[Organization][link_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}
		var wlink = $("[name='data[Organization][web_link]']").val();
		var input_link = wlink;
		if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
			input_link = 'http://'+wlink;
		}
		$.temp_data['link'][$.link_counter] = {
			title: $("[name='data[Organization][link_name]").val(),
			url: input_link
		}
		$.link_counter++;
		$("[name='data[Organization][link_name]").val('');
		$("[name='data[Organization][web_link]']").val('');

		var $ul = $('.popup-skill-links ul');
		$ul.empty();
		$.each($.temp_data.link, function(index, el) {

			var nwlink = el.url;

			var $li = $('<li>');
			var $span1 = $('<span class="list-text">').text(el.title).appendTo($li);
			var $span2 = $('<span class="list-icon">').appendTo($li);
			var $anc_link = $('<a href="'+nwlink+'" target="_blank" class="tipText open-link" title="Open Link">').appendTo($li);
			var $anc_link_icon = $('<i class="openlinkicon"></i>').appendTo($anc_link);
			var $anc_del = $('<a href="#" class="tipText" title="Remove Link" data-id="'+index+'" >').appendTo($li);
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


	$("#file_upload").change(function() {
		$("[name='data[OrganizationFile][upload_file]']").parents('.form-group').find('.error').text('');

		if(this.files[0] !== undefined){
			var file = this.files[0];
			var fnme = file.name
			var fnme = fnme.substr(0, fnme.lastIndexOf('.')) || fnme;
			var regex = /^[A-Za-z0-9\s-_()]+$/
			if(regex.test(fnme) == false) {
				$("[name='data[OrganizationFile][upload_file]']").parents('.form-group').find('.error').text('Image filename contains unsupported characters');
				$("[name='data[OrganizationFile][upload_file]']").val('');
				return;
			}

			$(this).parents("#files_add").find(".save_files").prop('disabled', true);
			var $parent = $(this).parents('#files_add');
			$.file_uploading(false, $parent);
	        var name = file.name,
	            size = file.size,
	            type = file.type,
	            $upText = $(this).parent().find('.up-text');
	        var filesize = file.size / 1048576;
			// name = newStr = name.replace(/-/g, "");
			$upText.html(name);

			var $form = $("#files_add");
			var formData = new FormData($form[0]),
				$fileInput = $(this),
				file = $fileInput[0].files[0];

			if ($fileInput.val() !== "" && file !== undefined) {
				var name = file.name.replace(/-/g, ''),
					size = file.size,
					type = file.type;
				formData.append('file_name', $fileInput[0].files[0], $fileInput[0].files[0]['name'].replace(/-/g, ''));

				$.ajax({
					url: $.module_url + 'temp_save_file',
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
							$form.find(".save_files").prop('disabled', false);
							$.tempfile = response.content
						}
					}
				})
			}
		}
	})

	$.file_counter = 0;
	$('.save_files').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('')
		var error = false;
		if($.trim($("[name='data[OrganizationFile][upload_file]']").val()) == ''){
			$("[name='data[OrganizationFile][upload_file]']").parents('.form-group').find('.error').text('File Name is required')
			error = true;
		}
		if($.trim($("[name='data[OrganizationFile][file_name]']").val()) == ''){
			$("[name='data[OrganizationFile][file_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		$.temp_data['file'][$.file_counter] = {
			title: $("[name='data[OrganizationFile][file_name]']").val(),
			filename: $.tempfile
		}
		$.file_counter++;

		$("[name='data[OrganizationFile][file_name]']").val('');
		$("#file_upload").val('');
		$("#file_upload").parent().find('.up-text').text("Click to upload a file");


		var $ul = $('.popup-skill-files ul');
		$ul.empty();
		$.each($.temp_data.file, function(index, el) {
			var $li = $('<li>');
			var $span1 = $('<span class="list-text">').text(el.title).appendTo($li);
			var $span2 = $('<span class="list-icon">').appendTo($li);
			var $anc_link = $('<a href="' + $.module_url + 'download_temp_files/'+el.filename+'" class="tipText" title="Download File" download>').appendTo($li);
			var $anc_link_icon = $('<i class="downloadblackicon"></i>').appendTo($anc_link);
			var $anc_del = $('<a href="#" class="tipText" title="Remove Now" data-id="'+index+'" data-file="'+el.filename+'">').appendTo($li);
			var $anc_del_icon = $('<i class="clearblackicon"></i>').appendTo($anc_del);
			$anc_del.on('click', function(event) {
				event.preventDefault();
				var key = $(this).data('id');
				var filename = $(this).data('file');
				var $parent = $(this).parents('li:first');

				$.ajax({
					url: $.module_url + 'temp_remove_file',
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

	$("#main_image").change(function() {
		$(this).parents('.ski-close:first').find(".error").text('');

		if(this.files[0] !== undefined){
			var file = this.files[0];
			var fnme = file.name
			var fnme = fnme.substr(0, fnme.lastIndexOf('.')) || fnme;
			var regex = /^[A-Za-z0-9\s-_()]+$/
			if(regex.test(fnme) == false) {
				$(this).parents('.ski-close:first').find(".error").text('Image filename contains unsupported characters');
				$("[name='data[Organization][image]']").val('');
				return;
			}

	        var name = file.name,
	            size = file.size,
	            type = file.type,
	            $upText = $(this).parent().find('.up-text');
	        var filesize = file.size / 1048576;
			// name = newStr = name.replace(/-/g, "");
			$upText.html(name);

			var $form = $("#detail_add");
			var formData = new FormData($form[0]),
				$fileInput = $(this),
				file = $fileInput[0].files[0];

			$form.find(".submit_data").prop('disabled', true);
			var $parent = $(this).parents('.ski-close:first');
			$.file_uploading(false, $parent);

			var filePath = file.name;
			var file_ext = filePath.substr(filePath.lastIndexOf('.')+1,filePath.length);

			if( $fileInput.val() !== "" && file !== undefined && $.inArray(file_ext, ['jpg','jpeg','png']) == -1  ){
				$form.find(".remove-image").trigger('click');
				return;
			}

			if ($fileInput.val() !== "" && file !== undefined) {
				var name = file.name.replace(/-/g, ''),
					size = file.size,
					type = file.type;
				formData.append('file_name', $fileInput[0].files[0], $fileInput[0].files[0]['name'].replace(/-/g, ''));

				$.ajax({
					url: $.module_url + 'temp_save_file',
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
							$form.find(".submit_data").prop('disabled', false);
							$.temp_detail_file = response.content
							$.temp_data.main['filename'] = $.temp_detail_file;
						}
					}
				})
			}
		}
	})

	$('.submit_data').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('');
		var error = false;

		if($.trim($("#OrganizationName").val()) == ''){
			$("#OrganizationName").parent().find('.error').text('Name is required');
			error = true;
		}
		if( $("#OrganizationTypeId").val() == ''){
			$("#OrganizationTypeId").parent().find('.error').text('Type is required');
			error = true;
		}
		if( $("#org_edomains").val() == null){
			$("#org_edomains").parent().find('.error').text('Email Domain is required');
			error = true;
		}

		if(error){
			$('#add_tabs a[href="#tab_main"]').tab('show');
			return;
		}

		$.temp_data.main.name = $("#OrganizationName").val();
		$.temp_data.main.type = $("#OrganizationTypeId").val();
		$.temp_data.main.filename = $.temp_detail_file;
		$.temp_data.main.info = $("#OrganizationInformation").val();


		// console.log($.temp_data)
		// return;
		$(this).prop('disabled', true);

		$.ajax({
			url: $.module_url + 'save_org_data',
			type: 'POST',
			dataType: 'json',
			data: $.temp_data,
			context: this,
			success: function(response){

				if(response.success){
					$(this).prop('disabled', false);
					$.popup_updates.action = true;
					$.popup_updates.section = 'add';
					$.popup_updates.reaction = 'org';
					// $.get_organizations();
					$('#modal_create').modal('hide');
				}
			}
		})
	})

	function sort_arr_obj (a, b){
	  	var aName = a.name.toLowerCase();
	  	var bName = b.name.toLowerCase();
	  	return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
	}

	$('#country_id').off('change').on('change', function(event) {
		event.preventDefault();
		var val = $(this).val();
		var code = $('option:selected', $(this)).data('code');
		if(val == ''){
			$('#state_id').empty().append('<option value="">Select State</option>');
			return;
		}

		$.ajax({
			url: $.module_url + 'get_states',
			type: 'POST',
			dataType: 'json',
			data: {code: code},
			context: this,
			success: function(response){
				if(response.success){
					$('#state_id').empty();
					if(response.content){
						var content = response.content.sort(sort_arr_obj);
						$('#state_id').append('<option value="">Select State</option>');
						$('#state_id').append(function() {
                            var output = '';
                            $.each(content, function(key, value) {
                                output += '<option value="' + value.id + '">' + value.name + '</option>';
                            });
                            return output;
                        });
					}
				}
			}
		})
	})

	$('.remove-image').off('click').on('click', function(event) {
		$(this).parents('.input-group').find('input[type="file"]').val('');
		$(this).parents('.input-group').find('.up-text').text('Click to upload a file');
		$(this).addClass('not-shown');
		if($(this).parents('.input-group').find('input[type="file"]').is($('#main_image'))) {
			$.temp_data.main['filename'] = '';
			$.temp_detail_file = '';
		}
	})

	$('.info-icon').tooltip({
		html: true,
		template: '<div class="tooltip" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>',
		container: 'body',
		placement: "top"
	});
})

</script>