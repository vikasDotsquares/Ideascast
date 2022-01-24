<?php echo $this->Html->css('projects/bootstrap-select/bootstrap.select'); ?>
<?php echo $this->Html->script('projects/plugins/bootstrap-select/bootstrap.select', array('inline' => true));?>
<style type="text/css">
    #list_percentages, #list_impacts {
        font-size: 12px;
    }
    .dropdown-menu.inner {
        box-shadow: none !important;
    }
    .dropdown-menu.inner > li a {
        padding: 4px 10px !important;
    }
    .btn.dropdown-toggle.btn-default {
        background-color: #fff ;
    }
    .wrapper_disabled {
        pointer-events: none;
    }
</style>

<?php

$RmDetail = $RmDetail['RmDetail'];
$exposer_data = risk_exposer($RmDetail['id']);

$current_user_id = $this->Session->read('Auth.User.id');

$wrapper_status = $edit_status = '';
if($RmDetail['status'] == 3){
    $edit_status = 'disabled';
    $wrapper_status = 'wrapper_disabled';
}
 ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Risk Profile</h3>
</div>
<div class="modal-body">


    <div class="allpopuptabs">
        <ul class="nav nav-tabs ">
            <li <?php if($type == 'status'){ ?> class="active" <?php } ?>>
                <a id="statusone" href="#status" data-toggle="tab" aria-expanded="true">Status</a>
            </li>
            <li>
                <a id="impactassessmenttwo" href="#impact_assessment" data-toggle="tab" aria-expanded="false">Impact Assessment</a>
            </li>
            <li <?php if($type == 'response'){ ?> class="active" <?php } ?>>
                <a id="riskResponsethree" href="#risk_response" data-toggle="tab" aria-expanded="false">Risk Response</a>
            </li>
            <li>
                <a id="residualRisk" href="#residual_risk" data-toggle="tab" aria-expanded="false">Residual Risks</a>
            </li>
        </ul>
    </div>
    <div id="elementTabContent" class="tab-content">
        <div class="tab-pane fade <?php if($type == 'status'){ ?> active in <?php } ?> <?php echo $wrapper_status; ?>" id="status">
        	<?php
                $update_class = 'update_status';
                if($RmDetail['status'] == 3){
                    $update_class = '';
                } // Signed-off or Overdue
            ?>
            <div class="risk-p-cont">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
							<strong class="updetastatus-h">Current Status:</strong>
                            <div class="currentstatus">
                                <?php
                                $status = 1;
                                if( $RmDetail['status'] != 3 && date('Y-m-d', strtotime($RmDetail['possible_occurrence'])) < date('Y-m-d') ) {
                                    $status = 4;
                                }
                                else if($RmDetail['status'] == 2){
                                    $status = 2;
                                }
                                else if($RmDetail['status'] == 3){
                                    $status = 3;
                                }
                                else{
                                    $status = 1;
                                }
                                ?>
                                <?php //echo (date('Y-m-d', strtotime($RmDetail['possible_occurrence'])));
                                if($status == 4){ ?>
                                    <span class="flag-iocn-p risksign-off"><a class="btn btn-default tipText" title="Overdue"> <i class="ps-flag bg-overdue"></i> </a></span>
                                    <div class="status-cont">
                                        <p class="">Risk is overdue</p>
                                    </div>
                            	<?php }else if($status == 1){ ?>
	                                <span class="flag-iocn-p"><a class="btn btn-default  current-tip tipText" title="Open"> <i class="current-icon ps-flag bg-not_started"></i> </a></span>
	                                <div class="status-cont">
	                                    <p class="current-text">Risk is Open, Waiting to Start</p>
	                                </div>
                                <?php } else if($status == 2){ ?>
	                                <span class="flag-iocn-p"><a class="btn btn-default current-tip  tipText" title="In Progress"> <i class="current-icon ps-flag bg-progressing"></i> </a></span>
	                                <div class="status-cont">
	                                    <p class="current-text">Risk is In Progress</p>
	                                </div>
                                <?php } else if($status == 3){ ?>
	                                <span class="flag-iocn-p risksign-off"><a class="btn btn-default  tipText" title="Completed"> <i class="ps-flag  bg-completed"></i> </a></span>
	                                <div class="status-cont">
	                                    <p class="">Risk was signed off: <?php echo _displayDate(date('Y-m-d h:i:s A',strtotime($RmDetail['modified'])), $format = 'd M Y, g:iA'); ?></p>
	                                </div>
                                <?php } ?>
                            </div>
                        	<?php  if($status != 4 && $RmDetail['status'] != 3  ){ ?>
                            <div class="row update-status-row">
                                <div class="col-md-6">
                                    <div class="updetastatus">
                                        <strong class="updetastatus-h">Update Status:</strong>
                                        <div class="updetastatus-info">
                                        	<?php if($status == 1){ ?>
	                                            <span class="flag-iocn-p"><a class="btn btn-default update_status tipText" title="In Progress" data-risk="<?php echo $RmDetail['id']; ?>" data-status="2" data-key="<?php echo $key; ?>"> <i class="ps-flag bg-progressing update-icon"></i> </a></span>
	                                            <p class="updetastatus-cont update-text">Risk is In Progress</p>
                                            <?php }elseif($status == 2){ ?>
	                                            <span class="flag-iocn-p"><a class="btn btn-default update_status tipText" title="Open" data-risk="<?php echo $RmDetail['id']; ?>" data-status="1" data-key="<?php echo $key; ?>"> <i class="ps-flag bg-not_started update-icon"></i>  </a></span>
	                                            <p class="updetastatus-cont update-text">Risk is Open, Waiting to Start</p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="updetastatus-info risksign" >
                                        <span class="flag-iocn-p"><a class="btn btn-xs btn-success todo-esd" <?php if(($status == 1 || $status == 2) && $current_user_id == $RmDetail['user_id']){ ?> id="sign_off" <?php } ?> > <i class="signoffwhite"></i> </a></span>
                                        <p class="updetastatus-cont">Risk Sign Off by Creator</p>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="risk-exp-bottom">
                                <div class="col-sm-4">
                                    <div class="risk-exposure imp-ass">
                                        <strong>Risk Exposure:</strong>
                                        <span class="colorbar navail"> <i class="">&nbsp;</i></span>
                                        <strong class="imp-ass-text none">None</strong>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="risk-signoff" style="display: none;">
                                        <span class="signofftext">Are you sure want to sign off the Risk?</span>
                                        <div class="btn-risk">
                                        	<a href="#" id="" class="btn btn-success btn-sm text-white signoff_yes" data-risk="<?php echo $RmDetail['id']; ?>" data-key="<?php echo $key; ?>">Yes</a>
                                        	<a href="#" id="signoff_no" class="btn btn-danger btn-sm text-white">No</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade <?php echo $wrapper_status; ?>" id="impact_assessment">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12 <?php echo $edit_status; ?>">
                        <div class="assessment-description">
                            <strong>Description: </strong>
                            <div class="desc-text">
                            	<?php
                            	if(isset($RmDetail['description']) && !empty($RmDetail['description'])){
                            		echo htmlentities($RmDetail['description'],ENT_QUOTES, "UTF-8");
                            	}else{
                            	?>
						    		<div class="no-desc-text">No description entered</div>
                            	<?php } ?>
						    </div>
                        </div>
                        <div class="assessment-sec-bottom">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6 assessment-wrapper <?php echo $edit_status; ?>">
                                            <strong>Risk Impact:</strong>
                                            <?php
                                            $list_impacts = [1 => 'Negligible', 2 => 'Minor', 3 => 'Moderate', 4 => 'Major', 5 => 'Critical'];
                                            $imp = '';
                                            if(isset($exposer_data) && !empty($exposer_data)){
                                                $imp = $exposer_data["RmExposeResponse"]["impact"];
                                            }
                                            echo $this->Form->select('list_impacts', $list_impacts, array('escape' => false, 'empty' => 'Not Set', 'class' => 'form-control selectpicker', 'id' => 'list_impacts', 'default' => $imp));
                                            ?>
                                        </div>
                                        <div class="col-sm-6 assessment-wrapper <?php echo $edit_status; ?>">
                                            <strong>Risk Probability:</strong>
                                            <?php
                                            $list_percentages = [1 => 'Rare', 2 => 'Unlikely', 3 => 'Possible', 4 => 'Likely', 5 => 'Almost Certain'];
                                            $per = '';
                                            if(isset($exposer_data) && !empty($exposer_data)){
                                            	$per = $exposer_data["RmExposeResponse"]["percentage"];
                                            }
                                            echo $this->Form->select('list_percentages', $list_percentages, array('escape' => false, 'empty' => 'Not Set', 'class' => 'form-control selectpicker', 'id' => 'list_percentages', 'default' => $per));

                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="risk-exposure imp-ass">
                                        <strong>Risk Exposure:</strong>
                                        <span class="colorbar navail"> <i class="">&nbsp;</i></span>
                                        <strong class="imp-ass-text none">None</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade <?php if($type == 'response'){ ?> active in <?php } ?> <?php echo $wrapper_status; ?>" id="risk_response">
            <div class="form-group responses <?php echo $edit_status; ?>">
                <div class="row">
                    <?php
                    $mitigation_user_name = $contingency_user_name = 'No input yet';
                    $mitigation_entered = $contingency_entered = false;
                    $mitigation_updated = $contingency_updated = $mitigation = $contingency = '';
                    if(isset($exposer_data) && !empty($exposer_data)){
                        $mitigation_user_id = $exposer_data["RmExposeResponse"]["mitigation_user_id"];
                        if(isset($mitigation_user_id) && !empty($mitigation_user_id)){
                            $userDetail = get_user_data($mitigation_user_id);
                            $mitigation_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'] .':';
                            $mitigation_updated = date('d M, Y g:i A', strtotime($exposer_data['RmExposeResponse']['mitigation_updated']));
                            $mitigation_updated = _displayDate($exposer_data['RmExposeResponse']['mitigation_updated']);
                            $mitigation = $exposer_data['RmExposeResponse']['mitigation'];
                            $mitigation_entered = true;
                        }

                        $contingency_user_id = $exposer_data["RmExposeResponse"]["contingency_user_id"];
                        if(isset($contingency_user_id) && !empty($contingency_user_id)){
                            $userDetail = get_user_data($contingency_user_id);
                            $contingency_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'] .':';
                            $contingency_updated = date('d M, Y g:i A', strtotime($exposer_data['RmExposeResponse']['contingency_updated']));
                            $contingency_updated = _displayDate($exposer_data['RmExposeResponse']['contingency_updated']);
                            $contingency = $exposer_data['RmExposeResponse']['contingency'];
                            $mitigation_entered = true;
                        }
                    }
                    ?>
                    <div class="col-md-12">
                        <div class="response-description">
                            <label><strong>Mitigation Plan:</strong>
                                <div class="updations">Last update by: <span class="updated_by"><?php echo $mitigation_user_name; ?></span> <span class="updated_on"><?php echo $mitigation_updated; ?></span>
                                </div>
                            </label>
                            <textarea class="form-control" rows="4" placeholder="Max Chars 1000" id="mitigation" name="mitigation"><?php echo (substr($mitigation, 0, 1000)); ?></textarea>
                            <span class="error-message error text-danger"></span>
                            <a class="btn btn-xs update_mitigation tipText <?php echo $edit_status; ?>" title="Save">
                                <i class="activegreen"></i>
                            </a>
                            <a class="btn btn-xs remove_mitigation tipText <?php echo $edit_status; ?>" title="Remove">
                                <i class="inactivered"></i>
                            </a>
                        </div>
                        <div class="response-description">
                            <label><strong>Contingency Plan:</strong>
                                <div class="updations">Last update by: <span class="updated_by"><?php echo $contingency_user_name; ?></span> <span class="updated_on"><?php echo $contingency_updated; ?></span>
                                </div>
                            </label>
                            <textarea class="form-control" rows="4" placeholder="Max Chars 1000" id="contingency" name="contingency" ><?php echo (substr($contingency, 0, 1000)); ?></textarea>
                            <span class="error-message error text-danger"></span>
                            <a class="btn btn-xs update_contingency tipText <?php echo $edit_status; ?>" title="Save">
                                <i class="activegreen"></i>
                            </a>
                            <a class="btn btn-xs remove_contingency tipText <?php echo $edit_status; ?>" title="Remove">
                                <i class="inactivered"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade <?php echo $wrapper_status; ?>" id="residual_risk">
            <div class="form-group responses <?php echo $edit_status; ?>">
                <div class="row">
                	<?php
                	$residual_user_name = 'No input yet';
                	$residual_entered = false;
                	$residual_updated = $residual = '';
                	if(isset($exposer_data) && !empty($exposer_data)){
                    	$residual_user_id = $exposer_data["RmExposeResponse"]["residual_user_id"];
                    	if(isset($residual_user_id) && !empty($residual_user_id)){
	                    	$userDetail = get_user_data($residual_user_id);
							$residual_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'] .':';
							$residual_updated = date('d M, Y g:i A', strtotime($exposer_data['RmExposeResponse']['residual_updated']));

							$residual_updated = _displayDate($exposer_data['RmExposeResponse']['residual_updated']);
							$residual = $exposer_data['RmExposeResponse']['residual'];
							$residual_entered = true;
						}
                    }
                	?>
                    <div class="col-md-12">
                        <div class="response-description">
                            <label><strong>Residual Risks:</strong>
                            	<div class="updations">Last update by: <span class="updated_by"><?php echo $residual_user_name; ?></span> <span class="updated_on"><?php echo $residual_updated; ?></span>
                            	</div>
                            </label>
                            <textarea class="form-control" rows="4" placeholder="Max Chars 1000" id="residual" name="residual" ><?php echo (substr($residual, 0, 1000)); ?></textarea>
                            <span class="error-message error text-danger"></span>
                            <a class="btn btn-xs update_residual tipText <?php echo $edit_status; ?>" title="Save">
                                <i class="activegreen"></i>
                            </a>
                            <a class="btn btn-xs remove_residual tipText <?php echo $edit_status; ?>" title="Remove">
						        <i class="inactivered"></i>
						    </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='loader_bar' id="loader_bar" style="display: none;"></div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button" class="btn btn-success update_data  <?php echo $edit_status; ?>" style="display: none;">Update</button>
    <button type="button" class="btn btn-success submit_data  <?php echo $edit_status; ?>" style="display: none;">Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
	$(function() {

		var db_impact = '<?php if(isset($exposer_data) && !empty($exposer_data)){echo $exposer_data["RmExposeResponse"]["impact"];} ?>';
		var db_percent = '<?php if(isset($exposer_data) && !empty($exposer_data)){echo $exposer_data["RmExposeResponse"]["percentage"];} ?>';

		;($.calculate_exposure = function(impact, percentage) {
			var data = {};
			if(impact > 0 && percentage > 0) {
				var $i = impact,
					$p = percentage;

				if($i == 1 && ($p == 5 || $p == 4)) {
					data = {'class': 'mid', 'text': 'MEDIUM'};
				}
				else if($i == 1 && ($p == 3 || $p == 2 || $p == 1)) {
					data = {'class': 'low', 'text': 'LOW'};
				}
				else if($i == 2 && $p == 5) {
					data = {'class': 'high', 'text': 'HIGH'};
				}
				else if($i == 2 && ($p == 4 || $p == 3)) {
					data = {'class': 'mid', 'text': 'MEDIUM'};
				}
				else if($i == 2 && ($p == 2 || $p == 1)) {
					data = {'class': 'low', 'text': 'LOW'};
				}
				else if($i == 3 && ($p == 5 || $p == 4)) {
					data = {'class': 'high', 'text': 'HIGH'};
				}
				else if($i == 3 && $p == 3) {
					data = {'class': 'mid', 'text': 'MEDIUM'};
				}
				else if($i == 3 && ($p == 2 || $p == 1)) {
					data = {'class': 'low', 'text': 'LOW'};
				}
				else if($i == 4 && $p == 5) {
					data = {'class': 'severe', 'text': 'SEVERE'};
				}
				else if($i == 4 && $p == 4) {
					data = {'class': 'high', 'text': 'HIGH'};
				}
				else if($i == 4 && ($p == 3 || $p == 2)) {
					data = {'class': 'mid', 'text': 'MEDIUM'};
				}
				else if($i == 4 && $p == 1) {
					data = {'class': 'low', 'text': 'LOW'};
				}
				else if($i == 5 && ($p == 5 || $p == 4)) {
					data = {'class': 'severe', 'text': 'SEVERE'};
				}
				else if($i == 5 && $p == 3) {
					data = {'class': 'high', 'text': 'HIGH'};
				}
				else if($i == 5 && ($p == 2 || $p == 1)) {
					data = {'class': 'mid', 'text': 'MEDIUM'};
				}
			}
			if(data.hasOwnProperty('class')) {
				$('.imp-ass i').removeAttr('class').addClass(data.class);
				$('.imp-ass .imp-ass-text').text(data.text);
				$('.imp-ass .colorbar').removeClass('navail');
			}
			else{
				$('.imp-ass .colorbar').addClass('navail');
				$('.imp-ass .imp-ass-text').text('None').addClass('none');
				$('.imp-ass i').removeAttr('class');
			}

		})(db_impact, db_percent);

		$('body').on('change', '#list_impacts', function(event){
			event.preventDefault();
			$.calculate_exposure($(this).val(), $('#list_percentages').val());
		})

		$('body').on('change', '#list_percentages', function(event){
			event.preventDefault();
			$.calculate_exposure($('#list_impacts').val(), $(this).val());
		})

		$.char_count = function(el, characters) {
			var value = $(el).val();
			if(value.length > characters){
				$(el).val($(el).val().substr(0, characters));
			}
			var remaining = characters -  value.length;
			$(el).parent().find('.error').html("Char "+characters+" , <strong>" +value.length+ "</strong> characters entered.");
		}

		/*var characters = 1000;
		$('body').delegate('#mitigation, #contingency, #residual', 'keyup', function(event){
			event.preventDefault();
			$.char_count(this, characters);
		})*/

		$.activeTab = $('#statusone')[0];
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		  	$.activeTab = e.target;
			if($.activeTab) {
				if($($.activeTab).is('#impactassessmenttwo')) {
					$('.update_data,.submit_data').show();
				}
				else if($($.activeTab).is('#riskResponsethree')) {
					$('.update_data,.submit_data').hide();
				}
                else if($($.activeTab).is('#statusone')) {
                    $('.update_data,.submit_data').hide();
                }
				else if($($.activeTab).is('#residualRisk')) {
					$('.update_data,.submit_data').hide();
				}
			}
		})

		$('.update_data,.submit_data').on('click', function(event){
			event.preventDefault();
			var data = {};
			var submit = ($(this).is('.submit_data')) ? true : false;
			$('#loader_bar').show();
			if($.activeTab) {
				if($($.activeTab).is('#impactassessmenttwo')) {
					data = {
						'risk_id': '<?php if(isset($RmDetail) && !empty($RmDetail)){echo $RmDetail["id"];} ?>',
						'impacts': $('#list_impacts').val(),
						'percentages': $('#list_percentages').val(),
					};
				}
			}

			$.update_flag = {risk_id: '<?php echo $RmDetail["id"]; ?>', key: '<?php echo $key; ?>'};
			$.ajax({
				url: $js_config.base_url + 'risks/save_exposer',
				type: 'POST',
				dataType: 'JSON',
				data: data,
				success: function(response) {
					if(response.success) {
						if(submit) {
                            $('#popup_model_box').modal('hide');
							$('#modal_small').modal('hide');
						}
					}
					$('#loader_bar').hide();
				}
			})

		})

		$('.update_mitigation').on('click', function(event) {
			event.preventDefault();
			var $this = $(this),
				data = {};

			$(this).parents('.response-description:first').find('.error').html('');
			if($('#mitigation').val() == '') {
				$this.parents('.response-description:first').find('.error').html('Please enter some text.');
				return;
			}
			data = {
					'risk_id': '<?php if(isset($RmDetail) && !empty($RmDetail)){echo $RmDetail["id"];} ?>',
					'mitigation': $('#mitigation').val(),
					'save_mitigation': true,
				};

			$('#loader_bar').show();
			$.update_flag = {risk_id: '<?php echo $RmDetail["id"]; ?>', key: '<?php echo $key; ?>'};
			$.ajax({
				url: $js_config.base_url + 'risks/save_response',
				type: 'POST',
				dataType: 'JSON',
				data: data,
				success: function(response) {
					if(response.success) {
						if(response.content){
							$this.parents('.response-description:first').find('.updated_by').text(response.content.username+": ");
							$this.parents('.response-description:first').find('.updated_on').text(response.content.updated);
						}
					}
					$('#loader_bar').hide();
				}
			})

		})

        $('.update_contingency').on('click', function(event){
            event.preventDefault();
            var $this = $(this),
                data = {};

            $(this).parents('.response-description:first').find('.error').html('');
            if($('#contingency').val() == '') {
                $this.parents('.response-description:first').find('.error').html('Please enter some text.');
                return;
            }
            data = {
                    'risk_id': '<?php if(isset($RmDetail) && !empty($RmDetail)){echo $RmDetail["id"];} ?>',
                    'contingency': $('#contingency').val(),
                    'save_contingency': true,
                };

            $('#loader_bar').show();
            $.update_flag = {risk_id: '<?php echo $RmDetail["id"]; ?>', key: '<?php echo $key; ?>'};
            $.ajax({
                url: $js_config.base_url + 'risks/save_response',
                type: 'POST',
                dataType: 'JSON',
                data: data,
                success: function(response) {
                    if(response.success) {
                        if(response.content){
                            $this.parents('.response-description:first').find('.updated_by').text(response.content.username+": ");
                            $this.parents('.response-description:first').find('.updated_on').text(response.content.updated);
                        }
                    }
                    $('#loader_bar').hide();
                }
            })
        })

        $('.update_residual').on('click', function(event){
            event.preventDefault();
            var $this = $(this),
                data = {};

            $(this).parents('.response-description:first').find('.error').html('');
            if($('#residual').val() == '') {
                $this.parents('.response-description:first').find('.error').html('Please enter some text.');
                return;
            }
            data = {
                    'risk_id': '<?php if(isset($RmDetail) && !empty($RmDetail)){echo $RmDetail["id"];} ?>',
                    'residual': $('#residual').val(),
                    'save_residual': true,
                };

            $('#loader_bar').show();
            $.update_flag = {risk_id: '<?php echo $RmDetail["id"]; ?>', key: '<?php echo $key; ?>'};
            $.ajax({
                url: $js_config.base_url + 'risks/save_response',
                type: 'POST',
                dataType: 'JSON',
                data: data,
                success: function(response) {
                    if(response.success) {
                        if(response.content){
                            $this.parents('.response-description:first').find('.updated_by').text(response.content.username+": ");
                            $this.parents('.response-description:first').find('.updated_on').text(response.content.updated);
                        }
                    }
                    $('#loader_bar').hide();
                }
            })

        })

		$('.remove_mitigation, .remove_contingency, .remove_residual').on('click', function(event){
			event.preventDefault();
			var $this = $(this),
				data = {
                    'risk_id': '<?php if(isset($RmDetail) && !empty($RmDetail)){echo $RmDetail["id"];} ?>',
                };

            if($(this).is('.remove_mitigation')) {
                data['remove_mitigation'] =  true;
            }
            else if($(this).is('.remove_contingency')) {
                data['remove_contingency'] =  true;
            }
            else if($(this).is('.remove_residual')) {
                data['remove_residual'] =  true;
            }
            $this.parents('.response-description:first').find('textarea').val('');
			$('#loader_bar').show();
			$.update_flag = {risk_id: '<?php echo $RmDetail["id"]; ?>', key: '<?php echo $key; ?>'};
			$.ajax({
				url: $js_config.base_url + 'risks/remove_response',
				type: 'POST',
				dataType: 'JSON',
				data: data,
				success: function(response) {
					if(response.success) {
                        // $this.parents('.response-description:first').find('.updated_by').text("No input yet");
                        // $this.parents('.response-description:first').find('.updated_on').text("");
					}
					$('#loader_bar').hide();
				}
			})

		})


        $.risk_status = function(params) {
            var dfd = new $.Deferred();

            $.ajax({
                url: $js_config.base_url + 'risks/risk_status',
                type: 'POST',
                dataType: 'JSON',
                global: false,
                data: params,
                success: function(response) {
                    if( response.success ) {
                        if(response.content){
                            // send web notification
                            $.socket.emit('socket:notification', response.content.socket, function(userdata){});
                        }
                    }
                    dfd.resolve(response.data);
                }
            })
            return dfd.promise();
        };

        $('.update_status').on('click', function(event) {
            event.preventDefault();
            var $this = $(this),
                risk_id = $(this).data('risk'),
                status = $(this).data('status'),
                key = $(this).data('key');

            $.risk_status({id: risk_id, status: status})
            	.done(function(msg){
            		$.update_flag = {risk_id: risk_id, key: key};
            		if(status == 1) {
            			$('.current-icon').removeClass('bg-progressing').addClass('bg-not_started') ;
            			$('.current-tip').attr('data-original-title','Open') ;
            			$('.current-text').html('Risk is Open, Waiting to Start');
            			$this.data('status', 2);
            			$this.find('i').removeClass('bg-not_started').addClass('bg-progressing') ;
						$this.attr('data-original-title','In Progress') ;
            			$('.update-text').html('Risk is In Progress');
            		}
            		else if(status == 2) {
            			//$('.current-icon').removeClass('fa').removeClass('fa-flag-o').addClass('far').addClass('fa-flag');
            			$('.current-icon').removeClass('bg-not_started').addClass('bg-progressing');
						$('.current-tip').attr('data-original-title','In Progress') ;
            			$('.current-text').html('Risk is In Progress');
            			$this.data('status', 1);
            			//$this.find('i').removeClass('far').removeClass('fa-flag').addClass('fa').addClass('fa-flag-o');
            			$this.find('i').removeClass('bg-progressing').addClass('bg-not_started');
						$this.attr('data-original-title','Open') ;
            			$('.update-text').html('Risk is Open, Waiting to Start');
            		}
            	});
        });

		$('body').on('click', '#sign_off', function(event){
			event.preventDefault();

			if( !$('.risk-signoff').hasClass('opened') ) {
				$('.risk-signoff').fadeIn().addClass('opened');
			}
		})

		$('body').on('click', '#signoff_no', function(event){
			event.preventDefault();
			$('.risk-signoff.opened').fadeOut().removeClass('opened');
		})

		$('.signoff_yes').on('click', function(event){
			event.preventDefault();
            var $this = $(this),
                risk_id = $(this).data('risk'),
                key = $(this).data('key');

			$.risk_status({id: risk_id, status: 3})
            	.done(function(response){
					$('.risk-signoff.opened').fadeOut().removeClass('opened');
					$('.update_status').removeClass('update_status');
					$('.update-status-row').slideUp(300, function(){
						$(this).remove();
						$('.current-icon').parents('.flag-iocn-p:first').addClass('risksign-off');
						$('.current-icon').parents('a').attr('title','').attr('data-original-title','Completed');
						$('.current-icon').removeAttr('class').addClass('bg-completed ps-flag');
            			$('.current-text').html('Risk was signed off: '+response);
                        $('.assessment-wrapper,.responses,.update_mitigation,.remove_mitigation,.update_contingency,.remove_contingency,.remove_residual,.update_residual,.update_data,.submit_data').addClass('disabled');
					});
					$.update_flag = {risk_id: risk_id, key: key};
            	})
		})

        $('.selectpicker').selectpicker({
            style: 'btn-default',
            size: 4
        });


		$('body').delegate('#mitigation', 'keyup focus', function(event){
			var characters = 1000;

			event.preventDefault();
			var $error_el = $(this).next('.error');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

		$('body').delegate('#contingency', 'keyup focus', function(event){
			var characters = 1000;

			event.preventDefault();
			var $error_el = $(this).next('.error');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})
		$('body').delegate('#residual', 'keyup focus', function(event){
			var characters = 1000;

			event.preventDefault();
			var $error_el = $(this).next('.error');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})

	})
</script>