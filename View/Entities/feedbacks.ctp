<?php echo $this->Html->css('projects/uploadfile');?>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <?php //pr($feedbacks); ?>
            <section class="content-header clearfix">
                <h1 class="pull-left"><?php if (isset($feedbacks['Project']['title'])) echo htmlentities($feedbacks['Project']['title']); ?><br>
                    <p class="text-muted date-time">
                        <span>View Feedback details</span>
                    </p>
                </h1>
            </section>
        </div>
        <div class="box-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box border-top margin-top">
                        <div class="box-body">
                            <div id="workspace">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <button data-target="#element_tabs" data-toggle="collapse" type="button" class="btn cd-toggle cd-tabs-button"> <span class="fa fa-bars"></span> </button>
                                        <div class="cd-tabs is-ended" style="margin: 15px 5px;">
                                            <ul class="cd-tabs-content clearfix " style="height: auto;position:relative;">
                                                <li data-content="feedbacks" style="" class="selected">
                                                    <div data-limit="5" data-model="feedback" id="Feedback_table" class="table_wrapper clearfix">
                                                        <?php
														if (isset($feedbacks) && !empty($feedbacks)) {
															$disabledSignOff = '';
															if(isset($feedbacks['Feedback']['sign_off']) && !empty($feedbacks['Feedback']['sign_off'])){
																$disabledSignOff = 'disabled';
															}

															$is_declined = '';
															if(isset($feedbacks['FeedbackResult'][0]['is_decline']) && !empty($feedbacks['FeedbackResult'][0]['is_decline'])){
																$is_declined = 1;
															}
															?>
                                                            <?php echo $this->Form->create('FeedbackResult', array('url' => array('controller' => 'entities', 'action' => 'feedback_save'), 'class' => 'padding formAddElementFeedback', 'style' => '', 'enctype' => 'multipart/form-data','id'=>'FeedbackResultFeedbacksForm')); ?>
															<input type="hidden" name="data[FeedbackResult][feedback_id]" class="form-control" value="<?php echo $feedbacks['Feedback']['id']; ?>" />
															<input type="hidden" name="data[FeedbackAttachment][project_id]" class="form-control" value="<?php echo $feedbacks['Feedback']['project_id']; ?>" />
															<input type="hidden" name="data[FeedbackAttachment][feedback_id]" class="form-control" value="<?php echo $feedbacks['Feedback']['id']; ?>" />
															<input type="hidden" name="data[FeedbackAttachment][element_id]" class="form-control" value="<?php echo $feedbacks['Feedback']['element_id']; ?>" />
															<input type="hidden" name="data[FeedbackAttachment][status]" class="form-control" value="1" />
															<input type="hidden" name="data[FeedbackAttachment][feedback_result_id]" class="form-control" value="<?php echo uniqid(); ?>" />
															<input type="hidden" name="data[FeedbackResult][id]" class="form-control" value="<?php if(isset($feedbacks['FeedbackResult']['id'])) echo $feedbacks['FeedbackResult']['id']; ?>" />

															<div class="row" style="padding :15px 15px 0px 15px;  border-top-left-radius: 3px;    background-color: #f5f5f5;  position:absolute; left:0; width:100%;   border: 1px solid #ddd; top:0;  border-top-right-radius: 3px;border-bottom:2px solid #ddd; border-left:0;border-right:0;border-top:0;margin:0;"  >
																	<div class="pull-left project-detail" style="padding-bottom:15px">
																		<?php /* ?><span class="bg-blakish nomargin-left sb_blog">Start: <?php if (isset($feedbacks['Feedback']['start_date'])){
																			//echo date('d M,Y', strtotime($feedbacks['Feedback']['start_date']));
																			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($feedbacks['Feedback']['start_date'])),$format = 'd M, Y');
																		}?>
																		</span><?php */ ?>
																		<span class="bg-black sb_blog">Send Feedback by: <?php if (isset($feedbacks['Feedback']['end_date'])){
																			//echo date('d M,Y', strtotime($feedbacks['Feedback']['end_date']));
																			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($feedbacks['Feedback']['end_date'])),$format = 'd M, Y');

																		}?>
																		</span>
																	</div>

																	<?php  //pr($feedbacks);
																	$is_feedbackd = '';
																	$beginOfDay = strtotime("midnight", time());
																	$endOfDay   = strtotime("tomorrow", $beginOfDay) - 1 ;

																	if(empty($disabledSignOff)){
																		if(($feedbacks['Feedback']['start_date'] <= date('Y-m-d 00:00:00')) && ($feedbacks['Feedback']['end_date'] >= date('Y-m-d 00:00:00'))){
																			if(empty($feedbacks['FeedbackResult'][0]['id'])   ){
																				if($is_declined != 1){
																					?>
																					<div class="form-group pull-right"   style="margin-left: 10px">
																						<input type="button"  rel="<?php echo $feedbacks['Feedback']['id']; ?>" class="btn btn-sm btn-warning decline_feedback" value="Decline" />
																					</div>
																					<?php
																				}
																			} else {}
																			if($is_declined != 1){  ?>
																				<div class="form-group pull-right">
																					<input type="button"  class="btn btn-sm btn-success save_feedback submit" value="Feedback" />
																				</div>
																				<?php
																			}
																		}
																	}

																	$dc = false;
																	$dcVoted = false;
																	$dcC = false;
																	$fb_req_status = '';
																	if($dcC ==false){
																		if(isset($feedbacks['FeedbackResult']) && !empty($feedbacks['FeedbackResult'])){
																			foreach($feedbacks['FeedbackResult'] as $result){
																				if(isset($result['is_decline']) && !empty($result['is_decline'])){
																					$dc = true;
																					?>
																					<div class="pull-right" style="padding-top: 5px;">
																						<!-- <span class="pull-right1" >Declined</span> -->
																						<?php $fb_req_status = 'Declined'; ?>
																						<!-- <a href="<?php //echo Router::url(['controller' => 'entities', 'action' => 'feedback_request', 'admin' => false], true) ?>" class="btn btn-sm btn-success" >Back</a> -->
																					</div>
																					<?php
																					break;
																				}
																				if (isset($result['created'] ) && !empty($result ['created']) ) {
																					if(!isset($disabledSignOff) || empty($disabledSignOff)){
																						$dcVoted = true;
																					}
																				}
																			}
																		}
																		if($dc==false && $dcVoted==false){
																			if(isset($disabledSignOff) && !empty($disabledSignOff)){
																				// echo '<span class="pull-right" style="padding-bottom:15px">Closed</span>';
																				$fb_req_status = 'Closed';
																		 	} else if(($feedbacks['Feedback']['start_date'] <= date('Y-m-d 00:00:00')) && ($feedbacks['Feedback']['end_date'] >= date('Y-m-d 00:00:00'))){
																				//echo 'Open';
																			} else if(($feedbacks['Feedback']['start_date'] > date('Y-m-d 00:00:00'))){
																				// echo '<span class="pull-right" style="padding-bottom:15px">Not Started</span>';
																				$fb_req_status = 'Not Started';
																			} else {
																				// echo '<span class="pull-right" style="padding-bottom:15px">Expired</span>';
																				$fb_req_status = 'Expired';
																			}
																		} else if($dc==false && $dcVoted==true && isset($feedbacks['FeedbackResult']) && !empty($feedbacks['FeedbackResult']) && feedback_is_declined($feedbacks['Feedback']['id']) >= 1){
																			// echo '<span class="pull-right" style="padding-bottom:15px">Closed</span>';
																			$fb_req_status = 'Closed';
																		}
																	}
																	?>
																</div>
																<div class="row" style="margin-top : 30px">
																	<br/>
																	<div class="panel  panel-dafult  voting_entities_view entities_view-list">
																		<div class="panel-heading bg-green">
																			Feedback Details
                                                                           <!-- <a class="pull-right" href="<?php echo SITEURL ?>entities/feedback_request" ><i class="fa fa-angle-double-left"></i> Back</a>-->
                                                                        </div>
																		<div class="panel-body">
																			<ul>
																				<li>
																					<label class="col-md-3">Project Title: </label>
																					<div class="col-md-9">
																						<?php if (isset($feedbacks['Project']['title'])) echo htmlentities($feedbacks['Project']['title']); ?>
																					</div>
																				</li>
																				<li>
																					<label class="col-md-3">Feedback Title: </label>
																					<div class="col-md-9">
																						<?php if (isset($feedbacks['Feedback']['title'])) echo htmlentities($feedbacks['Feedback']['title']); ?>
																					</div>
																				</li>
																				<li>
																					<label class="col-md-3">Requested By: </label>
																					<div class="col-md-9">
																						<a href="#" class="show_profile text-black" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $feedbacks['Owner']['UserDetail']['user_id']; ?>" >
																							<i class="fa fa-user text-maroon"></i>&nbsp;
																							<?php
																							if (isset($feedbacks['Owner']['UserDetail']['first_name'])) echo $feedbacks['Owner']['UserDetail']['first_name'];
																							if (isset($feedbacks['Owner']['UserDetail']['last_name'])) echo ' ' . $feedbacks['Owner']['UserDetail']['last_name']; ?>
																						</a>
																					</div>
																				</li>
																				<li>
																					<label class="col-md-3">Accompanying Note: </label>
																					<div class="col-md-9">
																						<?php if (isset($feedbacks['Feedback']['reason'])) echo $feedbacks['Feedback']['reason']; ?>
																					</div>
																				</li>
																				<li>
																					<label class="col-md-3">Status: </label>
																					<div class="col-md-9">
																						<?php  echo $fb_req_status; ?>
																					</div>
																				</li>
																				<li>
																					<?php if(isset($feedbacks['FeedbackAttachment']) && !empty($feedbacks['FeedbackAttachment'])){ ?>
																						<div class="form-group clearfix col-sm-12  margin-bottom">
																							<label class="col-md-3 nopadding">Attachments:</label>
																							<div class="col-md-9 nopadding">
																								<?php
																								foreach($feedbacks['FeedbackAttachment'] as $FeedbackAttachment){
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
																										<a href="<?php echo $downloadURL ?>" class="btn_file_link" data-remote="<?php echo $downloadURL ?>" data-id="<?php echo $id; ?>" > <?php echo $basename; ?></a>
																									</div>
																									<?php
																								}
																								?>
																							</div>
																						</div>
																					<?php } ?>
																				</li>
																			</ul>
                                                                        </div>
																	</div>
																	<?php if(feedback_is_declined($feedbacks['Feedback']['id']) == 1 && isset($feedbacks['FeedbackResult']) && !empty($feedbacks['FeedbackResult'])) { ?>
																		<div class="panel panel-success voting_entities_view">
																			<div class="panel-heading bg-green">Feedback</div>
																			<div class="panel-body">
																				<ul>
																					<?php if(isset($feedbacks['FeedbackResult']) && !empty($feedbacks['FeedbackResult'])){ ?>
																						<li>
																							<label class="">Feedback For:</label>
																							<div class="">
																								<?php if (isset($feedbacks['Feedback']['feedback_for'])) echo $feedbacks['Feedback']['feedback_for']; ?>
																							</div>
																						</li>
																						<li>
																							<div class="">
																								<?php
																								foreach($feedbacks['FeedbackResult'] as $result){
																									if(isset($result['is_decline']) && !empty($result['is_decline'])){
																										echo 'Declined'; break;
																									}
																									?>
																									<div style="clear:both" class="panel  panel-dafult  voting_entities_view">
																										<div class="panel-heading bg-green">Feedback Sent</div>
																										<div class="panel-body">
																											<div class="comment feedback-comment">
																												<span class="timestamp">
																													<?php if(isset($result['feedback']) && !empty($result['created'])) {
																															echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$result['created']),$format = 'd M,Y g:i A');
																													}?>
																												</span>
																												<p>
																													<div class="fsent fcomm" ><?php if(isset($result['feedback']) && !empty($result['feedback'])) echo $result['feedback'];  ?></div>
																												</p>
																												<?php if(isset($result['FeedbackAttachment']) && !empty($result['FeedbackAttachment'])){ ?>
																													<div class="form-group clearfix margin-bottom doc_adh">
																														<div class="col-sm-12 nopadding">
																															<?php
																															foreach($result['FeedbackAttachment'] as $FeedbackAttachment){
																																$id = $FeedbackAttachment['id'];
																																$element_idqq = $FeedbackAttachment['element_id'];
																																$feedback_id = $FeedbackAttachment['feedback_id'];
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
																																	<a href="<?php echo $downloadURL ?>" class="btn_file_link" data-remote="<?php echo $downloadURL ?>" data-id="<?php echo $id; ?>" > <?php echo $basename; ?></a>
																																</div>
																																<?php
																																}
																															?>
																														</div>
																													</div>
																												<?php } ?>
																											</div>
																										</div>
																										<div class="panel-heading bg-green">Feedback Rating</div>
																										<div class="panel-body">
																											<ul>
																												<li>
																													<label class="">Feedback Rating Received:&nbsp;</label>
																													<div class="" style="display:inline">
																														<?php
																														$rtts = $this->Common->feedbackRatebyResultID($result['feedback_id'],$result['id']);
																														$rttsC = $this->Common->feedbackRateC($result['feedback_id'],$result['id'] );
																														$rttsDetail = $this->Common->feedbackRateDetail($result['feedback_id'],$result['id'] );
																														if (isset($rtts)) echo $rtts;
																														?>
																													</div>
																												</li>
																												<li>
																													 <label class="">Comment received about your Feedback: </label>
																													 <div class="pull-right">
																														<span class="timestamp">
																														<?php
																														echo isset($rttsDetail) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$rttsDetail['modified']),$format = 'd M,Y g:i A')  : ' ';
																														?>
																														</span>
																													</div>
																													<div class="">
																														<div class="fsent frate" >
																															<?php   echo isset($rttsC) ? $rttsC : "None Given" ;  ?>
																														</div>
																													</div>
																												</li>
																											</ul>
																										</div>
																									</div>
																									<?php
																								}?>
																							</div>
																						</li>
																					<?php } ?>
																					<?php $feedback = 0;
																					if(isset($feedbacks['FeedbackResult']['created']) && !empty($feedbacks['FeedbackResult']['created'])){ ?>
																						<li>
																							<label class="col-sm-3">Feedbackd on: </label>
																							<div class="col-sm-9"><?php
																							echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$feedbacks['FeedbackResult']['created']),$format = 'd M, Y H:i:s');
																							//echo date('d M, Y H:i:s', $feedbacks['FeedbackResult']['created']); ?></div>
																						</li>
																					<?php } ?>
																					<?php if(isset($feedbacks['FeedbackResult']['modified']) && !empty($feedbacks['FeedbackResult']['modified']) && $feedbacks['FeedbackResult']['created'] != $feedbacks['FeedbackResult']['modified']){ ?>
																						<li>
																							<label class="col-sm-3">Feedback Updated on: </label>
																							<div class="col-sm-9"><?php
																							//echo date('d M, Y H:i:s', $feedbacks['FeedbackResult']['modified']);
																							echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$feedbacks['FeedbackResult']['modified']),$format = 'd M, Y H:i:s');
																							?></div>
																						</li>
																					<?php }  ?>
																				</ul>
																			</div>
																		</div>
																		<?php
																	}
																	?>
