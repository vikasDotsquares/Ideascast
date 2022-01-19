<style>
.textbox.combo{ margin: 20px 0 15px 0; }
#accordion .panel-default{
 background: #fff none repeat scroll 0 0;
}
#accordion .panel-default > .panel-heading {
  background: #f0ad4e none repeat scroll 0 0;
  border-color: #ddd;
  color: #000;
  display:block;
}

#accordion .panel-header, .panel-body{
	border:none;
	color: #333;
	background: #fff none repeat scroll 0 0;
}

 .datagrid-view2{ background: #fff !important;}


.multiselect-container.dropdown-menu > li:not(.multiselect-group) {
	margin-top: -5px;
}
.multiselect-container a:hover .username {
	color: #000 !important;
}
.multiselect-container > li.multiselect-item.multiselect-group > a{
    display: block;

    padding: 7px 5px 8px 41px !important;
}

.multiselect-container > li.multiselect-item.multiselect-group  a:hover {
    color: #FFF !important;
}
.input-group-btn .btn.btn-default.multiselect-clear-filter {
	line-height: 24px;
}
.multiselect-container.dropdown-menu > li:not(.multiselect-group) {
	margin-top: 0px;
}


.wysihtml5-sandbox {

    height: 75px !important;

}

.vote_result_box h4{
	font-size:13px !important;
}

</style>
<?php
$onemsg = null;
$datatitle = '';

if( isset( $message ) && !empty($message) && empty($overdue)  ){
	$onemsg = 'You cannot add a Feedback because this Task has been signed off.';
	$datatitle = 'Signed Off';
}

if( isset( $message ) && !empty($message) && !empty($overdue)  ){
	$onemsg = 'You cannot request Feedback because this Task is overdue.';
	$datatitle = 'Create Feedback';
}

if( isset( $messagepre ) && !empty($messagepre) ){
	$onemsg = 'You cannot add a Feedback because this Task has no schedule.';
	$datatitle = 'No Task Schedule';
}

if( isset( $notstarted ) && !empty($notstarted) ){
	$onemsg = 'You cannot add a Feedback because this Task has not started.';
	$datatitle = 'Not Started';
}

?>

											<div class="feedback_form">
                                                        <div data-msg="<?php echo htmlentities($onemsg);?>" class="list-form <?php echo $class_d;?> <?php echo $class_prevant;?> border bg-warning nopadding" data-title="<?php echo $datatitle; ?>" >
                                                            <a href="" class="list-group-item clearfix open_form noborder-radius" >
                                                                <span class="pull-left"><i class="asset-all-icon re-FeedbackBlack"></i>&nbsp;   New Feedback</span>
                                                                <span class="pull-right"><!--<i class="fa fa-plus"></i>--></span>
                                                            </a>
                                                            <div id="feedbackstep1">


                                                            <?php
															$pointer_event = '';
															if( $ele_signoff == true ){
																$pointer_event = 'signoffpointer';
															}

															echo $this->Form->create('Feedback', array('url' => array('controller' => 'entities', 'action' => 'add_feedback', $element_id), 'class' => "padding formAddElementFeedback $pointer_event", 'style' => '', 'enctype' => 'multipart/form-data')); ?>

																<input type="hidden" name="data[Feedback][element_id]" class="form-control" value="<?php echo $this->data['Element']['id']; ?>" />

																<input type="hidden" name="data[Feedback][workspace_id]" class="form-control" value="<?php echo $workspace_id; ?>" />

																<input type="hidden" name="data[Feedback][id]" class="form-control" id="newFeedbackid" value="" />

                                                                <div class="form-group">
                                                                    <label class=" " for=" ">Title:</label>
                                                                    <input type="hidden" name="data[Feedback][project_id]" value="<?php echo $project_id; ?>" />
                                                                    <input type="text" name="data[Feedback][title]" placeholder="Feedback title" class="form-control" value="" />
                                                                    <span class="error-message text-danger" style=""></span>
                                                                </div>


                                                                <div class="form-group">
                                                                    <label class=" " for=" ">Reason:</label>
                                                                    <textarea rows="2" class="form-control feedback_desc" placeholder="Feedback reason" name="data[Feedback][reason]" id="feedback_desc"></textarea>
                                                                    <span class="error-message text-danger" style=""> </span>
                                                                </div>

																<div class="form-group clearfix">
                                                                    <label>Feedback For:</label>
                                                                    <div>
<?php echo $this->Form->input('Feedback.feedback_for', array('type' => 'textarea', 'id' => 'feedbackfor_desc', 'class' => 'form-control feedbackfor_desc', 'div' => false, 'label' => false)); ?>
                                                                        <span class="error-message text-danger"> </span>
                                                                    </div>
                                                                </div>


                                                                <div class="form-group clearfix">
                                                                    <div class=" form-group input-daterange">
                                                                         <div class="row date-row">
                                                                        <div class="col-sm-6 create-edit-date-f">
																			<label class="control-label" for="start_date">Start Date:</label>
                                                                            <div class="input-group" style="position:relative;">
<?php
echo $this->Form->input('Feedback.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'feedbackstart_date', 'required' => false,'value' => '', 'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>
                                                                                <span class="error-message text-danger error-calendere" style=""> </span>
                                                                                <div class="input-group-addon open-start-date-picker_feedback1 calendar-trigger">
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-6 create-edit-date-f">
																			<label class="control-label" for="end_date">End Date:</label>
                                                                            <div class="input-group" style="position:relative;">
<?php
echo $this->Form->input('Feedback.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'feedbackend_date', 'required' => false,'value' => '', 'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>
                                                                                <span class="error-message text-danger error-calendere" style=""> </span>
                                                                                <div class="input-group-addon  open-end-date-picker_feedback calendar-trigger">
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                                <div class="form-group">

																		<a  id=""   class="btn btn-sm btn-success save_feedback submit save_con">
                                                                            <!--<i class="fa fa-fw fa-save"></i>--> Save & Continue
																		</a>
																	<a  id="cancel_feedback"  class="btn btn-sm btn-danger cancel_feedback submit save_con">
                                                                            <!--<i class="fa fa-fw fa-save"></i>--> Cancel
                                                                    </a>
                                                                </div>
                                                            </form>
                                                            </div>

                                                            <div id="feedbackstep2"  style="display:none;">

										<?php echo $this->Form->create('Feedback', array('url' => array('controller' => 'entities', 'action' => 'add_feedback', $element_id), 'id'=>'formAddFeedbackDoc', 'class' => "padding formAddElementFeedback $pointer_event", 'style' => '', 'enctype' => 'multipart/form-data')); ?>

										<input type="hidden" name="data[FeedbackUser][feedback_id]" id="feedback_id" class="form-control feedback_id" value="" />

													<div class="row">
														<div class="col-lg-8 col-md-8 col-sm-12">
															<label><a id="onn" href="javascript:void(0)"> Select Users </a>| <a href="javascript:void(0)" id="off"> Select Groups </a></label>

																<div class="form-group">

																	<select id="multiselect_groups"  multiple="multiple"></select>
																</div>
																<span class="error-message text-danger"> </span>


														</div>
														</div>

														<div class="row">
															<?php echo $this->Form->input('FeedbackAttachment.feedback_id', [ 'type' => 'hidden','id'=>'FeedbackAttachment']); ?>
															<?php echo $this->Form->input('FeedbackAttachment.element_id', [ 'type' => 'hidden', 'value' => $this->data['Element']['id']]); ?>
															<?php echo $this->Form->input('FeedbackAttachment.project_id', [ 'type' => 'hidden', 'value' => $project_id]) ;?>
															<div class="col-sm-12">
																<label>Upload</label>
															</div>
															<div class="col-lg-5 col-md-6 col-sm-11 mg_top">
																<div class="feedback-file-upload-save">
																<div class="input-group form-group">
																	<div class="input-group-addon">
																		<i class="uploadblack"></i>
                                                                    </div>
                                                                    <span data-original-title=" Click to Upload a file" class="docUpload icon_btn bg-white border-radius-right tipText feedback_att" title="">
													<input type="file" placeholder="Upload File" id="doc_feedback_file" class="form-control upload" name="data[FeedbackAttachment][file_name]"> <span id="feedbackupText" class="text-blue">Upload Document</span>
                                                                     </span>
                                                                </div>
																	</div>
																<a id="feedbacksave_document" class="btn btn-success btn_progress btn_progress_wrapper btn-sm save_document  nomargin-top"><div class="btn_progressbar"></div><span class="text">Upload</span></a>
																<div class="error-messages text-dangers showing" id="feedbacktest_for_error">Document is required.</div>
																<div class="error-messages text-dangers showing" id="feedbackdoc_type_error">Invalid file format.</div>
                                                            </div>


															<div class="col-lg-12 col-md-12 col-sm-12">
																<div id="feedbackDocs"></div>
															</div>
                                                        </div>

                                                                <div class="form-group step2_btn clearfix">

                                                                    <a  href="javascript:void(0)" class="btn btn-sm btn-danger pull-left nomargin-left " id="prev_2_feedback">
                                                                            <!--<i class="fa fa-trash"></i>--> Previous
                                                                    </a>
                                                                    <a  target="_blank" id="" href="#" class="btn btn-sm btn-success save_feedback_final pull-left submit">
                                                                            <!--<i class="fa fa-fw fa-save"></i>--> Finish
                                                                 </a>
<a href="javascript:void(0);" id="cancelfeedback_step2" class="btn btn-sm btn-danger">Cancel</a>
                                                                    </div>
                                                                </form>
                                                            </div>
														<?php echo $this->Form->end(); ?>
                                                        </div>
                                                        </div>









 <div class="table_wrapper clearfix" id="feedbacks_table" data-model="feedback" data-limit="1">
		<div class="table_head">
			<div class="row">
				<div class="col-sm-3 resp">
					<h5> Title</h5>
				</div>

				<div class="col-sm-2 text-left resp">
					<h5> Creator</h5>
				</div>

				<div class="col-sm-2 text-left resp">
					<h5> Start</h5>
				</div>

				<div class="col-sm-2 text-left resp">
					<h5> End</h5>
				</div>

				<div class="col-sm-3 text-center resp">
					<h5> Action</h5>
				</div>
			</div>
		</div>

	<div class="table-rows  table-catchers data_catcher">
<?php


// pr($feedbacks);
if (isset($feedbacks) && !empty($feedbacks)) {
	// pr($feedbacks);
    foreach ($feedbacks as $detail) {
        $data = $detail['Feedback'];
        $users = $detail['FeedbackUser'];
		//pr($detail);
		if(isset($data) && !empty($data)){
        ?>


				<?php

				$disabledSignOff = '';
				if(isset($data['sign_off']) && !empty($data['sign_off'])){
					$disabledSignOff = 'disabled';
				}
				?>

                <div class="row" id="feedback_row_<?php echo $data['id']; ?>"  style="padding-bottom: 10px;">
                    <div class="col-sm-3 resp" id="title<?php echo $data['id']; ?>" > <?php echo  htmlentities($data['title'], ENT_QUOTES, "UTF-8") ; ; ?></div>
					<!--<div class="col-sm-1 text-center resp" id="feedbackparicipants<?php echo $data['id']; ?>" > <?php echo count($detail['FeedbackUser']); ?></div>-->
					<div class="col-sm-2 text-left resp"   >

					<a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $data['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
		                    <i class="fa fa-user"></i>
		                </a>
						<?php
						$UD = $this->Common->userFullname($data['user_id']);
				echo $UD ;
				?>

					</div>

                    <div class="col-sm-2 text-left resp" id="feedbackstartdate<?php echo $data['id']; ?>"  ><?php
					echo date('d M, Y', strtotime($data['start_date']));
					//echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['start_date'])),$format = 'd M, Y');
					?></div>
                    <div class="col-sm-2 text-left resp" id="feedbackenddate<?php echo $data['id']; ?>"  > <?php
					echo date('d M, Y', strtotime($data['end_date']));
					//echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['end_date'])),$format = 'd M, Y');
					?></div>

                    <div class="col-sm-3 text-center resp">
                        <div class="btn-group" >

                            <?php
								$viewURL = SITEURL."entities/view_feedback/".$data['id'];
								?>

							<a data-toggle="modal" rel="<?php echo $data['id']; ?>" class="viewuser tipText"  data-target="#"   title="Feedback Details " data-whatever="<?php echo $viewURL; ?>"  data-tooltip="tooltip" data-elem="feedback" data-placement="top" ><i class="viewblack"></i></a>



                           <?php
						   $participants_feedbackuser = SITEURL."entities/participants_feedbackuser/".$data['id'];
						   ?>

							<a data-toggle="modal" rel="<?php echo $data['id']; ?>" class="viewuserPIUF update-form-user1 tipText <?php //echo $class_d;?>" data-msg="<?php echo $message;?>"  data-target="#"    title="Participant Info " data-whatever="<?php echo $participants_feedbackuser; ?>"  data-tooltip="tooltip" data-placement="top" >
							<i class="teamblack"></i></a>


					   <?php
							  $disabledfeedback = '';
							  if((isset($detail['Feedback']['end_date']) && !empty($detail['Feedback']['end_date']) && $detail['Feedback']['end_date'] < date('Y-m-d 00:00:00')) ){
                              $disabledfeedback = 'disabled';
							}
					  ?>

						<a href="#" <?php if(!empty($disabledfeedback)){ echo $disabledfeedback; }?> class=" update_feedback tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_feedback', $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="update" title="Update Feedback">
                                <i class="showmoreblack icon_down"></i>
                                <i class="showlessblack icon_up"></i>
                            </a>
                            <?php if(isset($data['sign_off']) && !empty($data['sign_off'])){ ?>
								<a href="#"  class="sign_off_feedback tipText <?php echo $disabledfeedback; ?>" data-remote="" data-id="<?php echo $data['id']; ?>" data-action="Reopen" title="Reopen" data-msg="Are you sure you want to reopen this Feedback?"  data-eid="<?php echo $this->data['Element']['id']; ?>" data-toggle="confirmation">
	                                <i class="reopenblack"></i>
	                            </a>
                            <?php }else{ ?>
								<a href="#"  class="sign_off_feedback tipText <?php echo $disabledfeedback; ?>" data-remote="" data-id="<?php echo $data['id']; ?>" data-action="Sign-Off" title="Sign Off" data-msg="Are you sure you want to sign off this Feedback?"  data-eid="<?php echo $this->data['Element']['id']; ?>"  data-toggle="confirmation">
	                                <i class="signoffblack"></i>
	                            </a>
                            <?php } ?>

                            <?php
                            $disabled = '';
                            if(isset($detail['FeedbackResult']) && !empty($detail['FeedbackResult'])){
                                $disabled = 'disabled';
                            }
                            ?>

							<a href="javascript:void(0);" class=" history_feedback tipText history" itemtype="feedback" itemid="historyfeedback_<?php echo $data['id']; ?>"  data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
								<i class="historyblack"></i>
							</a>
							<?php if($ele_signoff == false ){ ?>
								<a href="#"  class=" tipText remove_feedback_btn <?php echo $disabled; ?>" data-remote="<?php echo SITEURL.'entities/remove_feedback'; ?>" data-id="<?php echo $data['id']; ?>" data-action="remove" title="Remove Feedback" >
                                <i class="deleteblack"></i>
								</a>
							<?php } else { ?>
								<a href="#" <?php  echo $disabled; ?> class=" tipText disabled"  >
                                <i class="deleteblack"></i>
								</a>
							<?php } ?>

                        </div>
                    </div>

                          <div class="prt_user_row">
                    <div class="col-sm-12 col-sm-12 resp respElp">
						<div id="View-result-form<?php echo $data['id']; ?>" class="View-result-form" style="display:none;">

						</div>
		<div class="update-form" style="display:none;">
			<div id="step1a">
                <?php
                $id = $data['id'];
                echo $this->Form->create('Feedback', array('url' => array('controller' => 'entities', 'action' => 'update_feedback', $id), 'class' => "formupdateFeedback $pointer_event $disabledSignOff", 'style' => '', 'enctype' => 'multipart/form-data'));
                ?>
                <input type="hidden" name="data[Feedback][element_id]" class="form-control" value="<?php echo $this->data['Element']['id']; ?>" />
                <input type="hidden" name="data[Feedback][workspace_id]" class="form-control" value="<?php echo $workspace_id; ?>" />
                <input type="hidden" name="data[Feedback][id]" id="VoteUservote_id" class="form-control vote_id" value="<?php if (isset($data['id'])) echo $data['id']; ?>" />


                <div class="form-group">
                    <label class=" " for=" ">Title:</label>
                    <input type="hidden" name="data[Feedback][project_id]" value="<?php echo $project_id; ?>" />
                    <input type="text" name="data[Feedback][title]" placeholder="Feedback title" class="form-control" value="<?php if (isset($data['title'])) echo htmlentities($data['title']); ?>" />
                    <span class="error-message text-danger" style=""></span>
                </div>


                <div class="form-group">
                    <label class=" " for=" ">Reason:</label>
                    <textarea rows="2" class="form-control vote_desc" placeholder="Feedback reason" name="data[Feedback][reason]" id="feedback_desciption_<?php echo $data['id']; ?>"><?php if (isset($data['reason'])) echo $data['reason']; ?></textarea>
                    <span class="error-message text-danger" style=""> </span>
                </div>


				<div class="form-group">
                    <label class=" " for=" ">Feedback For:</label>
                    <textarea rows="2" class="form-control feedback_for feedbackfor_desc" placeholder="Feedback For" name="data[Feedback][feedback_for]" id="feedback_desciption_<?php echo $data['id']; ?>"><?php if (isset($data['feedback_for'])) echo $data['feedback_for']; ?></textarea>
                    <span class="error-message text-danger" style=""> </span>
                </div>

        <?php
        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $this->request->data['Feedback']['start_date'] = date('d-m-Y', strtotime($data['start_date']));
			//$this->request->data['Feedback']['start_date'] = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['start_date'])),$format = 'd M Y');
        }

        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $this->request->data['Feedback']['end_date'] = date('d-m-Y', strtotime($data['end_date']));
			//$this->request->data['Feedback']['end_date'] = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['end_date'])),$format = 'd M Y');
        }
        ?>



                                                                <div class="form-group clearfix">
                                                                    <div class="data-catcher-new form-group input-daterange ">
                                                                         <div class="row date-row" style="border:none;border:none;margin:0 -15px;">
                                                                        <div class="col-sm-6 create-edit-date-f">
																			<label class="control-label" for="start_date">Start Date:</label>
                                                                            <div class="input-group" style="position:relative;">
        <?php $feedbackstartdate = ( isset($this->request->data['Feedback']['start_date']) && !empty($this->request->data['Feedback']['start_date']) )? date("d M Y", strtotime($this->request->data['Feedback']['start_date'])) : date("d M Y");




		echo $this->Form->input('Feedback.start_date', [ 'type' => 'text', 'label' => false, 'rel' => $data['id'], 'div' => false, 'id' => "feedbackstart_date_$id", 'required' => false, 'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value'=> $feedbackstartdate]); ?>
                                                                                <span class="error-message text-danger error-calendere" style=""> </span>
																				   <div class="input-group-addon open-start-date-picker-update_feedback calendar-trigger">
																					<i class="fa fa-calendar"></i>
																				 </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-6 create-edit-date-f">
																			<label class="control-label" for="end_date">End Date:</label>
                                                                            <div class="input-group" style="position:relative;">


<?php $feedbackenddate = ( isset($this->request->data['Feedback']['end_date']) && !empty($this->request->data['Feedback']['end_date']) )? date("d M Y", strtotime($this->request->data['Feedback']['end_date'])) : date("d M Y");
		echo $this->Form->input('Feedback.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => "feedbackend_date_$id", 'rel' => $id, 'required' => false, 'readonly' => 'readonly', 'class' => 'form-control dates input-small end_date', 'value'=> $feedbackenddate ]); ?>
                                                                                <span class="error-message text-danger error-calendere" style=""> </span>
																					<div class="input-group-addon  open-end-date-picker-update_feedback calendar-trigger">
																						<i class="fa fa-calendar"></i>
																					</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                </div>


																				<?php if($ele_signoff == false ){ ?>
                                                                                    <div class="form-group">
                                                                                        <a id="<?php echo $id; ?>" href="#" class="btn btn-sm btn-success update_feedback_btn submit" <?php echo $disabledSignOff; ?>>
                                                                                                <!--<i class="fa fa-fw fa-save"></i>--> Update
                                                                                        </a>
                                                                                    </div>
																				<?php } else {?>
																					<div class="form-group">
                                                                                        <a id="<?php echo $id; ?>" href="#" class="btn btn-sm btn-success disabled submit">
                                                                                                 Update
                                                                                        </a>
                                                                                    </div>
																				<?php } ?>
                                                                                    </form>
                                                                                </div>
                                                                            </div>

																			 <div id="participants_feedback_user_<?php echo $data['id']; ?>" class="update-form-user panel panel-dafult" style="display:none;">

                                                                            </div>

																	</div>
																	</div>
																	</div>
        <div id="historyfeedback_<?php echo isset($data['id']) && !empty($data['id']) ? $data['id'] :'';; ?>" class="history_update" style="display: none;">
                    <?php  //include 'activity/update_history.ctp';?>
                </div>

    <?php } ?>
    <?php } ?>
<?php }else{
		echo '<span class="nodatashow feedback">No Feedback</span>';
	} ?>
                                                        </div>
														<!------ Feedback Rows -------->

													</div>
<script type="text/javascript">
$('body').delegate("input[name='data[Feedback][title]']", "keyup focus", function(event){
	var characters = 50;
	event.preventDefault();
	var $error_el = $(this).next('.error-message:first');
	if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
		$.input_char_count(this, characters, $error_el);
	}
})
</script>

