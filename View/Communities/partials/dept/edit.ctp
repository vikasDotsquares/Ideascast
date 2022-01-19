<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

$db_data = $dept_data[0]['departments'];
$dept_title =  html_entity_decode(htmlentities($db_data['name'] ,ENT_QUOTES, "UTF-8"));


$cskills = $csubjects = $cdomains = [];
if(!empty($dept_data[0][0]['skid'])){
	$skid = json_decode($dept_data[0][0]['skid'], true);
	$cskills = Set::extract($skid, '{n}.id');
}
if(!empty($dept_data[0][0]['sbid'])){
	$skid = json_decode($dept_data[0][0]['sbid'], true);
	$csubjects = Set::extract($skid, '{n}.id');
}
if(!empty($dept_data[0][0]['dmid'])){
	$skid = json_decode($dept_data[0][0]['dmid'], true);
	// pr($skid, 1);
	$cdomains = Set::extract($skid, '{n}.id');
}
 ?>
<div class="modal-header">
	<button type="button" class="close close-skill" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Edit Department</h4>
</div>
<div class="modal-body add-deprt-popup-cont dep-height">
	<ul class="nav nav-tabs" id="add_tabs">
		<li class="active">
			<a data-toggle="tab" data-type="details" class="active skilltab" data-target="#tab_main" href="#tab_main" aria-expanded="true">Details</a>
		</li>

		<li class="">
			<a data-toggle="tab" data-tab="skill_link" data-type="links" id="linksTab" data-target="#competencies" href="#competencies" aria-expanded="false" class="skilltab ">Competencies</a>
		</li>
	</ul>

	<div class="tab-content popup-select-icon">
		<div class="tab-pane fade active in" id="tab_main">
			<?php echo $this->Form->create('Competencies', array('class' => 'form-bordered', 'id' => 'detail_add', 'url'=>'manage_files', 'enctype'=>'multipart/form-data' ));
			?>
				<?php echo $this->Form->input('Department.id', array('type' => 'hidden', 'id' => 'data_id')); ?>
			<div class="row">
				<div class="form-group" >
					<label for="UserUser" class="col-lg-2 control-label" >Name: <sup class="text-danger">*</sup></label>
					<div class="col-lg-10" >
						<div class="form-control-skill">
							<?php echo $this->Form->input('Department.name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength'=>50, 'autocomplete'=>'off', 'id'=> 'name', 'value' => html_entity_decode(html_entity_decode($db_data['name'] ,ENT_QUOTES)))); ?>
							<label class="error text-red"></label>
						</div>
					</div>
				</div>
				<div class="form-group">
				  	<label for="UserUser" class="col-lg-2 control-label">Image: <i class="info-icon  " title="" data-original-title="150px by 150px Recommended"></i></label>
				  	<div class="col-lg-10">
					  	<div class="ski-close">
						  	<div class="input-group docupload-sec">
		                        <div class="input-group-addon up-icon">
		                            <i class="uploadblackicon"></i>
		                        </div>
		                        <span title="" class="docUpload icon_btn">
									<input type="file" name="data[Department][image]" class="form-control upload" id="main_image" placeholder="Upload File">
									<span class="up-text">Click to upload a file</span>
		                        </span>
		                        <div class="input-group-addon remove-image not-shown tipText" title="Clear File Name">
		                            <i class="clearblackicon"></i>
		                           <i class="fa fa-spinner fa-spin file-uploading"></i>
		                        </div>
		                    </div>

							<div class="two-msg-show">
								<?php if(!empty($db_data['image'])){ ?>
					  	<div class="show_images">
						    <span class="main_image"><?php echo $db_data['image']; ?></span>
						    <i data-type="dept" data-id="<?php echo $db_data['id']; ?>" class="clearredicon remove_image tipText" title="Remove Now"></i>
						</div>
						<?php } ?>
								<label class="error  text-red" style="display: table;"></label>
							</div>
		                </div>

				  	</div>
				</div>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
		<div class="tab-pane fade" id="competencies">
			<div class="row">
				<div class="form-group">
					<label for="" class="col-lg-2 control-label">Skills:</label>
					<div class="col-lg-10">
						<div class="form-control-skill">
							<?php echo $this->Form->input('Department.skill', array('type' => 'select', 'options' => $skill_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'dept_skills', 'multiple' => 'multiple', 'default' => $cskills  )); ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-lg-2 control-label">Subjects:</label>
					<div class="col-lg-10">
						<div class="form-control-skill">
							<?php echo $this->Form->input('Department.subject', array('type' => 'select', 'options' => $subject_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'dept_subjects', 'multiple' => 'multiple', 'default' => $csubjects  )); ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-lg-2 control-label">Domains:</label>
					<div class="col-lg-10">
						<div class="form-control-skill">
							<?php echo $this->Form->input('Department.domain', array('type' => 'select', 'options' => $kd_data, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'dept_domains', 'multiple' => 'multiple', 'default' => $cdomains )); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<div class="modal-footer clearfix">
	<span class="submitmsg"></span>
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
</style>
<script>
$(function(){
	$('#name').off('keyup').on('keyup', function(event) {
		if(event.which == 13) {
			$('.submit_data').trigger('click')
		}
	})
	$("form#detail_add").submit(function(){
		return false;
	})
	$('.up-icon').off('click').on('click', function(event){
		$(this).parents('.docupload-sec:first').find('input[type=file]').trigger('click');
	});

	$.temp_detail_file = '';
	$.temp_data = {
		type: 'dept',
		main: {
			id: '<?php echo $db_data['id']; ?>',
			name: $("#name").val(),
			filename: ''
		},
		competency: {
			skills: $('#dept_skills').val(),
			subjects: $('#dept_subjects').val(),
			domains: $('#dept_domains').val(),
		}
	};

	$dept_skills = $('#dept_skills').multiselect({
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
            	var selected = $('#dept_skills').val();
                $.temp_data.competency.skills = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.skills = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#dept_skills').val();
                $.temp_data.competency.skills = selected;
            }
        });

	$dept_subjects = $('#dept_subjects').multiselect({
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
            	var selected = $('#dept_subjects').val();
                $.temp_data.competency.subjects = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.subjects = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#dept_subjects').val();
                $.temp_data.competency.subjects = selected;
            }
        });

	$dept_domains = $('#dept_domains').multiselect({
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
            	var selected = $('#dept_domains').val();
                $.temp_data.competency.domains = selected;
			},
			onDeselectAll:function(){
                $.temp_data.competency.domains = '';
			},
            onChange: function(element, checked) {
            	var selected = $('#dept_domains').val();
                $.temp_data.competency.domains = selected;
            }
        });

	$("#main_image").change(function() {
		var file = this.files[0];
		if(this.files[0] !== undefined){
			var file = this.files[0];
			var fnme = file.name
			var fnme = fnme.substr(0, fnme.lastIndexOf('.')) || fnme;
			var regex = /^[A-Za-z0-9\s-_()]+$/
			if(regex.test(fnme) == false) {
				$("[name='data[Department][image]").parents('.ski-close').find('.error').text('Image filename contains unsupported characters');
				$("[name='data[Department][image]']").val('');
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

			$parent.find('.error').text('');
			if( $fileInput.val() !== "" && file !== undefined && $.inArray(file_ext, ['jpg','jpeg','png', 'gif']) == -1  ){
				$form.find(".remove-image").trigger('click');
				$parent.find('.error').text('Valid files are: jpg, jpeg, png, gif');
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

		if($.trim($("#name").val()) == ''){
			$("#name").parent().find('.error').text('Name is required');
			return;
		}
		var $this = $(this);
		$this.prop('disabled', true);

		var data = {'name': $("#name").val(), 'type': 'dept'};
		$.temp_data.main.name = $("#name").val();
		$.temp_data.main.filename = $.temp_detail_file;
		if($("#data_id").val() != ''){
			data.id = $('#data_id').val()
		}

		$.ajax({
			url: $.module_url + 'save_data',
			type: 'POST',
			dataType: 'json',
			data: $.temp_data,
			context: this,
			success: function(response){

				if(response.success){
					$(this).prop('disabled', false);
					$.popup_updates.action = true;
					$.popup_updates.section = 'edit';
					$.popup_updates.reaction = 'dept';
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
	$('.info-icon').tooltip({
		html: true,
		template: '<div class="tooltip" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>',
		container: 'body',
		placement: "top"
	});
})

</script>