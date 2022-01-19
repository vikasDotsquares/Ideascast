<?php
$el_id = $this->data['Element']['id'];

$onemsg = null;
$datatitle = '';

$class_dd = $class_d;
$class_prevantd = $class_prevant;
 
if( isset( $message ) && !empty($message) && empty($overdue) && empty($notstarted) ){
	$onemsg = 'You cannot add a Decision because this Task has been signed off.';
	$datatitle = 'Signed Off';
}

if( isset( $message ) && !empty($message) && !empty($overdue) && empty($notstarted)  ){
	$onemsg = 'You cannot create a Decision because this Task is overdue.';
	$datatitle = 'Create Decision';
}

if( isset( $messagepre ) && !empty($messagepre) ){
	//$onemsg = 'You cannot add a Decision because this Task has no schedule.';
	//$datatitle = 'No Task Schedule';
	$onemsg = '';
	$datatitle = '';
	$class_dd = $class_prevantd = '';
}

if( isset( $notstarted ) && !empty($notstarted) ){
	//$onemsg = 'You cannot add a Decision because this Task has not started.';
	//$datatitle = 'Not Started';
	$onemsg = '';
	$datatitle = '';
	$class_dd = $class_prevantd = '';
}


if (!isset($element_decision_data) || empty($element_decision_data)) {

?>

                                                        <div class="create_decision_form ">

                          <div style="display: block" data-msg="<?php echo $onemsg;?>"  class="<?php echo $class_dd;?> <?php echo $class_prevantd;?> list-form edit_decision border bg-warning nopadding" data-title="<?php echo $datatitle; ?>" >

                                                                <a class="list-group-item clearfix open_form noborder-radius" href="">
                                                                    <span class="pull-left"><i class="asset-all-icon re-DecisionBlack"></i>&nbsp; New Decision</span>
                                                                    <span class="pull-right"><!--<i class="fa fa-plus"></i>--></span>
                                                                </a>

    <?php
    echo $this->Form->create('ElementDecision', array('url' => array('controller' => 'entities', 'action' => 'add_decision', $this->data['Element']['id']), 'class' => 'formAddEditElementDecision', 'id' => 'formAddEditElementDecision', 'style' => '', 'class' => 'padding', 'enctype' => 'multipart/form-data'));


echo $this->Form->input('ElementDecision.create_activity', [ 'type' => 'hidden','value'=>true]);



    ?>                              <div class="form-group ">
                                                                    <label for=" " class=" ">Title:</label>
                                                                    <input type="text" name="data[ElementDecision][title]" placeholder="Decision title"  id="txt_decision_title" class="form-control <?php echo $tasksignoffcls;?>"  value=""  />
                                                                    <input type="hidden" name="data[ElementDecision][element_id]" id="txt_decision_element" class=""  value="<?php echo $this->data['Element']['id']; ?>" />
                                                                    <span style="" class="error-message text-danger"></span>
                                                                </div>
																<?php if($ele_signoff == false ){ ?>
                                                                <div style="" id="save_decision_wrappers" class="form-group text-center margin">
                                                                    <a class="btn btn-sm btn-success  submit" id="add_edit_elm_decision" href="javascript:void(0)" data-form="formAddElementDecisionOnly">
                                                                    <!--<i class="fa fa-fw fa-save"></i>--> Save </a>
                                                                </div>
																<?php } else {?>
																<div style="" id="save_decision_wrappers" class="form-group text-center margin">
                                                                    <a class="btn btn-sm btn-success disabled" id="add_edit_elm_decision" href="javascript:void(0)" >
                                                                     Save </a>
                                                                </div>
																<?php } ?>

    <?php echo $this->Form->end(); ?>
                                                            </div>
                                                        </div>
<?php } ?>



<div class="table_wrapper clearfix" id="decisions_table" style="margin-bottom: 10px;" data-model="decision" data-limit="3">
                                                        <div class="table_head">
                                                            <div class="row">
                                                                <div class="col-sm-3 resp"><h5> Title</h5></div>
                                                                <div class="col-sm-3 col-md-2 col-lg-3 resp"><h5> Creator</h5></div>
                                                                <div class="col-sm-2 resp"><h5> Added</h5></div>
                                                                <div class="col-sm-2 resp"><h5> Updated</h5></div>
                                                                <div class="col-sm-2 col-md-3 col-lg-2 text-center resp"><h5> Action</h5></div>
                                                            </div>
                                                        </div>

                                                        <div class="table-rows data_catcher">

                                                            <?php
                                                            $decision_sign_off = 1;
                                                            $el_id = $this->data['Element']['id'];
                                                            // pr($element_decision_data, 1);

