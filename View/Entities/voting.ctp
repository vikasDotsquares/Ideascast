<script src="<?php echo SITEURL; ?>/js/jquery.rangegroup.js" type="text/javascript"></script>

<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <?php //pr($votes); ?>
            <section class="content-header clearfix">
                <h1 class="pull-left"><?php if (isset($votes['Project']['title'])) echo $votes['Project']['title']; ?><br>
                    <p class="text-muted date-time">
                        <span>View Vote details</span>
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
                                                <li data-content="votes" style="" class="selected">
                                                    <div data-limit="5" data-model="vote" id="voting_table" class="table_wrapper clearfix">
                                                        <?php if (isset($votes) && !empty($votes)) {
																$disabledSignOff = '';
																if(isset($votes['Vote']['is_completed']) && !empty($votes['Vote']['is_completed'])){
																	$disabledSignOff = 'disabled';
																}
                                                           // pr($votes);
                                                            ?>
                                                            <?php echo $this->Form->create('VoteResult', array('url' => array('controller' => 'entities', 'action' => 'vote_save'), 'class' => 'padding formAddElementVote', 'style' => '', 'enctype' => 'multipart/form-data')); ?>
                                                             <input type="hidden" name="data[VoteResult][vote_id]" class="form-control" value="<?php echo $votes['Vote']['id']; ?>" />
                                                             <input type="hidden" name="data[VoteResult][id]" class="form-control" value="<?php if(isset($votes['VoteResult']['id'])) echo $votes['VoteResult']['id']; ?>" />
                                                             <input type="hidden" name="data[VoteResult][vote_type_id]" class="form-control" value="<?php echo $votes['VoteQuestion']['vote_type_id']; ?>" />
                                                              <input type="hidden" name="data[VoteResult][vote_question_id]" class="form-control" value="<?php echo $votes['VoteQuestion']['id']; ?>" />
                                                               <input type="hidden" name="data[VoteResult][vote_change_freq]" id="vote_change_freq" class="form-control" value="<?php echo $votes['Vote']['vote_change_freq']; ?>" />

															   <?php  //pr($votes);
																	$is_voted = '';
																	$is_alw = false;

																	$beginOfDay = strtotime("midnight", time());
																	$endOfDay   = strtotime("tomorrow", $beginOfDay) - 1 ;

																if(empty($disabledSignOff)){
																	if(isset($votes['VoteResult']['id']) && !empty($votes['VoteResult']['id'])){
																		$is_voted = '1';
																	}else if(($votes['Vote']['start_date'] <= date('Y-m-d 00:00:00')) && ($votes['Vote']['end_date'] >= date('Y-m-d 00:00:00'))){
																	$is_alw = true;
                                                                ?>
																   <div class="row" style="padding :15px 15px 0px 15px;  border-top-left-radius: 3px;    background-color: #f5f5f5;  position:absolute; left:0; width:100%;   border: 1px solid #ddd; top:0;  border-top-right-radius: 3px;border-bottom:2px solid #ddd; border-left:0;border-right:0;border-top:0;margin:0;"  >

																	<div class="pull-left project-detail" style="padding-bottom:15px">
																		<span class="bg-blakish nomargin-left sb_blog" style="cursor:default;">Start: <?php if (isset($votes['Vote']['start_date'])) echo date('d M, Y', strtotime($votes['Vote']['start_date'])); ?></span>
																		<span class="bg-black sb_blog" style="cursor:default;"  >End: <?php if (isset($votes['Vote']['end_date'])){
																		//echo date('d M,Y', strtotime($votes['Vote']['end_date']));
																		echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($votes['Vote']['end_date'])),$format = 'd M, Y');

																		}?></span>

																	</div>


																	<div class="form-group pull-right"   style=" ">
																		<input type="button"  rel="<?php echo $votes['Vote']['id']; ?>" class="btn btn-sm btn-warning decline_vote" value="Decline" />
																	</div>

																	<div class="form-group pull-right"   style="  margin-right: 10px;">
																		<input type="submit" class="btn btn-sm btn-success save_vote submit" value="Vote" />
																	</div>
																</div>
																<?php
																	}
																}
																if($is_alw == false){
																?>

																 <div class="row" style="padding :15px 15px 0px 15px;  border-top-left-radius: 3px;    background-color: #f5f5f5;  position:absolute; left:0; width:100%;   border: 1px solid #ddd; top:0;  border-top-right-radius: 3px;border-bottom:2px solid #ddd; border-left:0;border-right:0;border-top:0;margin:0;"  >

																 <div class="pull-left project-detail" style="padding-bottom:15px">
																		<?php /* ?><span class="bg-blakish nomargin-left sb_blog" style="cursor:default;">Start: <?php if (isset($votes['Vote']['start_date'])) {
																			//echo date('d M,Y', strtotime($votes['Vote']['start_date']));
																			echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($votes['Vote']['start_date'])),$format = 'd M, Y');
																		}?></span><?php */ ?>
																		<span class="bg-black sb_blog" style="cursor:default;">Send Vote by: <?php if (isset($votes['Vote']['end_date'])){
																		//echo date('d M,Y', strtotime($votes['Vote']['end_date']));
																		echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($votes['Vote']['end_date'])),$format = 'd M, Y');
																		}?></span>

																	</div>

																 <span class="pull-right" style="padding-bottom:15px; cursor:default;">
																 <?php
																$dc = false;
																$dcC = false;
																$dcN = false;
																$vote_req_status = '';
															//	pr($votes['VoteResult']); die;
																if(isset($votes['VoteResult']['VoteQuestionOption']) && empty($votes['VoteResult']['VoteQuestionOption'])){
																		$vote_req_status =  "Declined";
																		$dc = true;
																		$dcN = true;

																}


																if((isset($disabledSignOff) && !empty($disabledSignOff)) && ($dcN == false)){ $vote_req_status =  'Closed'; $dcC = true;
																}


																if($dcC ==false && $dcN == false){
																if($votes['VoteResult']['vote_question_option_id'] == 'D'){
																		$vote_req_status =  "Declined";
																		$dc = true;
																}



																if($dc == false){
																 if(strtotime($votes['Vote']['start_date']) > time() && isset($disabledSignOff) && empty($disabledSignOff)){
																 $vote_req_status =  'Not Started';
																}else if(empty($votes['VoteResult']['id']) && isset($disabledSignOff) && empty($disabledSignOff)){
																 $vote_req_status =  'Expired';
                                                                 }
																 else if(isset($votes['VoteResult']['created']) && !empty($votes['VoteResult']['created'])){
																  $vote_req_status =  'Voted';
																  }
																}
																}

																?>
																</span>
																</div>
																<?php } ?>

                                                            <div class="row" style="margin-top: 50px;" >

                                                            	<div class="panel  panel-dafult  voting_entities_view entities_view-list">
                                                                	<div class="panel-heading bg-green">
                                                                  		Vote Details
                                                                            <!--<a class="pull-right" href="<?php echo SITEURL ?>entities/vote_request" ><i class="fa fa-angle-double-left"></i> Back</a>-->
                                                                        </div>
                                                                    <div class="panel-body">
                                                                   	<ul>
                                                                    	<li class="voting-sec-main">
                                                                        	  <label class="voting-sec-col1">Project Title: </label>
                                                                    		 <div class="voting-sec-col2"><?php if (isset($votes['Project']['title'])) echo $votes['Project']['title']; ?></div>
                                                                        </li>
                                                                    	<li class="voting-sec-main">
                                                                        	 <label class="voting-sec-col1">Vote Title: </label>
                                                                   			<div class="voting-sec-col2"><?php if (isset($votes['Vote']['title'])) echo $votes['Vote']['title']; ?></div>
                                                                        </li>
                                                                        <li class="voting-sec-main">
                                                                        	 <label class="voting-sec-col1">Requested By: </label>
                                                                   			 <div class="voting-sec-col2">
																			  <a href="#" class="show_profile text-black" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo SITEURL; ?>shares/show_profile/<?php echo $votes['Owner']['UserDetail']['user_id']; ?>" ><i class="fa fa-user text-maroon"></i>
																			 <?php if (isset($votes['Owner']['UserDetail']['first_name'])) echo $votes['Owner']['UserDetail']['first_name']; if (isset($votes['Owner']['UserDetail']['last_name'])) echo ' ' . $votes['Owner']['UserDetail']['last_name']; ?>
																			 </a>
																			 </div>
                                                                        </li>
                                                                        <li class="voting-sec-main">
                                                                        	<label class="voting-sec-col1">Accompanying Note: </label>
                                                                   			<div class="voting-sec-col2"><?php if (isset($votes['Vote']['reason'])) echo $votes['Vote']['reason']; ?></div>
                                                                        </li>
                                                                        <li class="voting-sec-main">
                                                                        	 <label class="voting-sec-col1">Vote Method: </label>
                                                                    		<div class="voting-sec-col2"><?php if (isset($votes['VoteQuestion']['VoteType']['title'])){
																			echo $votes['VoteQuestion']['VoteType']['title'];
																			$typeDesc = $this->Common->VoteTypeDescriptionForRequest($votes['VoteQuestion']['VoteType']['id']);
																			?>&nbsp;<a href="javascript:void(0);" data-original-title="" tabindex="0" data-placement="top" class="btn toltipover" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="<?php echo $typeDesc;?>"><i class="fa fa-info"></i></a>

																			<?php /* <a title="<?php echo $typeDesc;?>"  class="todolist_links" style="cursor:pointer;"><i  class="fa fa-info-circle" id="typeDescIcon"></i></a>*/ ?>
																			<?php } ?>
																			</div>
                                                                        </li>
																		<?php if (isset($votes['VoteQuestion']['vote_type_id']) && $votes['VoteQuestion']['vote_type_id'] == '6'){

																		?>
																		<li class="voting-sec-main">
                                                                        	 <label class="voting-sec-col1">Total Votes: </label>
                                                                    		<div class="voting-sec-col2"><?php if (isset($votes['VoteQuestion'])) echo $votes['VoteQuestion']['distributed_count']; ?></div>
                                                                        </li>
																		<?php } ?>
                                                                    </ul>
                                                                        </div>
                                                                        </div>

                                                                        <div class="panel panel-success voting_entities_view entities_view-list">
                                                                            <div class="panel-heading bg-green">
                                                                          	Vote
                                                                            </div>
                                                                            <div class="panel-body">
                                                                            <ul>


                                                                        <li class="voting-sec-main">
                                                                        	<label class="voting-sec-col1">Voting For:</label>
                                                                    		<div class="voting-sec-col2"><?php if (isset($votes['VoteQuestion']['title'])) echo $votes['VoteQuestion']['title']; ?></div>
                                                                        </li>

                                                                         <?php
                                                                  $vote = 0;
                                                            // Time Check

															if ((strtotime($votes['Vote']['start_date']) <= time() && $votes['Vote']['end_date'] >= date('Y-m-d')) && isset($disabledSignOff) && empty($disabledSignOff)){

                                                                if (empty($votes['VoteResult']['id']) || (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '5')) || (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '6')) || (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '2')) ) {
                                                                    $vote =1;
																	                                                                   //pr($votes);
                                                                    ?>
                                                                         <?php
                                                                        if (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '1' || $votes['VoteQuestion']['vote_type_id'] == '4' || $votes['VoteQuestion']['vote_type_id'] == '3')) {

                                                                            if (isset($votes['VoteQuestion']['VoteQuestionOption']) && !empty($votes['VoteQuestion']['VoteQuestionOption'])) { ?>
                                                                                <li class="voting-sec-main">
                                                                             <label class="voting-sec-col1">Your Vote:</label>
                                                                                 <div class="voting-sec-col2">
                                                                                <?php
                                                                                foreach ($votes['VoteQuestion']['VoteQuestionOption'] as $options) {
                                                                                    $vote_question_option_id = '';
                                                                                    if(isset($votes['VoteResult']['vote_question_option_id']) && !empty($votes['VoteResult']['vote_question_option_id'])){
                                                                                        $vote_question_option_id = $votes['VoteResult']['vote_question_option_id'];
                                                                                    }
                                                                                    ?>

                                                                                		<div class="optionsa">
                                                                                        <input type="radio" <?php if($vote_question_option_id == $options['id']){ echo 'checked'; } ?>  required="required" name="data[VoteResult][vote_question_option_id]" value="<?php echo $options['id']; ?>" ><?php echo $options['option']; ?>
                                                                                   </div>

                                                                                <?php
                                                                                } ?> </div>
                                                                         </li>
                                                                            <?php }
                                                                        } else if (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '2')) {
                                                                            $disabled = false;
																			if(!empty($votes['VoteResult']['id']) ){
																				$disabled = true;

																			}
																			if($votes['VoteResult']['vote_question_option_id'] == 'D'){ ?>
																			<li class="voting-sec-main">
																			  <label class="voting-sec-col1">Your Vote: </label>
																			  <div class="voting-sec-col2"><?php  echo 'Declined'; ?></div>
																		  </li>
																			<?php }else{
																			?>
                                                                          <li class="voting-sec-main">
                                                                             <label class="voting-sec-col1">Your Vote:</label>
																			 <div class="voting-sec-col2">
                                                                            <div id="slider" class="type_2_slider"></div>
                                                                            <div class="amount"><input type="text"  value="<?php echo $votes['VoteResult']['vote_question_option_id']; ?>" id="amount" name="data[VoteResult][vote_question_option_id]" readonly ></div>
																			</div>
                                                                          </li>
                                                                            <?php

                                                                            $min = 0;
                                                                            $max = 10;
                                                                            if (isset($votes['VoteQuestion']['VoteQuestionOption'][0]['option'])) {
                                                                                $min = $votes['VoteQuestion']['VoteQuestionOption'][0]['option'];
                                                                            }

                                                                            if (isset($votes['VoteQuestion']['VoteQuestionOption'][1]['option'])) {
                                                                                $max = $votes['VoteQuestion']['VoteQuestionOption'][1]['option'];
                                                                            }
                                                                            ?>

                                                                            <script type="text/javascript" >
                                                                                $(function () {
                                                                                    $("#slider").slider({
																					    disabled: '<?php echo $disabled; ?>',
                                                                                        range: "max",
                                                                                        min: <?php echo $min; ?>,
                                                                                        max: <?php echo $max; ?>,
                                                                                        value: <?php if(isset($votes['VoteResult']['vote_question_option_id']) && !empty($votes['VoteResult']['vote_question_option_id'])){ echo $votes['VoteResult']['vote_question_option_id']; }else{ echo '0'; } ?>,
                                                                                        slide: function (event, ui) {
                                                                                            $("#amount").val(ui.value);
                                                                                        }
                                                                                    });
                                                                                    $("#amount").val($("#slider").slider("value"));
                                                                                });
                                                                            </script>

														<?php
															}
														}else if (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '5')) {
																$disabled = '';
																if(!empty($votes['VoteResult']['id'])){
																	$disabled = 'disabled';
																}

																if(isset($votes['VoteResult']['VoteQuestionOption']) && empty($votes['VoteResult']['VoteQuestionOption'])){ ?>
																	<li class="voting-sec-main">
                                                                          <label class="voting-sec-col1">Your Vote: </label>
                                                                          <div class="voting-sec-col2"><?php  echo 'Declined'; ?></div>
                                                                      </li>
															<?php	}else{ ?>
															<li>
															<label class="col-sm-12">Your Vote:</label>
															<div class="col-sm-12">
																<div class="slider_type_5">
																 <?php if(isset($votes['VoteQuestion']['VoteQuestionOption']) && !empty($votes['VoteQuestion']['VoteQuestionOption'])){
																	 //$val = 1;
																	 foreach($votes['VoteQuestion']['VoteQuestionOption'] as $votess){
																		$val =  $this->Common->VoteResultOption($votess['vote_question_id'],$votess['id']);
																		//$is_editable =  $this->Common->IsQuesEditble($votess['vote_question_id']);
																	 ?>
																	 <div class="slider_box">
																		<div class="slider_rang"><input id="<?php echo $votess['id']; ?>" <?php echo $disabled; ?>  type="range" value="<?php echo $val; ?>" class="range" range-group="myGroup1" name="range1"  min="0" max="10"></div>
																		<label><?php echo $votess['option']; ?></label>
																		<span class="range_span" id="val_<?php echo $votess['id']; ?>"><?php echo $val; ?></span>
																		<input type="hidden" name="data[VoteResult][vote_question_option_id][<?php echo $votess['id']; ?>]" id="inval_<?php echo $votess['id']; ?>" value="<?php echo $val; ?>" />
																	 </div>
																 <?php //pr($votess);
																 } ?>

																<script type="text/javascript" >
																$(document).ready(function(){
																	//$('#<?php echo $votess['id']; ?>').val('<?php echo $val; ?>');
																})
																</script>

														<?php   	} ?></div></div>
														</li>
														<?php
																}
															}else if (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '6')) {
																$disabled = '';
																if(!empty($votes['VoteResult']['id'])){
																	$disabled = 'disabled';
																}

																if(isset($votes['VoteResult']['VoteQuestionOption']) && empty($votes['VoteResult']['VoteQuestionOption'])){ ?>
																	<li class="voting-sec-main">
                                                                          <label class="voting-sec-col1">Your Vote: </label>
                                                                          <div class="voting-sec-col2"><?php  echo 'Declined'; ?></div>
                                                                      </li>
															<?php	}else{ ?>
															<liclass="voting-sec-main">
															<label class="col-sm-12">Your Vote:</label>
															<div class="col-sm-12">
																<div class="slider_type_5">
																 <?php if(isset($votes['VoteQuestion']['VoteQuestionOption']) && !empty($votes['VoteQuestion']['VoteQuestionOption'])){
																	 //$val = 1;
																	 $count = $votes['VoteQuestion']['distributed_count'];
																	 $totalVotes = $count;
																	 foreach($votes['VoteQuestion']['VoteQuestionOption'] as $votess){
																		$val =  $this->Common->VoteResultOption($votess['vote_question_id'],$votess['id']);
																		//$is_editable =  $this->Common->IsQuesEditble($votess['vote_question_id']);
																	 ?>
																	 <div class="slider_box">
																		<div class="slider_rang"><input id="<?php echo $votess['id']; ?>" <?php echo $disabled; ?>  type="range" value="<?php echo $val; ?>" class="range" range-group="myGroup1" name="range1" range-group-max-sum="<?php echo $totalVotes; ?>" min="0" max="<?php echo $totalVotes; ?>"></div>
																		<label><?php echo $votess['option']; ?></label>
																		<span class="range_span" id="val_<?php echo $votess['id']; ?>"><?php echo $val; ?></span>
																		<input type="hidden" name="data[VoteResult][vote_question_option_id][<?php echo $votess['id']; ?>]" id="inval_<?php echo $votess['id']; ?>" value="<?php echo $val; ?>" />
																	 </div>
																 <?php //pr($votess);
																 } ?>

																<script type="text/javascript" >
																$(document).ready(function(){
																	//$('#<?php echo $votess['id']; ?>').val('<?php echo $val; ?>');
																})
																</script>

														<?php   	} ?></div></div>
														</li>
														<?php
																}
															}

																 ?>

															<?php
															 } else if(isset($votes['VoteResult']['VoteQuestionOption']) && empty($votes['VoteResult']['VoteQuestionOption'])){

															 ?>
                                                                     <li class="voting-sec-main">
                                                                          <label class="voting-sec-col1">Your Vote: </label>
                                                                          <div class="voting-sec-col2"><?php  echo 'Declined'; ?></div>
                                                                      </li>
                                                          <?php }else{ ?>
                                                                <li class="voting-sec-main">
                                                                   <label class="voting-sec-col1">Your Vote:</label>
                                                                  <div class="voting-sec-col1"><?php  echo $votes['VoteResult']['VoteQuestionOption']['option']; ?></div>
                                                               </li>
                                                           <?php }
                                                        }else if(strtotime($votes['Vote']['start_date']) > time() && isset($disabledSignOff) && empty($disabledSignOff)){

														?>
																<li class="voting-sec-main">
                                                                    <label class="voting-sec-col1">Your Vote: </label>
                                                                    <div class="voting-sec-col2"><?php echo 'Not Started'; ?></div>
                                                                </li>
														<?php }else if(empty($votes['VoteResult']['id']) && isset($disabledSignOff) && empty($disabledSignOff)){

                                                                 ?>
                                                                 <li class="voting-sec-main">
                                                                    <label class="voting-sec-col1">Your Vote: </label>
                                                                    <div class="voting-sec-col2"><?php  echo 'Expired'; ?></div>
                                                                </li>
                                                        <?php }else{

																if (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '2')) { //pr($votes['VoteResult']);
                                                                            $disabled = false;
																			if(!empty($votes['VoteResult']['id']) && $votes['VoteResult']['vote_question_option_id'] != 'D'){
																				$disabled = true;



																			?>
                                                                          <li class="voting-sec-main">
                                                                             <label class="voting-sec-col1">Your Vote:</label>
																			 <div class="voting-sec-col2">
                                                                            <div id="slider" class="type_2_slider"></div>
                                                                            <div class="amount"><input type="text"  value="<?php echo $votes['VoteResult']['vote_question_option_id']; ?>" id="amount" name="data[VoteResult][vote_question_option_id]" readonly ></div>
																			</div>
                                                                          </li>
                                                                            <?php
                                                                            $min = 0;
                                                                            $max = 10;
                                                                            if (isset($votes['VoteQuestion']['VoteQuestionOption'][0]['option'])) {
                                                                                $min = $votes['VoteQuestion']['VoteQuestionOption'][0]['option'];
                                                                            }

                                                                            if (isset($votes['VoteQuestion']['VoteQuestionOption'][1]['option'])) {
                                                                                $max = $votes['VoteQuestion']['VoteQuestionOption'][1]['option'];
                                                                            }
                                                                            ?>

                                                                            <script type="text/javascript" >
                                                                                $(function () {
                                                                                    $("#slider").slider({
																					    disabled: '<?php echo $disabled; ?>',
                                                                                        range: "max",
                                                                                        min: <?php echo $min; ?>,
                                                                                        max: <?php echo $max; ?>,
                                                                                        value: <?php if(isset($votes['VoteResult']['vote_question_option_id']) && !empty($votes['VoteResult']['vote_question_option_id'])){ echo $votes['VoteResult']['vote_question_option_id']; }else{ echo '0'; } ?>,
                                                                                        slide: function (event, ui) {
                                                                                            $("#amount").val(ui.value);
                                                                                        }
                                                                                    });
                                                                                    $("#amount").val($("#slider").slider("value"));
                                                                                });
                                                                            </script>

														<?php
															}else if(!empty($votes['VoteResult']['id']) && $votes['VoteResult']['vote_question_option_id'] == 'D'){ ?>
																<li class="voting-sec-main">
																  <label class="voting-sec-col1">Your Vote: </label>
																  <div class="voting-sec-col2"><?php  echo 'Declined'; ?></div>
															  </li>

														<?php }else{ ?>
																<li class="voting-sec-main">
                                                                          <label class="voting-sec-col1">Your Vote: </label>
                                                                          <div class="voting-sec-col2"><?php  echo 'Closed'; ?></div>
                                                                      </li>
															<?php
															}
														}else if (isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '5') || isset($votes['VoteQuestion']['vote_type_id']) && ($votes['VoteQuestion']['vote_type_id'] == '6')) {
																$disabled = '';
																if(!empty($votes['VoteResult']['id'])){
																	$disabled = 'disabled';
																}

																if(isset($votes['VoteResult']['VoteQuestionOption']) && empty($votes['VoteResult']['VoteQuestionOption'])){ ?>
																	<li class="voting-sec-main">
                                                                          <label class="voting-sec-col1">Your Vote: </label>
                                                                          <div class="voting-sec-col2"><?php  echo 'Declined'; ?></div>
                                                                      </li>
															<?php	}else { ?>
															<li>

																 <?php if(isset($votes['VoteQuestion']['VoteQuestionOption']) && !empty($votes['VoteQuestion']['VoteQuestionOption']) && !empty($votes['VoteResult']['id'])){
																 ?>
																 <label class="col-sm-12">Your Vote:</label>
															<div class="col-sm-12">
																<div class="slider_type_5">
																<?php
																	 //$val = 1;
																	 foreach($votes['VoteQuestion']['VoteQuestionOption'] as $votess){
																		$val =  $this->Common->VoteResultOption($votess['vote_question_id'],$votess['id']);
																		//$is_editable =  $this->Common->IsQuesEditble($votess['vote_question_id']);
																	 ?>
																	 <div class="slider_box">
																		<div class="slider_rang"><input id="<?php echo $votess['id']; ?>" <?php echo $disabled; ?>  type="range" value="<?php echo $val; ?>" class="range" range-group="myGroup1" name="range1"  min="0" max="10"></div>
																		<label><?php echo $votess['option']; ?></label>
																		<span class="range_span" id="val_<?php echo $votess['id']; ?>"><?php echo $val; ?></span>
																		<input type="hidden" name="data[VoteResult][vote_question_option_id][<?php echo $votess['id']; ?>]" id="inval_<?php echo $votess['id']; ?>" value="<?php echo $val; ?>" />
																	 </div>
																 <?php //pr($votess);
																 } ?>
																</div>
																</div>
																<script type="text/javascript" >
																$(document).ready(function(){
																	//$('#<?php echo $votess['id']; ?>').val('<?php echo $val; ?>');
																})
																</script>

														<?php   	}else if(isset($votes['VoteQuestion']['VoteQuestionOption']) && !empty($votes['VoteQuestion']['VoteQuestionOption']) && empty($votes['VoteResult']['id']) && empty($disabledSignOff)){
																 ?>
																 <label class="col-sm-12">Your Vote:</label>
															<div class="col-sm-12">
																<div class="slider_type_5">
																<?php
																	 //$val = 1;
																	 foreach($votes['VoteQuestion']['VoteQuestionOption'] as $votess){
																		$val =  $this->Common->VoteResultOption($votess['vote_question_id'],$votess['id']);
																		//$is_editable =  $this->Common->IsQuesEditble($votess['vote_question_id']);
																	 ?>
																	 <div class="slider_box">
																		<div class="slider_rang"><input id="<?php echo $votess['id']; ?>" <?php echo $disabled; ?>  type="range" value="<?php echo $val; ?>" class="range" range-group="myGroup1" name="range1"  min="0" max="10"></div>
																		<label><?php echo $votess['option']; ?></label>
																		<span class="range_span" id="val_<?php echo $votess['id']; ?>"><?php echo $val; ?></span>
																		<input type="hidden" name="data[VoteResult][vote_question_option_id][<?php echo $votess['id']; ?>]" id="inval_<?php echo $votess['id']; ?>" value="<?php echo $val; ?>" />
																	 </div>
																 <?php //pr($votess);
																 } ?>
																</div>
																</div>
																<script type="text/javascript" >
																$(document).ready(function(){
																	//$('#<?php echo $votess['id']; ?>').val('<?php echo $val; ?>');
																})
																</script>

														<?php   	}

														else{
															?>
															<label class="voting-sec-col1">Your Vote:</label>
															<div class="voting-sec-col2">
                                                                         <?php  echo 'Closed'; ?>
                                                                     </div>
														<?php } ?>

														</li>
															<?php	}
															}else{

														?>

																	 <li class="voting-sec-main">
																		   <label class="voting-sec-col1">Your Vote:</label>
																		  <div class="voting-sec-col2"><?php if(isset($votes['VoteResult']['VoteQuestionOption']['option']) && !empty($votes['VoteResult']['VoteQuestionOption']['option'])){  echo $votes['VoteResult']['VoteQuestionOption']['option']; }else if(isset($votes['VoteResult']['VoteQuestionOption']) && empty($votes['VoteResult']['VoteQuestionOption'])){  echo 'Declined'; }else if(isset($disabledSignOff) && !empty($disabledSignOff)){ echo 'Closed';  }else{ echo '-'; } ?></div>
																	   </li>
                                                                <?php

																}
															}
														}
                                                                 ?>

                                                                <?php if(isset($votes['VoteResult']['created']) && !empty($votes['VoteResult']['created'])){ ?>
                                                                <li class="voting-sec-main">
                                                                    <label class="voting-sec-col1">Voted On: </label>
                                                                    <div class="voting-sec-col2">
																	<?php
																	if(isset($votes['VoteResult']['VoteQuestionOption']) && empty($votes['VoteResult']['VoteQuestionOption'])){
																		echo 'Declined: ';
																	}
                                                                    //echo date('d M, Y H:i:s', $votes['VoteUser'][0]['modified']);
																	echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$votes['VoteUser'][0]['modified']),$format = 'd M, Y H:i:s');
																	?></div>
                                                                </li>
                                                                <?php } ?>

                                                                <?php if(isset($votes['VoteResult']['modified']) && !empty($votes['VoteResult']['modified']) && $votes['VoteResult']['created'] != $votes['VoteResult']['modified']){ ?>
                                                                <li class="voting-sec-main">
                                                                    <label class="voting-sec-col1">Vote Updated on: </label>
                                                                    <div class="voting-sec-col2"><?php
																	//echo date('d M, Y H:i:s', $votes['VoteResult']['modified']);
																	echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$votes['VoteResult']['modified']),$format = 'd M, Y H:i:s');
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
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </div>
</div>

