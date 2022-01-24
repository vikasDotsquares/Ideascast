<?php
$onemsg = null;
$datatitle = '';

if( isset( $message ) && !empty($message) && empty($overdue) ){
	$onemsg = 'You cannot add a Vote because this Task has been signed off.';
	$datatitle = 'Signed Off';
}

if( isset( $message ) && !empty($message) && !empty($overdue)  ){
	$onemsg = 'You cannot request a Vote because this Task is overdue.';
	$datatitle = 'Create Vote';
}

if( isset( $messagepre ) && !empty($messagepre) ){
	$onemsg = 'You cannot add a Vote because this Task has no schedule.';
	$datatitle = 'No Task Schedule';
}

if( isset( $notstarted ) && !empty($notstarted) ){
	$onemsg = 'You cannot add a Vote because this Task has not started.';
	$datatitle = 'Not Started';
}
?>
<!-- Indivisual Form -->
<div class="vote_form mindmap_form">
    <div data-msg="<?php echo $onemsg; ?>" class="list-form <?php echo $class_d; ?> <?php echo $class_prevant;?> border bg-warning nopadding"  data-title="<?php echo $datatitle; ?>" >
        <a href="" class="list-group-item clearfix open_form noborder-radius" >
            <span class="pull-left"><i class="asset-all-icon re-VoteBlack"></i>&nbsp; New Vote</span>
            <span class="pull-right"><!--<i class="fa fa-plus"></i>--></span>
        </a>
        <div id="step1">


