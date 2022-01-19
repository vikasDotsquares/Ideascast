<?php 
$user_id = $this->Session->read('Auth.User.id');
$user_setting = mission_settings($user_id); 

if(isset($data) && !empty($data)) {
		
	$workspace_id = $data['workspace_id'];
	
	if( ( !isset($data['area_id']) || empty($data['area_id']) ) && ( !isset($data['element_id']) || empty($data['element_id']) ) ) {
		$elements = workspace_elements($workspace_id);
		if(isset($elements) && !empty($elements)) {
			$elements = Set::extract($elements, '/Element/id');
		} 
	}
	if( isset($data['area_id']) && !empty($data['area_id']) ) {
		$elements = area_element($data['area_id']);
		
		if(isset($elements) && !empty($elements)) {
			$elements = Set::extract($elements, '/Element/id');
		} 
	}
	else if(isset($data['element_id']) && !empty($data['element_id'])) {
		$elements = $data['element_id'];
	}
	
	$people = (isset($data['people']) && !empty($data['people'])) ? $data['people'] : null;
	$elements = (isset($elements) && !empty($elements)) ? $elements : null;
	$links = $documents = $notes = $mindmaps = [];
	
	
	
		$assets = workspace_element_assets($workspace_id, $elements, $people);
		
		$links = Set::extract($assets, '/ElementLink');
		$documents = Set::extract($assets, '/ElementDocument');
		$notes = Set::extract($assets, '/ElementNote');
		$mindmaps = Set::extract($assets, '/ElementMindmap');
	 
	// Get workspace areas
	$area_ids = $decisions = $feedbacks = $votes = null;
	$decision_counts = $feedback_counts = $vote_counts = 0;
	
	if( !isset($data['area_id']) || empty($data['area_id']) ) {
		$areas = $this->ViewModel->workspace_areas($workspace_id, false, true);
		if( isset($areas) && !empty($areas) ) {
			if(is_array($area_ids))
				$area_ids = array_merge($area_ids, array_values($areas));
			else
				$area_ids = array_values($areas);
		}
	}
	else {
		$area_ids = $data['area_id'];
	}
	// Get all decisions of all areas
	if( isset($area_ids) && !empty($area_ids) ) {
		$decision_data = _element_decision_and_detail($area_ids, true);
		
		if( isset($decision_data) && !empty($decision_data) ) {
			foreach( $decision_data as $eid => $data ) {
				if( isset($data['decision']) && !empty($data['decision']) ) {
					$decision_counts++;
					$decisions[$eid]['decision'] = $data['decision'];
					if( isset($data['decision_detail']) && !empty($data['decision_detail']) ) {
						$decisions[$eid]['decision_detail'] = $data['decision_detail'];
					}
				}
			}
		}
		
		$feedback_data = _element_feedbacks($area_ids );
		
		if( isset($feedback_data) && !empty($feedback_data) ) {
			foreach( $feedback_data as $k => $data ) {
				if( isset($data['feedback']) && !empty($data['feedback']) ) {
					foreach( $data['feedback'] as $eid => $fdata ) {
						// pr($fdata);
						if( isset($fdata['Feedback']) && !empty($fdata['Feedback']) ) {
							$feedback_counts++;
							$feedbacks[$fdata['Feedback']['element_id']]['feedback'] = $fdata['Feedback'];
							if( isset($fdata['FeedbackResults']) && !empty($fdata['FeedbackResults']) ) {
								$feedbacks[$fdata['Feedback']['element_id']]['feedback_detail'] = $fdata['FeedbackResults'];
							}
						}
					}
				}
			}
		}
		
		$vote_data = _element_votes($area_ids);
		
		if( isset($vote_data) && !empty($vote_data) ) {
			foreach( $vote_data as $k => $data ) {
					
				if( isset($data['vote']) && !empty($data['vote']) ) {
					foreach( $data['vote'] as $eid => $vdata ) {
						if( isset($vdata['Vote']) && !empty($vdata['Vote']) ) {
							$vote_counts++;
							$votes[$vdata['Vote']['element_id']]['vote'] = $vdata['Vote'];
							if( isset($vdata['VoteResults']) && !empty($vdata['VoteResults']) ) {
								$votes[$vdata['Vote']['element_id']]['vote_detail'] = $vdata['VoteResults'];
							}
						}
					}
				}
			}
		} 
		// pr($votes);
	}
	$raw_decisions = _decisions(); 
	
  
?>

<div class="idea-bucket-inner link_bucket" data-order="<?php if( isset($user_setting['links']['sort_order']) && !empty($user_setting['links']['sort_order']) ) {  echo $user_setting['links']['sort_order']; } ?>" data-slug="links">
	<div class="panel panel-default links" id="links">
		<div class="panel-heading">
			<i class="fa fa-link"></i> Links: <?php echo count($links); ?>
			<span data-original-title="Add Link" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_element', 'admin' => FALSE), TRUE); ?>"  data-hash="links" class="btn bg-white btn-xs pull-right tipText not_selected add_asset" style=""><i class="fa fa-plus" style=""></i></span>
		</div>
		<div class="panel-body">
			<?php if(isset($links) && !empty($links)) { ?>
				<?php foreach($links as $key => $val) { 
					$linkData = $val['ElementLink'];
				?>
					<div class="bucket-data notes_wrapper">
						<span class="ellipsis_text"><?php echo $linkData['title']; ?></span>
						<span class="options notes_options">
						<?php if(isset($linkData['creater_id']) && !empty($linkData['creater_id'])) { ?>
							<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $linkData['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
						<?php } ?>
							<a class="btn btn-xs btn-default trigger" href="#"><i class="fa fa-folder-open"></i></a>
						</span>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>

<div class="idea-bucket-inner note_bucket" data-order="<?php if( isset($user_setting['notes']['sort_order']) && !empty($user_setting['notes']['sort_order']) ) {  echo $user_setting['notes']['sort_order']; } ?>" data-slug="notes">
	<div class="panel panel-default notes" id="notes">
		<div class="panel-heading">
			<i class="fa fa-file-text"></i> Notes: <?php echo count($notes); ?>
			<span data-original-title="Add Note" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_element', 'admin' => FALSE), TRUE); ?>"  data-hash="notes" class="btn bg-white btn-xs pull-right tipText not_selected add_asset" style=""><i class="fa fa-plus" style=""></i></span>
			
		</div>
		<div class="panel-body">
			<?php if(isset($notes) && !empty($notes)) { ?>
				<?php foreach($notes as $key => $val) { 
					$noteData = $val['ElementNote'];
				?>
					<div class="bucket-data notes_wrapper">
						<span class="ellipsis_text"><?php echo $noteData['title']; ?></span>
						<span class="options notes_options"> 
							<?php if(isset($noteData['creater_id']) && !empty($noteData['creater_id'])) { ?>
								<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $noteData['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
							<?php } ?>
							<a class="btn btn-xs btn-default trigger" href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $noteData['element_id'], $noteData['id'], 'admin' => FALSE ), TRUE ); ?>#notes"><i class="fa fa-folder-open"></i></a>
						</span>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>

<div class="idea-bucket-inner document_bucket" data-order="<?php if( isset($user_setting['documents']['sort_order']) && !empty($user_setting['documents']['sort_order']) ) {  echo $user_setting['documents']['sort_order']; } ?>" data-slug="documents">
	<div class="panel panel-default documents" id="documents">
	  <div class="panel-heading">
		<i class="fa fa-folder-o"></i> Documents: <?php echo count($documents); ?>
	  <span data-original-title="Add Document" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_element', 'admin' => FALSE), TRUE); ?>"  data-hash="documents" class="btn bg-white btn-xs pull-right tipText not_selected add_asset" style=""><i class="fa fa-plus" style=""></i></span>
		
	  </div>
	  <div class="panel-body">
			<?php if(isset($documents) && !empty($documents)) { ?>
				<?php foreach($documents as $key => $val) { 
					$docData = $val['ElementDocument'];
				?>
					<?php
						$upload_path = WWW_ROOT . ELEMENT_DOCUMENT_PATH . DS . $docData['element_id'] . DS;
						
						$upload_file = $upload_path . $docData['file_name'];
						
						$ftype = pathinfo($upload_file);
						if (isset($ftype) && !empty($ftype)) {
							//
							$dirname = ( isset($ftype['dirname']) && !empty($ftype['dirname'])) ? $ftype['dirname'] : '';
							$basename = ( isset($ftype['basename']) && !empty($ftype['basename'])) ? $ftype['basename'] : '';
							$filename = ( isset($ftype['filename']) && !empty($ftype['filename'])) ? $ftype['filename'] : '';
							$extension = ( isset($ftype['extension']) && !empty($ftype['extension'])) ? $ftype['extension'] : '';
							$basename1 = $basename;
							$base_name = explode('.', $basename);
							
							if( is_array($base_name)) {
								unset($base_name[count($base_name)-1]);
								$basename1 = implode('', $base_name);
							} 
						}
					?>
					
					<div class="bucket-data doc_wrapper">
						<span class="ellipsis_text"><?php echo $docData['title']; ?></span>
						<span class="options docs_options">
							<?php if(isset($docData['creater_id']) && !empty($docData['creater_id'])) { ?>
								<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $docData['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
							<?php } ?>
							<a class="btn btn-xs btn-default trigger" href="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'download_asset', $docData['id'], 'admin' => FALSE), TRUE); ?>"><i class="fa fa-folder-open"></i></a>
						</span>
					</div>
				<?php } ?>
			<?php } ?>
	  </div>
	</div>
</div>

<div class="idea-bucket-inner decision_bucket" data-order="<?php if( isset($user_setting['decisions']['sort_order']) && !empty($user_setting['decisions']['sort_order']) ) {  echo $user_setting['decisions']['sort_order']; } ?>" data-slug="decisions">
	<div class="panel panel-default decisions" id="decisions">
		<div class="panel-heading">
			<i class="fa fa-expand"></i> Decisions: <?php echo count($decisions); ?>
			<span data-original-title="Add Decision" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_element', 'admin' => FALSE), TRUE); ?>"  data-hash="decisions" class="btn bg-white btn-xs pull-right tipText not_selected add_asset" style=""><i class="fa fa-plus" style=""></i></span>
			
		</div>
	  <div class="panel-body">
		<?php if( isset($decisions) && !empty($decisions) ) { ?>
			
			<?php foreach( $decisions as $el => $data ) {
					$decision = $data['decision']['ElementDecision']; 
					
			?>
				<div class="bucket-data decisions_wrapper">
					<span class="decision_title ellipsis_text"><?php echo $decision['title']; ?></span>
					<span class="decision_options options">
						<?php if(isset($decision['creater_id']) && !empty($decision['creater_id'])) { ?>
							<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $decision['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
						<?php } ?>
						<a class="btn btn-xs btn-default trigger" href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $decision['element_id'], $decision['id'], 'admin' => FALSE ), TRUE ); ?>#decisions"><i class="fa fa-folder-open"></i></a>
						<a class="btn btn-xs btn-default trigger" href="#"><i class="fa fa-flag-o"></i></a> 
					</span>
					<?php if( isset($data['decision_detail']) && !empty($data['decision_detail']) ) { ?>
					<table class="table table-bordered tbl">
						<tbody>
							<tr>
								<?php
									if( isset($raw_decisions) && !empty($raw_decisions) ) {
										foreach( $raw_decisions as $k => $v ) {
											if( _decision_state($decision['id'], $v) ) {
											?>
											<td width="16.666%" style="background-color: #92d050; color: #fff">
												<i class="fa fa-check"></i>
											</td>
											<?php
											} else {
											?>
											<td width="16.666%" style="background-color: #4f81bd; color: #fff"><?php echo $k+1; ?></td>
											<?php
											}
										}
									}
								?>
							</tr>
						</tbody>
					</table>
					<?php } ?>
				</div>
			<?php } ?>
			
		<?php } ?>
	  </div>
	</div>
</div>

<div class="idea-bucket-inner feedback_bucket" data-order="<?php if( isset($user_setting['feedbacks']['sort_order']) && !empty($user_setting['feedbacks']['sort_order']) ) {  echo $user_setting['feedbacks']['sort_order']; } ?>" data-slug="feedbacks">
	<div class="panel panel-default feedbacks" id="feedbacks">
		<div class="panel-heading"><i class="fa fa-bullhorn"></i> Feedbacks: <?php echo count($feedbacks); ?>
			<span data-original-title="Add Feedback" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_element', 'admin' => FALSE), TRUE); ?>"  data-hash="feedbacks" class="btn bg-white btn-xs pull-right tipText not_selected add_asset" style=""><i class="fa fa-plus" style=""></i></span>
		</div>
	  <div class="panel-body">
		  <?php if( isset($feedbacks) && !empty($feedbacks) ) {
			  foreach( $feedbacks as $key => $data ) {  
				$feedData = $data['feedback'];
				$feedDetailData = ( isset($data['feedback_detail']) && !empty($data['feedback_detail']) ) ? $data['feedback_detail'] : [];
			  ?>
			  
			  <?php
				  //pr($post);
				  $class_nameF = 'undefined';
				  $feedStatus = 'fa-flag-o';
				  $feedTip = '';
				  if( (isset( $feedData['start_date'] ) && !empty( $feedData['start_date'] )) && date( 'Y-m-d', strtotime( $feedData['start_date'] ) ) > date( 'Y-m-d' ) ) {
					  $class_nameF = 'not_started';
					  $feedStatus = 'fa-flag';
					  $feedTip = 'Not Started';
				  }
				  else if( (isset( $feedData['end_date'] ) && !empty( $feedData['end_date'] )) && date( 'Y-m-d', strtotime( $feedData['end_date'] ) ) < date( 'Y-m-d' ) && $feedData['sign_off']  != 1 ) { 
					  $class_nameF = 'overdue';
					  $feedStatus = 'fa-flag text-red';
					  $feedTip = 'Overdue';
				  }
				  else if( isset( $feedData['sign_off'] ) &&   $feedData['sign_off']  == 1 ) {
					  $class_nameF = 'completed';
					  $feedStatus = 'fa-flag text-green';
					  $feedTip = 'Completed';
				  }
				  else if( ((isset( $feedData['end_date'] ) && !empty( $feedData['end_date'] )) && (isset( $feedData['start_date'] ) && !empty( $feedData['start_date'] ))) && (date( 'Y-m-d', strtotime( $feedData['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $feedData['end_date'] ) ) >= date( 'Y-m-d' )  && $feedData['sign_off']  != 1) {
					  $class_nameF = 'progressing';
					  $feedStatus = 'fa-flag-o';
					  $feedTip = 'In Progress';
				  }
				  else {
					  $class_nameF = 'undefined';
				  }
				  
				  
				  $staDate = date('Y-m-d'); 
				  $daysLeft = daysLeft($staDate, $feedData['end_date']);
				  $remainingDays = 100 - $daysLeft;
				  $day_text = "N/A";
				  $stlo= "top:31px;";
				  if(  $class_nameF == 'not_started' ) {
					  $daysLeft = daysLeft( date('Y-m-d'), $feedData['start_date']);
					  $remainingDays = 100;
					  $day_text = "Start in ".$daysLeft." days";
					  $stlo= "top:23px;";
				  }
				  else if(  $class_nameF == 'progressing' ) { 
					  $day_text = "Due ".$daysLeft." days";	
					  $stlo= "top:23px;";										
				  }
				  else if(  $class_nameF == 'completed' ) {
					  $remainingDays = 100;
					  $daysLeft = 0;
					  $day_text = "ENDED";											
				  }
				  else if(  $class_nameF == 'overdue' ) {
					  $daysLeft = daysLeft( $feedData['end_date'], date('Y-m-d'));										 
					  $day_text = "Overdue ".$daysLeft." days";
					  $stlo= "top:23px;";
				  }
			  ?> 
					<div class="bucket-data feedbacks_wrapper">
						<span class="ellipsis_text"><?php echo $feedData['title']; ?></span>
						<span class="options feedback_options">
							<?php if(isset($feedData['user_id']) && !empty($feedData['user_id'])) { ?>
								<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $feedData['user_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
							<?php } ?>
							<a class="btn btn-xs btn-default trigger" href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $feedData['element_id'], $feedData['id'], 'admin' => FALSE ), TRUE ); ?>#feedbacks"><i class="fa fa-folder-open"></i></a>
							<a class="btn btn-xs btn-default trigger tipText" title="<?php echo $feedTip; ?>" href="#"><i class="fa <?php echo $feedStatus; ?>"></i></a>
							<a class="btn btn-xs btn-default trigger tipText" title="Participants" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'missions', 'action' => 'shared_users', 'feedback', $feedData['id'], 'admin' => false], true) ?>"><i class="fa fa-user-plus"></i></a>
						</span>
						
						<small >
							<small style="background-color: #e46c0a;"><?php echo $day_text; ?></small>
							<small style="background-color: #111111;"><?php echo count($feedDetailData) ?> Responses</small>
						</small>  
					</div>
			  <?php }
		  } ?>
	  </div>
	</div>
</div>

<div class="idea-bucket-inner vote_bucket" data-order="<?php if( isset($user_setting['votes']['sort_order']) && !empty($user_setting['votes']['sort_order']) ) {  echo $user_setting['votes']['sort_order']; } ?>" data-slug="votes">
	<div class="panel panel-default votes" id="votes">
		<div class="panel-heading"><i class="fa fa-inbox"></i> Votes: <?php echo count($votes); ?>
			<span data-original-title="Add Vote" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_element', 'admin' => FALSE), TRUE); ?>"  data-hash="votes" class="btn bg-white btn-xs pull-right tipText not_selected add_asset" style=""><i class="fa fa-plus" style=""></i></span>
		</div>
	  <div class="panel-body">
		  <?php if( isset($votes) && !empty($votes) ) {
			  foreach( $votes as $key => $data ) {  
				  $voteData = $data['vote'];
				  $voteDetailData = ( isset($data['feedback_detail']) && !empty($data['feedback_detail']) ) ? $data['feedback_detail'] : [];
			  ?>
			  
			  <?php
				  $class_name_vote = 'undefined';
				  $voteStatus = 'fa-flag-o';
				  $voteTip = '';
				  if( (isset( $voteData['start_date'] ) && !empty( $voteData['start_date'] )) && date( 'Y-m-d', strtotime( $voteData['start_date'] ) ) > date( 'Y-m-d' ) ) {
					  $class_name_vote = 'not_started';
					  $voteStatus = 'fa-flag';
					  $voteTip = 'Not Started';
					}
				  else if( (isset( $voteData['end_date'] ) && !empty( $voteData['end_date'] )) && date( 'Y-m-d', strtotime( $voteData['end_date'] ) ) < date( 'Y-m-d' ) && $voteData['is_completed']  != 1 ) { 
					  $class_name_vote = 'overdue';
					  $voteStatus = 'fa-flag text-red';
					  $voteTip = 'Overdue';
				  }
				  else if( isset( $voteData['is_completed'] ) &&   $voteData['is_completed']  == 1 ) {
					  $class_name_vote = 'completed';
					  $voteStatus = 'fa-flag text-green';
					  $voteTip = 'Completed';
				  }
				  else if( ((isset( $voteData['end_date'] ) && !empty( $voteData['end_date'] )) && (isset( $voteData['start_date'] ) && !empty( $voteData['start_date'] ))) && (date( 'Y-m-d', strtotime( $voteData['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $voteData['end_date'] ) ) >= date( 'Y-m-d' )  && $voteData['is_completed']  != 1) {
					  $class_name_vote = 'progressing';
					  $voteStatus = 'fa-flag-o';
					  $voteTip = 'In Progress';
				  }
				  else {
					  $class_name_vote = 'undefined';
				  }
				  
				  $staDate = date('Y-m-d'); 
				  $daysLeft = daysLeft($staDate, $voteData['end_date']);
				  $remainingDays = 100 - $daysLeft;
				  $day_text = "N/A";
				  $stlo= "top:31px;";
				  if(  $class_name_vote == 'not_started' ) {
					  $daysLeft = daysLeft( date('Y-m-d'), $voteData['start_date']);
					  $remainingDays = 100;
					  $day_text = "Start in ".$daysLeft." days";
					  $stlo= "top:23px;";
				  }
				  else if(  $class_name_vote == 'progressing' ) { 
					  $day_text = "Due ".$daysLeft." days";	
					  $stlo= "top:23px;";										
				  }
				  else if(  $class_name_vote == 'completed' ) {
					  $remainingDays = 100;
					  $daysLeft = 0;
					  $day_text = "ENDED";											
				  }
				  else if(  $class_name_vote == 'overdue' ) {
					  $daysLeft = daysLeft( $voteData['end_date'], date('Y-m-d'));										 
					  $day_text = "Overdue ".$daysLeft." days";
					  $stlo= "top:23px;";
				  }
			  ?> 
			  <div class="bucket-data votes_wrapper">
				  <span class="ellipsis_text"><?php echo $voteData['title']; ?></span>
				  <span class="options votes_options">
					  <?php if(isset($voteData['user_id']) && !empty($voteData['user_id'])) { ?>
						  <a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $voteData['user_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
					  <?php } ?>
					  <a class="btn btn-xs btn-default trigger" href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $voteData['element_id'], $voteData['id'], 'admin' => FALSE ), TRUE ); ?>#votes"><i class="fa fa-folder-open"></i></a>
					  <a class="btn btn-xs btn-default trigger tipText" title="<?php echo $voteTip; ?>" href="#"><i class="fa <?php echo $voteStatus; ?>"></i></a>
					  <a class="btn btn-xs btn-default trigger tipText" title="Participants" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'missions', 'action' => 'shared_users', 'vote', $voteData['id'], 'admin' => false], true) ?>"><i class="fa fa-user-plus"></i></a>
				  </span>
				  
					<small >
						<small style="background-color: #92d050; "><?php echo $day_text; ?></small>
						<small style="background-color: #111111;"><?php echo count($voteDetailData) ?> Responses</small>
					</small> 
			  </div>
			  <?php }
		  } ?> 
	  </div>
	</div>
</div>

<div class="idea-bucket-inner mindmap_bucket" data-order="<?php if( isset($user_setting['mindmaps']['sort_order']) && !empty($user_setting['mindmaps']['sort_order']) ) {  echo $user_setting['mindmaps']['sort_order']; } ?>" data-slug="mindmaps">
	<div class="panel panel-default mindmaps" id="mindmaps">
		<div class="panel-heading"><i class="fa fa-sitemap"></i> Mind Maps: <?php echo count($mindmaps); ?>
			<span data-original-title="Add Mind map" data-remote="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'update_element', 'admin' => FALSE), TRUE); ?>"  data-hash="mind_maps" class="btn bg-white btn-xs pull-right tipText not_selected add_asset" style=""><i class="fa fa-plus" style=""></i></span>
		</div>
		<div class="panel-body">
			<?php if(isset($mindmaps) && !empty($mindmaps)) { ?>
				<?php foreach($mindmaps as $key => $val) { 
					$mmData = $val['ElementMindmap']; 
					?>
					<div class="bucket-data notes_wrapper">
						<span class="ellipsis_text"><?php echo $mmData['title']; ?></span>
						<span class="options notes_options">
							<?php if(isset($mmData['creater_id']) && !empty($mmData['creater_id'])) { ?>
								<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $mmData['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
							<?php } ?>
							<a class="btn btn-xs btn-default trigger" href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $mmData['element_id'], $mmData['id'], 'admin' => FALSE ), TRUE ); ?>"><i class="fa fa-folder-open"></i></a>
						</span>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>

<?php 
}
else 
{
		?>

<div class="idea-bucket-inner link_bucket" data-order="<?php if( isset($user_setting['links']['sort_order']) && !empty($user_setting['links']['sort_order']) ) {  echo $user_setting['links']['sort_order']; } ?>" data-slug="links">
	<div class="panel panel-default links" id="links">
		<div class="panel-heading">
			<i class="fa fa-link"></i> Links (0)
		</div>
		<div class="panel-body">links</div>
	</div>
</div>

<div class="idea-bucket-inner note_bucket" data-order="<?php if( isset($user_setting['notes']['sort_order']) && !empty($user_setting['notes']['sort_order']) ) {  echo $user_setting['notes']['sort_order']; } ?>" data-slug="notes">
	<div class="panel panel-default notes" id="notes">
		<div class="panel-heading"><i class="fa fa-file-text"></i> Notes (0)</div>
		<div class="panel-body">notes</div>
	</div>
</div>

<div class="idea-bucket-inner document_bucket" data-order="<?php if( isset($user_setting['documents']['sort_order']) && !empty($user_setting['documents']['sort_order']) ) {  echo $user_setting['documents']['sort_order']; } ?>" data-slug="documents">
	<div class="panel panel-default documents" id="documents">
	  <div class="panel-heading"><i class="fa fa-folder-o"></i> Documents (0) </div>
	  <div class="panel-body">documents</div>
	</div>
</div>

<div class="idea-bucket-inner decision_bucket" data-order="<?php if( isset($user_setting['decisions']['sort_order']) && !empty($user_setting['decisions']['sort_order']) ) {  echo $user_setting['decisions']['sort_order']; } ?>" data-slug="decisions">
	<div class="panel panel-default decisions" id="decisions">
	  <div class="panel-heading"><i class="fa fa-expand"></i> Decisions (0) </div>
	  <div class="panel-body">decisions </div>
	</div>
</div>

<div class="idea-bucket-inner feedback_bucket" data-order="<?php if( isset($user_setting['feedbacks']['sort_order']) && !empty($user_setting['feedbacks']['sort_order']) ) {  echo $user_setting['feedbacks']['sort_order']; } ?>" data-slug="feedbacks">
	<div class="panel panel-default feedbacks" id="feedbacks">
		<div class="panel-heading"><i class="fa fa-bullhorn"></i> Feedbacks (0) </div>
		<div class="panel-body"> feedbacks</div>
	</div>
</div>

<div class="idea-bucket-inner vote_bucket" data-order="<?php if( isset($user_setting['votes']['sort_order']) && !empty($user_setting['votes']['sort_order']) ) {  echo $user_setting['votes']['sort_order']; } ?>" data-slug="votes">
	<div class="panel panel-default votes" id="votes">
		<div class="panel-heading"><i class="fa fa-inbox"></i> Votes (0) </div>
		<div class="panel-body">votes</div>
	</div>
</div>

<div class="idea-bucket-inner mindmap_bucket" data-order="<?php if( isset($user_setting['mindmaps']['sort_order']) && !empty($user_setting['mindmaps']['sort_order']) ) {  echo $user_setting['mindmaps']['sort_order']; } ?>" data-slug="mindmaps">
	<div class="panel panel-default mindmaps" id="mindmaps">
		<div class="panel-heading"><i class="fa fa-sitemap"></i> Mind Maps (0) </div>
		<div class="panel-body">mindmaps</div>
	</div>
</div>

<?php } ?>
<script type="text/javascript" >
$(function(){
	/* $.bucket_sorting();
	
	setTimeout(function(){
		var numericallyOrderedDivs = $('.idea-bucket-inner').sort(function (a, b) {
			return $(a).data('order') > $(b).data('order');
		});
		$("#wsp_buckets").html(numericallyOrderedDivs);
	}, 100) */
	// $.animateSort("#wsp_buckets", ".idea-bucket-inner", "data-order");
	
	// $("#ajax_overlay").show()
	setTimeout(function(){
		$.bucket_sorting();
		// $( ".idea-bucket-inner" ).removeAttr('style');
		// $("#ajax_overlay").hide()
		
		var numericallyOrderedDivs = $('.idea-bucket-inner').sort(function (a, b) {
			return $(a).data('order') > $(b).data('order');
		});
		$("#wsp_buckets").html(numericallyOrderedDivs);
	}, 1200)
	
	var $active_element = $('.el').filter(function () {
		return $(this).data("highlight") == true;
	});
	
	if( $active_element.length > 0 ) {
		$('.add_asset').removeClass('not_selected')
	}
	
})
	 
</script>
