<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

$db_data = $loc_data[0]['locations'];
$loctitle = html_entity_decode(html_entity_decode($db_data['name'] ,ENT_QUOTES, "UTF-8"));
$locinfo = html_entity_decode(html_entity_decode($db_data['information'] ,ENT_QUOTES, "UTF-8"));
$locadd = html_entity_decode(html_entity_decode($db_data['address'] ,ENT_QUOTES, "UTF-8"));
$loc_city = html_entity_decode(html_entity_decode($db_data['city'] ,ENT_QUOTES, "UTF-8"));

$cskills = $csubjects = $cdomains = [];
if(!empty($loc_data[0][0]['skid'])){
	$skid = json_decode($loc_data[0][0]['skid'], true);
	$cskills = Set::extract($skid, '{n}.id');
}
if(!empty($loc_data[0][0]['sbid'])){
	$skid = json_decode($loc_data[0][0]['sbid'], true);
	$csubjects = Set::extract($skid, '{n}.id');
}
if(!empty($loc_data[0][0]['dmid'])){
	$skid = json_decode($loc_data[0][0]['dmid'], true);
	// pr($skid, 1);
	$cdomains = Set::extract($skid, '{n}.id');
}
?>
<div class="modal-header">
	<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Edit Location</h4>
</div>
<div class="modal-body add-deprt-popup-cont">
	<ul class="nav nav-tabs" id="add_tabs">
		<li class="active">
			<a data-toggle="tab" data-type="details" class="active skilltab tab-detail" data-target="#tab_main" href="#tab_main" aria-expanded="true" >Details</a>
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
		<div class="tab-pane fade active in add-location-details"  id="tab_main" >
			<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'detail_add', 'url'=>'manage_files', 'enctype'=>'multipart/form-data' ));
			?>
			<input type="hidden" name="data[Location][id]" id="location_id" value="<?php echo $this->request->data['Location']['id']; ?>" >
			<div class="row">
				<!-- LEFT -->
				<div class="col-sm-6">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Name: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Location.name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off', 'value' => $loctitle)); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Information: </label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Location.information', array('type' => 'textarea', 'rows'=> 5, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control information', 'value' => $locinfo, 'maxlength'=>4096 )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group loc-img-info">
					  	<label for="UserUser" class="control-label">Image:  <i class="info-icon" title="" data-original-title="150px by 150px Recommended"></i></label>
					  	<div class="ski-close">
						  	<div class="input-group docupload-sec">
                                <div class="input-group-addon up-icon">
                                    <i class="uploadblackicon"></i>
                                </div>
                                <span title="" class="docUpload icon_btn">
									<input type="file" name="data[Location][image]" class="form-control upload" id="main_image" placeholder="Upload File" accept=".png, .jpg, .jpeg">
									<span class="up-text"  style="">Click to upload a file</span>
                                </span>
                                <div class="input-group-addon remove-image not-shown tipText" title="Clear Image">
                                    <i class="clearblackicon"></i>
                                   <i class="fa fa-spinner fa-spin file-uploading"></i>
                                </div>
                            </div>
							<div class="two-msg-show">
							<?php if(!empty($db_data['image'])){ ?>
					  	<div class="show_images">
						    <span class="main_image"><?php echo $db_data['image']; ?></span>
						    <i data-type="loc" data-id="<?php echo $db_data['id']; ?>" class="clearredicon remove_image tipText" title="Remove Now"></i>
						</div>
						<?php } ?>
						<div class="error text-red"></div>
						</div>

					  	</div>

					</div>
				</div>
				<!-- RIGHT -->
				<div class="col-sm-6">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Type: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Location.type_id', array('type' => 'select', 'options' => $loc_types, 'label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'Select Type' )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Country: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
					  		<?php if(isset($countries) && !empty($countries)){ ?>
					  			<select name="data[Location][country_id]" id="country_id" class="form-control">
					  				<option value="">Select Country</option>
					  			<?php foreach ($countries as $key => $value) {
					  				$sel = '';
					  				if($value['Country']['id'] == $db_data['country_id']){
					  					$sel = ' selected="selected"';
					  				}
					  			?>
					  				<option <?php echo $sel; ?> value="<?php echo $value['Country']['id']; ?>" data-code="<?php echo $value['Country']['countryCode']; ?>"><?php echo $value['Country']['countryName']; ?></option>
					  			<?php
					  			} ?>
					  			</select>
					  		<?php } ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Address: </label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Location.address', array('type' => 'textarea', 'rows'=> 5, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control', 'value' => $locadd )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group">
					  	<label for="UserUser" class="control-label">City/Town: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Location.city', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off', 'value' => $loc_city)); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group">
					  	<label for="UserUser" class="control-label">State/County: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Location.state_id', array('type' => 'select', 'options' => [], 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'state_id', 'empty' => 'Select State')); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Zip/Postcode: </label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Location.zip', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
				</div>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
		<div class="tab-pane fade"  id="tab_links" >
			<div class="row">
				<div class="form-group">
				  <label for="UserUser" class="col-lg-2 control-label">Web Link: <sup class="text-danger">*</sup></label>
				  <div class="col-lg-10">
					  <div class="form-control-skill">
					<?php echo $this->Form->input('Location.web_link', array('type' => 'url', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'placeholder'=> '', 'autocomplete'=>'off', 'title'=>'', 'maxlength'=>255 )); ?>
					<label class="error  text-red"></label>
				  </div>
					</div>
				</div>
				<div class="form-group">
				  <label for="UserUser" class="col-lg-2 control-label">Title: <sup class="text-danger">*</sup></label>
					<div class="col-lg-10">
						<div class="form-control-skill">
					  <div class="title-group-w">
						<?php echo $this->Form->input('Location.link_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'maxlength'=>50, 'autocomplete'=>'off' )); ?>
						<button type="button" class="btn outline-btn-s save_links">Add</button>
					  </div>
					  <label class="error  text-red"></label>
					</div>
					</div>
				</div>
				<?php $links = $this->Permission->get_links('loc', $db_data['id']); ?>
				<div class="col-lg-12">
					<div class="popup-skill-list popup-skill-links">
						<ul>
							<?php if(isset($links) && !empty($links)) { ?>
							<?php foreach ($links as $key => $value) {
									$link = $value['up']; ?>
							<li class="pre-added">
								<span class="list-text"><?php echo htmlentities($link['title']) ; ?></span>
								<span class="list-icon">
									<a href="<?php echo $link['link']; ?>" target="_blank" class="tipText open-link" title="Open Link">
										<i class="openlinkicon"></i>
									</a>
									<a href="#" class="remove_link tipText" data-id="<?php echo $link['id']; ?>" data-type="loc" title="Remove Link">
										<i class="clearblackicon"></i>
									</a>
								</span>
							</li>
							<?php } ?>
							<?php } ?>
						</ul>
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
									<input type="file" name="data[LocationFile][upload_file]" class="form-control upload" id="file_upload" placeholder="Upload File">
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
										<?php echo $this->Form->input('LocationFile.file_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>

										<button type="button" class="btn outline-btn-s save_files">Add</button>
								  	</div>
								  	<label class="error  text-red" style="display: table;"></label>
						  		</div>
							</div>
						</div>
						<?php $files = $this->Permission->get_files('loc', $db_data['id']); ?>
					<div class="col-lg-12">
						<div class="popup-skill-list popup-skill-files">
							<ul>
								<?php if(isset($files) && !empty($files)) { ?>
								<?php foreach ($files as $key => $value) {
										$file = $value['up']; ?>
								<li class="pre-added">
									<span class="list-text"><?php echo htmlentities($file['title']); ?> </span>
									<span class="list-icon">
										<a href="<?php echo Router::url(array('controller' => 'communities', 'action' => 'download_files', 'loc', $file['id'], 'admin' => false)); ?>" data-type="loc" class="tipText" title="Download File" download ><i class="downloadblackicon"></i></a>
										<a href="#" class="remove_file tipText" title="Remove Now" data-id="<?php echo $file['id']; ?>" data-type="loc">
											<i class="clearblackicon"></i>
										</a>
									</span>
								</li>
								<?php } ?>
								<?php } ?>
							</ul>
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
							<?php echo $this->Form->input('Location.skill', array('type' => 'select', 'options' => $skill_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'location_skills', 'multiple' => 'multiple', 'default' => $cskills )); ?>
						</div>
						<label class="error text-red"></label>
					</div>
					<div class="form-group" >
						<label for="UserUser" class="col-sm-2 control-label">Subjects:</label>
						<div class="col-sm-10">
							<?php echo $this->Form->input('Location.subject', array('type' => 'select', 'options' => $subject_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'location_subjects', 'multiple' => 'multiple', 'default' => $csubjects )); ?>
						</div>
						<label class="error text-red"></label>
					</div>
					<div class="form-group" >
						<label for="UserUser" class="col-sm-2 control-label">Domains:</label>
						<div class="col-sm-10">
							<?php echo $this->Form->input('Location.domain', array('type' => 'select', 'options' => $kd_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'location_domains', 'multiple' => 'multiple', 'default' => $cdomains )); ?>
						</div>
						<label class="error text-red"></label>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer clearfix">
	<button type="button" class="btn btn-primary submit_data">Save</button>
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
</style>
<script>
$(function(){

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
			id: '<?php echo $db_data['id']; ?>',
			name: '<?php echo addslashes($db_data['name']); ?>',
			info: $('[name="data[Location][information]"]').val() || '',
			filename: '',
			type: '<?php echo $db_data['type_id']; ?>',
			country: '<?php echo $db_data['country_id']; ?>',
			address: $('[name="data[Location][address]"]').val() || '',
			city: '<?php echo addslashes($db_data['city']); ?>',
			state: '<?php echo $db_data['state_id']; ?>',
			zip: $('[name="data[Location][zip]"]').val() || '',
		},
		link: {},
		file: {},
		competency: {
			skills: $('#location_skills').val(),
			subjects: $('#location_subjects').val(),
			domains: $('#location_domains').val(),
		}
	};

	$location_skills = $('#location_skills').multiselect({
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
            	var selected = $('#location_skills').val();
                $.temp_data.competency.skills = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.skills = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#location_skills').val();
                $.temp_data.competency.skills = selected;
            }
        });

	$location_subjects = $('#location_subjects').multiselect({
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
            	var selected = $('#location_subjects').val();
                $.temp_data.competency.subjects = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.subjects = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#location_subjects').val();
                $.temp_data.competency.subjects = selected;
            }
        });

	$location_domains = $('#location_domains').multiselect({
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
            	var selected = $('#location_domains').val();
                $.temp_data.competency.domains = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.domains = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#location_domains').val();
                $.temp_data.competency.domains = selected;
            }
        });

	$.link_counter = 0;
	$('.save_links').off('click').on('click', function(event) {
		event.preventDefault();
		$('.error').text('');
		var error = false;
		if($.trim($("[name='data[Location][web_link]']").val()) == ''){
			$("[name='data[Location][web_link]']").parent().find('.error').text('Link is required')
			error = true;
		}
		if( $.trim($("[name='data[Location][web_link]']").val()) != '' && $.isValidUrl($.trim($("[name='data[Location][web_link]']").val())) == -1 ){
			$("[name='data[Location][web_link]']").parent().find('.error').text('Missing a protocol, hostname or filename')
			error = true;
		}
		if($.trim($("[name='data[Location][link_name]']").val()) == ''){
			$("[name='data[Location][link_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		var wlink = $("[name='data[Location][web_link]']").val();
		var input_link = wlink;
		if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
				input_link = 'http://'+wlink;
		}
		$.temp_data['link'][$.link_counter] = {
			title: $("[name='data[Location][link_name]").val(),
			url: input_link
		}
		$.link_counter++;
		$("[name='data[Location][link_name]").val('');
		$("[name='data[Location][web_link]']").val('');

		var $ul = $('.popup-skill-links ul');
		$('li.post-added', $ul).remove();
		$.each($.temp_data.link, function(index, el) {

			var nwlink = el.url;

			var $li = $('<li class="post-added">');
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

		$("[name='data[LocationFile][upload_file]']").parents('.form-group').find('.error').text('');

		if(this.files[0] !== undefined){
			var file = this.files[0];
			var fnme = file.name
			var fnme = fnme.substr(0, fnme.lastIndexOf('.')) || fnme;
			var regex = /^[A-Za-z0-9\s-_()]+$/
			if(regex.test(fnme) == false) {
				$("[name='data[LocationFile][upload_file]']").parents('.form-group').find('.error').text('Image filename contains unsupported characters');
				$("[name='data[LocationFile][upload_file]']").val('');
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
		if($.trim($("[name='data[LocationFile][upload_file]']").val()) == ''){
			$("[name='data[LocationFile][upload_file]']").parents('.form-group').find('.error').text('File Name is required')
			error = true;
		}
		if($.trim($("[name='data[LocationFile][file_name]']").val()) == ''){
			$("[name='data[LocationFile][file_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		$.temp_data['file'][$.file_counter] = {
			title: $("[name='data[LocationFile][file_name]']").val(),
			filename: $.tempfile
		}
		$.file_counter++;

		$("[name='data[LocationFile][file_name]']").val('');
		$("#file_upload").val('');
		$("#file_upload").parent().find('.up-text').text("Click to upload a file");


		var $ul = $('.popup-skill-files ul');
		$('li.post-added', $ul).remove();
		$.each($.temp_data.file, function(index, el) {
			var $li = $('<li class="post-added">');
			var $span1 = $('<span class="list-text">').text(el.title).appendTo($li);
			var $span2 = $('<span class="list-icon">').appendTo($li);
			var $anc_link = $('<a href="' + $.module_url + 'download_temp_files/'+el.filename+'"  class="tipText" title="Download File" download>').appendTo($li);
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
				$(this).parents('.ski-close:first').find('.error').text('Image filename contains unsupported characters');
				$("[name='data[Location][image]']").val('');
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

		if($.trim($("#LocationName").val()) == ''){
			$("#LocationName").parent().find('.error').text('Name is required');
			error = true;
		}
		if($.trim($("#LocationTypeId").val()) == ''){
			$("#LocationTypeId").parent().find('.error').text('Type is required');
			error = true;
		}
		if($.trim($("#country_id").val()) == ''){
			$("#country_id").parent().find('.error').text('Country is required');
			error = true;
		}
		if($.trim($("#LocationCity").val()) == ''){
			$("#LocationCity").parent().find('.error').text('City/Town is required');
			error = true;
		}
		if($.trim($("#state_id").val()) == ''){
			$("#state_id").parent().find('.error').text('State is required');
			error = true;
		}
		/* if($.trim($("#LocationZip").val()) == ''){
			$("#LocationZip").parent().find('.error').text('Zip/Postcode is required');
			error = true;
		} */

		if(error){
			$('#add_tabs a[href="#tab_main"]').tab('show');
			return;
		}

		var data = {'name': $("#LocationName").val(), 'type': 'dept'};
		$.temp_data.main.name = $("#LocationName").val();
		$.temp_data.main.info = $("#LocationInformation").val();
		$.temp_data.main.filename = $.temp_detail_file;
		$.temp_data.main.type = $("#LocationTypeId").val();
		$.temp_data.main.country = $("#country_id").val();
		$.temp_data.main.address = $("#LocationAddress").val();
		$.temp_data.main.city = $("#LocationCity").val();
		$.temp_data.main.state = $("#state_id").val();
		$.temp_data.main.zip = $("#LocationZip").val();

		// return;
		$(this).prop('disabled', true);

		$.ajax({
			url: $.module_url + 'save_location_data',
			type: 'POST',
			dataType: 'json',
			data: $.temp_data,
			context: this,
			success: function(response){

				if(response.success){
					$(this).prop('disabled', false);
					$.popup_updates.action = true;
					$.popup_updates.section = 'edit';
					$.popup_updates.reaction = 'loc';
					// $.get_locations();
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
                            	var sel = '';
                            	if($.temp_data.main.state == value.id){
                            		sel = ' selected ';
                            	}
                                output += '<option value="' + value.id + '" '+sel+'>' + value.name + '</option>';
                            });
                            return output;
                        });
					}
				}
			}
		})
	})
	$('#country_id').trigger('change')


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