$element_decision_data_tot = ( isset($element_decision_data) && !empty($element_decision_data) ) ? count($element_decision_data) : 0;

$el_decision = array();
$class_d_d = "";
if (isset($element_decision_data) && !empty($element_decision_data)) {
	// pr($element_decision_data);
    foreach ($element_decision_data as $key => $values) {
        $el = $values['Element'];
        $el_decision = $values['ElementDecision'];
        $el_decision_detail = $values['ElementDecisionDetail'];
        $decision_sign_off = 1;

        if (isset($el_decision['sign_off']) && !empty($el_decision['sign_off']) && $el_decision['sign_off'] > 0  && $ele_signoff == false ) {
            $decision_sign_off = 0;
			$tasksignoffcls = 'signofftask';
			$class_d_d = "showdecision";
			$message ="This decision has been signed off.";
        } else if( isset($el_decision['sign_off']) && !empty($el_decision['sign_off']) && $el_decision['sign_off'] == 0  && $ele_signoff == true ){
			$decision_sign_off = 0;
			$tasksignoffcls = 'signofftask';
			$class_d_d = "showdecision";
		} else if( isset($el_decision['sign_off']) && !empty($el_decision['sign_off']) && $el_decision['sign_off'] > 0  && $ele_signoff == true  ){
			$decision_sign_off = 0;
			$tasksignoffcls = 'signofftask';
			$class_d_d = "showdecision";
		}



		/* if (isset($el_decision['sign_off']) && !empty($el_decision['sign_off']) && $el_decision['sign_off'] > 0 && $ele_signoff == false ) {
			$class_d = "disabled showdecision";
			$message ="This decision has been signed off.";
		} */
        ?>

                                                                    <div class="row">
                                                                        <div class="col-sm-3 row_decision_title resp" >
                                                                                <?php echo  htmlentities($el_decision['title'], ENT_QUOTES, "UTF-8") ;  ?>
                                                                        </div>
																		<div class="col-sm-3 col-md-2 col-lg-3 row_decision_title resp" >
																		<?php if(isset($el_decision['creater_id']) && !empty($el_decision['creater_id'])){ ?>
                                                                                 <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $el_decision['creater_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                    <i class="fa fa-user"></i>
                </a>
        <?php $elementmm_creator = $this->Common->elementDecision_creator($el_decision['id'],$project_id,$this->Session->read('Auth.User.id'));
			echo $elementmm_creator;
		}else{
		 echo "N/A";

		}
		?>
                                                                        </div>
                                                                        <div class="col-sm-2 resp" ><span class="deta-time-i">
        <?php
			//echo date('d M, Y g:iA', strtotime($el_decision['created']));
			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($el_decision['created'])),$format = 'd M, Y g:iA');
			?>
                                                                 </span></div>
                                                                        <div class="col-sm-2 resp" ><span class="deta-time-i">
                                                                                <?php
																				//echo date('d M, Y g:iA', strtotime($el_decision['modified']));
																				echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($el_decision['modified'])),$format = 'd M, Y g:iA');
																				?></span>
                                                                        </div>
                                                                        <div class="col-sm-2 col-md-3 col-lg-2 text-center resp">
                                                                            <div class="btn-group">

                                                                                <?php if ($decision_sign_off && $ele_signoff == false) {?>
              <a href="#" class=" update_decision tipText" title="Update Decision" data-id="<?php echo $el_decision['id']; ?>" data-action="update"> <i class="edit-icon"></i> </a>

																				<?php if( $el_decision['creater_id'] == $this->Session->read('Auth.User.id') ){?>
                                      <a href="#" class="sign_off_decision tipText" title="Sign Off" data-value="1" data-msg="Are you sure you want to sign off this Decision?"  data-toggle="confirmation" data-id="<?php echo $el_decision['id']; ?>" data-header="Sign Off" data-eid="<?php echo $this->data['Element']['id']; ?>" > <i class="signoffblack"></i> </a>
																				<?php } else { ?>
								<a href="#" class="disabled   tipText" title="Sign-off" > <i class="signoffblack"></i> </a>


	<?php }
        } else {
            if ($this->data['Element']['sign_off'] == 0) {
                $reopen_disabled = '';

    if(isset($prj[0]['Workspace']['sign_off']) && $prj[0]['Workspace']['sign_off'] == 1){
        $reopen_disabled = 'disabled';
    }


                ?>
                       <a href="#" class="<?php echo $reopen_disabled;?> reopen_decision tipText" title="Reopen" data-toggle="confirmation" data-msg="Are you sure you want to reopen this Decision?" data-value="0" data-id="<?php echo $el_decision['id']; ?>" data-header="Reopen Decision" data-eid="<?php echo $this->data['Element']['id']; ?>" > <i class="signoffblack"></i><span class="hidden-md hidden-sm"> Reopen</span></a>
            <?php } else {
					if($ele_signoff == false ){
				?>
                         <a href="#" class="reopen_decision_rest tipText" title="Reopen" data-toggle="confirmation" data-value="0" data-id="<?php echo $el_decision['id']; ?>" data-header="Reopen Decision" data-eid="<?php echo $this->data['Element']['id']; ?>" > <i class="signoffblack"></i><span class="hidden-md hidden-sm"> Reopen</span></a>
		<?php  } else { ?>
				<a href="#" class="disabled reopen_decision_rest tipText" title="Reopen" data-toggle="confirmation" data-value="0" data-id="<?php echo $el_decision['id']; ?>" data-header="Reopen Decision" data-eid="<?php echo $this->data['Element']['id']; ?>" > <i class="signoffblack"></i><span class="hidden-md hidden-sm"> Reopen</span></a>
		<?php }
		} ?>
        <?php } ?>





                                                                                <!--
                                                                                        <a href="#" class="btn btn-sm btn-info update_decision tipText" title="Update Decision" data-id="<?php echo $el_decision['id']; ?>" data-value="<?php echo $el_decision['title']; ?>" data-action="update"> <i class="fa fa-pencil"></i> </a>

                                                                                        <a href="#" class="btn btn-sm btn-warning sign_off_decision tipText" title="Sign-off" data-value="1" data-msg="Are you sure you want to Sign off?"  data-toggle="confirmation" data-id="<?php echo $el_decision['id']; ?>" data-header="Confirmation" data-eid="<?php echo $this->data['Element']['id']; ?>" > <i class="fa fa-sign-out"></i> </a> -->


