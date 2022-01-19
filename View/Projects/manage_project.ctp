<?php

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/manage_project', array('inline' => true));
echo $this->Html->css(array(
	'projects/edit_project',
));

if(isset($_SESSION['data']) && !empty($_SESSION['data'])){
	$this->request->data = $_SESSION['data'];
}
/*
	function asrt($a, $b) {
		$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a['title']);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b['title']);
	    return strcasecmp($t1, $t2);
	}

	$manage_project = $manage_project[0];
	// pr($manage_project);
	$others = $manage_project[0];
	$all_currencies = $all_ptypes = $all_progs = $all_types = $sel_types = [];
	if(!empty($others['all_currencies'])){
		$all_currencies = json_decode($others['all_currencies'], true);
		$all_currencies = Set::combine($all_currencies, '{n}.id', '{n}.title');
	}
	if(!empty($others['all_ptypes'])){
		$all_ptypes = json_decode($others['all_ptypes'], true);
		$all_ptypes = Set::combine($all_ptypes, '{n}.id', '{n}.title');
	}
	if(!empty($others['all_progs'])){
		$all_progs = json_decode($others['all_progs'], true);
		$all_progs = Set::combine($all_progs, '{n}.id', '{n}.title');
	}
	if(!empty($others['all_currencies'])){
		$all_currencies = json_decode($others['all_currencies'], true);
		$all_currencies = Set::combine($all_currencies, '{n}.id', '{n}.title');
	}
	if(isset($project_id) && !empty($project_id)) {
		$all_types = (!empty($manage_project['all_types']['all_type'])) ? json_decode($manage_project['all_types']['all_type'], true) : [];
		$sel_types = (!empty($manage_project['sel_types']['sel_type'])) ? explode(",", $manage_project['sel_types']['sel_type']) : [];
		// pr($sel_types);
	}
	else{
		$all_types = (!empty($others['all_types'])) ? json_decode($others['all_types'], true) : [];
	}
	if(isset($all_types) && !empty($all_types)) {
		$all_types = Set::combine($all_types, '{n}.id', '{n}.title');
	}
	asort($all_currencies);
	asort($all_ptypes);
	asort($all_progs);
	asort($all_types);
*/


$user_id = $this->Session->read('Auth.User.id');
$timeZone = getTimezoneDetail('Timezone',$user_id,'name');

$timeZone  = 'Europe/London';

if((!isset($timeZone) || empty($timeZone)) || ($timeZone ==  'Etc/Unknown')){
	$timeZone = 'Europe/London';
}

$projects_signoff = '';
if( isset($this->request->data['Project']['sign_off']) && !empty($this->request->data['Project']['sign_off']) && $this->request->data['Project']['sign_off'] == 1 ){
	$projects_signoff = 'disabled';
}
$is_owner = false;
$url_project = '';
if (isset($project_id)) {
    $is_owner = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
    $original_owner = $this->Common->userprojectOwner($project_id, $this->Session->read('Auth.User.id'));
    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
    $url_project = $project_id;
}

