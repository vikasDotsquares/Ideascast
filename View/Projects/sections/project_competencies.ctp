<style type="text/css">
	.project-document-popup .error.text-red {
		display: block;
	}
</style>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

$ssd = $this->Permission->project_sksudm($project_id);
$others = $ssd[0][0];
$all_skills = (!empty($others['skills'])) ? json_decode($others['skills'], true) : [];
$all_subjects = (!empty($others['subjects'])) ? json_decode($others['subjects'], true) : [];
$all_domains = (!empty($others['domains'])) ? json_decode($others['domains'], true) : [];

$prj_skills = (!empty($others['pskills'])) ? json_decode($others['pskills'], true) : [];
$prj_subjects = (!empty($others['psubjects'])) ? json_decode($others['psubjects'], true) : [];
$prj_domains = (!empty($others['pdomains'])) ? json_decode($others['pdomains'], true) : [];

if(isset($prj_skills) && !empty($prj_skills)){
	$prj_skills = Set::extract($prj_skills, '{n}.id');
}
if(isset($prj_subjects) && !empty($prj_subjects)){
	$prj_subjects = Set::extract($prj_subjects, '{n}.id');
}
if(isset($prj_domains) && !empty($prj_domains)){
	$prj_domains = Set::extract($prj_domains, '{n}.id');
}
//
function asrt($a, $b) {
	$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
	$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
    return strcasecmp($t1, $t2);
}
if(isset($all_skills) && !empty($all_skills)){
	usort($all_skills, 'asrt');
	$all_skills = htmlentity($all_skills, 'title');
	$all_skills = Set::combine($all_skills, '{n}.id', '{n}.title');
}
if(isset($all_subjects) && !empty($all_subjects)){
	usort($all_subjects, 'asrt');
	$all_subjects = htmlentity($all_subjects, 'title');
	$all_subjects = Set::combine($all_subjects, '{n}.id', '{n}.title');
}
if(isset($all_domains) && !empty($all_domains)){
	usort($all_domains, 'asrt');
	$all_domains = htmlentity($all_domains, 'title');
	$all_domains = Set::combine($all_domains, '{n}.id', '{n}.title');
}
?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Project Competencies</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body popup-select-icon project-com-popup clearfix">
		<div class="row">
			<div class="form-group">
				<label for="UserUser" class="col-sm-2 control-label">Skills:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Project.skills', array('type' => 'select', 'options' => $all_skills, 'default' => $prj_skills, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'prj_skills', 'multiple' )); ?>
				</div>
			</div>
			<div class="form-group">
				<label for="UserUser" class="col-sm-2 control-label">Subjects:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Project.subjects', array('type' => 'select', 'options' => $all_subjects, 'default' => $prj_subjects, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'prj_subjects', 'multiple' )); ?>
				</div>
			</div>
			<div class="form-group">
				<label for="UserUser" class="col-sm-2 control-label">Domains:</label>
				<div class="col-sm-10">
					<?php echo $this->Form->input('Project.subjects', array('type' => 'select', 'options' => $all_domains, 'default' => $prj_domains, 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'prj_domains', 'multiple' )); ?>
				</div>
			</div>
		</div>
		</div>
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		<button type="button"  class="btn btn-success submit-competencies" >Save</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
<script type="text/javascript">
	$(function(){

		var project_id = '<?php echo $project_id; ?>';

		$('.submit-competencies').off('click').on('click', function(event) {
			event.preventDefault();
			var skills = $('#prj_skills').val(),
				subjects = $('#prj_subjects').val(),
				domains = $('#prj_domains').val();

			$.ajax({
	            type: 'POST',
	            data: { skills: skills, subjects: subjects, domains: domains },
	            dataType: 'json',
	            url: $js_config.base_url + 'projects/save_project_competencies/' + project_id,
	            success: function(response) {
	            	if(response.success){
	            		$('#model_bx').modal('hide');
	            		$.competency_updated = true;
	            	}
	            }
	        });
		});

		$prj_skills = $('#prj_skills').multiselect({
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
        });
		$prj_skills = $('#prj_subjects').multiselect({
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
        });
		$prj_skills = $('#prj_domains').multiselect({
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
        });
	})
</script>