<a href="javascript:void(0);" class=" history_decision tipText history" itemtype="element_decisions" itemid="historydecision_<?php echo $el_decision['id']; ?>" data-id="<?php echo $el_decision['id']; ?>" data-action="remove"  title="History"  >
																					<i class="historyblack"></i>
																				</a>


                                                                                <!-- <a href="#" class="btn btn-sm btn-danger remove_decision tipText"  data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_decision', $el_decision['element_id'], 'admin' => FALSE), TRUE); ?>" title="Remove Decision" data-id="<?php echo $el_decision['id']; ?>" data-action="delete"> <i class="fa fa-trash"></i> </a> -->
<?php if($ele_signoff == false ){ ?>
<a href="#" class=" delete_resource tipText"  data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_decision', $el_decision['element_id'], 'admin' => FALSE), TRUE); ?>" title="Remove Decision" data-id="<?php echo $el_decision['id']; ?>" data-msg="Are you sure you want to delete this Decision?"  data-parent="#decisions_table" data-type="decision"> <i class="deleteblack"></i> </a>
<?php } else {?>
<a href="#" class="disabled tipText"  > <i class="deleteblack"></i> </a>
<?php } ?>


                                                                            </div>

                                                                        </div>



                                                                    </div>
                                                            <div id="historydecision_<?php echo isset($el_decision['id']) && !empty($el_decision['id']) ? $el_decision['id'] :'';; ?>" class="history_update" style="display: none;">
                    <?php  //include 'activity/update_history.ctp';?>
                </div>
    <?php } ?>

<?php }else{
		echo '<span class="nodatashow decision">No Decision</span>';
	}  ?>
                                                        </div>

                                                    </div>