?>
<div class="row">
	<div class="col-xs-12">
		<?php echo $this->Session->flash(); ?>
		<?php
        echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'manage_project', $url_project), 'role' => 'form', 'id' => 'frm_manage_project', 'class' => 'clearfix'));
        ?>
        <?php echo $this->Form->input('UserProject.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => '')); ?>
        <?php echo $this->Form->input('Project.id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => '')); ?>
		<div class="box-content edit-project-cont">
			<div class="panel panel-primary ">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo (isset($project_id)) ? 'Edit' : 'Add'; ?> Project</h3>
					<div class="edit-project-btn">
						<button class="btn save-prof <?php echo $projects_signoff; ?>" type="submit"><?php echo (isset($project_id)) ? 'Save' : 'Add'; ?></button>
						<a class="btn btn-primary cancel-prof" href="<?php echo (isset($project_id) && !empty($project_id)) ? Router::Url(array('controller' => 'projects', 'action' => 'index', $project_id, 'admin' => FALSE), TRUE) : Router::Url(array('controller' => 'projects', 'action' => 'lists', 'admin' => FALSE), TRUE); ?>" >Cancel</a>
					</div>
				</div>

				<div class="edit-project-inner">
					<div class="row">
						<div class="edit-project-col-1">
							<div class="form-group">
								<label class="control-label" for="UserClassification">Title: <sup>*</sup></label>
								<?php
								echo $this->Form->input('Project.title', ['type'=> 'text', 'class' => 'form-control', 'id' => 'txa_title', 'required' => false, 'placeholder' => '100 chars', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'maxlength' => 100]); ?>
							</div>
							<div class="form-group">
								<label class="control-label" for="UserClassification">Description: <sup>*</sup></label>
								<?php echo $this->Form->textarea('Project.description', [ 'class' => 'form-control', 'id' => 'txa_objective', 'required' => false, 'escape' => true, 'rows' => 3, 'placeholder' => '500 chars', 'maxlength' => 500]); ?>
								<span class="error-message error text-danger"></span>
                        		<?php echo $this->Form->error('Project.description'); ?>

							</div>
							<div class="form-group">
								<label class="control-label" for="UserClassification">Outcome: <sup>*</sup></label>
								<?php echo $this->Form->textarea('Project.objective', [ 'class' => 'form-control', 'id' => 'txa_description', 'required' => false, 'escape' => true, 'rows' => 10, 'placeholder' => '500 chars', 'maxlength' => 500 ] ); ?>
								<span class="error-message error text-danger" ></span>
                                    <?php echo $this->Form->error('Project.objective'); ?>
							</div>
						</div>
						<div class="edit-project-col-2">
							<div class="form-group">
								<div class="edit-form-group">
									<div class="edit-column-col">
										<label class="control-label" for="UserClassification">Start Date: <sup>*</sup></label>
										<div class="input-group">
											<?php echo $this->Form->input('Project.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date', 'required' => false, 'readonly' => 'readonly', $projects_signoff, 'class' => 'form-control dates input-small']); ?>
											<div class="input-group-addon <?php if( empty($projects_signoff) ){?>  open-start-date-picker calendar-trigger<?php } ?>">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
									</div>
									<div class="edit-column-col">
										<label class="control-label" for="UserClassification">End Date: <sup>*</sup></label>
										<div class="input-group">
											<?php echo $this->Form->input('Project.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date', 'required' => false, 'readonly' => 'readonly', $projects_signoff, 'class' => 'form-control dates input-small']); ?>
											<div class="input-group-addon <?php if( empty($projects_signoff) ){?>  open-end-date-picker calendar-trigger<?php } ?>">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="edit-form-group">
									<div class="edit-column-col">
										<label class="control-label" for="UserClassification">Type: <sup>*</sup></label>
										<?php
										echo $this->Form->input('Project.aligned_id', array(
											'options' => $aligneds,
											'empty' => 'Select Type',
											'type' => 'select',
											'required' => false,
											'label' => false,
											'div' => false,
											'class' => 'form-control'
										));
										?>
									</div>
									<div class="edit-column-col">
										<label class="control-label" for="UserClassification">Currency: <sup>*</sup></label>
										<?php
										$currencylist =  array_map('utf8_decode', $this->Common->getcurrencybyid());
										echo $this->Form->input('Project.currency_id', array(
												'options' => $currencylist,
												'empty' => 'Select Currency',
												'type' => 'select',
												'required' => false,
												'label' => false,
												'div' => false,
												'class' => 'form-control'
											));
										?>
									</div>
								</div>
							</div>
							<div class="form-group task-type-project">
								<label class="task-type-label" >
									<span class="risktype">Task Types in Project:  <sup>*</sup></span>
									<span class="addupdate">
										<ul class="nav nav-tabs" id="tab_tt_add_edit">
											<li class="active"><a href="#select_task_types" data-toggle="tab" aria-expanded="true">Select </a></li>
											<li class=""><a href="#add_task_type" class="" data-toggle="tab" lister="lister" aria-expanded="false">Add</a></li>
											<li class=""><a href="#update_types" class="edit-task-types" data-toggle="tab" lister="lister" aria-expanded="false">Edit</a></li>
										</ul>
									</span>
								</label>
								<div id="select_task_types" class="tab-pane fade active in popup-select-icon">
									<select class="form-control project_types" style="height:30px;display:none;"  id="project_types" multiple="multiple" name="data[ProjectElementType][id][]">
									<?php
										if( isset($project_id) && !empty($project_id) ){

											if(isset($projectType) && !empty($projectType)) {
												foreach($projectType as $k => $v){
													$selected = $data_opt = '';
													if(in_array($k, array_values($projectElementTypeSelected)) ){
														$selected = 'selected="selected"';
														$usedType = custom_type_involved($k,$project_id);
														if(!empty($usedType)){
														$selected = 'selected="selected" disabled';
														}

													}
													if($v == 'General') {
														$data_opt = 'data-disabled="1" ';
													}
									?>
									<option value="<?php echo $k; ?>"  <?php echo $selected; ?> <?php echo $data_opt; ?> ><?php echo htmlentities($v); ?></option>
									<?php
											}
										}

									} else {
										if(isset($projectType) && !empty($projectType)) {
											$atype = [];
											if(isset($projectElementTypeSelected) && !empty($projectElementTypeSelected)){
												$atype = array_values($projectElementTypeSelected);
											}
											foreach($projectType as $k => $v){

												$selected = $data_opt = '';
												if(in_array($v['ProjectElementTypeTemp']['id'], $atype) ){
													$selected = 'selected="selected"';
												}

												if($v == 'General') {
													$data_opt = 'data-disabled="1" ';
												}

												?>
													<option value="<?php echo $v['ProjectElementTypeTemp']['id']; ?>" <?php echo $selected;?> <?php echo $data_opt; ?> ><?php echo htmlentities($v['ProjectElementTypeTemp']['title']); ?></option>
										<?php
												}
											}
									}
									?>
									</select>
									<?php
									if(isset($this->validationErrors['ProjectElementType']) && !empty($this->validationErrors['ProjectElementType'])){ ?>
										<span class="error-message error text-danger"><?php echo $this->validationErrors['ProjectElementType']['id']; ?></span>
									<?php } else if (isset($this->request->data['error']['eletasktype']) && !empty($this->request->data['error']['eletasktype'])){ ?>
										<span class="error-message error text-danger"><?php echo $this->request->data['error']['eletasktype']; ?></span>
									<?php } ?>
								</div>
								<div id="add_task_type" class="tab-pane fade">
									<div class="project-task-add">
										<input type="text" class="form-control risk-types-input" id="RiskTypes" maxlength="50" placeholder="50 chars" autocomplete="off">
										<span class="iocn-add-clear">
											<a href="#"><i class="workspace-icon tipText ico-types ico-add-type" title="Add"></i></a>
											<a href="#"><i class="clearblackicon tipText ico-types ico-clear-type" title="Clear"></i></a>
										</span>
									</div>
								</div>
								<div id="update_types" class="tab-pane fade">
									<div class="rm-type-dd">
										<span class="selected-type">No Task Type found</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label" for="UserClassification">Color Theme:</label>
								<?php echo $this->Form->input('Project.color_code', [ 'type' => 'hidden', 'id' => 'color_code']); ?>
								<div class="noborder">
									<a href="#" data-color="panel-red" data-preview-color="bg-red" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-red"></i></a>
                                    <a href="#" data-color="panel-blue" data-preview-color="bg-blue" class="btn btn-default <?php echo $projects_signoff; ?>  btn-xs el_color_box"><i class="fa fa-square text-blue"></i></a>
                                    <a href="#" data-color="panel-maroon" data-preview-color="bg-maroon" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-maroon"></i></a>
                                    <a href="#" data-color="panel-aqua" data-preview-color="bg-aqua" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-aqua"></i></a>
                                    <a href="#" data-color="panel-yellow" data-preview-color="bg-yellow" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-yellow"></i></a>
                                    <a href="#" data-color="panel-green" data-preview-color="bg-green" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-green"></i></a>
                                    <a href="#" data-color="panel-teal" data-preview-color="bg-teal" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-teal"></i></a>
                                    <a href="#" data-color="panel-purple" data-preview-color="bg-purple" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-purple"></i></a>
                                    <a href="#" data-color="panel-navy" data-preview-color="bg-navy" class="btn btn-default <?php echo $projects_signoff; ?> btn-xs el_color_box"><i class="fa fa-square text-navy"></i></a>
								</div>
							</div>
							<div class="form-group include-programs popup-select-icon">
								<label class="control-label" for="UserClassification">Include in Programs:</label>
								<?php
								echo $this->Form->input('programs', array(
                                        'options' => $my_programs,
                                        'class' => 'form-control',
										'multiple'=>true,
                                        'id' => 'programs',
                                        'label' => false,
                                        'div' => false,
										'selected' => $project_programs
                                    ));
								  ?>
							</div>
							<div class="form-group task-type-project">
								<?php
    							if(isset($project_id) && !empty($project_id)){
    							$ragCLS = $this->Common->getRAG($project_id);
    							?>
    							<input type="hidden" value="<?php echo $ragCLS['rag_color']; ?>" name="email_rag">
    							<input type="hidden" value="<?php echo $this->request->data['Project']['rag_status']; ?>" name="email_rag_type">
    							<?php } ?>
								<label class="task-type-label" >
									<span class="risktype">RAG: </span>
									<span class="addupdate">
										<ul class="nav nav-tabs" style="cursor: default;">
											<li class="active">
												<a class="add-link" href="#select_manual" data-toggle="tab" aria-expanded="true">Manual</a></li>
											<li class=""><a href="#rag_rules" class="" data-toggle="tab" lister="lister" aria-expanded="false">Rules</a></li>
										</ul>
									</span>
								</label>
								<div id="select_manual" class="tab-pane fade active in">
									<div class="edit-rag-sec" id="edit-rag">
										<?php
										if(isset($this->request->data['Project']['rag_status']) && !empty($this->request->data['Project']['rag_status']) ){
											echo $this->Form->input('Project.rag_status', [ 'type' => 'hidden', 'id' => 'rag_status' ]);
										}else{
											echo $this->Form->input('Project.rag_status', [ 'type' => 'hidden', 'id' => 'rag_status','value'=>3]);
										}
										?>
										<a href="javascript:;" class="link_chk red <?php echo (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 1) ? 'checked' : ''; ?>" data-value="1"> </a>
										<a href="javascript:;" class="link_chk amber <?php echo (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 2) ? 'checked' : ''; ?>" data-value="2"> </a>
										<a href="javascript:;" class="link_chk green <?php echo (isset($this->request->data['Project']['rag_status']) && $this->request->data['Project']['rag_status'] == 3) ? 'checked' : (empty($this->request->data['Project']['rag_status']) ?  'checked' : '' ); ?>" data-value="3"> </a>
									</div>
								</div>
								<div id="rag_rules" class="tab-pane fade">
									<?php
									$disableInputAmber = 'disableInput';
									$amberChecked = "";
									$redChecked = "";
									$AmberData = 0;
									$RedData = 0;

									$ambrr = (isset($this->request->data['ProjectRag']['amber_value']) && !empty($this->request->data['ProjectRag']['amber_value'])) ? trim($this->request->data['ProjectRag']['amber_value']) : '';

									$redrr = (isset($this->request->data['ProjectRag']['red_value']) && !empty($this->request->data['ProjectRag']['red_value'])) ? trim($this->request->data['ProjectRag']['red_value']) : '';

									if(isset($ambrr) && !empty($ambrr)){
										$disableInputAmber = '';
										$amberChecked = "checked";
										$AmberData = 1;
										$Amberattr = '';
									}else{
										$disableInputAmber = 'disableInput';
										$amberChecked = "";
										$AmberData = 0;
										$Amberattr = 'disabled';
									}
									?>
									<div class="manual-sec">
										<div class="manual-sec-inner">
											<div class="manual-cols">
												<input type="hidden" name="data[ProjectRag][amber_check]" value="<?php echo $AmberData; ?>" />
												<a href="javascript:;" class="link_radio amber <?php echo $amberChecked; ?>" data-value="<?php echo $AmberData; ?>"> </a>
											</div>
											<div class="manual-cols">
												<div class="input text">
													<?php echo $this->Form->input('ProjectRag.amber_value', [ 'type' => 'text', 'class' => 'rag_input amber_input  form-control '.$disableInputAmber, 'label' => false, 'autocomplete' => 'off', $Amberattr]); ?>
												</div>
											</div>
											<div class="manual-cols">
												% Overdue Tasks <i class="clearblackicon tipText amber-clear" title="Clear"></i>
											</div>
										</div>
										<div class="manual-sec-inner">
											<?php
											$disableInputRed = 'disableInput';
											if(isset($redrr) && !empty($redrr)){
												$disableInputRed = '';
												$redChecked = "checked";
												$RedData = 1;
												$Redattr = '';
											}else{
												$disableInputRed = 'disableInput';
												$redChecked = "";
												$RedData = 0;
												$Redattr = 'disabled';
											}
											?>
											<div class="manual-cols">
												<input type="hidden" name="data[ProjectRag][red_check]" value="<?php echo $RedData; ?>" />
												<a href="javascript:;" class="link_radio red <?php echo $redChecked; ?>" data-value="<?php echo $RedData; ?>"> </a>
											</div>
											<div class="manual-cols">
												<div class="input text">
													<?php echo $this->Form->input('ProjectRag.red_value', [ 'type' => 'text', 'class' => 'rag_input red_input form-control '.$disableInputRed, 'label' => false, 'autocomplete' => 'off', $Redattr]); ?>
												</div>
											</div>
											<div class="manual-cols">
												% Overdue Tasks <i class="clearblackicon tipText red-clear" title="Clear"></i>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php // is rewarded
								$cl = '';
	                            if(isset($project_id) && !empty($project_id)) {
	                                if(is_reward_activity($project_id)) {
	                                    $cl = 'not-editable';
	                                }
	                            }
                            ?>
							<?php // PASSWORD DELETE
                                $pass_cl = 'not-editable';
                                if($is_owner) {
                                    $pass_cl = '';
                                }
                                if(!isset($project_id) || empty($project_id)) {
                                    $pass_cl = '';
                                }
                            ?>
							<div class="form-group checkbox-proj-edit <?php echo $cl; ?>">
								<?php
								echo $this->Form->input('UserProject.is_rewards', array(
                                        'type' => 'checkbox',
                                        'format' => array('before', 'input', 'between', 'after', 'error'),
											'div' => false,
											'label' => false,
                                    ));
								 ?>
								<label class="control-label" for="UserProjectIsRewards">Include Rewards</label>
							</div>
							<div class="form-group checkbox-proj-edit <?php echo $pass_cl; ?>">
								<?php echo $this->Form->input('UserProject.pass_protected', array(
											'type' => 'checkbox',
											'format' => array('before', 'input', 'between', 'after', 'error'),
											'div' => false,
											'label' => false,
										)); ?>
								<label class="control-label" for="UserProjectPassProtected">Require Password to Delete</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
