<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
$db_data = $org_data[0]['organizations'];
$orgName = html_entity_decode(html_entity_decode($db_data['name'] ,ENT_QUOTES, "UTF-8"));
$orgInfo = html_entity_decode(html_entity_decode($db_data['information'] ,ENT_QUOTES, "UTF-8"));
// pr($org_data);
$cskills = $csubjects = $cdomains = $org_locs = $org_edms = [];
if(!empty($org_data[0][0]['skid'])){
	$skid = json_decode($org_data[0][0]['skid'], true);
	$cskills = Set::extract($skid, '{n}.id');
}
if(!empty($org_data[0][0]['sbid'])){
	$skid = json_decode($org_data[0][0]['sbid'], true);
	$csubjects = Set::extract($skid, '{n}.id');
}
if(!empty($org_data[0][0]['dmid'])){
	$skid = json_decode($org_data[0][0]['dmid'], true);
	// pr($skid, 1);
	$cdomains = Set::extract($skid, '{n}.id');
}
if(!empty($org_data[0][0]['olid'])){
	$skid = json_decode($org_data[0][0]['olid'], true);
	$org_locs = Set::extract($skid, '{n}.id');
}
if(!empty($org_data[0][0]['oedid'])){
	$skid = json_decode($org_data[0][0]['oedid'], true);
	$org_edms = Set::extract($skid, '{n}.id');
}

// pr($locations);
//

// $org_locs = (isset($org_data[0][0]['olid']) && !empty($org_data[0][0]['olid'])) ? explode(',', $org_data[0][0]['olid']) : [];
// $org_edms = (isset($org_data[0][0]['oedid']) && !empty($org_data[0][0]['oedid'])) ? explode(',', $org_data[0][0]['oedid']) : [];
// pr($org_edms, 1);
?>
<div class="modal-header">
	<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Edit Organization</h4>
