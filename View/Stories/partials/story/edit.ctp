<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

	$db_data = $data[0]['stories'];
	$detail = $data[0][0];
	$detail_title = html_entity_decode(html_entity_decode($db_data['name'] ,ENT_QUOTES, "UTF-8"));

	$all_types = (!empty($detail['all_types'])) ? json_decode($detail['all_types'], true) : false;
	$all_skills = (!empty($detail['all_skills'])) ? json_decode($detail['all_skills'], true) : false;
	$all_subjects = (!empty($detail['all_subjects'])) ? json_decode($detail['all_subjects'], true) : false;
	$all_domains = (!empty($detail['all_domains'])) ? json_decode($detail['all_domains'], true) : false;
	$all_org = (!empty($detail['all_org'])) ? json_decode($detail['all_org'], true) : false;
	$all_locations = (!empty($detail['all_locations'])) ? json_decode($detail['all_locations'], true) : false;
	$all_dept = (!empty($detail['all_dept'])) ? json_decode($detail['all_dept'], true) : false;
	$all_story = (!empty($detail['all_story'])) ? json_decode($detail['all_story'], true) : false;
	$all_users = (!empty($detail['all_users'])) ? json_decode($detail['all_users'], true) : false;

	$all_types = Set::combine( $all_types, '{n}.id', '{n}.name' );
	$all_skills = Set::combine( $all_skills, '{n}.id', '{n}.name' );
	$all_subjects = Set::combine( $all_subjects, '{n}.id', '{n}.name' );
	$all_domains = Set::combine( $all_domains, '{n}.id', '{n}.name' );
	$all_org = Set::combine( $all_org, '{n}.id', '{n}.name' );
	$all_locations = Set::combine( $all_locations, '{n}.id', '{n}.name' );
	$all_dept = Set::combine( $all_dept, '{n}.id', '{n}.name' );
	$all_story = Set::combine( $all_story, '{n}.id', '{n}.name' );
	$all_users = Set::combine( $all_users, '{n}.id', '{n}.name' );

	$all_skills = htmlentity($all_skills);
	$all_subjects = htmlentity($all_subjects);
	$all_domains = htmlentity($all_domains);
	$all_org = htmlentity($all_org);
	$all_locations = htmlentity($all_locations);
	$all_dept = htmlentity($all_dept);
	$all_story = htmlentity($all_story);
	$all_users = htmlentity($all_users);


	uasort($all_types, "compareASCII");
	uasort($all_skills, "compareASCII");
	uasort($all_subjects, "compareASCII");
	uasort($all_domains, "compareASCII");
	uasort($all_org, "compareASCII");
	uasort($all_locations, "compareASCII");
	uasort($all_dept, "compareASCII");
	uasort($all_story, "compareASCII");
	uasort($all_users, "compareASCII");


	// SELECTED DATA IDs
	$selected_users = (!empty($detail['selected_users'])) ? json_decode($detail['selected_users'], true) : [];
	$selected_org = (!empty($detail['selected_org'])) ? json_decode($detail['selected_org'], true) : [];
	$selected_locations = (!empty($detail['selected_locations'])) ? json_decode($detail['selected_locations'], true) : [];
	$selected_dept = (!empty($detail['selected_dept'])) ? json_decode($detail['selected_dept'], true) : [];
	$selected_skills = (!empty($detail['selected_skills'])) ? json_decode($detail['selected_skills'], true) : [];
	$selected_subjects = (!empty($detail['selected_subjects'])) ? json_decode($detail['selected_subjects'], true) : [];
	$selected_domains = (!empty($detail['selected_domains'])) ? json_decode($detail['selected_domains'], true) : [];
	$selected_stories = (!empty($detail['selected_stories'])) ? json_decode($detail['selected_stories'], true) : [];


	$selected_users = Set::extract( $selected_users, '{n}.id' );
	$selected_org = Set::extract( $selected_org, '{n}.id' );
	$selected_locations = Set::extract( $selected_locations, '{n}.id' );
	$selected_dept = Set::extract( $selected_dept, '{n}.id' );
	$selected_skills = Set::extract( $selected_skills, '{n}.id' );
	$selected_subjects = Set::extract( $selected_subjects, '{n}.id' );
	$selected_domains = Set::extract( $selected_domains, '{n}.id' );
	$selected_stories = Set::extract( $selected_stories, '{n}.id' );

	$selected_links = (!empty($detail['selected_links'])) ? json_decode($detail['selected_links'], true) : [];
	usort($selected_links, function($a, $b) {
	    $t1 = ($a['id']);
	    $t2 = ($b['id']);
	    return $t2 - $t1;
	});

	$selected_files = (!empty($detail['selected_files'])) ? json_decode($detail['selected_files'], true) : [];
	usort($selected_files, function($a, $b) {
	    $t1 = ($a['id']);
	    $t2 = ($b['id']);
	    return $t2 - $t1;
	});
	// pr($selected_files);
	// mpr($selected_users, $selected_org, $selected_locations, $selected_dept, $selected_skills, $selected_subjects, $selected_domains, $selected_stories);