<style type="text/css">
	section.content{
		padding-top: 0;
	}
</style>
<script type="text/javascript">
	// $('.breadcrumb-wrap').remove()
	$('html').scrollTop(0);
	$('html').addClass('no-scroll');
	$(() => {

		$('body').delegate('input[name="rm_types"]', 'keyup', function(event) {
	        var $ts = $(this)
	        if ($(this).val().length > characters) {
	            $(this).val($(this).val().substr(0, characters));
	        }

			$ts.parents(".form-group").find('.create-error-message').hide();

	        var remaining = characters - $(this).val().length;
			$(this).parents('.project_type').find('.error-message').show();

	        $(this).parents('.project_type').find('.error-message').html("Chars 50 , <strong>" + $(this).val().length + "</strong> characters entered.");

			if ( remaining == 50  ) {
				$(this).parents('.project_type').find('.error-message').html();
				$(this).parents('.project_type').find('.error-message').hide();
	        }

	    });

	    $('body').delegate('.risk-types-input', 'keyup focus', function(event){
	        var characters = 50;

	        event.preventDefault();
	        var $error_el = $(this).parents('.project_type').find('.error-message');
	        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
	            $.input_char_count(this, characters, $error_el);
	        }
	    })

	    $('body').delegate('input[name="rm_type"]', 'keyup focus', function(event){
	        var characters = 50;

	        event.preventDefault();
	        var $error_el = $(this).parents('.project_type').find('.error-message');
	        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
	            $.input_char_count(this, characters, $error_el);
	        }
	    })

	    var $triggered = $('.rm-type-dd');
        var $list = $triggered.find('ul.dd-data');
        $('.rm-type-dd').data('list', $list);
        $list.data('triggered', $triggered);
        $(document).on('click', function(e) {
            $('.rm-type-dd').each(function() {
                var $list = $(this).find('ul.dd-data');
                //the 'is' for buttons that trigger popups
                //the 'has' for icons within a button that triggers a popup
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $list.has(e.target).length === 0) {
                    var $list = $(this).data('list');
                }
            });
        });
        $('body').delegate('.cancel_assignment', 'click', function(event) {
			event.preventDefault();
			$(this).parents('.list-group-item').removeClass('clicked');
		})
		$('body').delegate('.assing_element', 'click', function(event) {
			event.preventDefault();
			$that = $(this);
			var projectid = $that.parent().find('select').data('projectid');
			var eletypeid = $that.parent().find('select').data('eletypeid');
			var seltypeid = $that.parent().find('select').val();

			if( projectid !== "" &&  eletypeid !== "" &&  seltypeid !== ""  ){

				$.ajax({
					type: 'POST',
					dataType: 'JSON',
					data: $.param({project_id : projectid, typeid : seltypeid, exitstype : eletypeid}),
					url: $js_config.base_url + 'projects/switch_element_type/',
					global: true,
					success: function(response) {
						if( response.success ) {
							$.default_project_types(projectid).done(function() {
                                $.custom_project_type(projectid).done(function() {

                                });
                            });
						}
					}
				});
			}
		});

		$('body').delegate('.typereassign', 'click', function(event) {
			event.preventDefault();

			var $that = $(this);
			var typeid = $that.data('typeid');
			var project_id = $that.data('project_id');
			var $ul = $that.parents(".list-group");
			var $li = $that.parents(".list-group-item:first");
			var $listdd = $that.parents(".list-group-item").find('.elementtypelist');
			var $listddnot = $that.parents(".list-group").find('li.list-group-item').not($that.next()).find('.elementtypelist');

			var type_length = $js_config.project_task_types;
			console.log('adfsdf', type_length)
			$ul.find('.list-group-item').not($li).removeClass('clicked');
			if(type_length > 1)
				$li.toggleClass('clicked');
			if( $li.hasClass('clicked') ){

				if( typeid !== "" &&  project_id !== "" ){
					$.ajax({
						type: 'POST',
						dataType: 'JSON',
						data: $.param({project_id:project_id,typeid:typeid}),
						url: $js_config.base_url + 'projects/project_element_type/',
						global: false,
						success: function(response_data) {

                            if( response_data.success ) {

                                if( response_data.content != null ) {
									$listdd.find('.element_typeid').html('');
									var mySelect = $listdd.find('.element_typeid');
									mySelect.append($('<option></option>').val('').html('Select Task Types'));
                                    $.each( response_data.content, function( key, val ) {
										mySelect.append(
											$('<option></option>').val(key).html(val)
										);
                                    });
                                }
                            }
						}
					});
				}
			}
		})

		$.setSelectedColorClass = function () {
            var previewClass = ($.trim($('input#color_code').val()) != '') ? $('input#color_code').val() : 'bg-jeera'

            var splited = previewClass.split('-'),
                    previewText = 'Color preview';

            if (splited[1] != '') {
                previewClass = 'panel-' + splited[1];

                previewText = splited[1];
            }

            $(".preview span").removeAttr('class')
                    .attr('class', previewClass)
                    .text(previewText)

            $("a[data-color=" + previewClass + "]").find('i').removeClass('fa-square').addClass('fa-check')
        }
        $.setSelectedColorClass();


        $(".el_color_box").on('click', function (event) {
            event.preventDefault();

            $.each($('.el_color_box'), function (i, el) {
                $(el).find('i').addClass('fa-square').removeClass('fa-check')
            })

            var $cb = $(this)
            $cb.find('i').addClass('fa-check').removeClass('fa-square')

            var $frm = $cb.closest('form#frm_manage_project')
            var $hd = $frm.find('input#color_code')
            var cls = $hd.val()
            // console.log($frm)
            // console.log(cls)
            var foundClass = (cls.match(/(^|\s)bg-\S+/g) || []).join('')
            if (foundClass != '') {
                $hd.val('')
            }

            var applyClass = $cb.data('color')

            var splited = applyClass.split('-'),
                    previewClass = 'bg-jeera',
                    previewText = 'Color preview';


            if (splited[1] != '') {
                previewClass = 'bg-' + splited[1];
                previewText = $.ucwords(splited[1]);
            }

            $(".preview span").removeAttr('class')
                    .attr('class', previewClass)
                    .text(previewText)

            $hd.val(applyClass);
        })

        $('body').delegate('.link_chk', 'click', function(event) {
			event.preventDefault();
			$('.link_chk').removeClass('checked');
			$(this).addClass('checked');

			var $input = $(this).parent("#edit-rag").find('input'),
				status = $(this).data('value');

			$input.val(status);

            if($('.link_chk.green.checked').length <= 0) {
            }
            else {
                 $('a[href="#rag_rules"]').removeClass('disableLink');
            }
		})

		$('body').delegate('#txa_title', 'keyup focus', function(event){
            var characters = 100;
            event.preventDefault();

			$(this).parent().find('div.error-message').hide();

            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })


		$('body').delegate('#txa_objective', 'keyup focus', function(event){
            var characters = 500;
            event.preventDefault();

			$(this).parent().find('div.error-message').hide();

            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })


        $('body').delegate('#txa_description', 'keyup focus', function(event){
            var characters = 500;
            event.preventDefault();

			$(this).parent().find('div.error-message').hide();

            var $error_el = $(this).parent().find('.error');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })

		$('#project_types').multiselect({
			enableUserIcon: false,
			multiple: true,
			enableHTML: true,
			enableFiltering: true,
			includeSelectAllOption: true,
			nonSelectedText: 'Select Task Types',
			numberDisplayed: 2,
			filterPlaceholder: 'Search Task Types',
			enableCaseInsensitiveFiltering: true,
			buttonWidth: '100%',
			maxHeight: 262
		})
		$.programs = $('#programs').multiselect({
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '100%',
	        numberDisplayed: 2,
	        maxHeight: 262,
	        checkboxName: 'program_id[]',
	        enableFiltering: true,
			includeSelectAllOption:true,
	        filterPlaceholder: 'Search Programs',
	        enableCaseInsensitiveFiltering: true,
	        enableUserIcon: false,
	        nonSelectedText: 'No Programs',
			onChange: function(option, checked, select) {
				// Get selected options.
				var selectedOptions = jQuery('#programs option:selected');

				if (selectedOptions.length > 111111111111149) {
					// Disable all other checkboxes.
					var nonSelectedOptions = jQuery('#programs option').filter(function() {
						return !jQuery(this).is(':selected');
					});

					nonSelectedOptions.each(function() {
						var input = jQuery('input[value="' + jQuery(this).val() + '"]');
						input.prop('disabled', true);
						input.parent('li').addClass('disabled');
					});
				}
				else {
					// Enable all checkboxes.
					jQuery('#programs option').each(function() {
						var input = jQuery('input[value="' + jQuery(this).val() + '"]');
						input.prop('disabled', false);
						input.parent('li').addClass('disabled');
					});
				}
			},

			onInitialized: function(option, checked, select) {
				// Get selected options.
				var selectedOptions = jQuery('#programs option:selected');

				if (selectedOptions.length > 111111111111149) {
					// Disable all other checkboxes.
					var nonSelectedOptions = jQuery('#programs option').filter(function() {
						return !jQuery(this).is(':selected');
					});

					nonSelectedOptions.each(function() {
						var input = jQuery('input[value="' + jQuery(this).val() + '"]');
						input.prop('disabled', true);
						input.parent('li').addClass('disabled');
					});
				}
				else {
					// Enable all checkboxes.
					jQuery('#programs option').each(function() {
						var input = jQuery('input[value="' + jQuery(this).val() + '"]');
						input.prop('disabled', false);
						input.parent('li').addClass('disabled');
					});
				}
			},
	    });


	    $('.nav.nav-tabs').removeAttr('style');

	    // RESIZE MAIN FRAME
	    ($.adjust_resize = function(){
	        var $project_wrap = $('.edit-project-inner');
	        $project_wrap.animate({
	            minHeight: (($(window).height() - $project_wrap.offset().top) ) - 17,
	            maxHeight: (($(window).height() - $project_wrap.offset().top) ) - 17
	        }, 1 );
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
	        $.adjust_resize();
	        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
	        setTimeout( () => clearInterval(fix), 1500);
	    })

	    // RESIZE FRAME ON WINDOW RESIZE EVENT
	    $(window).resize(function() {
	        $.adjust_resize();
	    })

	    $("#list_tabs").on('show.bs.tab', function(e){
	        const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
	        setTimeout( () => clearInterval(fix), 1000);
	    })

	    $("#tab_tt_add_edit").on('shown.bs.tab', function(e){
	        if($(e.target).is('.edit-task-types')) {
	        	setTimeout(() => {
		        	var $triggered = $('.rm-type-dd');
			        var $list = $triggered.find('ul.dd-data');
			        $triggered.data('list', $list);
			        $list.data('triggered', $triggered);
	        		$triggered.trigger('click')
        		}, 1)
	        }
	    })

	})
