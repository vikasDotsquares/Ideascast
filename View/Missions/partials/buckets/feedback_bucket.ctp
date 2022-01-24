<?php
$feedbacks = $data;
?>

<div class="idea-bucket-inner feedback_bucket" data-order="<?php echo $sort_order; ?>" data-slug="feedbacks">
	<div class="panel panel-default feedbacks" id="feedbacks">
		<div class="panel-heading"><i class="asset-all-icon feedbackwhite"></i> Feedback: <?php echo (isset($feedbacks) && !empty($feedbacks))? count($feedbacks) : 0 ; ?>
			<span data-original-title="Add Feedback" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'select_element', $workspace_id, 'feedbacks', 'admin' => FALSE), TRUE); ?>" data-toggle="modal" data-target="#modal_box" data-hash="feedbacks" class="btn btn-xs pull-right tipText add_asset <?php if(empty($el_count)){ ?>disabled<?php } ?>" style=""><i class="addwhite" style=""></i></span>
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
				  $feedStatus = 'ps-flag bg-undefined';
				  $feedTip = '';
				  if( (isset( $feedData['start_date'] ) && !empty( $feedData['start_date'] )) && date( 'Y-m-d', strtotime( $feedData['start_date'] ) ) > date( 'Y-m-d' ) ) {
					  $class_nameF = 'not_started';
					  $feedStatus = 'ps-flag bg-not_started';
					  $feedTip = 'Not Started';
				  }
				  else if( (isset( $feedData['end_date'] ) && !empty( $feedData['end_date'] )) && date( 'Y-m-d', strtotime( $feedData['end_date'] ) ) < date( 'Y-m-d' ) && $feedData['sign_off']  != 1 ) {
					  $class_nameF = 'overdue';
					  $feedStatus = 'ps-flag bg-overdue';
					  $feedTip = 'Overdue';
				  }
				  else if( isset( $feedData['sign_off'] ) &&   $feedData['sign_off']  == 1 ) {
					  $class_nameF = 'completed';
					  $feedStatus = 'ps-flag  bg-completed';
					  $feedTip = 'Completed';
				  }
				  else if( ((isset( $feedData['end_date'] ) && !empty( $feedData['end_date'] )) && (isset( $feedData['start_date'] ) && !empty( $feedData['start_date'] ))) && (date( 'Y-m-d', strtotime( $feedData['start_date'] ) ) <= date( 'Y-m-d' )) && date( 'Y-m-d', strtotime( $feedData['end_date'] ) ) >= date( 'Y-m-d' )  && $feedData['sign_off']  != 1) {
					  $class_nameF = 'progressing';
					  $feedStatus = 'ps-flag bg-progressing';
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
				  $status_class = '';
				  if(  $class_nameF == 'not_started' ) {
					  $daysLeft = daysLeft( date('Y-m-d'), $feedData['start_date']);
					  $remainingDays = 100;
					  $days_info = $daysLeft." day";
					  if($daysLeft ==1){
						  $days_info = $daysLeft." day";
					  }else  if($daysLeft > 1){
						  $days_info = $daysLeft." days";
					  }
					  
					  $day_text = "<span>Start </span><span>".$days_info."</span>";
					  $stlo= "top:23px;";
					  $status_class = 'not-start';
				  }
				  else if(  $class_nameF == 'progressing' ) {
					  $days_info = " Today";
					  if($daysLeft ==1){
						  $days_info = $daysLeft." day";
					  }else  if($daysLeft > 1){
						  $days_info = $daysLeft." days";
					  }
					  $day_text = "<span>Due </span><span>".$days_info."</span>";
					  $stlo= "top:23px;";
					  $status_class = 'progressing';
				  }
				  else if(  $class_nameF == 'completed' ) {
					  $remainingDays = 100;
					  $daysLeft = 0;
					  $day_text = "Completed";
					  $status_class = 'complete';
				  }
				  else if(  $class_nameF == 'overdue' ) {
					 
					  $daysLeft = daysLeft( $feedData['end_date'], date('Y-m-d'));
					   $days_info = $daysLeft." day";
					  if($daysLeft ==1){
						  $days_info = $daysLeft." day";
					  }else  if($daysLeft > 1){
						  $days_info = $daysLeft." days";
					  }
					  $day_text = "<span>Overdue </span><span>".$days_info." </span>";
					  $stlo= "top:23px;";
					  $status_class = 'overdue';
				  }
			  ?>
					<div class="bucket-data feedbacks_wrapper">
						<span class="ellipsis_text"><?php echo $feedData['title']; ?></span>
						<span class="options feedback_options">
							<?php if(isset($feedData['user_id']) && !empty($feedData['user_id'])) { ?>
								<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $feedData['user_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
							<?php } ?>


							<?php
								$creator_name = 'N/A';
								if(isset($feedData['user_id']) && !empty($feedData['user_id'])) {
									$userDetail = $this->ViewModel->get_user( $feedData['user_id'], null, 1 );
									if(isset($userDetail) && !empty($userDetail)) {
										$creator_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									}
								}
							?>

							<?php
								$updated_user_name = 'N/A';
								if(isset($feedData['updated_user_id']) && !empty($feedData['updated_user_id'])) {
									$userDetail = $this->ViewModel->get_user( $feedData['updated_user_id'], null, 1 );
									if(isset($userDetail) && !empty($userDetail)) {
										$updated_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									}
								}
							?>

							<a class="btn btn-xs btn-default trigger open_asset" href="#" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p>Created By: <?php echo $creator_name; ?></p> <p>Created On: <?php echo _displayDate($feedData['created']); ?></p> <p>Last Updated: <?php echo _displayDate($feedData['modified']); ?></p> <p>Updated By: <?php echo $updated_user_name; ?></p> </div>"  data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $feedData['element_id'], $feedData['id'], 'mission', 'admin' => FALSE ), TRUE ); ?>" data-hash="feedbacks"><i class="fa fa-folder-open"></i></a>



							<a class="btn btn-xs btn-default trigger tipText" title="<?php echo $feedTip; ?>" href="#"><i class="<?php echo $feedStatus; ?>"></i></a>
							<a class="btn btn-xs btn-default trigger tipText" title="Participants" href="#" data-toggle="modal" data-target="#modal_box" data-remote="<?php echo Router::Url(['controller' => 'missions', 'action' => 'shared_users', 'feedback', $feedData['id'], 'admin' => false], true) ?>"><i class="fa fa-user-plus"></i></a>
						</span>

						<small class="status-class <?php echo $status_class; ?>">
							<small style=""><?php echo $day_text; ?></small>
							<small style="background-color: #111111;"><span><?php echo ( isset($feedDetailData) && !empty($feedDetailData) ) ? count($feedDetailData) : 0; ?></span> <span> Responses</span></small>
						</small>
					</div>
			  <?php } ?>

		<?php }
		else {
		?>
		<div class="no-data">No Feedback</div>
		<?php
		  } ?>
	  </div>
	</div>
</div>

<script type="text/javascript" >
$(function(){

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