<?php if (isset($decisions) && !empty($decisions)) {

?>

                                                        <ul class="list-group margin-tb clearfix "  id="decisionList" <?php if ($element_decision_data_tot < 1) { ?> style="display: none;"<?php } ?> >



                                                            <li class="list-group-item pull-left list-head" id="decisionListTitle" style="display: none;">
                                                                <div class="form-group nopadding">
                                                                    <label class="" for="">Title: </label>

																	<div class="decision_input_group">
																		<input type="text" name="data[ElementDecision][title]" placeholder="Decision title"  id="decision_title" class="form-control <?php echo $tasksignoffcls; ?>"  value="<?php echo htmlentities($this->ViewModel->getElementDecision($el_id, 'title')); ?>" />

																		<?php
																		if( $ele_signoff == false ){
																			if ($decision_sign_off) { ?>
																			<span class="error-message text-danger" style=""></span>
																			<a href="javascript:void(0)" id="save_decision" class="btn btn-sm btn-success save_decision_trigger <?php echo $class_d.' '.$class_d_d;?>" data-msg="<?php echo $message;?>" >Save
																			</a>
																		<?php } else { ?>
																			<span class="error-message text-danger" style=""></span>
																			<a href="javascript:void(0)" id="save_decision" class="btn btn-sm btn-success save_decision_trigger <?php echo $class_d.' '.$class_d_d;?>" data-msg="<?php echo $message;?>" >Save
																			</a>
																		<?php }
																		} else { ?>
																			<span class="error-message text-danger" style=""></span>
																			<a href="javascript:void(0)" id="save_decision" class="btn btn-sm btn-success showdecision save_decision_trigger <?php echo $class_d.' '.$class_d_d;?>" data-msg="<?php echo $message;?>" >Save
																			</a>
																		<?php } ?>
																	</div>

                                                                </div>
                                                            </li>

                                                            <li class="list-group-item pull-left list-head bg-dark-gray">
                                                                <span class="list-col list-col-1"> Decision Stage</span>
                                                                <span class="list-col list-col-2"> Stage Status</span>
                                                                <span class="actions border-radius list-col-3"></span>
                                                            </li>


                                                            <?php
															$ds = 1;
															foreach ($decisions as $key => $values) { ?>

                                                                <?php
                                                                $s_status = 0;
                                                                $el_decisions = null;

                                                                $el_decisions = $this->ViewModel->getDecisionDetail($values['Decision']['id'], $el_id);
                                                                $el_dd_flag = false;

                                                                if (isset($el_decisions) && !empty($el_decisions)) {

                                                                    if (isset($el_decisions[0]['element_decision_details']) && !empty($el_decisions[0]['element_decision_details'])) {
                                                                        $el_dd_flag = true;
                                                                        $s_status = $el_decisions[0]['element_decision_details']['stage_status'];
                                                                        // e(' ** '.$s_status.'->'.$el_decisions[0]['element_decision_details']['id']);
                                                                    }
                                                                }
                                                                ?>

                                                                <li class="list-group-item pull-left desci-listing" >
                                                                    <span class="list-col list-col-1">
                                                                        <span >
                                                                        <?php echo $ds.'. '.htmlentities($values['Decision']['title']); ?>
                                                                            <a href="#" class="tipText tips" title="" data-original-title="<?php echo htmlentities($values['Decision']['tip_text']); ?>">
                                                                                <span class="badge fa fa-question"> </span>
                                                                            </a>
                                                                        </span>
                                                                    </span>
                                                                    <a class="list-col list-col-2">
        <?php
        $iddid = ($el_dd_flag) ? $el_decisions[0]['element_decision_details']['id'] : 0;
        if ($this->ViewModel->getElementDecisionStatus($iddid)) {
            ?>
                                                                            <i class="fa fa-check text-green"></i>
        <?php } else { ?>
                                                                            <i class="fa fa-times text-red"></i>
        <?php } ?>
                                                                    </a>
                                                                    <span class="actions border-radius list-col-3">
                                                                        <a class="action-button bg-white plus">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                        <a class="action-button bg-white minus">
                                                                            <i class="fa fa-minus"></i>
                                                                        </a>
                                                                    </span>
                                                                </li>

                                                                <li class="list-group-item pull-left desci-form list-head" style="display: none;" data-edd-id="<?php echo ( $el_dd_flag ) ? $el_decisions[0]['element_decision_details']['id'] : '' ?>">

        <?php
        echo $this->Form->create('ElementDecisionDetail', array('url' => array('controller' => 'entities', 'action' => 'add_decision_detail', $element_id), 'class' => 'formAddElementDecision', 'id' => 'formAddElementDecision_' . $key, 'style' => '', 'enctype' => 'multipart/form-data'));


        ?>


                                                                    <div class="padding-bottom clearfix decision_detail margin-top" id="decision_description" style="">
                                                                        <?php // Required hidden fields  ?>

                                                                        <input type="hidden" name="data[ElementDecisionDetail][create_activity]" class="form-control" value="1" />

                                                                        <input type="hidden" name="data[ElementDecision][element_id]" class="form-control" value="<?php echo $this->data['Element']['id']; ?>" />

                                                                        <input type="hidden" name="data[ElementDecisionDetail][element_decision_id]" class="form-control" value="<?php echo $this->ViewModel->getElementDecision($el_id . 'id'); ?>" />

                                                                        <input type="hidden"  name="data[ElementDecisionDetail][primary_id]" class="form-control" value="<?php echo ($el_dd_flag) ? $el_decisions[0]['element_decision_details']['id'] : ''; ?>" />

                                                                        <input type="hidden" name="data[ElementDecisionDetail][decision_id]" class="form-control" value="<?php echo $values['Decision']['id'] ?>" />

        <?php // Required hidden fields   ?>

        <?php // Description for stage
		$radiobuttoncls = "";
		if( ($ele_signoff == true && isset($el_decision['sign_off'])) || (!empty($el_decision['sign_off']) && $el_decision['sign_off'] > 0)  ){
			$radiobuttoncls = "disabled";
		}
		?>
                                                                        <div class="form-group margin-top">
                                                                            <label class="" for=" ">Description:</label>


                                                                            <span class="pull-right stagecompletediscrition" for="">

                                                                                <span class="pull-left stage_group stage_group_wrapper <?php echo $radiobuttoncls;?>" >
        <?php $iddid = ($el_dd_flag) ? $el_decisions[0]['element_decision_details']['id'] : 0; ?>
                                                                                    <input type="radio" name="data[ElementDecisionDetail][stage_status]" class="stage_radio ok" <?php if ($this->ViewModel->getElementDecisionStatus($iddid) == true) { ?> checked="checked" <?php } ?> value="1" id="stage_status_complete<?php echo $key; ?>" />

                                                                                    <label class=" pull-right " for="stage_status_complete<?php echo $key; ?>">Stage Complete </label>

                                                                                </span>

                                                                                <span class="pull-right stage_group stage_group_wrapper  <?php echo $radiobuttoncls;?>"  for="">
                                                                                    <input type="radio" name="data[ElementDecisionDetail][stage_status]" class="stage_radio cancel"  <?php if ($this->ViewModel->getElementDecisionStatus($iddid) == false) { ?> checked="checked" <?php } ?> value="0" />

                                                                                    <label class="pull-right " for="stage_status_incomplete">Stage Incomplete </label>

                                                                                </span>
                                                                            </span>


                                                                            <textarea rows="10" class="form-control decision_desc <?php echo $tasksignoffcls;?>" placeholder="Decision description" name="data[ElementDecisionDetail][description]" ><?php echo ($el_dd_flag) ? $el_decisions[0]['element_decision_details']['description'] : ''; ?></textarea>

                                                                            <span class="error-message text-danger" style=""> </span>
                                                                        </div>
                                                                    </div>
        <?php // Description for stage  ?>

        <?php // Submit  ?>
																<?php

										 $el_decision['sign_off'] = (!isset($el_decision['sign_off']) || empty($el_decision['sign_off']) ) ? 0 : 1;




																if( isset($ele_signoff) && $ele_signoff == false &&  $el_decision['sign_off'] >=0){ ?>
                                                                    <div class="form-group text-center no-margin" id="save_decision_wrapper" style="">
                                                                        <a data-form="formAddElementDecision_<?php echo $key; ?>" href="javascript:void(0)" class="btn btn-sm btn-success save_decision submit " data-id="<?php echo ($el_dd_flag) ? $el_decisions[0]['element_decision_details']['id'] : ''; ?>"><?php echo ($el_dd_flag) ? 'Save' : 'Save'; ?>
                                                                        </a>
                                                                    </div>
																<?php } else { ?>
																	<div class="form-group text-center no-margin" style="">
                                                                        <a  data-form="formAddElementDecision_<?php echo $key; ?>" href="javascript:void(0)" data-msg="<?php echo $message;?>" class="btn btn-sm btn-success submit showdecision" data-id="<?php echo ($el_dd_flag) ? $el_decisions[0]['element_decision_details']['id'] : ''; ?>"><?php echo ($el_dd_flag) ? 'Save' : 'Save'; ?>
                                                                        </a>
                                                                    </div>
																<?php } ?>
        <?php // Submit   ?>
        <?php echo $this->Form->end(); ?>
                                                                </li>
    <?php $ds++; } // end decisions list foreach   ?>
<?php } // end decisions list if   ?>
                                                    </ul>







<style>

.edit_decision{
    display: block;
    margin: 0 0 15px 0;
    border: none;
    padding: 0;
}
.create_decision_form{ overflow: hidden;l
</style>
<script type="text/javascript">
	$(function(){
		$('body').delegate("#txt_decision_title", "keyup focus", function(event){
			var characters = 50;
			event.preventDefault();
			var $error_el = $(this).parents("#formAddEditElementDecision").find('.error-message:first');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})



		$('body').delegate("#decision_title", "keyup focus", function(event){
			var characters = 50;
			event.preventDefault();
			var $error_el = $(this).parents("ul.list-group").find('.error-message:first');
			if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
				$.input_char_count(this, characters, $error_el);
			}
		})
	})
</script>