<style>
.fa-info {
    background: #00aff0 none repeat scroll 0 0;
    border-radius: 50%;
    color: #fff;
    font-size: 13px;
    height: 22px;
    line-height: 21px !important;
    width: 22px;
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
</style>


<script type="text/javascript" >
 $("body").delegate(".decline_vote", "click", function (event) {

 	vote_id = $(this).attr('rel');
	BootstrapDialog.show({
            title: '<h3 class="h3_style">Decline Vote</h3>',
            message: 'Are you sure you want to decline this Vote request?',
            type: BootstrapDialog.TYPE_DANGER,
            draggable: true,
            buttons: [
                {
                    //icon: '',
                    label: ' Decline',
                    cssClass: 'btn-success',
                    autospin: true,
                    action: function (dialogRef) {
                    	window.location.href= $js_config.base_url + 'entities/decline_vote/'+vote_id;
                        dialogRef.enableButtons(false);
                		dialogRef.setClosable(false);

                    }
                },
                {
                    label: ' Cancel',
                    //icon: '',
                    cssClass: 'btn-danger',
                    action: function (dialogRef) {
                        dialogRef.close();
                    }

                }
            ]
        })

    	/*$('#confirm-boxs').find('#modal_body').text("Are you sure, you would like to decline this Vote request?");
         vote_id = $(this).attr('rel');
         $('#confirm-boxs').modal({keyboard: true})
                .on('click', '#s_off_yes', function () {
					url = '<?php echo Router::url("/")."entities/decline_vote/" ?>'+vote_id;
					window.location.href= url;
        });*/
    });

	$(function(){

		$('.todolist_links').tooltip({
            template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
            ,
			'container': 'body',
			'placement': 'top',
        })

		 $('.todolist_links').on('mouseleave', function (e) {
            var $tooltip = $(this).data('bs.tooltip'),
                    $tip = $tooltip.$tip;

            $tip.hide()

        })
	})

</script>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="popup_modal" class="modal modal-success fade ">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
 </div>
 <div class="modal fade modal-danger" id="confirm-boxs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-radius-top">
            <div class="modal-header border-radius-top" id="modal_header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>Confirmation </div>

            <div class="modal-body" id="modal_body"></div>

            <div class="modal-footer" id="modal_footer">
                <a class="btn btn-success btn-sm" id="s_off_yes">
                    <span class="text">Yes</span>
                </a>
                <button type="button" id="s_off_no" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
            </div>


        </div>
    </div>
</div>