<?php if(feedback_is_declined($feedbacks['Feedback']['id']) <= 0) { ?>
	<div class="panel panel-success voting_entities_view">
		<div class="panel-heading bg-green">Feedback</div>
		<div class="panel-body">
			<ul>
				<li>
					<label class="">Feedback For:</label>
					<div class="">
						<?php if (isset($feedbacks['Feedback']['feedback_for'])) echo $feedbacks['Feedback']['feedback_for']; ?>
					</div>
				</li>
				<li>
					<label class="">Your Feedback:</label>
					<div class="">
						<div class="form-group">
							<?php if(isset($disabledSignOff) && !empty($disabledSignOff)){
								echo '<span class="pull-right" style="padding-bottom:15px">Closed</span>';
							}else if(($feedbacks['Feedback']['start_date'] <= date('Y-m-d 00:00:00')) && ($feedbacks['Feedback']['end_date'] >= date('Y-m-d 00:00:00'))){ ?>
								<textarea required rows="5" class="form-control feedback_desc" placeholder="Feedback" name="data[FeedbackResult][feedback]"></textarea>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<label>Upload</label>
							</div>
							<div class="col-lg-9 col-md-10 col-sm-10 mg_top">
								<div class="feedback-file-upload">
									<div class="input-group form-group">
										<div class="input-group-addon"><i class="uploadblack"></i></div>
                                        <span data-original-title=" Click to Upload a file" class="docUpload icon_btn bg-white border-radius-right tipText feedback_att" title="">
											<input type="file" placeholder="Upload File" id="doc_feedback_file" class="form-control upload" name="data[FeedbackAttachment][file_name]">
											<span id="feedbackupText" class="text-blue">Upload Document</span>
										</span>
									</div>
								</div>
								<a id="feedbacksave_document" class="btn btn-success btn_progress btn_progress_wrapper btn-sm save_document">
									<div class="btn_progressbar"></div>
									<span class="text">Upload</span>
								</a>
								<div class="error-messages text-dangers showing" id="feedbacktest_for_error">Document is required.</div>
								<div class="error-messages text-dangers showing" id="feedbackdoc_type_error">Invalid file format.</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 save_document_upload">
								<div id="feedbackDocs"></div>
							</div>
						</div>
						<?php
					}else if(($feedbacks['Feedback']['start_date'] > date('Y-m-d 00:00:00'))){
						echo 'Not Started';
					}else{ echo 'Expired';  }
				?>
																			</div>
																		 </li>



					<?php if(isset($feedbacks['FeedbackResult']) && !empty($feedbacks['FeedbackResult'])){ ?>
						<li>
							<div class="">
								<?php
								foreach($feedbacks['FeedbackResult'] as $result){
									if(isset($result['is_decline']) && !empty($result['is_decline'])){
										echo 'Declined'; break;
									}
									?>
									<div style="clear:both" class="panel  panel-dafult  voting_entities_view">
										<div class="panel-heading bg-green">Feedback Sent</div>
										<div class="panel-body">
											<div class="comment feedback-comment">
												<span class="timestamp">
													<?php if(isset($result['feedback']) && !empty($result['created'])) {
														//echo '<b>'.date('d M,Y h:i A',$result['created']).'</b>';
														echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$result['created']),$format = 'd M,Y g:i A');
													}?>
												</span>
												<p>
													<div class="fsent fcomm" ><?php if(isset($result['feedback']) && !empty($result['feedback'])) echo $result['feedback'];  ?></div>
												</p>
												<?php if(isset($result['FeedbackAttachment']) && !empty($result['FeedbackAttachment'])){ ?>
													<div class="form-group clearfix margin-bottom doc_adh">
														<div class="col-sm-12 nopadding">
															<?php
															foreach($result['FeedbackAttachment'] as $FeedbackAttachment){
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
																	<a href="<?php echo $downloadURL ?>" class="btn_file_link" data-remote="<?php echo $downloadURL ?>" data-id="<?php echo $id; ?>" > <?php echo $basename; ?></a>
																</div>
																<?php
															} ?>
														</div>
													</div>
													<?php
												}
												?>
											</div>
										</div>
										<div class="panel-heading bg-green">Feedback Rating</div>
										<div class="panel-body">
											<ul>
												<li>
													<label class="">Feedback Rating Received:&nbsp;</label>
													<div class="" style="display:inline">
														<?php
														$rtts = $this->Common->feedbackRatebyResultID($result['feedback_id'],$result['id']);
														$rttsC = $this->Common->feedbackRateC($result['feedback_id'],$result['id'] );
														$rttsDetail = $this->Common->feedbackRateDetail($result['feedback_id'],$result['id'] );
														if (isset($rtts)) echo $rtts;
														?>
													</div>
												</li>
												<li>
													<label class="">Comment received about your Feedback: </label>
													<div class="pull-right">
														<span class="timestamp">
															<?php
															echo isset($rttsDetail) ? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$rttsDetail['modified']),$format = 'd M,Y g:i A')  : ' ';
															?>
														</span>
													</div>
													<div class="">
														<div class="fsent frate" >
															<?php echo isset($rttsC) ? $rttsC : "None Given" ;  ?>
														</div>
													</div>
												</li>
											</ul>
										</div>
									</div>
									<?php
								}
								?>
							</div>
						</li>
						<?php
					}
					$feedback = 0;
				}
				?>
				<?php if(isset($feedbacks['FeedbackResult']['created']) && !empty($feedbacks['FeedbackResult']['created'])){ ?>
					<li>
						<label class="col-sm-3">Feedbackd on: </label>
						<div class="col-sm-9"><?php
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$feedbacks['FeedbackResult']['created']),$format = 'd M, Y H:i:s');
						//echo date('d M, Y H:i:s', $feedbacks['FeedbackResult']['created']); ?></div>
					</li>
				<?php } ?>
				<?php if(isset($feedbacks['FeedbackResult']['modified']) && !empty($feedbacks['FeedbackResult']['modified']) && $feedbacks['FeedbackResult']['created'] != $feedbacks['FeedbackResult']['modified']){ ?>
					<li>
						<label class="col-sm-3">Feedback Updated on: </label>
						<div class="col-sm-9"><?php
						//echo date('d M, Y H:i:s', $feedbacks['FeedbackResult']['modified']);
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$feedbacks['FeedbackResult']['modified']),$format = 'd M, Y H:i:s');
						?></div>
					</li>
				<?php }  ?>
			</ul>
		</div>
	</div>
