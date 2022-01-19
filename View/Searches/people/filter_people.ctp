<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<?php
// pr($data);
$data = $data[0][0];
$user = (!empty($data['user'])) ? json_decode($data['user'], true) : [];
$organization = (!empty($data['organization'])) ? json_decode($data['organization'], true) : [];
$location = (!empty($data['location'])) ? json_decode($data['location'], true) : [];
$department = (!empty($data['department'])) ? json_decode($data['department'], true) : [];
$tag = (!empty($data['tag'])) ? json_decode($data['tag'], true) : [];
$skill = (!empty($data['skill'])) ? json_decode($data['skill'], true) : [];
$subject = (!empty($data['subject'])) ? json_decode($data['subject'], true) : [];
$domain = (!empty($data['domain'])) ? json_decode($data['domain'], true) : [];
$story = (!empty($data['story'])) ? json_decode($data['story'], true) : [];


function make_select($arr) {
    $data = [];
    if(isset($arr) && !empty($arr)){
        foreach ($arr as $key => $value) {
            $data[$value['id']] = htmlentities($value['title'], ENT_QUOTES, "UTF-8");
        }
    }
    return $data;
}
function asrt($a, $b) {
    $t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
    $t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
    return strcasecmp($t1, $t2);
}
usort($user, 'asrt');
usort($organization, 'asrt');
usort($location, 'asrt');
usort($department, 'asrt');
usort($tag, 'asrt');
usort($skill, 'asrt');
usort($subject, 'asrt');
usort($domain, 'asrt');
usort($story, 'asrt');
$user = make_select($user);
$organization = make_select($organization);
$location = make_select($location);
$department = make_select($department);
$tag = make_select($tag);
$skill = make_select($skill);
$subject = make_select($subject);
$domain = make_select($domain);
$story = make_select($story);
// pr($story);
?>
<!-- POPUP MODEL BOX CONTENT HEADER -->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">Filter People</h3>
</div>
<!-- POPUP MODAL BODY -->
<div class="modal-body popup-select-icon popup-multselect-list clearfix">
    <div class="filter-col-row">
        <div class="filter-col-1 ">
            <div class="form-group">
                <?php echo $this->Form->input('sel_people', array('type' => 'select', 'options' => $user, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_people', 'multiple' => 'multiple' )); ?>
            </div>
            <div class="form-group">
                <?php echo $this->Form->input('sel_org', array('type' => 'select', 'options' => $organization, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_org', 'multiple' => 'multiple' )); ?>
            </div>
            <?php  ?><div class="form-group ">
                <?php echo $this->Form->input('sel_loc', array('type' => 'select', 'options' => $location, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_loc', 'multiple' => 'multiple' )); ?>
            </div>
            <div class="form-group mb0">
                <?php echo $this->Form->input('sel_dep', array('type' => 'select', 'options' => $department, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_dep', 'multiple' => 'multiple' )); ?>
            </div>
        </div>
        <div class="filter-col-2">
            <div class="form-group">
                <?php echo $this->Form->input('sel_tag', array('type' => 'select', 'options' => $tag, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_tag', 'multiple' => 'multiple' )); ?>
            </div>
            <div class="form-group">
                <?php echo $this->Form->input('sel_skills', array('type' => 'select', 'options' => $skill, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_skills', 'multiple' => 'multiple' )); ?>
            </div>
            <?php  ?><div class="form-group ">
                <?php echo $this->Form->input('sel_sub', array('type' => 'select', 'options' => $subject, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_sub', 'multiple' => 'multiple' )); ?>
            </div><?php  ?>
            <div class="form-group">
                <?php echo $this->Form->input('sel_dom', array('type' => 'select', 'options' => $domain, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_dom', 'multiple' => 'multiple' )); ?>
            </div>
            <div class="form-group mb0">
                <?php echo $this->Form->input('sel_story', array('type' => 'select', 'options' => $story, 'label' => false, 'div' => false, 'class' => 'form-control',  'id' => 'sel_story', 'multiple' => 'multiple' )); ?>
            </div>
        </div>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button"  class="btn btn-success submit-pfilter ftbtndisable">Filter</button>
    <button type="button"  class="btn btn-danger clear-pfilter">Clear</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
</div>
<script type="text/javascript" >
	$(function(){

        if($._people_filter.user.length > 0){
            $('#sel_people').val($._people_filter.user);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }
        if($._people_filter.org.length > 0){
            $('#sel_org').val($._people_filter.org);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }
        if($._people_filter.loc.length > 0){
            $('#sel_loc').val($._people_filter.loc);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }
        if($._people_filter.dept.length > 0){
            $('#sel_dep').val($._people_filter.dept);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }
        if($._people_filter.tag.length > 0){
            $('#sel_tag').val($._people_filter.tag);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }
        if($._people_filter.skill.length > 0){
            $('#sel_skills').val($._people_filter.skill);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }
        if($._people_filter.sub.length > 0){
            $('#sel_sub').val($._people_filter.sub);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }
        if($._people_filter.domain.length > 0){
            $('#sel_dom').val($._people_filter.domain);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }
        if($._people_filter.story.length > 0){
            $('#sel_story').val($._people_filter.story);
            $('.submit-pfilter').removeClass('ftbtndisable');
        }

        $('#sel_people, #sel_org, #sel_loc, #sel_dep, #sel_tag, #sel_skills, #sel_sub, #sel_dom, #sel_story').off('change').on('change', function(event) {
            event.preventDefault();
            var people = $('#sel_people').val() || [],
                org = $('#sel_org').val() || [],
                loc = $('#sel_loc').val() || [],
                dept = $('#sel_dep').val() || [],
                tag = $('#sel_tag').val() || [],
                skill = $('#sel_skills').val() || [],
                sub = $('#sel_sub').val() || [],
                domain = $('#sel_dom').val() || [];
                story = $('#sel_story').val() || [];

            if(people.length <= 0 && org.length <= 0 && loc.length <= 0 && dept.length <= 0 && tag.length <= 0 && skill.length <= 0 && sub.length <= 0 && domain.length <= 0 && story.length <= 0){
                $('.submit-pfilter').addClass('ftbtndisable');
            }
            else{
                $('.submit-pfilter').removeClass('ftbtndisable');
            }
        });

        $('.submit-pfilter').off('click').on('click', function(event) {
            event.preventDefault();
            var people = $('#sel_people').val() || [],
                org = $('#sel_org').val() || [],
                loc = $('#sel_loc').val() || [],
                dept = $('#sel_dep').val() || [],
                tag = $('#sel_tag').val() || [],
                skill = $('#sel_skills').val() || [],
                sub = $('#sel_sub').val() || [],
                domain = $('#sel_dom').val() || [];
                story = $('#sel_story').val() || [];

            $._people_filter = {
                user: people,
                org: org,
                loc: loc,
                dept: dept,
                tag: tag,
                skill: skill,
                sub: sub,
                domain: domain,
                story: story
            }
            if(people.length > 0 || org.length > 0 || loc.length > 0 || dept.length > 0 || tag.length > 0 || skill.length > 0 || sub.length > 0 || domain.length > 0 || story.length > 0){
                $.filterBy = true;
                $('#modal_filter_people').modal('hide');
            }
        });

        $('.clear-pfilter').off('click').on('click', function(event) {
            event.preventDefault();
            $._people_filter = {
                user: [],
                org: [],
                loc: [],
                dept: [],
                tag: [],
                skill: [],
                sub: [],
                domain: [],
                story: []
            }
            $('#sel_people').val([]).multiselect('refresh');
            $('#sel_org').val([]).multiselect('refresh');
            $('#sel_loc').val([]).multiselect('refresh');
            $('#sel_dep').val([]).multiselect('refresh');
            $('#sel_tag').val([]).multiselect('refresh');
            $('#sel_skills').val([]).multiselect('refresh');
            $('#sel_sub').val([]).multiselect('refresh');
            $('#sel_dom').val([]).multiselect('refresh');
            $('#sel_story').val([]).multiselect('refresh');
            $('.submit-pfilter').addClass('ftbtndisable');
            $('.filter-icon').removeClass('filterblue').addClass('filterblack');
            $.getPeopleList().done(function(){
                $('#modal_filter_people').modal('hide');
                history.pushState(null, null, $js_config.base_url + 'resources/people');
            });
        });

		$sel_people = $('#sel_people').multiselect({
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
            nonSelectedText: 'Select People'
        });
		$sel_org = $('#sel_org').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Organization',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Organization'
        });
		$sel_loc = $('#sel_loc').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Location',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Location'
        });
		$sel_dep = $('#sel_dep').multiselect({
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
            nonSelectedText: 'Select Departments'
        });
		$sel_tag = $('#sel_tag').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dept[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Tags',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Tags'
        });
		$sel_skills = $('#sel_skills').multiselect({
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
            nonSelectedText: 'Select Skills'
        });
		$sel_sub = $('#sel_sub').multiselect({
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
            nonSelectedText: 'Select Subjects'
        });
        $sel_dom = $('#sel_dom').multiselect({
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
            nonSelectedText: 'Select Domains'
        });
		$sel_story = $('#sel_story').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'story[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search Stories',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select Stories'
        });
	})
</script>