<?php
$pointer_event = '';
if( $ele_signoff == true ){
	$pointer_event = 'signoffpointer';
}
echo $this->Form->create('Vote', array('url' => array('controller' => 'entities', 'action' => 'add_vote', $element_id), 'class' => "padding formAddElementVote $pointer_event", 'style' => '', 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" name="data[Vote][element_id]" class="form-control" value="<?php echo $this->data['Element']['id']; ?>" />

            <input type="hidden" name="data[Vote][id]" id="VoteUservote_id" class="form-control vote_id newVoteId" value="" />


            <div class="form-group">
                <label class=" " for=" ">Title:</label>
                <input type="hidden" name="data[Vote][project_id]" value="<?php echo $project_id; ?>" />
                <input type="hidden" name="data[Vote][workspace_id]" class="form-control" value="<?php echo $workspace_id; ?>" />
                <input type="text" name="data[Vote][title]" placeholder="Vote title" class="form-control" value="" />
                <span class="error-message text-danger" style=""></span>
            </div>


            <div class="form-group">
                <label class=" " for=" ">Reason:</label>
                <textarea rows="2" class="form-control vote_desc" placeholder="Vote Reason" name="data[Vote][reason]" id="vote_desc"></textarea>
                <span class="error-message text-danger" style=""> </span>
            </div>
            <div class="form-group clearfix ">
                <div class="form-group input-daterange">
                     <div class="row date-row">
                    <div class="col-sm-6 create-edit-date-f">
						<label class="control-label" for="start_date">Start Date:</label>
                        <div class="input-group" style="position:relative;">
<?php echo $this->Form->input('Vote.start_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'start_date1', 'required' => false, 'value' => '', 'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>
                            <span class="error-message text-danger error-calendere" style=""> </span>
                            <div class="input-group-addon open-start-date-picker1 calendar-trigger">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>



                    </div>


                    <div class="col-sm-6 create-edit-date-f">
						<label class="control-label" for="end_date">End Date:</label>
                        <div class="input-group" style="position:relative;">
<?php echo $this->Form->input('Vote.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => 'end_date1', 'required' => false, 'value' => '', 'readonly' => 'readonly', 'class' => 'form-control dates input-small']); ?>
                            <span class="error-message text-danger error-calendere" style=""> </span>
                            <div class="input-group-addon  open-end-date-picker1 calendar-trigger">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>

</div>
                    </div>

                </div>
            </div>
            <div class="form-group">
                <a  target="_blank" id="" href="#" class="btn btn-sm btn-success save_vote submit save_con" >
                        <!--<i class="fa fa-fw fa-save"></i>--> Save & Continue
                </a>&nbsp;&nbsp;<a href="javascript:void(0);" id="cancelvotefirststep" class="btn btn-sm btn-danger save_con">Cancel</a>
            </div>

            </form>
        </div>

        <div id="step2"  style="display:none;">
<?php echo $this->Form->create('Vote', array('url' => array('controller' => 'entities', 'action' => 'add_vote', $element_id), 'class' => "padding formAddElementVote $pointer_event", 'style' => '', 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" name="data[VoteQuestion][vote_id]" id="vote_id" class="form-control vote_id" value="" />

            <input type="hidden" name="data[VoteQuestion][id]" id="question_id" class="form-control question_id" value="" />


            <div class="form-group clearfix" >
                <label class="col-sm-2 " for=" ">Voting Method:</label>
                <div class="col-sm-6">
<?php
// get vote type description
$voteTypeWithDescription = array();
foreach($voteTypes as $key => $typeList){
		$typeDesc = $this->Common->VoteTypeDescription($key);
		$voteTypeWithDescription[] = array('name' => $typeList, 'value' => $key, 'title' => $typeDesc);

}
//pr($voteTypeWithDescription);

echo $this->Form->input('VoteQuestion.vote_type_id', array('options' => $voteTypeWithDescription, 'empty' => '- Select -', 'type' => 'select', 'id' => 'votesystem', 'class' => 'form-control', 'title'=>'', 'div' => false, 'label' => false)); ?>
                    <span class="error-message text-danger" style=""> </span>
                </div>
				<div class="col-sm-4" style="display:none;" id="voteTypeDescription" ><textarea id="voteTypeDescriptionInput"readonly="readonly" ></textarea></div>
            </div>

            <div class="form-group clearfix">
                <label class="col-sm-2">Voting For:</label>
                <div class="col-sm-6">
<?php echo $this->Form->input('VoteQuestion.title', array('type' => 'text', 'id' => 'votesquestion', 'class' => 'form-control votesquestion', 'div' => false, 'label' => false)); ?>
                    <span class="error-message text-danger"> </span>
                </div>
            </div>

            <div id="number_of_options" style="display:none;">
                <div class="form-group clearfix">
                    <label class="col-sm-2">Number of Options:</label>
                    <div id="options_count" class="col-sm-6"></div>

                </div>
            </div>


			<div id="distributed_options" style="display:none;">
                <div class="form-group clearfix">
                    <label class="col-sm-2">Number of Votes:</label>
                    <div id="distributed_count" class="col-sm-6"></div>

                </div>
            </div>

            <div id="options" style="display:none;">
                <div class="form-group clearfix">
                    <label class="col-sm-2">Voting Options:</label>
                    <div id="options_questions" class="col-sm-10 nopadding"></div>
                    <span class="error-message text-danger"> </span>
                </div>
            </div>


            <div class="form-group step2_btn">

                <a  href="javascript:void(0);" class="btn btn-sm btn-danger " id="prev_step2">
                        <!--<i class="fa fa-trash"></i>--> Previous
                </a>
                <a  target="_blank" id="save_vote_2" href="#" class="btn btn-sm btn-success save_vote submit">
                        <!--<i class="fa fa-fw fa-save"></i>--> Save & Continue
                </a><a href="javascript:void(0);" id="cancelvote" class="btn btn-sm btn-danger">Cancel</a>

            </div>
            </form>
        </div>


        <div id="step3"  style="display:none;">
<?php echo $this->Form->create('Vote', array('url' => array('controller' => 'entities', 'action' => 'add_vote', $element_id), 'id' => 'VoteUpdateElementForm', 'class' => "padding formAddElementVote_finish $pointer_event", 'style' => '', 'enctype' => 'multipart/form-data')); ?>
            <input type="hidden" name="data[VoteUser][vote_id]" id="VoteUservote_id" class="form-control vote_id" value="" />

            <div class="panel  panel-dafult">
                <div class="panel-heading bg-green"><a style="color:#fff" id="onn1" href="javascript:void(0)"> Select Users </a>| <a style="color:#fff" href="javascript:void(0)" id="off1"> Select Groups </a></div>
                <div class="panel-body">
				<div class="col-lg-8 col-md-8 col-sm-12">
                    <div class="form-group clearfix popup-select-icon popup-multselect-list">

                        <select id="multi_participant_vote_users"  multiple="multiple"></select>

                        <!--

                                 <div class="col-lg-8 col-md-8 col-sm-8 upade_feedbacdurgesh">
                                        <div class="form-group">
                                                <select id="demo-input-local" placeholder="Search by username" class="easyui-combogrid form-control" name="data[VoteUser][list][]" style="width:250px" data-options="
                                                                panelWidth: 635,
                                                                multiple: true,
                                                                idField: 'id',
                                                                textField: 'name',
                                                                url: '<?php echo SITEURL; ?>entities/users_listing',
                                                                method: 'get',
                                                                mode: 'remote',
                                                                columns: [[
                                                                        {id:'ckss', field:'ckss',checkbox:true},

                                                                        {field:'name',title:'Users',width:120},

                                                                ]],
                                                                fitColumns: true
                                                        ">
                                                </select>
                                        </div>
</div>

                        -->
                    </div>
                </div>
</div>
            </div>
            <div class="form-group step2_btn">
                <a  href="javascript:void(0)" class="btn btn-sm btn-danger   " id="prev_step3">
                        <!--<i class="fa fa-trash"></i>--> Previous
                </a>
                <a    id="finish_vote" href="#" class="btn btn-sm btn-success save_vote_final submit">
                        <!--<i class="fa fa-fw fa-save"></i>-->Finish
                </a>
                <a    id="cancel_final_vote" href="#" class="btn btn-sm btn-danger cancel_vote">
                        <!--<i class="fa fa-fw fa-save"></i>-->Cancel
                </a>

            </div>
            </form>
        </div>



<?php echo $this->Form->end(); ?>
    </div>
</div>




<div class="table_wrapper clearfix" id="votes_table" data-model="vote" data-limit="5">
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
    <!--<div class="table-rows data_catcher" style="max-height:734px; overflow-y:auto;">-->
	<div class="table-rows table-catchers  data_catcher">
        <?php
        if (isset($votes) && !empty($votes)) {
            // pr($votes);
            foreach ($votes as $detail) {
                $data = $detail['Vote'];
                $users = $detail['VoteUser'];
                $VoteQuestion = $detail['VoteQuestion'];
                if (isset($VoteQuestion) && !empty($VoteQuestion)) {
                    ?>

                    <?php
                    $disabledSignOff = '';
                    if (isset($detail['Vote']['is_completed']) && !empty($detail['Vote']['is_completed'])) {
                        $disabledSignOff = 'disabled';
                    }
                    ?>

                    <div class="row" id="vote_row_<?php echo $data['id']; ?>" style="padding-bottom: 10px;">
                        <div class="col-sm-3 resp" id="title<?php echo $data['id']; ?>" > <?php

						$vtitle = str_replace("'", "", $data['title']);
						$vtitle = str_replace('"', "", $vtitle);

						$vtitle = str_replace("'", "", $vtitle);
						$vtitle = str_replace('"', "", $vtitle);

						//echo substr(htmlentities($vtitle),0,50);
						echo htmlentities($data['title'], ENT_QUOTES, "UTF-8") ; 

						//echo $data['title']; ?></div>
                        <div class="col-sm-2 text-left resp" id="paricipants<?php echo $data['id']; ?>" >
						<a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $data['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                    <i class="fa fa-user"></i>
						</a>
							<?php
							$UD = $this->Common->userFullname($data['user_id']);
					echo $UD ;
					?>

						<?php //echo count($detail['VoteUser']); ?></div>
                        <div class="col-sm-2 text-left resp" id="startdate<?php echo $data['id']; ?>"  ><?php
						echo date('d M, Y', strtotime($data['start_date']));
						//echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['start_date'])),$format = 'd M, Y');
						?></div>
                        <div class="col-sm-2 text-left resp" id="enddate<?php echo $data['id']; ?>"  > <?php
						echo date('d M, Y', strtotime($data['end_date']));
						//echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['end_date'])),$format = 'd M, Y');
						?></div>

                        <div class="col-sm-3 text-center resp">
                            <div class="btn-group" >
                                <!--
                                <a href="javascript:;" class="btn btn-sm  btn-success tipText"  data-id="<?php echo $data['id']; ?>"  data-eid="<?php echo $this->data['Element']['id']; ?>" data-uid="<?php echo $user_id; ?>" data-sessionid="<?php echo $session_id; ?>" title="Question Details" >
                                    <i class="fa fa-eye"></i>
                                </a>
                                -->
                                <?php
                                $viewURL = SITEURL . "entities/view_question/" . $data['id'];
                                $paticipantURL = SITEURL . "entities/participants_voteuser/" . $data['id'];
                                ?>

                                <a data-toggle="modal" rel="<?php echo $data['id']; ?>" class="viewuser tipText"  data-target="#"   title="Vote Details " data-whatever="<?php echo $viewURL; ?>"  data-tooltip="tooltip" data-elem="vote" data-placement="top" ><i class="viewblack"></i></a>

                                <a data-toggle="modal" rel="<?php echo $data['id']; ?>" class="viewuserPIU update-form-user1 tipText <?php //echo $class_d; ?>" data-msg="<?php echo $message; ?>" data-target="#"   title="Participant Info " data-whatever="<?php echo $paticipantURL; ?>"  data-tooltip="tooltip" data-placement="top" >
                                    <i class="teamblack"></i>
                                </a>

                                <?php
                                $disabledvote = '';
                                if ((isset($detail['Vote']['end_date']) && !empty($detail['Vote']['end_date']) && $detail['Vote']['end_date'] < date('Y-m-d 00:00:00')) ) {
                                    $disabledvote = 'disabled';
                                }
                                ?>

                                <a href="#" <?php if (!empty($disabledvote)) { echo $disabledvote; }  ?> class=" update_vote tipText" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_vote', $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="update" title="Update Vote">
                                    <i class="showlessblack icon_down"></i>
                                    <i class="showmoreblack icon_up"></i>
                                </a>

                                <?php if(!empty($data['is_completed'])){ ?>
                                    <a href="#" class="sign_off_vote tipText  <?php echo $disabledSignOff; ?>" data-remote="" data-id="<?php echo $data['id']; ?>" data-action="Reopen" title="Reopen" data-msg="Are you sure you want to reopen this Vote?"  data-eid="<?php echo $this->data['Element']['id']; ?>"  data-toggle="confirmation">
                                        <i class="reopenblack"></i>
                                    </a>
                                <?php }else{ ?>
                                <a href="#" class="sign_off_vote tipText  <?php echo $disabledSignOff; ?>" data-remote="" data-id="<?php echo $data['id']; ?>" data-action="Sign-Off" title="Sign Off" data-msg="Are you sure you want to sign off this Vote?"  data-eid="<?php echo $this->data['Element']['id']; ?>"  data-toggle="confirmation">
                                    <i class="signoffblack"></i>
                                </a>
                                <?php } ?>

                                <?php
                                $disabled = '';


								if (isset($disabledSignOff) && !empty($disabledSignOff)) {
                                    $disabled = 'disabled';
                                }
                                ?>

                                <a href="javascript:void(0);"   class="history_vote tipText history"  itemtype="votes" itemid="historyvote_<?php echo $data['id']; ?>"  data-id="<?php echo $data['id']; ?>" data-action="remove"  title="History"  >
                                    <i class="historyblack"></i>
                                </a>

                                <a href="#"  class="remove_vote tipText remove_vote_btn <?php echo $disabled; ?>" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'remove_vote', $this->data['Element']['id'], $data['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['id']; ?>" data-action="remove" title="Remove Vote" >
                                    <i class="deleteblack"></i>
                                </a>

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
                                        echo $this->Form->create('Vote', array('url' => array('controller' => 'entities', 'action' => 'update_vote', $id), 'class' => "formupdateVote $pointer_event $disabled", 'style' => '', 'enctype' => 'multipart/form-data'));
                                        ?>
                                        <input type="hidden" name="data[Vote][element_id]" class="form-control" value="<?php echo $this->data['Element']['id']; ?>" />
                                        <input type="hidden" name="data[Vote][id]" id="VoteUservote_id" class="form-control vote_id" value="<?php if (isset($data['id'])) echo $data['id']; ?>" />
                                        <div class="form-group">
                                            <label class=" " for=" ">Title:</label>
                                            <input type="hidden" name="data[Vote][project_id]" value="<?php echo $project_id; ?>" />
                                            <input type="hidden" name="data[Vote][workspace_id]" class="form-control" value="<?php echo $workspace_id; ?>" />
                                            <input type="text" name="data[Vote][title]" placeholder="Vote title" class="form-control" value="<?php if (isset($data['title'])) echo htmlentities($data['title']); ?>" />
                                            <span class="error-message text-danger" style=""></span>
                                        </div>
                                        <div class="form-group">
                                            <label class=" " for=" ">Reason:</label>
                                            <textarea rows="2" class="form-control vote_desc" placeholder="Vote Reason" name="data[Vote][reason]" id="vote_desciption_<?php echo $data['id']; ?>"><?php if (isset($data['reason'])) echo $data['reason']; ?></textarea>
                                            <span class="error-message text-danger" style=""> </span>
                                        </div>
                                        <?php
                                        if (isset($data['start_date']) && !empty($data['start_date'])) {
                                            //$this->request->data['Vote']['start_date'] = date('d-m-Y', strtotime($data['start_date']));
											$this->request->data['Vote']['start_date'] = date('d M Y', strtotime($data['start_date']));
                                           // $this->request->data['Vote']['start_date'] = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['start_date'])),$format = 'd-m-Y');
                                        }

                                        if (isset($data['end_date']) && !empty($data['end_date'])) {
                                            //$this->request->data['Vote']['end_date'] = date('d-m-Y', strtotime($data['end_date']));
											$this->request->data['Vote']['end_date'] = date('d M Y', strtotime($data['end_date']));
											//$this->request->data['Vote']['end_date'] = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['end_date'])),$format = 'd-m-Y');
                                        }
                                        ?>

										<div class="form-group clearfix ">
											<div class="form-group input-daterange data-catcher-new">
												 <div class="row date-row" style="border:none;border:none;margin:0 -15px;">
												<div class="col-sm-6 create-edit-date-f">
													<label class="control-label" for="start_date">Start Date:</label>
													<div class="input-group" style="position:relative;">
										<?php
										$VoteStartDate = date('d M Y', strtotime($this->request->data['Vote']['start_date']));
										echo $this->Form->input('Vote.start_date', [ 'type' => 'text', 'label' => false, 'rel' => $data['id'], 'div' => false, 'id' => "start_date_$id", 'required' => false, 'readonly' => 'readonly', 'class' => 'form-control dates input-small start_date', 'value'=> $VoteStartDate ]); ?>
														<span class="error-message text-danger error-calendere" style=""> </span>
														<div class="input-group-addon open-start-date-picker-update calendar-trigger">
															<i class="fa fa-calendar"></i>
														</div>
													</div>



												</div>


												<div class="col-sm-6 create-edit-date-f">
													<label class="control-label" for="end_date">End Date:</label>
													<div class="input-group" style="position:relative;">
										<?php
										$VoteEndDate = date('d M Y', strtotime($this->request->data['Vote']['end_date']));
										echo $this->Form->input('Vote.end_date', [ 'type' => 'text', 'label' => false, 'div' => false, 'id' => "end_date_$id", 'rel' => $id, 'required' => false, 'readonly' => 'readonly', 'class' => 'form-control dates input-small end_date', 'value'=> $VoteEndDate]); ?>
														<span class="error-message text-danger error-calendere" style=""> </span>
														<div class="input-group-addon  open-end-date-picker-update calendar-trigger">
															<i class="fa fa-calendar"></i>
														</div>
													</div>

													</div>
												</div>

											</div>
										</div>


                                        <!--
                                        <div class="form-group clearfix">

                                            <label class="col-sm-2">Participant Users:</label>
                                            <div class="col-sm-6">
            <?php //echo $this->Form->input('VoteUser.list', array('type' => 'text', 'id' => 'demo-input-local1', 'class' => 'form-control demo-input-local', 'div' => false, 'label' => false));  ?>
                                                <span class="error-message text-danger"> </span>
                                            </div>
                                        </div>
                                        -->
										<?php
										if($ele_signoff == false ){
										?>
                                        <div class="form-group">
                                            <a  target="_blank" id="<?php echo $id; ?>" href="#" class="btn btn-sm btn-success update_vote_btn submit" <?php echo $disabled; ?>>
                                                    <!--<i class="fa fa-fw fa-save"></i>--> Update
                                            </a>
                                        </div>
										<?php } else {?>
										<div class="form-group">
                                            <a  target="_blank" id="<?php echo $id; ?>" href="#" class="btn btn-sm btn-success disabled submit">
                                                    <!--<i class="fa fa-fw fa-save"></i>--> Update
                                            </a>
                                        </div>
										<?php }?>
                                        </form>
                                    </div>
                                </div>
                                <div id="participants_user_<?php echo $data['id']; ?>" class="update-form-user   panel  panel-dafult" style="display:none;">

                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="historyvote_<?php echo isset($data['id']) && !empty($data['id']) ? $data['id'] : '';
            ; ?>" class="history_update" style="display: none;">
                    <?php //include 'activity/update_history.ctp';?>
                    </div>

            <?php } ?>
            <?php } ?>
        <?php }else{
		echo '<span class="nodatashow vote">No Votes</span>';
		}  ?>
    </div>
    <?php if (isset($votePageCount) && !empty($votePageCount)) { ?>
        <div class="ajax-pagination clearfix">
    <?php echo $this->element('pagination', array('model' => 'Vote', 'limit' => 5, 'pgeCount' => $votePageCount)); ?>
        </div>

<?php } ?>
</div>


<style>

.wysihtml5-sandbox {

    height: 75px !important;

}
</style>
<script type="text/javascript">
$('body').delegate("input[name='data[Vote][title]']", "keyup focus", function(event){
	var characters = 50;
	event.preventDefault();
	var $error_el = $(this).next('.error-message:first');
	if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
		$.input_char_count(this, characters, $error_el);
	}
})
</script>