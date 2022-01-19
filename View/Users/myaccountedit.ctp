<?php echo $this->Html->script('jquery.validate', array('inline' => true)); ?>
<?php //echo $this->Html->script('projects/plugins/wysihtml5.editor', array('inline' => true)); ?>
<?php //echo $this->Html->script('custom_validate', array('inline' => true)); ?>
<?php //echo $this->Html->script('projects/skillpdf', array('inline' => true)); ?>
<?php echo $this->Html->script('projects/typehead.upload', array('inline' => true)); ?>
<?php //echo $this->Html->css('skillpdf'); ?>
<?php echo $this->Html->css('projects/typehead.upload'); ?>
<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>
<style type="text/css">
    .no-data-there {
        pointer-events: none;
    }
    .no-data-there .toltipover {
        pointer-events: none !important;
    }
    .multiselect.btn.disabled {
        background-color: #eeeeee !important;
    }
    #edit_profile_tabs li.disabled a {
        pointer-events: none;
        color: #bcbcbc !important;
    }
	label.error {
        color: #dd4b39;
        font-size: 11px;
        font-weight: normal;
        display: block;
	}
    .email-error {
        color: #dd4b39;
        font-size: 11px;
        font-weight: normal;
        display: block;
        max-width: 100%;
        margin-bottom: 5px;
    }
</style>
<?php
// $admin_user = (isset($this->data['UserDetail']['administrator']) && !empty($this->data['UserDetail']['administrator'])) ? true : false;

if(isset($this->data)){
    $edited_user_id = $this->data['UserDetail']['user_id'];
}

if(isset($_SESSION['data']) && !empty($_SESSION['data'])){
$this->request->data = $_SESSION['data'];}
$reports_to_id = (isset($this->data['UserDetail']['reports_to_id']) && !empty($this->data['UserDetail']['reports_to_id'])) ? $this->data['UserDetail']['reports_to_id'] : 0;
?>
<?php echo $this->Session->flash( ); ?>
<?php
if( !empty($_REQUEST['refer']) ){
    $url = $_REQUEST['refer'];
} else {
    $url = SITEURL . "projects/lists";
}

 ?>
<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'myaccountedit'), 'type' => 'file', 'class' => 'form-horizontal form-bordered user-form', 'enctype' => 'multipart/form-data', 'id' => 'User_add_org')); ?>
<div class="panel panel-primary editprofileinfo">
    <div class="panel-heading">
        <h3 class="panel-title">Edit Profile</h3>
		<div class="editprofile-btn">
		<button type="button" class="btn save-prof">Save</button>
	   <button type="button" data-url="<?php echo $url; ?>" id="discard_updates" class="btn btn-primary cancel-prof" data-dismiss="modal">Cancel</button>
		</div>

    </div>