</script>
<script type="text/javascript">
	$(() => {
		var sdate_picker = {
            showButtonPanel: false,
            firstDay: 1,
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            closeText: 'Close',
            currentText: 'Today',
            dateFormat: 'dd M yy',
            gotoCurrent: false,
            hideIfNoPrevNext: true,
            prevText: "Prev",
            nextText: "Next",
            showAnim: "fade",
        }
		var edate_picker = {
            showButtonPanel: false,
            firstDay: 1,
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            closeText: 'Close',
            currentText: 'Today',
            dateFormat: 'dd M yy',
            gotoCurrent: false,
            hideIfNoPrevNext: true,
            prevText: "Prev",
            nextText: "Next",
            showAnim: "fade",
        }


        $("#start_date").datepicker($.extend(sdate_picker, {
            onClose: function (selectedDate) {
                var startDate = $(this).datepicker('getDate');
                var endDate = $("#end_date").datepicker('getDate');

              //  $("#end_date").datepicker("setDate", startDate);
                $('#end_date').datepicker('option', 'minDate', startDate);

                if (($("#end_date").datepicker('getDate') == null || startDate > endDate) && startDate != null) {
                  	if(new Date() > startDate){
					  	$('#end_date').datepicker('option', 'minDate', 0);
				  	}else{
                    	$('#end_date').datepicker('option', 'minDate', startDate);
				  	}
                }

				if (( new Date() > startDate) && startDate != null && endDate == null) {
					$('#end_date').datepicker('option', 'minDate', 0);
                }
            }
        }))

		$("#end_date").datepicker(
			$.extend(edate_picker, {
				onSelect: function (selectedDate) {
					var endDate = $("#end_date").datepicker('getDate');
					$('#start_date').datepicker('option', 'maxDate', endDate);
				}
			})
	    );


	    <?php

	    $dates = false;
	    $projectEndDate = '';

	    if (isset($this->request->data['Project']['start_date']) && !empty($this->request->data['Project']['start_date'])) {
	        $dates = true;

			$this->request->data['Project']['start_date'] = date("Y-m-d",strtotime($this->request->data['Project']['start_date']));

			$this->request->data['Project']['end_date'] = date("Y-m-d",strtotime($this->request->data['Project']['end_date']));

			$projectStartDate = date("d M Y",strtotime($this->request->data['Project']['start_date']));
			$projectEndDate = date("d M Y",strtotime($this->request->data['Project']['end_date']));
	        ?>

	            sdate = new Date("<?php echo $projectStartDate; ?>");

				 sdate = new Date(sdate);

	             curdate = new Date("<?php echo date('Y/m/d'); ?>");
				 curdate = new Date(curdate);

	            $("#start_date").datepicker("setDate", sdate);
	            $("#end_date").datepicker("option", "minDate",sdate);
	            if (curdate < sdate) {
	                $('#start_date').datepicker('option', 'minDate', curdate);
	            } else {
	                $('#start_date').datepicker('option', 'minDate', sdate);
	                $("#start_date").datepicker("setDate", sdate);
	            }


				/*According to client 13 feb 2018*/

				<?php
				if (isset($this->request->data['Project']['created']) && !empty($this->request->data['Project']['created'])) {
				?>

				 CCdate = new Date("<?php echo date('r', strtotime(date('Y-m-d',$this->request->data['Project']['created']))); ?>");
				 CCdate = new Date(CCdate);

				 var CSdate = new Date("<?php echo $projectStartDate; ?>");
				 CSdate = new Date(CSdate);

				 if (CCdate <= curdate) {
					 CCdate = new Date("<?php echo date('r', strtotime("-1 month" , $this->request->data['Project']['created'])); ?>");
					 CCdate = new Date(CCdate);
				 }

				 if ( CSdate < curdate ){
					CCdate =  CSdate;
				 }

				 $('#start_date').datepicker('option', 'minDate', CCdate);
	             $("#start_date").datepicker("setDate", sdate);

				<?php }
				?>

	            $("#el_dc_yes").trigger('change')

	    <?php
	    } else {
	        ?>
	    	curdate = new Date("<?php echo date('r', strtotime("-1 month" , strtotime(date('Y-m-d')))); ?>");
			curdate = new Date(curdate);

			$("#start_date").datepicker("option", "minDate",curdate);
	        <?php
	    }

	    if ( isset($this->request->data['Project']['end_date']) && !empty($this->request->data['Project']['end_date']) && $this->request->data['Project']['end_date'] != '1970-01-01') {
	        $dates = true;
	        ?>
	            edate = new Date("<?php echo $projectEndDate; ?>");
	            edate = new Date(edate);
	            $("#end_date").datepicker("setDate", edate);
	            $("#start_date").datepicker("option", "maxDate",edate);

	            $("#el_dc_yes").trigger('change')

	    <?php } else { ?>
			$("#end_date").datepicker("option", "minDate", 0);
	    <?php } ?>

	        $('.open-start-date-picker').on('click', function (event) {
	            event.preventDefault();
	            $("#start_date").datepicker('show').focus();
	        });

	        $('.open-end-date-picker').on('click', function (event) {
	            event.preventDefault();
	            $("#end_date").datepicker('show').focus();
	        });
	})
</script>