<?php
$votes = $data;
?>

<div class="idea-bucket-inner vote_bucket" data-order="<?php echo $sort_order; ?>" data-slug="votes">
	<div class="panel panel-default votes" id="votes">
		<div class="panel-heading"><i class="asset-all-icon votewhite"></i> Votes: <?php echo ( isset($votes) && !empty($votes) )? count($votes) : 0; ?>
			<span data-original-title="Add Vote" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'select_element', $workspace_id, 'votes', 'admin' => FALSE), TRUE); ?>" data-toggle="modal" data-target="#modal_box" data-hash="votes" class="btn btn-xs pull-right tipText add_asset <?php if(empty($el_count)){ ?>disabled<?php } ?>" style=""><i class="addwhite" style=""></i></span>
		</div>
	  <div class="panel-body">
		  <?php if( isset($votes) && !empty($votes) ) {
			  foreach( $votes as $key => $data ) {
				  $voteData = $data['vote'];
				  $voteDetailData = ( isset($data['vote_detail']) && !empty($data['vote_detail']) ) ? $data['vote_detail'] : [];
			  ?>

			  <?php
				  $class_name_vote = 'undefined';
				  $voteStatus = 'ps-flag bg-undefined';
				  $voteTip = '';
				  if( (isset( $voteData['start_date'] ) && !empty( $voteData['start_date'] )) && date( 'Y-m-d', strtotime( $voteData['start_date'] ) ) > date( 'Y-m-d' ) ) {
					  $class_name_vote = 'not_started';
					  $voteStatus = 'ps-flag bg-not_started';
					  $voteTip = 'Not Started';
					}
				  else if( (isset( $voteData['end_date'] ) && !empty( $voteData['end_date'] )) && date( 'Y-m-d', strtotime( $voteData['end_date'] ) ) < date( 'Y-m-d' ) && $voteData['is_completed']  != 1 ) {
					  $class_name_vote = 'overdue';
					  $voteStatus = 'ps-flag bg-overdue';
					  $voteTip = 'Overdue';
				  }
				  else if( isset( $voteData['is_completed'] ) &&   $voteData['is_completed']  == 1 ) {
					  $class_name_vote = 'completed';
					  $voteStatus = 'ps-flag  bg-completed';
					  $voteTip = 'Completed';
				  }
				  else if( ((isset( $voteData['end_date'] ) && !empty( $voteData['end_date'] )) && (isset( $voteData['start_date'] ) && !empty( $voteData['start_date'] ))) && (date( 'Y-m-d', strtotime( $voteData['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $voteData['end_date'] ) ) >= date( 'Y-m-d' )  && $voteData['is_completed']  != 1) {
					  $class_name_vote = 'progressing';
					  $voteStatus = 'ps-flag bg-progressing';
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
				  $status_class = 'not-start';
				  if(  $class_name_vote == 'not_started' ) {
					  $daysLeft = daysLeft( date('Y-m-d'), $voteData['start_date']);
					  $days_info = $daysLeft." day";
					  if($daysLeft ==1){
						  $days_info = $daysLeft." day";
					  }else  if($daysLeft > 1){
						  $days_info = $daysLeft." days";
					  }
					  $remainingDays = 100;
					  $day_text = "<span>Start </span><span>".$days_info." <span>";
					  $stlo= "top:23px;";
					  $status_class = 'not-start';
				  }
				  else if(  $class_name_vote == 'progressing' ) {
					  //$days_info = $daysLeft." days";
					  $days_info = " Today";
					  if($daysLeft ==1){
						  $days_info = $daysLeft." day";
					  }else  if($daysLeft > 1){
						  $days_info = $daysLeft." days";
					  }
					  $day_text = "<span>Due </span><span>".$days_info." </span>";
					  $stlo= "top:23px;";
					  $status_class = 'progressing';
				  }
				  else if(  $class_name_vote == 'completed' ) {
					  $remainingDays = 100;
					  $daysLeft = 0;
					  $day_text = "Completed";
					  $status_class = 'complete';
				  }
				  else if(  $class_name_vote == 'overdue' ) {
					  $daysLeft = daysLeft( $voteData['end_date'], date('Y-m-d'));
					  $days_info = $daysLeft." day";
					  if($daysLeft ==1){
						  $days_info = $daysLeft." day";
					  }else  if($daysLeft > 1){
						  $days_info = $daysLeft." days";
					  }
					  $day_text = "<span>Overdue</span><span> ".$days_info." </span>";
					  $stlo= "top:23px;";
					  $status_class = 'overdue';
				  }
			  ?>
			  <div class="bucket-data votes_wrapper">
				  <span class="ellipsis_text"><?php echo $voteData['title']; ?></span>
				  <span class="options votes_options">
					  <?php if(isset($voteData['user_id']) && !empty($voteData['user_id'])) { ?>
						  <a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $voteData['user_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
					  <?php } ?>


					  <?php
						  $creator_name = 'N/A';
						  if(isset($voteData['user_id']) && !empty($voteData['user_id'])) {
							  $userDetail = $this->ViewModel->get_user( $voteData['user_id'], null, 1 );
							  if(isset($userDetail) && !empty($userDetail)) {
								  $creator_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
							  }
						  }
					  ?>

					  <?php
						  $updated_user_name = 'N/A';
						  if(isset($voteData['updated_user_id']) && !empty($voteData['updated_user_id'])) {
							  $userDetail = $this->ViewModel->get_user( $voteData['updated_user_id'], null, 1 );
							  if(isset($userDetail) && !empty($userDetail)) {
								  $updated_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
							  }
						  }


						  $stdate = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A', $voteData['created']),$format = 'd M Y g:i A');
						  $mddate = $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A', $voteData['modified']),$format = 'd M Y g:i A');
					  ?>


					  <a class="btn btn-xs btn-default trigger open_asset" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p>Created By: <?php echo $creator_name; ?></p> <p>Created On: <?php echo $stdate; ?></p> <p>Last Updated: <?php echo $mddate; ?></p> <p>Updated By: <?php echo $updated_user_name; ?></p> </div>"  href="#" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $voteData['element_id'], $voteData['id'], 'mission', 'admin' => FALSE ), TRUE ); ?>" data-hash="votes"><i class="fa fa-folder-open"></i></a>
					  <a class="btn btn-xs btn-default trigger tipText" title="<?php echo $voteTip; ?>" href="#"><i class="<?php echo $voteStatus; ?>"></i></a>
					  <a class="btn btn-xs btn-default trigger tipText" title="Participants" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'missions', 'action' => 'shared_users', 'vote', $voteData['id'], 'admin' => false], true) ?>"><i class="fa fa-user-plus"></i></a>
				  </span>

					<small  class="status-class <?php echo $status_class; ?>">
						<small ><?php echo $day_text; ?></small>
						<small style="background-color: #111111;"><span><?php echo vote_responses($voteData['id']) ?></span> <span>Responses</span></small>
					</small>
			  </div>
			  <?php } ?>
		  <?php }
		  else {
		  ?>
		  <div class="no-data">No Votes</div>
		  <?php
		  } ?>
	  </div>
	</div>
</div>

<script type="text/javascript" >
$(function(){
	$('.popover').remove()
	$('[data-toggle="popover"]').popover({
        placement : 'bottom',
		container: 'body',
        trigger : 'hover'
    });

})
</script>


<style type="text/css">
	.status-class.not-start small:first-child {
		background-color: #827847;
	}
	.status-class.progressing small:first-child {
		background-color: #ff7701;
	}
	.status-class.complete small:first-child {
		background-color: #92d050;
	}
	.status-class.overdue small:first-child {
		background-color: #f55454;
	}
	.ps-flag{ margin-top:0; margin-right: 0;}
</style>