<div class="editprofileinner">
	<div class="editprofilenav">
	<ul class="nav nav-tabs" id="edit_profile_tabs">
		<li class="active">
			<a data-toggle="tab" data-type="details" class="active skilltab tab-detail" data-target="#editprofile-tab" href="#editprofile-tab" aria-expanded="true">Details</a>
		</li>
        <?php $is_editable = (is_field_editable('interests')) ? true : false; ?>
		<li class="<?php if(!$is_editable){ ?>disabled<?php } ?>">
			<a data-toggle="tab" data-type="links" id="linksTab" data-target="#editinterests-tab" href="#editinterests-tab" aria-expanded="false" class="tab-link">Interests</a>
		</li>
        <?php $is_editable = (is_field_editable('skills')) ? true : false; ?>
		<li class="<?php if(!$is_editable){ ?>disabled<?php } ?>">
			<a data-toggle="tab" data-type="files" id="filesTab" data-target="#editskills-tab" href="#editskills-tab" aria-expanded="false" class="tab-file">Skills</a>
		</li>
        <?php $is_editable = (is_field_editable('subjects')) ? true : false; ?>
		<li class="<?php if(!$is_editable){ ?>disabled<?php } ?>">
			<a data-toggle="tab" data-type="files" id="compTab" data-target="#editsubjects-tab" href="#editsubjects-tab" aria-expanded="false" class="tab-comp">Subjects</a>
		</li>
        <?php $is_editable = (is_field_editable('domains')) ? true : false; ?>
		<li class="<?php if(!$is_editable){ ?>disabled<?php } ?>">
			<a data-toggle="tab" data-type="files" id="compTab" data-target="#editdomains-tab" href="#editdomains-tab" aria-expanded="false" class="tab-comp">Domains</a>
		</li>
	</ul>
		</div>

	<div class="tab-content profile-tab-content">
	 <div class="tab-pane fade active in" id="editprofile-tab">
    <div class="prof-editscroll">
    <?php echo $this->Form->input('User.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
    <?php
	if(isset($this->params->query['refer']) && !empty($this->params->query['refer'])){
	echo $this->Form->input('UserRefer.refer', array('type' => 'hidden', 'label' => false,'value'=>$this->params->query['refer'], 'div' => false, 'class' => 'form-control'));} ?>
    <?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden')); ?>

    <div class=" form-horizontal">
       <!-- <div class="panel-heading"></div>-->


        <div class="details-prof-cont profile-tab-cont">

            <?php $no_org = (!isset($organizations) || empty($organizations)) ? 'no-data-there' : ''; ?>
            <div class="row <?php echo $no_org; ?>">
                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('organization')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserDetailOrganizationId">Organization:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php if( $is_editable ){ ?>
                            <?php echo $this->Form->input('UserDetail.organization_id', array('type' => 'select', 'options' => $organizations, 'empty' => 'Select Organization', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'organization_id')); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('uorg', array('type' => 'select', 'options' => $organizations, 'empty' => 'Select Organization', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'organization_id', 'default' => $this->data['UserDetail']['organization_id'], 'disabled' => true )); ?>
                            <?php echo $this->Form->input('UserDetail.organization_id', array('type' => 'hidden')); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please select organization" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
                <?php
                $profilesPic = $this->Common->get_profile_pic($this->data['User']['id']);
                // pr( $profilesPic);
                $paths = pathinfo($profilesPic);
                $filename = $paths['basename'];
                $upic_not_found = false;
                if(!file_exists(WWW_ROOT . USER_PIC_PATH . $filename)){$upic_not_found = true;}
                if($upic_not_found){
                    $profilesPic = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                }
                ?>
 				<div class="col-md-6">
                    <?php $is_editable = (is_field_editable('image')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="">Image:</label>
                        <div class="col-lg-9 col-sm-12">
                            <?php if($is_editable){ ?>
                           <a class="style-popple-icon tipText" data-remote="<?php echo Router::url(['controller' => 'users', 'action' => 'profile', $edited_user_id, 'admin' => false], true); ?>" data-target="#uimage_modal" data-toggle="modal" title="Click To Change Image">
								<img src="<?php echo $profilesPic; ?>" class="user-image user-own-pic" align="left" width="36" height="36">
							</a>
                            <?php }else{ ?>
                            <a class="style-popple-icon tipText">
                                <img src="<?php echo $profilesPic; ?>" class="user-image user-own-pic" align="left" width="36" height="36">
                            </a>
                            <?php } ?>
                        </div>

                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6 <?php echo $no_org; ?>">
                    <?php $is_editable = (is_field_editable('location')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserDetailLocationId">Location:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('UserDetail.location_id', array('type' => 'select', 'options' => [], 'empty' => 'Select Location', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'location_id')); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('uloc', array('type' => 'select', 'options' => [], 'empty' => 'Select Location', 'label' => false, 'div' => false, 'class' => 'form-control', 'disabled' => true, 'default' => $this->data['UserDetail']['location_id'] )); ?>
                            <?php echo $this->Form->input('UserDetail.location_id', array('type' => 'hidden')); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please select location" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('department')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Department:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php
                            $department = $this->Common->getDepartmentList();
                            $department = array_unique(array_filter($department));
                            // Update array values
                            $department = array_map(function ($v) {
                                return html_entity_decode(html_entity_decode($v, ENT_QUOTES));
                            }, $department);
                            if($is_editable){
                            echo $this->Form->input('UserDetail.department_id', array('options' => $department, 'id' => 'department_dropdown', 'empty' => 'Select Department', 'label' => false, 'div' => false, 'class' => 'form-control'));
                            ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('udept', array('options' => $department, 'id' => 'department_dropdown', 'empty' => 'Select Department', 'label' => false, 'div' => false, 'class' => 'form-control', 'disabled' => true)); ?>
                            <?php echo $this->Form->input('UserDetail.department_id', array('type' => 'hidden')); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please select department" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('first_name')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">First Name:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('UserDetail.first_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('ufn', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'disabled' => true, 'value' => $this->data['UserDetail']['first_name'])); ?>
                            <?php echo $this->Form->input('UserDetail.first_name', array('type' => 'hidden')); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter first name" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('last_name')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Last Name:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('UserDetail.last_name', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control')); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('uln', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'disabled' => true, 'value' => $this->data['UserDetail']['last_name'])); ?>
                            <?php echo $this->Form->input('UserDetail.last_name', array('type' => 'hidden')); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter last name" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('email')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Email:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('User.email', array('type' => 'email', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete'=>'off','id'=>'email', )); ?>
                            <label id="email-error" class="error" for="email"></label>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('User.email', array('type' => 'email', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete'=>'off', 'readonly'=>'true' )); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Email address (Username)" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('contact_number')) ? true : false; ?>
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-3 control-label">Contact Number:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('UserDetail.contact', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'maxlength' => 50)); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('uln', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'disabled' => true, 'value' => $this->data['UserDetail']['contact'], 'maxlength' => 50)); ?>
                            <?php echo $this->Form->input('UserDetail.contact', array('type' => 'hidden')); ?>
                            <?php } ?>
                            <span class="error custom-error"></span>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please enter contact number with no spaces"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('reports_to')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="reports_to_id">Reports To:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('UserDetail.reports_to_id', array('type' => 'select', 'options' => $all_report_users, 'empty' => 'Select Person', 'label' => false, 'div' => false, 'class' => 'form-control', 'id' => 'reports_to_id' )); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('urti', array('type' => 'select', 'options' => $all_report_users, 'empty' => 'Select Person', 'label' => false, 'div' => false, 'class' => 'form-control', 'default' => $this->data['UserDetail']['reports_to_id'], 'disabled'=> true )); ?>
                            <?php echo $this->Form->input('UserDetail.reports_to_id', array('type' => 'hidden')); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please select who this person reports to" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('dotted_lines_to')) ? true : false; ?>
                    <div class="form-group">
                        <label for="UserUser" class="col-lg-3 control-label">Dotted Lines To:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('UserDottedLine.dotted_user_id', array('type' => 'select', 'options' => $all_users, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'dotted_user_id' )); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('udui', array('type' => 'select', 'options' => $all_users, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'default' => $this->data['UserDottedLine']['dotted_user_id'], 'disabled' => true, 'style' => 'height: 36px;', 'id' => 'dotted_user_id')); ?>
                            <?php //echo $this->Form->input('UserDottedLine.dotted_user_id', array('type' => 'select', 'options' => $all_users, 'label' => false, 'div' => false, 'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'dotted_user_id', 'style' => 'display: none' )); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a tabindex="0" data-placement="top" class="btn toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="Please select who this person has dotted line reports to"><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('job_title')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Job Title:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('UserDetail.job_title', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'value' => html_entity_decode(html_entity_decode($this->data['UserDetail']['job_title'] ,ENT_QUOTES)) )); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('ujt', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'value' => html_entity_decode(html_entity_decode($this->data['UserDetail']['job_title'] ,ENT_QUOTES)), 'disabled' => true )); ?>
                            <?php echo $this->Form->input('UserDetail.job_title', array('type' => 'hidden' )); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter job title" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <?php $is_editable = (is_field_editable('job_role')) ? true : false; ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Job Role:</label>
                        <div class="col-lg-8 col-sm-11 ">
                            <?php if($is_editable){ ?>
                            <?php echo $this->Form->input('UserDetail.job_role', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'value' => html_entity_decode(html_entity_decode($this->data['UserDetail']['job_role'] ,ENT_QUOTES)))); ?>
                            <?php }else{ ?>
                            <?php echo $this->Form->input('ujr', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'disabled' => true, 'value' => html_entity_decode(html_entity_decode($this->data['UserDetail']['job_role'] ,ENT_QUOTES)) ) ); ?>
                            <?php echo $this->Form->input('UserDetail.job_role', array('type' => 'hidden' )); ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter job role" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php $is_editable = (is_field_editable('biography')) ? true : false; ?>
                <label class="col-md-1 control-label" style="width: 12.5%;" for="UserClassification">Biography:</label>
                <div class="col-md-10 col-sm-11" style="width:83.35%">
                    <?php if($is_editable){ ?>
                    <?php echo $this->Form->textarea('UserDetail.bio', [ 'class' => 'form-control', 'id' => 'txa_title', 'escape' => true, 'rows' => 7, 'placeholder' => '','style'=>'resize:none;', 'value' => html_entity_decode(html_entity_decode($this->data['UserDetail']['bio'] ,ENT_QUOTES))]); ?>
                    <?php }else{ ?>
					<?php echo $this->Form->textarea('ubio', [ 'class' => 'form-control',  'escape' => true, 'rows' => 7, 'placeholder' => '','style'=>'resize:none;', 'value' => html_entity_decode(html_entity_decode($this->data['UserDetail']['bio'] ,ENT_QUOTES)), 'disabled' => true]); ?>
                    <?php echo $this->Form->textarea('UserDetail.bio', [ 'class' => 'form-control', 'id' => 'txa_title', 'escape' => true, 'rows' => 7, 'placeholder' => '','style'=>'resize:none; display: none;', 'value' => html_entity_decode(html_entity_decode($this->data['UserDetail']['bio'] ,ENT_QUOTES))]); ?>
                    <?php } ?>
                </div>
                <div class="col-lg-1 col-sm-1" style="width:0.333%;margin:0; padding:0">
                    <a data-content="Please enter bio" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                </div>
            </div>

			<div class="row padding-top">
                <?php $is_editable = (is_field_editable('linkedin_url')) ? true : false; ?>
				<label class="col-md-1 control-label" style="width: 12.5%;" for="UserClassification">LinkedIn:</label>
                <div class="col-md-10 col-sm-11" style="width:83.35%">
                    <?php if($is_editable){ ?>
                    <?php echo $this->Form->input('UserDetail.linkedin_url', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=>"https://in.linkedin.com/in/xxxxx" )); ?>
                    <?php }else{ ?>
				    <?php echo $this->Form->input('ulu', array('type' => 'text', 'label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete'=>'off', 'disabled' => true, 'value' => $this->data['UserDetail']['linkedin_url'])); ?>
                    <?php echo $this->Form->input('UserDetail.linkedin_url', array('type' => 'hidden' )); ?>
                    <?php } ?>
                </div>
                <div class="col-lg-1 col-sm-1" style="width:0.333%;margin:0; padding:0">
                    <a data-content="Copy and paste in your LinkedIn Me URL. For example, https://www.linkedin.com/in/xxxxxxx" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="left" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                </div>
			</div>

        </div>


    </div>
</div>

	 </div>
    <?php $is_editable = (is_field_editable('interests')) ? true : false; ?>
	<div class="tab-pane fade" id="editinterests-tab">
		<div class="prof-editscroll">
    		<div class="profile-tab-cont interests-tab-info" >
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-lg-3 control-label" for="UserClassification">Interests:</label>
                            <div class="col-lg-8 col-sm-11">
                                <div class="tag-container <?php if(!$is_editable){ ?>restricted<?php } ?>">
                                    <div class="input-group mb-3 interest-wrapper">
                                      <input type="text" id="userinterest" class="form-control" placeholder="Enter Interest" aria-label="Enter Interest" aria-describedby="basic-addon2" autocomplete="off"  >
                                        <div class="input-group-addon saveinterest" style="">
                                            <i class="addwhite" ></i>
                                        </div>
                                        <div class="input-group-addon bg-red border-red closeinterest hide" >
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    <div class="interest-container"></div>
                                </div>
                                <label id="userinterests-error" class="error" for="SkillSkill"></label>
                            </div>

                            <div class="col-lg-1 col-sm-1">
                                <a data-content="Please enter your personal interests" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
    		</div>
        </div>
    </div>


		<div class="tab-pane fade" id="editskills-tab">
			<div class="prof-editscroll">
			<div class="profile-tab-cont" >
        <div class="row">
            <?php $is_editable = (is_field_editable('skills')) ? true : false; ?>
		 <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Skills:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php
                                $user_subjects = $this->Permission->user_selected_skills($edited_id);
                                $tag_html = '';
                                $tag_ids = '';
                                if(isset($user_subjects) && !empty($user_subjects)){
                                    $tag_html = '<div class="wrapped-container">';
                                    foreach ($user_subjects as $key => $value) {
                                        $tag_detail = $value['lib'];
                                        $user_tag_detail = $value['user_data'];
                                        $sub_detail = $value['details'];
                                        $user_level = '';
                                        $user_experience = '';
                                        $level_text = 'Beginner';
                                        $exp_text = '1 Year';
                                        if( isset($sub_detail) && !empty($sub_detail) ){
                                            $user_level = $sub_detail['user_level'];
                                            $user_experience = $sub_detail['user_experience'];
                                            $level_text = (!empty($user_level)) ? $user_level : 'Beginner';
                                            $exp_text =( $user_experience == 1 || $user_experience == '') ? '1 Year' : $user_experience.' Years';
                                            // e($user_experience);
                                        }

                                        $pdf_count = $value[0]['pdf_count'];


                                        $tag_html .='<div class="tag-rows"><span data-level="'.$user_level.'" data-experience="'.$user_experience.'" data-tagid="'.$user_tag_detail['skill_id'].'" class="tag-text"  ><span class="tag-title">'.htmlentities($tag_detail['title'], ENT_QUOTES, "UTF-8").'</span><br />(<span class="tag-exp">'.$level_text.'</span>, <span class="tag-years">'.$exp_text.'</span>, <span class="tag-pdf">'.$pdf_count.' Files</span>)</span> <span class="tag-delete tipText" title="Delete"><i class="deleteblack"></i></span><span class="tag-edit tipText" title="Edit"><i class="edit-icon"></i></span></div>';
                                        if(count($user_subjects) == ($key - 1)){
                                            e('same');
                                        }
                                        $tag_ids .= $user_tag_detail['skill_id'].',';
                                    }
                                    $tag_html .= '</div>';
                                }

                            ?>
							<div class="tags-wrapper skill-wrapper <?php if(!$is_editable){ ?>restricted<?php } ?>">

								<input type="text" name="skill-input" class="get-tags skill-input" autocomplete="off" placeholder="Click here to search for a Skill" style="padding: 0px 3px 10px 3px;" data-url="<?php echo Router::url(['controller' => 'users', 'action' => 'get_skills', 'admin' => false], true); ?>" data-pdfurl="<?php echo Router::url(['controller' => 'users', 'action' => 'get_skill_pdf', 'admin' => false], true); ?>" data-pdfupload="<?php echo Router::url(['controller' => 'users', 'action' => 'skillpdfupload', 'admin' => false], true); ?>" data-pdfdelete="<?php echo Router::url(['controller' => 'users', 'action' => 'skillpdfdelete', 'admin' => false], true); ?>" />
								<input type="hidden" name="skill_pdf_ids" id="skill_pdf_ids" value="<?php echo substr(trim($tag_ids), 0, -1); ?>" />
								<span class="loader-icon fa fa-spinner fa-pulse"></span>
								<?php echo $tag_html;?>

							</div>
							<label id="SkillSkill-error" class="error" for="SkillSkill"></label>
                        </div>

                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter skills" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
</div>
                </div>
			</div>

	 </div></div>
		<div class="tab-pane fade" id="editsubjects-tab">
			<div class="prof-editscroll">
			<div class="profile-tab-cont">
				<div class="row">
                    <?php $is_editable = (is_field_editable('subjects')) ? true : false; ?>
		 <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Subjects:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php $user_subjects = $this->Permission->user_selected_subjects($edited_id);
                            $tag_html = '';
                            $tag_ids = '';
                            if(isset($user_subjects) && !empty($user_subjects)){
                                $tag_html = '<div class="wrapped-container">';
                                foreach ($user_subjects as $key => $value) {
                                    $tag_detail = $value['lib'];
                                    $user_tag_detail = $value['user_data'];
                                    $sub_detail = $value['details'];
                                    $user_level = '';
                                    $user_experience = '';
                                    $level_text = 'Beginner';
                                    $exp_text = '1 Year';
                                    if( isset($sub_detail) && !empty($sub_detail) ){
                                        $user_level = $sub_detail['user_level'];
                                        $user_experience = $sub_detail['user_experience'];
                                        $level_text = (!empty($user_level)) ? $user_level : 'Beginner';
                                        $exp_text =( $user_experience == 1 || $user_experience == '') ? '1 Year' : $user_experience.' Years';
                                        // e($user_experience);
                                    }

                                    $pdf_count = $value[0]['pdf_count'];


                                    $tag_html .='<div class="tag-rows"><span data-level="'.$user_level.'" data-experience="'.$user_experience.'" data-tagid="'.$user_tag_detail['subject_id'].'" class="tag-text"  ><span class="tag-title">'.htmlentities($tag_detail['title'], ENT_QUOTES, "UTF-8").'</span><br />(<span class="tag-exp">'.$level_text.'</span>, <span class="tag-years">'.$exp_text.'</span>, <span class="tag-pdf">'.$pdf_count.' Files</span>)</span> <span class="tag-delete tipText" title="Delete"><i class="deleteblack"></i></span><span class="tag-edit tipText" title="Edit"><i class="edit-icon"></i></span></div>';
                                    if(count($user_subjects) == ($key - 1)){
                                        e('same');
                                    }
                                    $tag_ids .= $user_tag_detail['subject_id'].',';
                                }
                                $tag_html .= '</div>';
                            }

                            ?>

                            <div class="tags-wrapper subject-wrapper <?php if(!$is_editable){ ?>restricted<?php } ?>">

                                <input type="text" name="subject-input" class="get-tags subject-input" autocomplete="off" placeholder="Click here to search for a Subject" style="padding: 0 3px 10px 3px;" data-url="<?php echo Router::url(['controller' => 'subjects', 'action' => 'get_subjects', 'admin' => false], true); ?>" data-pdfurl="<?php echo Router::url(['controller' => 'subjects', 'action' => 'get_pdf', 'admin' => false], true); ?>" data-pdfupload="<?php echo Router::url(['controller' => 'subjects', 'action' => 'pdf_upload', 'admin' => false], true); ?>" data-pdfdelete="<?php echo Router::url(['controller' => 'subjects', 'action' => 'pdf_delete', 'admin' => false], true); ?>" />
                                <input type="hidden" name="subject_pdf_ids" id="subject_pdf_ids" value="<?php echo substr(trim($tag_ids), 0, -1); ?>" />
                                <span class="loader-icon fa fa-spinner fa-pulse"></span>
								 <?php echo $tag_html;?>
                            </div>
                            <label id="SkillSkill-error" class="error" for="SkillSkill"></label>
                        </div>

                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter subjects" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>

                </div>
				</div>
	 </div>
		</div></div>
		<div class="tab-pane fade" id="editdomains-tab">
			<div class="prof-editscroll">
			<div class="profile-tab-cont">
				<div class="row">
                    <?php $is_editable = (is_field_editable('domains')) ? true : false; ?>
		<div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="UserClassification">Domains:</label>
                        <div class="col-lg-8 col-sm-11">
                            <?php
                                $user_subjects = $this->Permission->user_selected_domains($edited_id);

                                // pr($user_subjects);
                                $tag_html = '';
                                $tag_ids = '';
                                if(isset($user_subjects) && !empty($user_subjects)){
                                    $tag_html = '<div class="wrapped-container">';
                                    foreach ($user_subjects as $key => $value) {
                                        $tag_detail = $value['lib'];
                                        $user_tag_detail = $value['user_data'];
                                        $sub_detail = $value['details'];
                                        $user_level = '';
                                        $user_experience = '';
                                        $level_text = 'Beginner';
                                        $exp_text = '1 Year';
                                        if( isset($sub_detail) && !empty($sub_detail) ){
                                            $user_level = $sub_detail['user_level'];
                                            $user_experience = $sub_detail['user_experience'];
                                            $level_text = (!empty($user_level)) ? $user_level : 'Beginner';
                                            $exp_text =( $user_experience == 1 || $user_experience == '') ? '1 Year' : $user_experience.' Years';
                                            // e($user_experience);
                                        }

                                        $pdf_count = $value[0]['pdf_count'];


                                        $tag_html .='<div class="tag-rows"><span data-level="'.$user_level.'" data-experience="'.$user_experience.'" data-tagid="'.$user_tag_detail['domain_id'].'" class="tag-text"  ><span class="tag-title">'.htmlentities($tag_detail['title'], ENT_QUOTES, "UTF-8").'</span><br />(<span class="tag-exp">'.$level_text.'</span>, <span class="tag-years">'.$exp_text.'</span>, <span class="tag-pdf">'.$pdf_count.' Files</span>)</span> <span class="tag-delete tipText" title="Delete"><i class="deleteblack"></i></span><span class="tag-edit tipText" title="Edit"><i class="edit-icon"></i></span></div>';

                                        $tag_ids .= $user_tag_detail['domain_id'].',';
                                    }
                                    $tag_html .= '</div>';
                                }

                            ?>
                            <div class="tags-wrapper domain-wrapper <?php if(!$is_editable){ ?>restricted<?php } ?>">

                                <input type="text" name="domain-input" class="get-tags domain-input" autocomplete="off" placeholder="Click here to search for a Domain" style="padding: 0 3px 10px 3px;" data-url="<?php echo Router::url(['controller' => 'knowledge_domains', 'action' => 'get_domains', 'admin' => false], true); ?>" data-pdfurl="<?php echo Router::url(['controller' => 'knowledge_domains', 'action' => 'get_pdf', 'admin' => false], true); ?>" data-pdfupload="<?php echo Router::url(['controller' => 'knowledge_domains', 'action' => 'pdf_upload', 'admin' => false], true); ?>" data-pdfdelete="<?php echo Router::url(['controller' => 'knowledge_domains', 'action' => 'pdf_delete', 'admin' => false], true); ?>" />
                                <input type="hidden" name="domain_pdf_ids" id="domain_pdf_ids" value="<?php echo substr(trim($tag_ids), 0, -1); ?>" />
                                <span class="loader-icon fa fa-spinner fa-pulse"></span>
								<?php echo $tag_html;?>
                            </div>
                            <label id="SkillSkill-error" class="error" for="SkillSkill"></label>
                        </div>

                        <div class="col-lg-1 col-sm-1">
                            <a data-content="Please enter domains" title="" data-trigger="hover" data-toggle="popover" role="button" class="btn  toltipover" data-placement="top" tabindex="0" data-original-title=""><i class="fa fa-info fa-3 martop"></i></a>
                        </div>
                    </div>
 </div>
                </div></div>
			</div>
	 </div>

	</div>

</div>








</div>
<?php echo $this->Form->end(); ?>

<div class="dummy-skill-html">
	<h5 class="title"></h5>
	<form id="skillpdf">
		<div class="manage-box-in">
			<div class="management-attach" style="padding-bottom: 6px; padding-right: 42px;">
				<div class="row">
    				<div class="col-xs-6 col-sm-6" style="padding-right: 3px;">
    					<label class="control-label" for="level">Level:</label>
    					<select class="form-control user_level" name="user_level" id="userskilllevel">
    						<option value="Beginner" >Beginner</option>
    						<option value="Intermediate">Intermediate</option>
    						<option value="Advanced">Advanced</option>
    						<option value="Expert">Expert</option>
    					</select>
    				</div>
    				<div class="col-xs-6 col-sm-6" style="padding-left: 3px;"><label class="control-label" for="experience">Experience:</label>
    					<select class="form-control user_experience" name="user_experience" id="userskillexperience">
    						<option value="1" >1 Year</option>
    						<option value="2">2 Years</option>
    						<option value="3">3 Years</option>
    						<option value="4">4 Years</option>
    						<option value="5">5 Years</option>
    						<option value="6-10">6-10 Years</option>
    						<option value="11-15">11-15 Years</option>
    						<option value="16-20">16-20 Years</option>
    						<option value="Over 20">Over 20 Years</option>
    					</select>
    				</div>
    				<div class="save-action-btn">
    					<span class="save-button saveskillsdata tipText" title="Save" ><i class="savewhite24" ></i></span>
    				</div>
    			</div>
				<span class="user_skill_error text-danger nopadding">...</span>

			</div>
			<div class="row" style="border-bottom: 1px solid #3c8dbc; margin-bottom:8px;"></div>
			<div class="pull-left" style="font-weight: 700;">Attachments:</div>
			<ul class="pdf-list attachments-pdf"></ul>
			<div class="management-attach-file">
				<div class="attach-file-sec">
					<div class="btn btn-file">
						Choose File
						<input type="file" id="pdf_file" name="pdf_file" value="Choose File" accept="application/pdf" class="form-control">
					</div>
					<input type="text" class="form-control" name="pdf_name">
				</div>

				<span class="download-action">
					<span class="save-button savepdf btns-gray tipText" title="Upload"><i class="uploadwhite24"></i></span>
				</span>
			</div>
			<div class="pdf-size">5 pdfs limit and max. 5MB each</div>

		</div><input type="hidden" class="form-control skill_id" name="skill_id" value="">
	</form>
</div>

<?php $org_id = (isset($this->data['UserDetail']['organization_id']) && !empty($this->data['UserDetail']['organization_id'])) ? $this->data['UserDetail']['organization_id'] : 0; ?>
<?php $loc_id = (isset($this->data['UserDetail']['location_id']) && !empty($this->data['UserDetail']['location_id'])) ? $this->data['UserDetail']['location_id'] : 0; ?>

<style type="text/css">
    .multiselect-container.dropdown-menu > li:not(.multiselect-group) {
        display: flow-root;
        width: 100% !important;
    }
</style>
<?php $dotted_is_editable = (is_field_editable('dotted_lines_to')) ? 1 : 2; ?>
<script type="text/javascript" >
    var dotted_is_editable = '<?php echo $dotted_is_editable; ?>';
    var edited_user_id = '<?php echo $edited_user_id; ?>';
$(function(){

	$(".user_skill_error", $('.management-box')).addClass('text-danger').removeClass('text-success').hide();

	$( "body" ).delegate( ".management-attach select", "change", function() {

		$('.saveskillsdata').removeClass('disable').removeAttr('style'); ;
	})
    // if(dotted_is_editable == 1){
        $dotted_user_id = $('#dotted_user_id').multiselect({
            enableUserIcon: false,
            buttonClass: 'btn btn-default aqua',
            buttonWidth: '100%',
            buttonContainerWidth: '100%',
            numberDisplayed: 2,
            maxHeight: '318',
            checkboxName: 'dotted_user_id[]',
            includeSelectAllOption: true,
            enableFiltering: true,
            filterPlaceholder: 'Search People',
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: 'Select People',
            onSelectAll:function(){
            },
            onDeselectAll:function(){
            },
            onChange: function(element, checked) {
            }
        });
    // }
    var orga_id = '<?php echo $org_id; ?>';
    var loc_id = '<?php echo $loc_id; ?>';
    $('#organization_id').on('change', function(event) {
        event.preventDefault();
        var org_id = $(this).val();
        if(org_id != '' && org_id != undefined) {
            $.org_location(org_id);
            $.validate_org_email();
        }
        else{
            $('#location_id').empty();
            $('#location_id').append('<option value="">Select Location</option>');
        }
    });

    function sort_arr_obj (a, b){
        var aName = a.name.toLowerCase();
        var bName = b.name.toLowerCase();
        return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
    }

    $.validate_org_email = function(){
        var orgid = $('#organization_id').val();
        var email = $('#email').val();
        var uid = $('#UserId').val();

        $('#UserEmail-error,#email-error').text('')
        $.ajax({
            url: $js_config.base_url + 'users/validate_email',
            type: 'POST',
            dataType: 'json',
            data: {orgid: orgid, 'data[User][email]': email, uid: uid},
            success: function(response){
                console.log('response', response)
                if(response == false){
                    if($('#UserEmail-error').length <= 0){
                        $('#email-error').text('Your email address is invalid or already taken.')
                    }
                    else{
                        $('#UserEmail-error').text('Your email address is invalid or already taken.').show()
                    }
                }
            }
        })
    }

    ;($.org_location = function(org_id){
        $.ajax({
            url: $js_config.base_url + 'users/org_location',
            type: 'POST',
            dataType: 'json',
            data: {org_id: org_id},
            success: function(response){
                if(response.success){
                    $('#location_id').empty();
                    if(response.content){
                        var content = response.content.sort(sort_arr_obj);
                        $('#location_id').append('<option value="">Select Location</option>');
                        $('#location_id').append(function() {
                            var output = '';
                            $.each(content, function(key, value) {
                                var selected = '';
                                if(loc_id == value.id){
                                    selected = 'selected="selected"';
                                }
                                output += '<option value="' + value.id + '" '+selected+'>' + value.name + '</option>';
                            });
                            return output;
                        });
                    }
                }
            }
        })

    })(orga_id)

})




// Submit Add Form
    $("#uimage_modal").on("hidden.bs.modal", function() {
        $(this).find('.modal-content').html('')
        $(this).removeData("bs.modal")
    });
    $("#Userd").submit(function (e) {
        var postData = new FormData($(this)[0]);
        var formURL = $(this).attr("action");

        $.ajax({
            url: formURL,
            type: "POST",
            data: postData,
            success: function (response) {
                if ($.trim(response) != 'success') {
                    $('#uimage_modal').html(response);
                } else {
                    location.reload(); // Saved successfully
                }
            },
            cache: false,
            contentType: false,
            processData: false,
            error: function (jqXHR, textStatus, errorThrown) {
                // Error Found
            }
        });
        e.preventDefault(); //STOP default action
        //e.unbind(); //unbind. to stop multiple form submit.
    });


</script>
<style>

    .select2-selection__choice__remove {
            float: right !important;
            padding: 0 0 0 5px;
    }
    .pdf-icon {
        background: url(../images/lightbulb.png) no-repeat left center;
        width: 11px;
        height: 11px;
        display: inline-block;
        vertical-align: top;
        margin-left: 4px;
    }
    .interest-wrapper .saveinterest  {
        border-color: #3c8dbc;
		background-color: #3c8dbc;
        cursor: pointer;
    }
    .interest-error {
        border-color: #d73925;
    }
    .not_editable {
        cursor: not-allowed;
    }
    .not_editable #userinterest, .not_editable .saveinterest  {
        pointer-events: none;
    }
    .dummy-skill-html {
        display: none;
    }
    .user_skill_error {
        display: none;
    }
    .loading-bar{
        display:none;
    }
    .modal-open .modal ,.fade.in{
        /* overflow-x: hidden !important;
        overflow-y: auto !important; */
    }


    .form-group .col-lg-1 {
        padding: 0;
    }

    .fa-info {
        background: #3c8dbc none repeat scroll 0 0;
        border-radius: 50%;
        color: #fff;
        font-size: 13px;
        height: 22px;
        line-height: 24px;
        width: 22px;
    }

    #User label { font-size : 13px ;}

	.interest-confirm {
        display: none;
    }
    .btn-confirm {
        padding: 1px 4px;
        border-radius: 50%;
    }
    .no-scroll {
        overflow: hidden;
    }
    .prof-editscroll {
        min-height: 600px;
    }
    /*.profile-tab-content .tab-pane {
        overflow: auto;
        min-height: 600px;
    }*/
    .saveinterest.disable {
        opacity: 0.5;
        pointer-events: none;
    }

</style>
<script type="text/javascript">
    $(function(){
        $("#UserDetailLinkedinUrl").on('focus', function(event) {
            event.preventDefault();
            if($(this).val() == '') {
                $(this).val('https://')
            }
        }).on('blur', function(event) {
            event.preventDefault();
            if($(this).val() == 'https://') {
                $(this).val('')
            }
        });
    })


$(function() {

    $.checkInterestCount().done(function(response){
        if(response == false){
            $(".interest-wrapper").addClass('not_editable');
        }
    })
	$('.saveinterest').on('click', function (event) {
		event.stopImmediatePropagation();
		event.preventDefault();

        var $this = $(this);
        $this.addClass('disable');

		var $userinerest = $.trim($("#userinterest").val());
		$("#userinterest").val('');

		if( $userinerest.length > 0  ){

			var $interestid = '';
			if( $(".interest-container").find(".active").data("interestid") ){
				var $interestid = $(".interest-container").find(".active").data("interestid");
			}
			var save = false;
            $.checkInterestCount().done(function(status){
                if($interestid != ''){
                    save = true;
                }
                else if( status == true ) {
                    save = true;
                }
                if(save){
                    // $(".interest-container").html('<div class="loading-bar"></div>');
                    $("#userinterest").removeClass('interest-error');
        			$.ajax({
        				url: $js_config.base_url + 'users/save_interest',
        				type: "POST",
        				data: $.param({'interest-title':$userinerest, 'interest-id':$interestid, edited_id: $js_config.edited_id}),
        				dataType: "JSON",
        				global: false,
        				success: function(response) {
        					$("#userinterest").val('');
							$(".closeinterest").addClass('hide');
                            $.showUserInterests();
                            $this.removeClass('disable')
                            $.checkInterestCount().done(function(response){
                                if(response == false){
                                    $(".interest-wrapper").addClass('not_editable');
                                }
                            })
        				}
        			})

                } else {
                    $(".interest-wrapper").addClass('not_editable');
                }
            })

		} else {
			$("#userinterest").addClass('interest-error');
		}
	});

    $( "body" ).delegate( "#userinterest", "keyup", function(event) {
        event.preventDefault();
        var code = event.keyCode || event.which;
        if(code == 13) {
            $('.saveinterest').trigger('click');
        }
    })

    $( "body" ).delegate( ".save-prof", "click", function(event) {
        $(".user-form").submit();
    });

	$( "body" ).delegate( ".interest-edit", "click", function() {
		event.preventDefault();

		$(this).parents('.interest-container').find('.interest-row').removeClass('active');
		$(this).parents('.interest-container').find('.interest-row').find('.interest-confirm').hide('slide', {direction: 'right'}, 600);

			$("#userinterest").removeClass("interest-error");
			var $btn_txt = $(this).parents('.interest-row:first');
			var $interestUpdateVal = $btn_txt.find(".interest-text").text();
			var $interestUpdateValId = $btn_txt.data(".interestid");

			$("#userinterest").val($interestUpdateVal).focus();
			$btn_txt.addClass('active');
			$("#userinterest").parent().removeClass('not_editable');
			$(".closeinterest").removeClass('hide');

	});

	$( "body" ).delegate( ".closeinterest", "click", function() {
		event.preventDefault();

		$(this).parents('.interest-container').find('.interest-row').find('.interest-confirm').hide('slide', {direction: 'right'}, 600);

		var $btn_txt = $(".interest-container").find('.active');
		$("#userinterest").val('');
		$btn_txt.removeClass('active');
		$(".closeinterest").addClass('hide');
		$("#userinterest").removeClass("interest-error");
		$.checkInterestCount().done(function(response){
			if(response == false){
				$(".interest-wrapper").addClass('not_editable');
			}
			else{
				$(".interest-wrapper").removeClass('not_editable');
			}
		})

	});

	$( "body" ).delegate( ".interest-delete", "click", function() {
		event.preventDefault();

		$(".confirm-no").trigger('click');

		$(this).parents('.interest-container').find('.interest-row').removeClass('active');
		$(".closeinterest").addClass('hide');
		$("#userinterest").val('');
		$(this).parents('.interest-container').find('.interest-row').find('.interest-confirm').hide('slide', {direction: 'right'}, 600);

		var $btn_grp = $(this).parents('.interest-row:first');
		$('.interest-confirm', $btn_grp).show('slide', {direction: 'right'}, 600);

		//$(this).parents('.interest-container').find('.interest-row').find('.interest-confirm');
		$(this).hide();

		$(".interest-edit",$btn_grp).hide();

	});


	$( "body" ).delegate( ".confirm-no", "click", function() {
		event.preventDefault();
		var $btn_grp = $(this).parents('.interest-row:first');
		$('.interest-confirm', $btn_grp).hide('slide', {direction: 'right'}, 600,function(){
			$(".interest-delete", $btn_grp).show();
			$(".interest-edit", $btn_grp).show();
		});

	});

	$( "body" ).delegate( ".confirm-yes", "click", function() {
		event.preventDefault();
		var $btn_grp = $(this).parents('.interest-row:first'),
		$list = $(this).parents('.interest-row:first'),
		data = $list.data(),
		interestid = data.interestid;

		$.ajax({
			url: $js_config.base_url + 'users/delete_user_interest',
			type: 'POST',
			dataType: 'json',
			data: {id: interestid},
			success: function(response) {
				$('.interest-confirm', $btn_grp).hide('slide', {direction: 'right'}, 600, function(){
					$list.slideUp(300, function(){
						$(this).remove();
                        $.checkInterestCount().done(function(response){
                            if(response == false){
                                $(".interest-wrapper").addClass('not_editable');
                            }
                            else{
                                $(".interest-wrapper").removeClass('not_editable');
                            }
							if( $(".interest-row").length == 0 ){
								$html = '<div class="interest-row no-interest-record">No interests found</div>';
								$(".interest-container").html($html);
							}
                        })
					})
				});
			}
		})

	})



	$.sortSkillList = function() {

		var newitems = $("#User").find('.tag-container-skills').html();
		var birdList = $(".tag-container-skills"); //get birds unordered list
		var newBirdItem = $(newitems, {'style': 'display: none'}); // create new li element
		var bird = newitems // get data from form field
		birdList.append(newBirdItem); // append li element to bird list
		$(newBirdItem).hide();
		$(birdList).find("div").sort(function(a, b) {
			return $(a).text().toLowerCase().localeCompare($(b).text().toLowerCase());
		}).each(function() {
			$(birdList).append(this);
		});
		$(newBirdItem).fadeIn(300)
	}

	//$.sortSkillList();


	$.sortListAccount = function($elem) {

			$elem.sort(function (a, b) {
				var contentA = $(a).attr('data-title').toLowerCase();
				var contentB = $(b).attr('data-title').toLowerCase();
				var fullcontent =  (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
			}).append($(".tag-container-skills")).show()

			console.log(fullcontent);

		}

	var elem = $('.tag-container-skills').find('.tag-row');

	if(elem.length > 0  ){
		//$.sortListAccount(elem);
	}



});


    ($.showUserInterests = function(){
        var dfd = new $.Deferred();
        $.ajax({
            url: $js_config.base_url + 'users/user_interest_list/' + $js_config.edited_id,
            type: 'POST',
            dataType: 'json',
            data: {},
            success: function(response) {
                $(".interest-container").html(response);
                dfd.resolve('done');
            }
        })
        return dfd.promise();
    })();

    $.checkInterestCount = function(){
        var dfd = new $.Deferred();
        $.ajax({
            url: $js_config.base_url + 'users/check_interest_count/' + $js_config.edited_id,
            type: 'POST',
            dataType: 'json',
            data: {},
            success: function(response) {
                dfd.resolve(response.success);
            }
        })
        return dfd.promise();
    }
 $(function () {



    $("#UserDetailFirstName").keypress(function(event) {
        var character = String.fromCharCode(event.keyCode);
        return isValid(character);
    });
    $("#UserDetailLastName").keypress(function(event) {
        var character = String.fromCharCode(event.keyCode);
        return isValid(character);
    });

    function isValid(str) {
        return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
    }

    $("#UserDetailFirstName").bind('paste', function(e) {
        var character = e.originalEvent.clipboardData.getData('Text');
           return isValid(character);
    });

    $("#UserDetailLastName").bind('paste', function(e) {
        var character = e.originalEvent.clipboardData.getData('Text');
           return isValid(character);
    });

    $('body').delegate('#UserDetailJobTitle', 'keyup focus', function(event){
        var characters = 100;
        event.preventDefault();
        var $error_el = $(this).parent().find('.error');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    });
    $('body').delegate('.term', 'keyup focus', function(event){
        var characters = 750;
        event.preventDefault();
        var $error_el = $(this).parent().find('.error');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
        if($(this).val() == '' || $(this).val() === undefined) {
            $(this).attr('placeholder', 'Begin typing here to search for a Skill');
        }
    });

    $('body').delegate('.term', 'click', function(event){
        var val = $(this).val();
        if(val == '' || val === undefined) {
            $(this).attr('placeholder', '');
        }
    });

    $('body').delegate('.term', 'blur', function(event){
        var val = $(this).val();
        if(val == '' || val === undefined) {
            $(this).attr('placeholder', 'Begin typing here to search for a Skill');
        }
    });
    $('body').delegate('#UserDetailLinkedinUrl', 'keyup focus', function(event){
        var characters = 750;
        event.preventDefault();
        var $error_el = $(this).parent().find('.error');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    });
	$('body').on('change','#department_dropdown', function(event){
		//$('input[name="data[UserDetail][department]"]').val( $(this).find('option:selected').text() );
    });

});

$('html').scrollTop(0);
$(function(){
    $('#edit_profile_tabs').removeAttr('style');
    $('html').addClass('no-scroll');

    // RESIZE MAIN FRAME
    ($.adjust_resize = function(){
        var $scroll_wrapper = $('.prof-editscroll', $('.tab-pane:visible'));
        $scroll_wrapper.animate({
            minHeight: (($(window).height() - $scroll_wrapper.offset().top) ) - 37,
            maxHeight: (($(window).height() - $scroll_wrapper.offset().top) ) - 37
        }, 1)
    })();

    // WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
    var interval = setInterval(function() {
        if (document.readyState === 'complete') {
            $.adjust_resize();
            clearInterval(interval);
        }
    }, 1);

    // RESIZE FRAME ON SIDEBAR TOGGLE EVENT
    $(".sidebar-toggle").on('click', function() {
        // $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1500);
    })

    // RESIZE FRAME ON WINDOW RESIZE EVENT
    $(window).resize(function() {
        $.adjust_resize();
    })


    $("#edit_profile_tabs").on('show.bs.tab', function(e){
        // $.adjust_resize();
        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
        setTimeout( () => clearInterval(fix), 1000);
    })

    var $activeTab = null;
    $("#edit_profile_tabs").on('shown.bs.tab', function (e) {
        $activeTab = $(e.target);
        var $ele = $($activeTab.attr('href')).find('.prof-editscroll');
    })
    var reports_to_id = '<?php echo $reports_to_id ?>';

    function sort_arr_obj (a, b){
        var aName = a.label.toLowerCase();
        var bName = b.label.toLowerCase();
        return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
    }

    ;($.dottect_users = (reports_to) => {
        if(reports_to == '' || reports_to == 0 || reports_to == undefined) return;
        var dfd = new $.Deferred();
        $.ajax({
            url: $js_config.base_url + 'users/dottect_users',
            type: 'POST',
            dataType: 'json',
            data: {reports_to: reports_to, edited_user_id: edited_user_id},
            success: function(response) {
                var content = response.content.sort(sort_arr_obj);
                // if(dotted_is_editable == 1){
                    $("#dotted_user_id").multiselect('dataprovider', content);
                // }
                setTimeout(()=>{
                    if(dotted_is_editable == 1){
                        $("#dotted_user_id").val(response.dotted_users).multiselect('select', response.dotted_users).multiselect("refresh");
                    }
                    else{
                        $("#dotted_user_id").val(response.dotted_users).multiselect('select', response.dotted_users).multiselect("refresh").multiselect('disable');
                    }
                }, 1);
                dfd.resolve(response);
            }
        })
        return dfd.promise();
    })(reports_to_id)

    $('#reports_to_id').on('change', function(event) {
        event.preventDefault();
        var val = $(this).val();
        if(val != '' && val != 0 && val != undefined){
            $.dottect_users(val);
        }
    });

    $('#discard_updates').on('click', function(event) {
        event.preventDefault();
        var url = $(this).data('url');
        location.href = url;
    });

    ;($.report_to_users = () => {
        var dfd = new $.Deferred();
        $.ajax({
            url: $js_config.base_url + 'users/report_to_users',
            type: 'POST',
            dataType: 'json',
            data: {edited_user_id: edited_user_id},
            success: function(response) {
                var content = response.content.sort(sort_arr_obj);
                var selected_id = response.reports_to_id;

                $('#reports_to_id').empty();
                    $('#reports_to_id').append('<option value="">Select Person</option>');

                    if (content != null) {
                        $('#reports_to_id').append(function() {

                            var output = '';

                            $.each(content, function(key, value) {
                                var sel = '';
                                if (value.value == selected_id) {
                                    sel = 'selected="selected"';
                                }
                                output += '<option ' + sel + ' value="' + value.value + '">' + value.label + '</option>';
                            });
                            return output;
                        });
                        // $('#reports_to_id').prop('disabled', false);
                    }

                dfd.resolve(response);
            }
        })
        return dfd.promise();
    })();



$("#User_add_org").validate({

rules: {

		'data[UserDetail][first_name]': "required",
		'data[UserDetail][last_name]': "required",
		'data[User][email]': {
			required: true,
			email: true,
			"remote":
			{
			  url: $js_config.subdomain_base_url+'users/validate_email',
			  type: "post",
			  crossDomain: true,

			  data:
			  {
				  email: function(response)
				  {  // console.log(response);
					  return $('#User :input[name="data[User][email]"]').val();
				  },
                  orgid: function(response){
                        return $('#organization_id').val();

                  },
                  uid: function(response){
                        return $('#UserId').val();

                  }
			  }
			}

		},
},
messages: {

		'data[UserDetail][first_name]': {
			required: "First Name is required",
		},
		'data[UserDetail][last_name]': {
			required: "Last Name is required",
		} ,

		'data[User][email]': {
			required: "Email is required",
			email: "Your email address must be in the format of name@domain.com",
			remote: jQuery.validator.format("Your email address is invalid or already taken")

		}

	}

})

})


</script>
<div class="modal modal-success fade" id="uimage_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog profile-view-header ">
    <!-- <div class="modal-dialog modal-lg profile-hack"> -->
        <div class="modal-content"></div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div>