?>
<div class="modal-header">
	<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Edit Story</h4>
</div>
<div class="modal-body add-deprt-popup-cont org-popup-cont strorie-popup-cont">
	<ul class="nav nav-tabs" id="add_tabs">
		<li class="active">
			<a data-toggle="tab" data-type="details" class="active tab-detail" data-target="#tab_main" href="#tab_main" aria-expanded="true" >Details</a>
		</li>
		<li class="">
			<a data-toggle="tab" data-type="Stroy" id="storyTab" data-target="#tab_stories" href="#tab_stories" aria-expanded="false" class="tab-story" >Story</a>
		</li>
		<li class="">
			<a data-toggle="tab" data-type="links" id="linksTab" data-target="#tab_links" href="#tab_links" aria-expanded="false" class="tab-link" >Links</a>
		</li>

		<li class="">
			<a data-toggle="tab" data-type="files" id="filesTab" data-target="#tab_files" href="#tab_files" aria-expanded="false" class="tab-file" >Files</a>
		</li>

		<li class="">
			<a data-toggle="tab" data-type="Related" id="relatedTab" data-target="#tab_related" href="#tab_related" aria-expanded="false" class="tab-related" >Related</a>
		</li>
	</ul>
	<div class="tab-content ">
		<div class="tab-pane fade active in add-location-details popup-select-icon"  id="tab_main" >
			<?php echo $this->Form->create('StoryMain', array('class' => 'form-bordered', 'id' => 'detail_add', 'url'=>'manage_files', 'enctype'=>'multipart/form-data' ));
			?>
			<div class="row">
				<!-- LEFT -->
				<div class="col-sm-12">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Name: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Story.name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>100, 'autocomplete'=>'off', 'value' => $detail_title)); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>



				</div>
				<!-- RIGHT -->
				<div class="col-sm-6">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Type: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Story.type_id', array('type' => 'select', 'options' => $all_types, 'label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'Select Type', 'default' => $db_data['type_id'] )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
					</div>
				<div class="col-sm-6">
					<div class="form-group loc-img-info">
					  	<label for="UserUser" class="control-label">Image:  <i class="info-icon  tipText" title="" data-original-title="150px by 150px Recommended"></i></label>
					  	<div class="ski-close">
						  	<div class="input-group docupload-sec">
                                <div class="input-group-addon up-icon">
                                    <i class="uploadblackicon"></i>
                                </div>
                                <span title="" class="docUpload icon_btn">
									<input type="file" name="data[Story][image]" class="form-control upload" id="main_image" placeholder="Upload File" accept=".png, .jpg, .jpeg">
									<span class="up-text"  style="">Click to upload a file</span>
                                </span>
                                <div class="input-group-addon remove-image not-shown tipText" title="Clear Image">
                                    <i class="clearblackicon"></i>
                                   <i class="fa fa-spinner fa-spin file-uploading"></i>
                                </div>
								<label class="error text-red"></label>
								<?php if(isset($db_data['image']) && !empty($db_data['image'])) { ?>
								<div class="two-msg-show">
									<div class="show_images">
									    <span class="main_image"><?php echo $db_data['image']; ?></span>
									    <i data-type="story" data-id="<?php echo $db_data['id']; ?>" class="clearredicon remove_image tipText" title="Remove Now"></i>
									</div>
									<div class="error text-red"></div>
								</div>
								<?php } ?>

                            </div>
					  	</div>

					</div>

				</div>
				<div class="col-sm-12">
					<div class="form-group summary-filed">
					  	<label for="UserUser" class="control-label">Summary: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Story.summary', array('type' => 'textarea', 'rows'=> 5, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control information', 'maxlength'=>500, 'value' => html_entity_decode(html_entity_decode($db_data['summary'] ,ENT_QUOTES, "UTF-8"))  )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
				</div>

			</div>
			<?php echo $this->Form->end(); ?>
		</div>

		<div class="tab-pane fade add-location-details"  id="tab_stories">
			<div class="row ">
			<div class="col-sm-12">
					<div class="form-group">
					  	<label for="UserUser" class="control-label">Story: <sup class="text-danger">*</sup></label>
					  	<div class="form-control-skill">
							<?php echo $this->Form->input('Story.story', array('type' => 'textarea', 'rows'=> 5, 'cols'=>35, 'label' => false, 'div' => false, 'class' => 'form-control story-textarea', 'maxlength'=>4096, 'value' => html_entity_decode(html_entity_decode($db_data['story'] ,ENT_QUOTES, "UTF-8")) )); ?>
							<label class="error text-red"></label>
				  		</div>
					</div>
				</div>
			</div>
		</div>

		<div class="tab-pane fade"  id="tab_links" >
			<div class="row">
				<div class="form-group">
				  <label for="UserUser" class="col-lg-2 control-label">Web Link: <sup class="text-danger">*</sup></label>
				  <div class="col-lg-10">
					  <div class="form-control-skill">
					<?php echo $this->Form->input('Story.web_link', array('type' => 'url', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'placeholder'=> '', 'autocomplete'=>'off', 'title'=>'', 'maxlength'=>255 )); ?>
					<label class="error  text-red"></label>
				  </div>
					</div>
				</div>
				<div class="form-group">
				  <label for="UserUser" class="col-lg-2 control-label">Title: <sup class="text-danger">*</sup></label>
					<div class="col-lg-10">
						<div class="form-control-skill">
					  <div class="title-group-w">
						<?php echo $this->Form->input('Story.link_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'required'=>false, 'maxlength'=>50, 'autocomplete'=>'off' )); ?>
						<button type="button" class="btn outline-btn-s save_links">Add</button>
					  </div>
					  <label class="error  text-red"></label>
					</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="popup-skill-list popup-skill-links">
						<ul>
							<?php if(isset($selected_links) && !empty($selected_links)) { ?>
							<?php foreach ($selected_links as $key => $value) {
									$link = $value; ?>
							<li class="pre-added">
								<span class="list-text"><?php echo htmlentities($link['title']) ; ?></span>
								<span class="list-icon">
									<a href="<?php echo $link['link']; ?>" target="_blank" class="tipText open-link" title="Open Link">
										<i class="openlinkicon"></i>
									</a>
									<a href="#" class="remove_link tipText" data-id="<?php echo $link['id']; ?>" data-type="story" title="Remove Link">
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
			<?php echo $this->Form->create('StoryImage', array('class' => 'form-bordered', 'id' => 'files_add', 'url'=>'manage_files', 'enctype'=>'multipart/form-data' ));
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
									<input type="file" name="data[StoryFile][upload_file]" class="form-control upload" id="file_upload" placeholder="Upload File">
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
										<?php echo $this->Form->input('StoryFile.file_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off')); ?>

										<button type="button" class="btn outline-btn-s save_files">Add</button>
								  	</div>
								  	<label class="error  text-red" style="display: table;"></label>
						  		</div>
							</div>
						</div>
					<div class="col-lg-12">
						<div class="popup-skill-list popup-skill-files">
							<ul>
								<?php if(isset($selected_files) && !empty($selected_files)) { ?>
								<?php foreach ($selected_files as $key => $value) {
										$file = $value; ?>
								<li class="pre-added">
									<span class="list-text"><?php echo htmlentities($file['title']); ?> </span>
									<span class="list-icon">
										<a href="<?php echo Router::url(array('controller' => 'stories', 'action' => 'download_files', 'story', $file['id'], 'admin' => false)); ?>" data-type="story" class="tipText" title="Download File" download><i class="downloadblackicon"></i></a>
										<a href="#" class="remove_file tipText" title="Remove Now" data-id="<?php echo $file['id']; ?>" data-type="story">
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

		<div class="tab-pane fade"  id="tab_related" >
			<div class="row popup-select-icon">
				<div class="lo-add-competencies">
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">People:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Story.skill', array('type' => 'select', 'options' => $all_users, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'story_people', 'multiple' => 'multiple', 'default' => $selected_users )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Organizations:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Story.skill', array('type' => 'select', 'options' => $all_org, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'story_org', 'multiple' => 'multiple', 'default' => $selected_org )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Locations:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Story.skill', array('type' => 'select', 'options' => $all_locations, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'story_locations', 'multiple' => 'multiple', 'default' => $selected_locations )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Departments:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Story.skill', array('type' => 'select', 'options' => $all_dept, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'story_departments', 'multiple' => 'multiple', 'default' => $selected_dept )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Skills:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Story.skill', array('type' => 'select', 'options' => $all_skills, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'story_skills', 'multiple' => 'multiple', 'default' => $selected_skills )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Subjects:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Story.subject', array('type' => 'select', 'options' => $all_subjects, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'story_subjects', 'multiple' => 'multiple', 'default' => $selected_subjects )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
			<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Domains:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Story.domain', array('type' => 'select', 'options' => $all_domains, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'story_domains', 'multiple' => 'multiple', 'default' => $selected_domains )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
					<div class="form-group" >
				<label for="UserUser" class="col-sm-2 control-label">Stories:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Story.skill', array('type' => 'select', 'options' => $all_story, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'story_stories', 'multiple' => 'multiple', 'default' => $selected_stories )); ?>
				</div>
				<label class="error text-red"></label>
			</div>
				</div></div>
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

	$('input[name="data[Story][name]"]').focus();

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
			name: $('[name="data[Story][name]"]').val(),
			type: '<?php echo $db_data['type_id']; ?>',
			filename: '',
			summary: $('[name="data[Story][summary]"]').val() || '',
			story: $('[name="data[Story][story]"]').val() || ''
		},
		link: {},
		file: {},
		related: {
			people: $('#story_people').val(),
			organizations: $('#story_org').val(),
			locations: $('#story_locations').val(),
			departments: $('#story_departments').val(),
			skills: $('#story_skills').val(),
			subjects: $('#story_subjects').val(),
			domains: $('#story_domains').val(),
			stories: $('#story_stories').val()
		}
	};

	$.link_counter = 0;
	$('.save_links').off('click').on('click', function(event) {
		event.preventDefault();

		$('.error').text('');
		var error = false;
		if($.trim($("[name='data[Story][web_link]']").val()) == ''){
			$("[name='data[Story][web_link]']").parent().find('.error').text('Link is required')
			error = true;
		}
		if( $.trim($("[name='data[Story][web_link]']").val()) != '' && $.isValidUrl($.trim($("[name='data[Story][web_link]']").val())) == -1 ){
			$("[name='data[Story][web_link]']").parent().find('.error').text('Missing a protocol, hostname or filename')
			error = true;
		}
		if($.trim($("[name='data[Story][link_name]']").val()) == ''){
			$("[name='data[Story][link_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}
		if(error){
			return;
		}

		var wlink = $("[name='data[Story][web_link]']").val();
		var input_link = wlink;
		if (wlink && !wlink.match(/^http([s]?):\/\/.*/)) {
				input_link = 'http://'+wlink;
		}
		$.temp_data['link'][$.link_counter] = {
			title: $("[name='data[Story][link_name]").val(),
			url: input_link
		}
		$.link_counter++;
		$("[name='data[Story][link_name]").val('');
		$("[name='data[Story][web_link]']").val('');

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

		$("[name='data[StoryFile][upload_file]']").parents('.form-group').find('.error').text('');

		if(this.files[0] !== undefined){
			var file = this.files[0];
			var fnme = file.name
			var fnme = fnme.substr(0, fnme.lastIndexOf('.')) || fnme;
			var regex = /^[A-Za-z0-9\s-_()]+$/
			if(regex.test(fnme) == false) {
				$("[name='data[StoryFile][upload_file]']").parents('.form-group').find('.error').text('Image filename contains unsupported characters');
				$("[name='data[StoryFile][upload_file]']").val('');
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
		if($.trim($("[name='data[StoryFile][upload_file]']").val()) == ''){
			$("[name='data[StoryFile][upload_file]']").parents('.form-group').find('.error').text('File Name is required');
			error = true;
		}
		if($.trim($("[name='data[StoryFile][file_name]']").val()) == ''){
			$("[name='data[StoryFile][file_name]']").parents('.form-control-skill:first').find('.error').text('Title is required');
			error = true;
		}

		if(error){
			return;
		}

		$.temp_data['file'][$.file_counter] = {
			title: $("[name='data[StoryFile][file_name]']").val(),
			filename: $.tempfile
		}
		$.file_counter++;

		$("[name='data[StoryFile][file_name]']").val('');
		$("#file_upload").val('');
		$("#file_upload").parent().find('.up-text').text("Click to upload a file");


		var $ul = $('.popup-skill-files ul');
		$('li.post-added', $ul).remove();
		$.each($.temp_data.file, function(index, el) {
			var $li = $('<li class="post-added">');
			var $span1 = $('<span class="list-text">').text(el.title).appendTo($li);
			var $span2 = $('<span class="list-icon">').appendTo($li);
			var $anc_link = $('<a href="' + $.module_url + 'download_temp_files/'+el.filename+'"  class="tipText" title="Download File"  download>').appendTo($li);
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

			$(".submit_data").prop('disabled', true);
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
							$(".submit_data").prop('disabled', false);
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
		var errorStory = false;

		if($.trim($("#StoryName").val()) == ''){
			$("#StoryName").parent().find('.error').text('Name is required');
			error = true;
		}
		if( $("#StoryTypeId").val() == ''){
			$("#StoryTypeId").parent().find('.error').text('Type is required');
			error = true;
		}
		if( $("#StorySummary").val() == ''){
			$("#StorySummary").parent().find('.error').text('Summary is required');
			error = true;
		}
		if( $("#StoryStory").val() == ''){
			$("#StoryStory").parent().find('.error').text('Story is required');
			errorStory = true;
		}

		if(error){
			$('#add_tabs a[href="#tab_main"]').tab('show');
			return;
		}
		else if(errorStory){
			$('#add_tabs a[href="#tab_stories"]').tab('show');
			return;
		}

		$.temp_data.main.name = $("#StoryName").val();
		$.temp_data.main.type = $("#StoryTypeId").val();
		$.temp_data.main.filename = $.temp_detail_file;
		$.temp_data.main.summary = $("#StorySummary").val();
		$.temp_data.main.story = $("#StoryStory").val();

		$.ajax({
			url: $.module_url + 'save_story',
			type: 'POST',
			dataType: 'json',
			data: $.temp_data,
			context: this,
			success: function(response){

				if(response.success){
					// $('.search-box[data-type="story"]').val('');
					$(this).prop('disabled', false);
					$.popup_updates.action = true;
					$.popup_updates.section = 'edit';
					$.popup_updates.reaction = 'story';
					$('#modal_create').modal('hide');
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


	$story_people = $('#story_people').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search People',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select People',
            onSelectAll:function(){
            	var selected = $('#story_people').val();
                $.temp_data.related.people = selected;
			},
			onDeselectAll:function(){
                $.temp_data.related.people = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#story_people').val();
                $.temp_data.related.people = selected;
            }
        });

	$story_org = $('#story_org').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Organizations',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Organizations',
            onSelectAll:function(){
            	var selected = $('#story_org').val();
                $.temp_data.related.organizations = selected;
			},
			onDeselectAll:function(){
                $.temp_data.related.organizations = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#story_org').val();
                $.temp_data.related.organizations = selected;
            }
        });

	$story_locations = $('#story_locations').multiselect({
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
            	var selected = $('#story_locations').val();
                $.temp_data.related.locations = selected;
			},
			onDeselectAll:function(){
                $.temp_data.related.locations = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#story_locations').val();
                $.temp_data.related.locations = selected;
            }
        });

	$story_departments = $('#story_departments').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Departments',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Departments',
            onSelectAll:function(){
            	var selected = $('#story_departments').val();
                $.temp_data.related.departments = selected;
			},
			onDeselectAll:function(){
                $.temp_data.related.departments = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#story_departments').val();
                $.temp_data.related.departments = selected;
            }
        });

	$story_skills = $('#story_skills').multiselect({
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
            	var selected = $('#story_skills').val();
                $.temp_data.related.skills = selected;
			},
			onDeselectAll:function(){
                $.temp_data.related.skills = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#story_skills').val();
                $.temp_data.related.skills = selected;
            }
        });

	$story_subjects = $('#story_subjects').multiselect({
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
            	var selected = $('#story_subjects').val();
                $.temp_data.related.subjects = selected;
			},
			onDeselectAll:function(){
                $.temp_data.related.subjects = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#story_subjects').val();
                $.temp_data.related.subjects = selected;
            }
        });

	$story_domains = $('#story_domains').multiselect({
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
            	var selected = $('#story_domains').val();
                $.temp_data.related.domains = selected;
			},
			onDeselectAll:function(){
                $.temp_data.related.domains = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#story_domains').val();
                $.temp_data.related.domains = selected;
            }
        });

	$story_stories = $('#story_stories').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Stories',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Stories',
            onSelectAll:function(){
            	var selected = $('#story_stories').val();
                $.temp_data.related.stories = selected;
			},
			onDeselectAll:function(){
                $.temp_data.related.stories = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#story_stories').val();
                $.temp_data.related.stories = selected;
            }
        });

})

</script>