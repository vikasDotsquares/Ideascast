<script src="<?php echo SITEURL; ?>js/jquery.canvasjs.min.js" type="text/javascript"></script>

	<div class="panel-dafult vote_result_box">


		<div class="panel-header bg-green">

			<h4 class="modal-title">
			 Feedback Details
			</h4>

		</div>
		<!---->
		<div class="panel-body">
		<div class="row">

		<div class="col-sm-12">
			<?php
			$feedback_id = '';

		if(isset($feedback_details['Feedback']['id'])){ $feedback_id =  $feedback_details['Feedback']['id']; } ?>
		<div class="modal-body modal-bodydd">
        <div class="row">
		<div class="col-md-6 col-lg-12">	<div class="row" style="border: none; padding-top: 15px;">

			<?php
			$ArrFeedbackResult = array();
			if(isset($feedback_details) && !empty($feedback_details)){ ?>

				<div class="form-group clearfix col-sm-12">
				<label  class="control-label">Feedback Reason:</label>
				<div class="control-label"><?php if(isset($feedback_details['Feedback']['reason'])) echo   htmlentities($feedback_details['Feedback']['reason'], ENT_QUOTES, "UTF-8") ;    ?></div>
				</div>

				<div class="form-group clearfix col-sm-12">
				<label  class="control-label">Feedback For:</label>
				<p class="control-label"><?php if(isset($feedback_details['Feedback']['feedback_for']))  
				echo   htmlentities($feedback_details['Feedback']['feedback_for'], ENT_QUOTES, "UTF-8") ;  
				?></p>
				</div>
				<?php if(isset($feedback_details['FeedbackAttachment']) && !empty($feedback_details['FeedbackAttachment'])){ ?>
					<div class="form-group clearfix col-sm-12  margin-bottom">
					<label  class="control-label">Attachments:</label>
					<div class="control-label">
					<?php
						foreach($feedback_details['FeedbackAttachment'] as $FeedbackAttachment){
							$id = $FeedbackAttachment['id'];
							$element_idqq = $FeedbackAttachment['element_id'];
							$feedback_id = $FeedbackAttachment['feedback_id'];
							//$id = $FeedbackAttachment['id'];
							$upload_path = WWW_ROOT . ELEMENT_DOCUMENT_PATH . DS . $element_idqq .  DS . 'feedbacks' . DS. $feedback_id .  DS ;
							$upload_file = $upload_path . $FeedbackAttachment['file_name'];

							$ftype = pathinfo($upload_file);
							if (isset($ftype) && !empty($ftype)) {
								// pr($ftype);
								$dirname = ( isset($ftype['dirname']) && !empty($ftype['dirname'])) ? $ftype['dirname'] : '';
								$basename = ( isset($ftype['basename']) && !empty($ftype['basename'])) ? $ftype['basename'] : '';
								$filename = ( isset($ftype['filename']) && !empty($ftype['filename'])) ? $ftype['filename'] : '';
								$extension = ( isset($ftype['extension']) && !empty($ftype['extension'])) ? $ftype['extension'] : '';
							}
					?>
					<div class="image_namebox">
						<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
							<span class="icon_text"><?php echo $extension; ?></span>
                        </span>
						<?php $downloadURL = Router::Url(array('controller' => 'entities', 'action' => 'download_feedback_doc', $id, 'admin' => FALSE), TRUE); ?>
                        <a href="<?php echo $downloadURL ?>" class="btn_file_link" data-remote="<?php echo $downloadURL ?>" data-id="<?php echo $id; ?>" > <span class="feedback_f_filename"><?php echo $filename; ?>.</span><span class="feedback_f_ext"><?php echo $extension; ?></span></a>
					</div>
					<?php } ?>
					</div>
					</div>
				<?php } ?>
			</div>
            </div>
			<?php if(isset($feedback_id) && !empty($feedback_id)){ ?>
         <div class="col-md-6 col-lg-12">
			<div class="participants participants-feedback" style="border-top: none !important;">
				<div class="row">
					<div class="form-group clearfix  col-sm-3 col-md-12 col-lg-3">
						<label class="control-label">Total Invites:</label>
						<div class="colum-data"><?php $total = $this->Common->totalFeedbackinvites($feedback_id); echo $total; ?></div>
					</div>

					<div class="form-group clearfix col-sm-3 col-md-12 col-lg-3">
						<label  class="control-label ">Participants:</label>
						<div class="colum-data"><?php $totalparticipants = $this->Common->totalFeedbackparticipants($feedback_id); echo $totalparticipants; ?></div>
					</div>

					<div class="form-group clearfix col-sm-3 col-md-12 col-lg-3">
						<label  class="control-label ">Request Declined:</label>
						<div class="colum-data"><?php $totaldeclined = $this->Common->totalFeedbackdeclined($feedback_id); echo $totaldeclined; ?></div>
					</div>

					<div class="form-group clearfix col-sm-3 col-md-12 col-lg-3">
						<label  class="control-label">No Response:</label>
						<div class="colum-data"><?php echo ($total - ($totalparticipants + $totaldeclined)); ?></div>
					</div>
				</div>
			</div>
            </div>
			<?php } ?>
			</div>

				<div class="participants">
					<div class="feed_row">
						<label  class="feedback_title">Feedback Received</label>



							<!--<a class="accordion-toggle list-group-item clearfix open_form noborder-radius" data-toggle="collapse" data-parent="#accordion" href="#task-activity-Feed">
								<span class="pull-left"><i class="fa fa-book"></i> Activity</span>
								<span class="pull-right"><i class="glyphicon glyphicon-plus"></i></span>
							</a>
							 <div id="task-activity-Feed" class="panel-collapse collapse history_update_new table-rows">
									fvbfbgf
							</div>   -->







							<?php
							if(isset($feedback_details['FeedbackResult']) && !empty($feedback_details['FeedbackResult'])){ ?>
							<div class="feedback_list">
							<?php
								foreach($feedback_details['FeedbackResult'] as $fbr){
									//pr($fbr, 1);
									$user_id = $this->Session->read('Auth.User.id');
									$element_id = $feedback_details['Feedback']['element_id'];
									$project_id = $feedback_details['Feedback']['project_id'];
									$fbuid = $feedback_details['Feedback']['user_id'];
									$fbr_id = $fbr['id'];
									$feedback_by = '';
									if(isset($fbr['User']['UserDetail']['full_name']) && !empty($fbr['User']['UserDetail']['full_name'])){
										$feedback_by = $fbr['User']['UserDetail']['full_name'];
									}

									$feedback_on = '';
									if(isset($fbr['created']) && !empty($fbr['created'])){
										//$feedback_on = 'Received: '.date('d M, Y h:i A',$fbr['created']);
										$feedback_on = 'Received: '.$this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$fbr['created']),$format = 'd M, Y h:i A');
									}

									$feedback = '';
									if(isset($fbr['feedback']) && !empty($fbr['feedback'])){
										$feedback = $fbr['feedback'];
									}

									$is_decline = '';
									if(isset($fbr['is_decline']) && !empty($fbr['is_decline'])){
										$is_decline = $fbr['is_decline'];
									}
									$tt = 'false';
									//$creater_id = $this->Common->element_manage_editable($element_id, $project_id,$user_id);
									//if(!empty($creater_id)){
									$creater_id = 0;
									if($fbuid == $user_id){
										$creater_id = $user_id;
										$cls = "give_it";
									}else{
									   $cls = "disabled";
									   $tt = 'true';
									}
							?>

							<div class="feed_back_list">

								<ul>
									<li >
									<div class="amount">

										<?php

										$rtts = $this->Common->feedbackRate($fbr['feedback_id'],$fbr_id,$creater_id,$fbr['user_id']);
										$rttsC = $this->Common->feedbackRateC($fbr['feedback_id'],$fbr_id,$creater_id,$fbr['user_id']);



										$ftip ='Give It';
										$clsG ='';


									// echo $fbr['feedback_id']."<br>".$fbr_id."<br>".$creater_id."<br>".$fbr['user_id'];
										if($rtts != "Not Given"  && $cls == 'give_it'){
											$clsG = "alloeD";
											$cls = "give_it";
											$tt = 'true';
											$ftip = 'Feedback Already Rated';
										}
										?>
										<?php if($rtts == "Not Given") { ?>
										<input type="hidden"  id="rating_<?php echo $fbr_id; ?>" value="0" name="rating"  >
										<?php } else { ?>
										<input type="hidden"  id="rating_<?php echo $fbr_id; ?>" value="<?php echo $rtts; ?>" name="rating"  >
										<?php }  ?>

										<input type="hidden"  id="feedback_id_<?php echo $fbr_id; ?>" value="<?php echo $fbr['feedback_id']; ?>" name="feedback_id"  >
										<input type="hidden"  id="creater_id_<?php echo $fbr_id; ?>" value="<?php echo $creater_id; ?>" name="creater_id"  >
										<input type="hidden"  id="user_id_<?php echo $fbr_id; ?>" value="<?php echo $fbr['user_id']; ?>" name="user_id"  >
									</div>
										<?php
											if(isset($is_decline) && !empty($is_decline)){
/* 												echo '<div class="feedback_by bg-gray"> <div class="col-sm-3"><b class="feedback_title_rate">'.$feedback_by.'</b> <span class="rating"> (<b>Rating:

												<span id="rate'.$fbr_id.'">'.number_format($this->Common->feedbackRateAverage( $fbr['user_id']),1).'</span>

												)</span></div> */

												echo '<div class=" feedback_title list-group-item clearfix open_form noborder-radius">
												<span class="pull-left"><i  class="fa fa-user text-maroon" data-remote="'.SITEURL.'shares/show_profile/'.$fbr['User']['id'].'"  data-target="#popup_modal"  data-toggle="modal" class=" view_profile text-maroon"></i></span><span class="pull-left col-xs-5 accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#feedback_by'.$fbr_id.'">'.$feedback_by.' </span>
												<span class=" accordion-toggle "  data-toggle="collapse" data-parent="#accordion" href="#feedback_by'.$fbr_id.'"> '.$feedback_on.' </span>
												<span class="pull-right accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#feedback_by'.$fbr_id.'"><i class="glyphicon glyphicon-plus"></i></span>
												</div>';

												echo '<div id="feedback_by'.$fbr_id.'" class="panel-collapse feedback_by collapse" data-toggle="collapse"><div class="col-sm-12 scrolll"><b> Feedback Request: Declined </b></div> <div class="clearfix"></div> </div>';
											}else{
											echo '<div class=" feedback_title list-group-item clearfix open_form noborder-radius">
												<span class="pull-left"><i  class="fa fa-user text-maroon" data-remote="'.SITEURL.'shares/show_profile/'.$fbr['User']['id'].'"  data-target="#popup_modal"  data-toggle="modal" class=" view_profile text-maroon"></i></span><span class="pull-left col-xs-5 accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#feedback_by'.$fbr_id.'">'.$feedback_by.' </span>
												<span class=" accordion-toggle "  data-toggle="collapse" data-parent="#accordion" href="#feedback_by'.$fbr_id.'"> '.$feedback_on.' </span>
												<span class="pull-right accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#feedback_by'.$fbr_id.'"><i class="glyphicon glyphicon-plus"></i></span>
												</div>';



												echo '<div id="feedback_by'.$fbr_id.'" data-toggle="collapse" class="feedback_by  panel-collapse collapse">
												<div class="row">';
												?>


												<div class="feedback-left pull-left col-sm-8">
														<p>
														<label>Feedback Received</label>
														<div class="fsent fcomm"><?php echo $feedback; ?></div>
														<!--<textarea rows="4" readonly="true" style="width:100%" placeholder=""><?php //echo $feedback; ?></textarea></p>  -->
														<?php if(isset($fbr['FeedbackAttachment']) && !empty($fbr['FeedbackAttachment'])){ ?>
														<div class="form-group clearfix margin-bottom doc_adh">
														<div class="col-sm-12 nopadding">
														<?php
															foreach($fbr['FeedbackAttachment'] as $FeedbackAttachment){
																$id = $FeedbackAttachment['id'];
																$element_idqq = $FeedbackAttachment['element_id'];
																$feedback_id = $FeedbackAttachment['feedback_id'];
																//$id = $FeedbackAttachment['id'];
																$upload_path = WWW_ROOT . ELEMENT_DOCUMENT_PATH . DS . $element_idqq .  DS . 'feedbacks' . DS. $feedback_id .  DS ;
																$upload_file = $upload_path . $FeedbackAttachment['file_name'];

																$ftype = pathinfo($upload_file);
																if (isset($ftype) && !empty($ftype)) {
																	// pr($ftype);
																	$dirname = ( isset($ftype['dirname']) && !empty($ftype['dirname'])) ? $ftype['dirname'] : '';
																	$basename = ( isset($ftype['basename']) && !empty($ftype['basename'])) ? $ftype['basename'] : '';
																	$filename = ( isset($ftype['filename']) && !empty($ftype['filename'])) ? $ftype['filename'] : '';
																	$extension = ( isset($ftype['extension']) && !empty($ftype['extension'])) ? $ftype['extension'] : '';
																}
														?>
														<div class="image_namebox">
															<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
																<span class="icon_text"><?php echo $extension; ?></span>
															</span>
															<?php $downloadURL = Router::Url(array('controller' => 'entities', 'action' => 'download_feedback_doc', $id, 'admin' => FALSE), TRUE); ?>
															<?php echo $basename; ?><a href="javascript:void(0)<?php //echo $downloadURL ?>" id="addto_document_feedback_doc" class="tipText grayscale add_doc" title="Add to documents" data-remote="<?php //echo $downloadURL ?>" data-title="<?php echo $ftype['filename']; ?>" data-id="<?php echo $id; ?>" > <?php //echo $basename; ?>
</a>
															<i class="fa fa-folder-o add_doc_icon add_to_document"></i>
														</div>
														<?php } ?>
														</div>
														</div>
													<?php }

													?>
												</div>
												<div class="feedback-right pull-right col-sm-4">
													<?php if($cls == 'give_it' || $clsG=='alloeD'){
												echo '<label class="feed_slide_label">Rate Feedback:</label> <div id="slider'.$fbr_id.'" class="range_slider "></div>';
												}
												 if($clsG=='alloeD') {
													 echo '<div class="fsent frate">'.$rttsC.'</div>';
												 } else {
													echo '<textarea placeholder="Provide comment on feedback received." id="comment_'.$fbr_id.'"  class="gfback">'.$rttsC.'</textarea>';
												 }

												if($cls == 'give_it' || $clsG=='alloeD'){
												if($clsG=='alloeD')
												$cls ='disabled give_it2';
												echo '<a  href="javascript:void(0)" title="'.$ftip.'"   rel="'.$fbr_id.'" class="'.$cls.' tipText pull-right" >Give It</a>';
												}

											/* 	echo '</div><div class="col-sm-3"><b class="feedback_title_rate">'.$feedback_on.'</b></div></div><div class="col-sm-12 scrolll">'.$feedback.' <span class="pull-right"><i class="glyphicon glyphicon-plus"></i></span></div>';  */


											?>
												</div>
												<?php



											}
										?>
											<script type="text/javascript" >
													$(document).ready(function(){
													   var dds = $("#rating_<?php echo $fbr_id; ?>").val();
														if(dds  != 'Not Given'){
															var rrtdd = dds;
														}else{
															var rrtdd = 0;
														}

														//console.log(rrtdd);

														$("#slider<?php echo $fbr_id; ?>").slider({
															disabled: '',
															range: "max",
															min: 0,
															max: 10,
															value: rrtdd,
															slide: function (event, ui) {
																$("#rating_<?php echo $fbr_id; ?>").val(ui.value);
																$("#feedback_by<?php echo $fbr_id; ?> .ui-slider-handle").text(ui.value);
															},
															disabled : <?php echo $tt; ?>
														});
														var DSV = $("#rating_<?php echo $fbr_id; ?>").val()
														//$(".ui-slider-handle").text('0');
														$("#feedback_by<?php echo $fbr_id; ?> .ui-slider-handle").text(DSV);
													});
												</script>

									</li>
								</ul>
							</div>
							<script type="text/javascript">
								var selectIds = $('<?php echo '#feedback_by'.$fbr_id; ?>');
								$(function ($) {


									selectIds.on('show.bs.collapse hidden.bs.collapse ', function (e) {
										//console.log($(this));
									   $(this).prev().find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
									//console.log(selectIds);
									})



								});
							</script>
							<?php
								} ?>
							<div class="col-sm-12 feedback_list">
							<?php }else{ ?>
							<div class="col-sm-12 feedback_list">
								<div class="feed_back_list" style="text-align: center; padding:15px;">
									No Feedback
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		</div>

		</div>
		</div>


	</div><!-- /.modal-content -->


<style>
.fsent {
	display: block;
	width: 100%;
	/* border: 1px solid #ccc; */
	float: left;
	/* padding: 10px 15px; */
	margin-bottom: 5px;
	overflow-x: auto;
	overflow-y: auto;
	max-height: 100px;
}
.fsent.fcomm {
	min-height: 100px;
}
.fsent.frate {
	min-height: 50px;
}
.bg-green-active, .modal-success .modal-header, .modal-header, .modal-success .modal-footer, .panel-primarys > .panel-heading {
  background-color: #5f9323 !important;
  color: #ffffff;
}

.modal-dialog .modal-content .modal-footer {
    background: #eeeeee none repeat scroll 0 0 !important;
    border-top-color: #aaaaaa;
    color: #333333;
}

.canvasjs-chart-credit {
  display: none;
}
.chartContainer{min-height:300px;}
.participants { border-top:2px solid #00ACD6; padding:15px 0;}
.participants .control-label { float:left; width:120px;}
.participants .colum-data { display: block; overflow:hidden; text-align:center;}
.participants .row { border:none;}
.vote_result_box { margin-bottom:15px}
.vote_result_box .panel-header {    background: #67a028 !important;}
.vote_result_box .row { border:none; margin-left: -15px;margin-right: -15px;}
.vote_result_box .panel-body {max-width: 100%;   overflow:initial;}
.vote_result_box .vote_detail_left { padding-top:20px;}
.vote_result_box h4.modal-title { padding:10px 15px; border-top-left-radius: 3px;border-top-right-radius: 3px; font-size: 13px; font-weight: 400;}
.vote_result_box .panel-body .col-sm-6 { display:table-cell; vertical-align:top;}



.image_namebox {
    float: left;
    margin: 5px 15px 5px 0;
}

.btn_file_link{
	color:saddlebrown;
}
.btn_file_link:hover{
	color:saddlebrown;
}

.image_namebox:first-child{
    margin-left: 10px;
}

.comment {
  background: #cccccc none repeat scroll 0 0;
  border-radius: 10px !important;
}
.feed_back_list {

}

.feed_back_list li{ margin-bottom:0 !important;overflow:hidden ;}
.modal-bodydd .ui-widget-content {
  background: #35414f url("images/ui-bg_dots-small_35_35414f_2x2.png") repeat scroll 50% 50%;
  border: 1px solid #aaaaaa;
  color: #2c4359;

}

.modal-bodydd .ui-widget-header {
  background: #ffffff none repeat scroll 0 0;
  border: 1px solid #2c4359;
  color: #e1e463;
  font-weight: bold;
}

.scrolll {
    display: block !important;
    float: left;
    max-height: 60px !important;
    overflow: auto;
}

.validatebox-text{text-indent:-9999999px}

.give_it2 {
    background: #67a028 none repeat scroll 0 0;
    border-radius: 5px;
    color: #ffffff;
    display: inline-block;
    float: left;
    padding: 4px 10px 3px;
	opacity : 0.5;
}
.give_it2:hover {  color: #ffffff; }
.give_it:hover {  color: #ffffff; }

.fa.fa-user{ cursor:pointer;}

.feedback_title .accordion-toggle{ padding-left:6px;}

@media (min-width:992px) and (max-width:1199px) {
	.participants-feedback{
	border-top:none;
	padding-top:0px;
}
.participants {
  border-top: medium none;
  padding-top: 0px;
}
}



</style>

<script type="text/javascript" >
$(window).ready(function(){
	$('.give_it').on('click', function(){
	    var thiss = $(this);
		fbr_id = $(this).attr('rel');
		fbr_id = parseInt(fbr_id);
		if (fbr_id > 0){
		//console.log(fbr_id);
		rating = $('#rating_'+fbr_id).val();
		feedback_id = $('#feedback_id_'+fbr_id).val();
		user_id = $('#user_id_'+fbr_id).val();
		creater_id = $('#creater_id_'+fbr_id).val();
		action = '<?php echo Router::url("/"); ?>entities/rate_feedback';
		comment = $('#comment_'+fbr_id).val();

		data_string = "fbr_id="+fbr_id+"&rating="+rating+"&feedback_id="+feedback_id+"&user_id="+user_id+"&creater_id="+creater_id+"&comment="+comment;
//console.log(data_string);
		$.ajax({
            type: 'POST',
            data: data_string,
            url: action,
            global: true,
            dataType: 'JSON',
           success: function (res) {
			   if(res.success){
                setTimeout(function () {
					if(res.update){
						$.modal_alert('Your Feedback rating has been updated.', 'Feedback Rating');
					}else{
						$.modal_alert('Your Feedback rating has been submitted.', 'Feedback Rating');
					}
					$('#comment_'+fbr_id).attr('readonly', true);
					$("#slider"+fbr_id).slider({
						range: "max",
						min: 0,
						max: 10,
						value: rating,
						disabled : true
					});
					thiss.removeClass('give_it');
					thiss.addClass('give_it2');
					thiss.attr('title','Feedback Already Rated');
					thiss.attr('title','Feedback Already Rated');
					thiss.attr('data-original-title','Feedback Already Rated');
					thiss.removeAttr('rel');
                }, 1000);
			   }else{
				   $.modal_alert('There is some problem to save your rating. Please try again.');
			   }
            }
            //success: function (response, statusText, jxhr) { }
        });
		}

	});
});
</script>