</div>
<div class="modal-body add-deprt-popup-cont org-popup-cont">

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
		<div class="tab-pane fade active in add-location-details popup-select-icon"  id="tab_main" >
			<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'detail_add', 'url'=>'manage_files', 'enctype'=>'multipart/form-data' ));
			?>
			<input type="hidden" name="data[Organization][id]" id="location_id" value="<?php echo $db_data['id']; ?>" >
			<div class="row">
				<!-- LEFT -->
				<div class="col-sm-6">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Name: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Organization.name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off', 'value' => $orgName)); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Locations: </label>
					  	<div class="form-control-skill">
					  		<?php
					  		$used_loc = 0;
					  		$used_loc_array = [];
					  		if(isset($org_locs) && !empty($org_locs)){
					  			$used_loc = $this->Permission->org_location_used($org_locs);
					  			$used_loc = $used_loc[0][0]['used_user'];
					  			$user_location = $this->Permission->user_location_used($db_data['id'], $org_locs);
					  			if(isset($user_location) && !empty($user_location)){
					  				foreach ($user_location as $key => $value) {
					  					$used_loc_array[$value['ol']['id']] = $value[0]['used_user'];
					  				}
					  			}
					  		}
					  		// pr($used_loc_array);
					  		?>
					  		<input type="hidden" name="used_loc" id="used_loc" value="<?php echo $used_loc; ?>" >
					  		<select name="data[Organization][locations][]" class="form-control" id="org_locations" multiple="">
						  		<?php if(isset($locations) && !empty($locations)){ ?>
						  		<?php foreach ($locations as $key => $value) { ?>
						  		<?php $total = 0;
						  		if(array_key_exists($key, $used_loc_array) && in_array($key, $org_locs)){
						  			$total = $used_loc_array[$key];
						  		} ?>
						  		<?php $selected = '';
						  		if(in_array($key, $org_locs)){
						  			$selected = "selected='selected'";
						  		} ?>
					  				<option value="<?php echo $key; ?>" <?php echo $selected; ?> data-total="<?php echo $total; ?>"><?php echo html_entity_decode(html_entity_decode($value, ENT_QUOTES, "UTF-8")); ?></option>
						  		<?php } ?>
						  		<?php } ?>
					  		</select>
							<?php //echo $this->Form->input('Organization.locations', array('type' => 'select', 'options' => $locations, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'org_locations', 'multiple' => 'multiple', 'default' => $org_locs )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
				</div>
				<!-- RIGHT -->
				<div class="col-sm-6">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Type: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Organization.type_id', array('type' => 'select', 'options' => $org_types, 'label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'Select Type', 'default' => $db_data['type_id'] )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					<div class="form-group loc-img-info">
					  	<label for="UserUser" class="control-label">Image:  <i class="info-icon  tipText" title="" data-original-title="150px by 150px Recommended"></i></label>
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

								<div class="two-msg-show">
									<?php if(!empty($db_data['image'])){ ?>
									  	<div class="show_images">
										    <span class="main_image"><?php echo $db_data['image']; ?></span>
										    <i data-type="org" data-id="<?php echo $db_data['id']; ?>" class="clearredicon remove_image tipText" title="Remove Now"></i>
										</div>
									<?php } ?>
									<div class="error text-red"></div>
								</div>
                            </div>
					  	</div>

					</div>

				</div>
				<div class="col-sm-12">
					<div class="form-group information-filed">
					  	<label for="UserUser" class="control-label">Information: </label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Organization.information', array('type' => 'textarea', 'rows'=> 5, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control information', 'value' => $orgInfo, 'maxlength'=>4096 )); ?>
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
								<?php echo $this->Form->input('Organization.edomains', array('type' => 'select', 'options' => $email_domails, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'org_edomains', 'multiple', 'default' => $org_edms )); ?>
								<label class="error text-red"></label>
									</div>
					  		</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
		<div class="tab-pane fade" id="tab_links" >
			<div class="row">
				<div class="form-group">
				  <label for="UserUser" class="col-lg-2 control-label">Web Link: <sup class="text-danger">*</sup></label>
				  <div class="col-lg-10">
					  <div class="form-control-skill">
					<?php echo $this->Form->input('Organization.web_link', array('type' => 'url', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'placeholder'=> '', 'autocomplete'=>'off', 'title'=>'', 'maxlength'=>255  )); ?>
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
				<?php $links = $this->Permission->get_org_links( $db_data['id']); ?>
				<div class="col-lg-12">
					<div class="popup-skill-list popup-skill-links">
						<ul>
							<?php if(isset($links) && !empty($links)) { ?>
							<?php foreach ($links as $key => $value) {
									$link = $value['up']; ?>
							<li class="pre-added">
								<span class="list-text"><?php echo htmlentities($link['title'],ENT_QUOTES, "UTF-8") ; ?></span>
								<span class="list-icon">
									<a href="<?php echo $link['link']; ?>" target="_blank" class="tipText open-link" title="Open Link">
										<i class="openlinkicon"></i>
									</a>
									<a href="#" class="remove_link tipText" data-id="<?php echo $link['id']; ?>" data-type="org" title="Remove Link">
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
						<?php $files = $this->Permission->get_org_files( $db_data['id']); ?>
					<div class="col-lg-12">
						<div class="popup-skill-list popup-skill-files">
							<ul>
								<?php if(isset($files) && !empty($files)) { ?>
								<?php foreach ($files as $key => $value) {
										$file = $value['up']; ?>
								<li class="pre-added">
									<span class="list-text"><?php echo htmlentities($file['title'],ENT_QUOTES, "UTF-8"); ?> </span>
									<span class="list-icon">
										<a href="<?php echo Router::url(array('controller' => 'communities', 'action' => 'download_files', 'org', $file['id'], 'admin' => false)); ?>" data-type="org" class="tipText" title="Download File" download ><i class="downloadblackicon"></i></a>
										<a href="#" class="remove_file tipText" title="Remove Now" data-id="<?php echo $file['id']; ?>" data-type="org">
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
		<div class="tab-pane fade"  id="tab_comp">
			<div class="row popup-select-icon">
				<div class="lo-add-competencies">
					<div class="form-group" >
						<label for="UserUser" class="col-sm-2 control-label">Skills:</label>
						<div class="col-sm-10">
							<?php echo $this->Form->input('Organization.skill', array('type' => 'select', 'options' => $skill_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'org_skills', 'multiple' => 'multiple', 'default' => $cskills )); ?>
						</div>
						<label class="error text-red"></label>
					</div>
					<div class="form-group" >
						<label for="UserUser" class="col-sm-2 control-label">Subjects:</label>
						<div class="col-sm-10">
							<?php echo $this->Form->input('Organization.subject', array('type' => 'select', 'options' => $subject_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'org_subjects', 'multiple' => 'multiple', 'default' => $csubjects )); ?>
						</div>
						<label class="error text-red"></label>
					</div>
					<div class="form-group" >
						<label for="UserUser" class="col-sm-2 control-label">Domains:</label>
						<div class="col-sm-10">
							<?php echo $this->Form->input('Organization.domain', array('type' => 'select', 'options' => $kd_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'org_domains', 'multiple' => 'multiple', 'default' => $cdomains )); ?>
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

	$('input[name="data[Organization][name]"]').focus();

	$("form#detail_add, form#files_add").submit(function(){
		return false;
	})
	$('.up-icon').off('click').on('click', function(event){
		$(this).parents('.docupload-sec:first').find('input[type=file]').trigger('click');
	});

	$.location_updated = false;
	$.temp_detail_file = '';
	$.tempfile = '';
	$.temp_data = {
		main: {
			id: '<?php echo $db_data['id']; ?>',
			name: $('[name="data[Organization][name]"]').val() || '',
			info: $('[name="data[Organization][information]"]').val() || '',
			filename: '',
			type: '<?php echo $db_data['type_id']; ?>',
			locations: $('#org_locations').val()
		},
		link: {},
		file: {},
		competency: {
			skills: $('#org_skills').val(),
			subjects: $('#org_subjects').val(),
			domains: $('#org_domains').val(),
		}
	};

	$('#org_locations').off('change').on('change', function(event){
		var that = $(this);
		$.total_used_loc = 0;
		$('option', $(that)).each(function(index, el) {
        	if(!$(this).prop('selected') && $(this).data('total'))
        		$.total_used_loc += $(this).data('total');
        });
	});

	$.total_used_loc = 0;
	$org_locations = $('#org_locations').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'loc_chk[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Locations',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Locations',
            onSelectAll:function(){
            	var selected = $('#org_locations').val();
                $.temp_data.main.locations = selected;
                // $.location_updated = true;
			},
			onDeselectAll:function(){
                $.temp_data.main.locations = '';
                $.location_updated = true;
                $.total_used_loc = 0;
                $('option', $('#org_locations')).each(function(index, el) {
                	$.total_used_loc += $(this).data('total');
                });
			},
            onChange: function(option, checked) {
            	var selected = $('#org_locations').val();
                $.temp_data.main.locations = selected;
                // $.location_updated = true;
                if(checked == false && option.data('total')){
                	$.location_updated = true;
                }
                $.total_used_loc = 0;
                $('option', $('#org_locations')).each(function(index, el) {
                	if(!$(this).prop('selected') && $(this).data('total'))
                		$.total_used_loc += $(this).data('total');
                });
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
		$('li.post-added', $ul).remove();

		$.each($.temp_data.link, function(index, el) {

			var nwlink = el.url;

			var $li = $('<li class="post-added">');
			var $span1 = $('<span class="list-text">').text(el.title).appendTo($li);
			var $span2 = $('<span class="list-icon">').appendTo($li);
			var $anc_link = $('<a href="' + nwlink + '" target="_blank" class="tipText open-link" title="Open Link">').appendTo($li);
			var $anc_link_icon = $('<i class="openlinkicon"></i>').appendTo($anc_link);
			var $anc_del = $('<a href="#" class="tipText" title="Remove Link" data-id="' + index + '" >').appendTo($li);
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
			$("[name='data[OrganizationFile][upload_file]']").parents('.form-group').find('.error').text('File Name is required');
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
		$('li.post-added', $ul).remove();
		$.each($.temp_data.file, function(index, el) {
			var $li = $('<li class="post-added">');
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

		$(this).parents('.form-group:first').find(".error").text('');
		if(this.files[0] !== undefined){
			var file = this.files[0];
			var fnme = file.name
			var fnme = fnme.substr(0, fnme.lastIndexOf('.')) || fnme;
			var regex = /^[A-Za-z0-9\s-_()]+$/
			if(regex.test(fnme) == false) {
				$(this).parents('.form-group:first').find(".error").text('Image filename contains unsupported characters');
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

		if(error){
			$('#add_tabs a[href="#tab_main"]').tab('show');
			return;
		}

		var used_loc = $("#used_loc").val();
		if($.total_used_loc > 0) {
			console.log('$.total_used_loc',$.total_used_loc)
			// open popup
			$('#modal_delete')
				.css('z-index', 3000)
				.modal({
					remote: $.module_url + 'confirm_org_update/' + $.temp_data.main.id + '/' + $.total_used_loc
				})
				.modal('show')
			return;
		}
		// console.log('asdfdsf',$.location_updated)
		// return;

		// console.log($.temp_data);
		// return;
		// $(this).prop('disabled', true);

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
					$.popup_updates.section = 'edit';
					$.popup_updates.reaction = 'org';
					$.get_locations();
					$.countRows('loc', $('#tab_loc.ssd-tabs'));
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

})

</script>