</form>
                                                            </div>
                                                        <?php //} ?>
                                                    </div>



                                                </li>
                                            </ul>
                                        </div>
                                        <!-- End conversations Tab	-->
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </div>
</div>


<style>
	.fsent {
		display: block;
	    width: 100%;
	    border: 1px solid #ccc;
	    float: left;
	    padding: 10px 15px;
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
    .optionsa input {  margin-right: 5px;}
    .optionsa {  padding: 0;}

input.range {
  margin-top: 50px;
  transform: rotate(-90deg);
}

.col-sm-2 > label {
  margin-left: 20px;
  margin-top: 35px;
}

.range_span{
  clear: both;
  float: left;
  text-align: center;
  width: 80px;
}

.comment {

  border-radius: 5px;
  float: left;
  margin: 5px 0;
  padding: 5px 10px 0;
  width: 100%;
}

.timestamp {
  float: right;
  text-align: right;
  width: 100%;
}

.showing{ display:none}
    .error-messages {
    color: #dd4b39;
    font-size: 11px;
    }
.hiding{ display: block}


.comment .doc_adh {
    border: 1px solid burlywood;
    border-radius: 10px;
    box-shadow: 0px 0px 20px 0px #deb887 inset;
    padding: 5px;
    text-align: justify;
}
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
.comment > p {
  text-transform: capitalize;
}

.image_namebox:first-child{
  /*   margin-left: 10px; */
}

.comment {
  background: #eee none repeat scroll 0 0;
  border-radius: 10px !important;
}

</style>


<script type="text/javascript" >
 $("body").delegate(".decline_feedback", "click", function (event) { //console.log('dddd');
    $('#confirm-boxs').find('#modal_body').text("Are you sure you want to decline this Feedback request?");
         feedback_id = $(this).attr('rel');
         $('#confirm-boxs').modal({keyboard: true})
                .on('click', '#s_off_yes', function () {
					url = '<?php echo SITEURL."entities/decline_feedback/" ?>'+feedback_id;
					window.location.href= url;
                    /*  $.ajax({
                        type: 'POST',
                        data: 'feedback_id='+feedback_id,
                        url: url,
                        global: false,
                        dataType: 'JSON',
                        beforeSend: function () {

                        },
                        complete: function () {
                            setTimeout(function () {
                                //$('#confirm-boxs').modal('hide');
								location.reload();
                            }, 300)
                        },
                        success: function (response, statusText, jxhr) {
                            if(response == 'success')
							// $('#confirm-boxs').modal('hide');
							location.reload();
                        }
                    });  */
        });
    });


	$(document).ready(function(){
		$.checkExtention = function (file) {
			var file = file || null;

			if (file === null)
				return false;

			var validExtensions = $js_config.allowed_ext; //array of valid extensions
			var fileName = file.name;
			var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1).toLowerCase();
			if ($.inArray(fileNameExt, validExtensions) == -1) {
				return false;
			}
			return true;
		}
		var publik = {};
		publik.feedback_doc_file_valid = false;
		$('#doc_feedback_file').on('change', function (event) {
			var file = this.files[0];
			if ($.checkExtention(file)) {
				publik.feedback_doc_file_valid = true;
				//$(this).parents(".col-sm-5:first").find('span.error-message.text-danger').html("")
			}
			else {
				//$(this).parents(".col-sm-5:first").find('span.error-message.text-danger').html("Invalid file type.")
				publik.feedback_doc_file_valid = false;
			}
		});


		$("#doc_feedback_file").on('change', function (event) {
			event.preventDefault();

			var file = this.files[0],
					name = file.name,
					size = file.size,
					type = file.type,
					$upText = $(this).parent().find('#feedbackupText');
			ext =name.substring(name.lastIndexOf('.'));
			filename =name.substring(0,name.lastIndexOf('.'));
			if(filename.length > 20){
				filename = filename.substring(0,20)+'......'+ext;
			}else{
				filename = name;
			}
			var filesize = file.size / 1048576;
			$upText.html(filename + ', ' + filesize.toFixed(2) + 'MB')
		});


		// Feedback upload document

	$("body").delegate("#feedbacksave_document", 'click', function (event) {
		event.preventDefault();

		var $t = $(this),
				url = $js_config.base_url + "entities/add_feedback_doc/<?php echo $feedbacks['Feedback']['element_id']; ?>" ,
				//$table = $("#documents_table:first"),
				// $form = $("form#FeedbackResultFeedbackSaveForm"),
				$form = $("form#FeedbackResultFeedbacksForm"),
				action = url;//$form.attr("action"),
				$upText = $form.find("#feedbackupText");

		var formData = new FormData($form[0]),
				$fileInput = $('.voting_entities_view').find('input#doc_feedback_file'),
				//$fileTT = $form.find('input#doc_title'),
				$span_text = $t.find('span.text'),
				$div_progress = $t.find('div.btn_progressbar'),
				file = $fileInput[0].files[0];
//console.log($fileInput);
		// 3-9-15 updates
		 var $bar_wrap = $("#progress_wrapper"),
			$progress = $("#progress"),
			$progress_bar = $("#progress_bar"),
			$percent_text = $("#percent_text");
		// 3-9-15 updates


		var valid_flag = false,
			sizeMB = 0;


		if ($fileInput.val() !== "" && file !== undefined) {
			var name = file.name,
					size = file.size,
					type = file.type;

			var vextension = $.checkExtention(file);

			if (publik.feedback_doc_file_valid && $.checkExtention(file) ) {
				formData.append('extension_valid', 2);
				formData.append('file_name', $fileInput[0].files[0]);
				valid_flag = true;
				sizeMB = parseInt(size / (1024*1024))
			}
			else {
				formData.append('file_name', '');
				formData.append('extension_valid', 1);
			}
		}else if($('#update_valid').val()=='valid'){

                   valid_flag = true;
                }
		// formData.append('data[FeedbackAttachment][feedback_id]', $('[name="data[FeedbackResult][feedback_id]"]').val());
		var other_data = $form.serializeArray();
		$.each(other_data,function(key,input){
			formData.append(input.name,input.value);
		});
		$('div#feedbackdoc_type_error').addClass('showing');
		$('div#feedbackdoc_type_error').removeClass('hiding');
		$('div#feedbacktest_for_error').addClass('showing');
		$('div#feedbacktest_for_error').removeClass('hiding');

		if( sizeMB > 0 && sizeMB > 10 ) {
			$(".ajax_flash").text("File size limit exceeded,Please upload a file upto 10MB.").fadeIn(500)
				setTimeout( function() {
					if( $(".ajax_flash").length > 0 ) {
						$(".ajax_flash").fadeOut(600).text('');
					}
				}, 3000)
			return;
		}
		var $id = $form.find("input[name='data[FeedbackAttachment][feedback_id]']")
		//if( ( $fileInput.val() !== ""  && $fileTT.val() !== "" ) || $upText.text()!='Upload Document') {

		if ( valid_flag && ($fileInput.val() !== "" || $upText.text() != 'Upload Document')) {

			$.ajax({
				type: 'POST',
				dataType: "JSON",
				url: action,
				data: formData,
				global: true,
				cache: false,
				contentType: false,
				processData: false,
				xhr: function () {
// 3-9-15 updates
					var xhr = new window.XMLHttpRequest();

					//Upload progress
					xhr.upload.addEventListener("progress", function (event) {
						if (event.lengthComputable) {
							var percentComplete = Math.round(event.loaded / event.total * 100);
							$progress_bar.css("width", percentComplete + "%")
							$percent_text.html(percentComplete + "%")
							$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text(percentComplete + "%")
						}
					}, false);

					//Download progress
					xhr.addEventListener("progress", function (event) {
						if (event.lengthComputable) {
							var percentComplete = Math.round(event.loaded / event.total * 100);
							$progress_bar.css("width", percentComplete + "%")
							$percent_text.html(percentComplete + "%")
							$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text(percentComplete + "%")

						}
					}, false);

					return xhr;
				},
				beforeSend: function () {
					$bar_wrap.animate({
						opacity: 1
					}, 100, function(){

					})
					$progress.addClass('bg-yellow')
					$progress_bar.css({'width': 0})
					$percent_text.html('')
					$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text("Loading...")
				},
// 3-9-15 updates
				complete: function () {
					$bar_wrap.animate({
						opacity: 0
					}, 400, function(){
						setTimeout(function () {
							$progress.removeClass('bg-yellow')
							$progress_bar.css({'width': 0})
							$percent_text.html('')
							$('.ajax_overlay_preloader > .gif_preloader > .loading_text').text("Loading...")
						}, 100)
						$upText.html("Upload Document")
					})


				},
				success: function (response) {

					// $form[0].reset()
					$('#feedbackupText').text('Upload Document');
					$form.find('div#error-messages').removeClass('hiding');
					$form.find('div.error-messages').removeClass('hiding');
					$form.find('div#error-messages').addClass('showing');
					$form.find('div.error-messages').addClass('showing');

					if (response.success) {
						// set extension check flag to false again after upload success
						publik.doc_file_valid = false;

						//$form.find('span.error-message.text-danger').html('');

						if (!$.isEmptyObject(response.content)) {
							setTimeout(function () {
								var data = response.content.FeedbackAttachment
//console.log(data)

								$("#documents_table").find('.row').removeClass('bg-warning')

								// Update specific row if clicked on edit link
								if ($id.val() !== '') {
									var filename = data.file_name,
											// ext = filename.split('.'),
											// extsize = (ext.length) - 1,
											extention = filename.substring(filename.lastIndexOf('.')),
											// extention = ext[extsize],
											file_link_html =
											'<div class="image_namebox"><span class="download_asset icon_btn icon_btn_sm icon_btn_teal">' +
											'<span class="icon_text">' + extention + '</span>' +
											'</span>' +
											'<a href="' + $js_config.base_url + 'entities/download_feedback_doc/' + data.id + '" class="btn_file_link">' + filename + '</a>'+'</div><div id="feedbackDocs"></div>';


									$('#feedbackDocs').replaceWith(file_link_html);
									feedback_desc = $('.feedback_desc').val();
									$form[0].reset();
									$('.feedback_desc').val(feedback_desc);
                                    $('#feedbacksave_document').html('<div class="btn_progressbar"></div><span class="text">Upload</span>');
								}
								else {
									var cloneHtml = create_doc_row(data);
										$cloneRow = $('<div />');
									$cloneRow.addClass('row').css({opacity: 0}).html(cloneHtml)


									$table.find('.data_catcher').append($cloneRow)
									$cloneRow.animate( {
										opacity: 1
									}, 800, function () {

									})
									//$form[0].reset()
								}

							}, 0)
						}

					}

					else {

						if (!$.isEmptyObject(response.content)) {
							$.each(response.content,
									function (ele, msg) {

										var $inpEle = $('input[name="data[FeedbackAttachment][' + ele + ']"]');
										$inpEle.parents('div:first').find('span.error-message.text-danger').html(msg);

									})
						}
					}
				}
			});
		}
				else {


			//var $error_div = $form.parent().find('#test_for_error')

			//	$error_div.html('Error').addClass('error-message').addClass('text-danger');



			if (  !valid_flag || $fileInput.val() == "" && $upText.text() == 'Upload Document') {

				if( $fileInput.val() == '' && $upText.text() == 'Upload Document') {

					//$('div#doc_type_error').addClass('showing');
					//$('div#doc_type_error').removeClass('hiding');
					$('div#feedbacktest_for_error').removeClass('showing');
					$('div#feedbacktest_for_error').addClass('hiding');
					//console.log('vikas');

				}
				else {

					$('div#feedbackdoc_type_error').addClass('hiding');
					$('div#feedbackdoc_type_error').removeClass('showing');
					//console.log('aakash');
				}

			}




			// alert("Please select a file.") doc_type_error

			// //console.log($form.find("input#doc_file").parents('.col-sm-5:first').find('div.error-message').html('hi let me check'));
			// //console.log($form.find(".row").find('.col-sm-5:eq(1)').find('div.error-message') );
			// //console.log(.html('hi let me check'));
			// $form.find("input#doc_file").parents(".col-sm-5:first").find('div.changer');
			// $("#doc_file").parents(".col-sm-5:first").find('div.changer').addClass('class')

			// publik.doc_file_valid = false;
			//console.log($("#doc_file").parents(".col-sm-5:first").find('span.error-message.text-danger').html())
		}
	});



	});


	$(function(e){
		$('.save_feedback').click(function(a){

			a.preventDefault();

			$flag = false;


			var mg = $('.feedback_desc').val();

			if($.trim(mg).length > 0){

		//	if($('.feedback_desc').val().length > 1){
					$flag = true;
			}
			if($('.save_document_upload').find('.image_namebox').length > 0){
					$flag = true;
			}

			if($flag == true){
				 $('#FeedbackResultFeedbacksForm').submit();
				//$('#FeedbackResultFeedbackSaveForm').submit();
				$('.error-fed').remove();
				$(this).attr('disabled','disabled');

			}else{
				$('.error-fed').remove();
				$('<span class="error-message error-fed" >Please proivde a feedback or upload the relevant document.</span>').insertAfter('.feedback_desc');
			}


		})
	})

</script>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="popup_modal" class="modal modal-success fade ">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
 </div>
<div class="modal modal-success fade" id="confirm-boxs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top" id="modal_header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button><h3 class="modal-title">Decline Feedback</h3></div>

            <div class="modal-body" id="modal_body"></div>

            <div class="modal-footer" id="modal_footer">
                <a class="btn btn-success btn-ok btn_progress btn_progress_wrapper" id="s_off_yes">
                    <div class="btn_progressbar"></div>
                    <span class="">Decline</span>
                </a>
                <button type="button" id="s_off_no" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>

            <div class="modal-footer" id="modal_footer_2" style="display: none;">
                <a class="btn btn-success btn-ok" id="confirm-yes">Yes</a>
                <a class="btn btn-danger " id="confirm-no" data-dismiss="modal">No</a>
            </div>

        </div>
    </div>
</div>