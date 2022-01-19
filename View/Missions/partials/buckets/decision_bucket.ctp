<?php
$raw_decisions = _decisions();
$decisions = $data;
$decisions = (isset($decisions) && !empty($decisions)) ? $decisions : array();
?>

<div class="idea-bucket-inner decision_bucket" data-order="<?php echo $sort_order; ?>" data-slug="decisions">
	<div class="panel panel-default decisions" id="decisions">
		<div class="panel-heading">
			<i class="asset-all-icon decisionwhite"></i> Decisions: <?php echo ( isset($decisions) && !empty($decisions) ) ? count($decisions) : 0; ?>
			<span data-original-title="Add Decision" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'select_element', $workspace_id, 'decisions', 'admin' => FALSE), TRUE); ?>" data-toggle="modal" data-target="#modal_box" data-hash="decisions" class="btn btn-xs pull-right tipText add_asset <?php if(empty($el_count)){ ?>disabled<?php } ?>" style=""><i class="addwhite" style=""></i></span>

		</div>
	  <div class="panel-body">
		<?php if( isset($decisions) && !empty($decisions) ) { ?>

			<?php foreach( $decisions as $el => $data ) {
					// $decision = $data['decision']['ElementDecision'];
					$decision = $data['ElementDecision'];

				// $element_decisions = _element_decisions($data['decision']['ElementDecision']['element_id'] , 'decision');
				$element_decisions = _element_decisions($data['ElementDecision']['element_id'] , 'decision');


				  $class_nameD =   $element_decisions['decision_short_term'];
				  $iconD = 'fa fa-flag-o';
				  $tipD = $element_decisions['decision_tiptext'];


				  if( (isset( $class_nameD ) && !empty( $class_nameD )) && ($class_nameD == 'NON')   ) {
					 // $class_nameD = 'not_started';
					  $iconD = 'ps-flag bg-undefined';

				  }
				  else if( (isset( $class_nameD ) && !empty( $class_nameD )) && ($class_nameD == 'PRG')   ) {
					//  $class_nameD = 'overdue';
					  //$iconD = 'fa-flag-o';
					  $iconD = 'ps-flag bg-progressing';

				  }
				  else if( (isset( $class_nameD ) && !empty( $class_nameD )) && ($class_nameD == 'CMP')   ) {
					 // $class_nameD = 'completed';
					  $iconD = 'ps-flag  bg-completed';

				  }
			?>
				<div class="bucket-data decisions_wrapper">
					<span class="decision_title ellipsis_text"><?php echo $decision['title']; ?></span>
					<span class="decision_options options">
						<?php if(isset($decision['creater_id']) && !empty($decision['creater_id'])) { ?>
							<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $decision['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
						<?php } ?>


						<?php
							$creator_name = 'N/A';
							if(isset($decision['creater_id']) && !empty($decision['creater_id'])) {
								$userDetail = $this->ViewModel->get_user( $decision['creater_id'], null, 1 );
								if(isset($userDetail) && !empty($userDetail)) {
									$creator_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
								}
							}
						?>

						<?php
							$updated_user_name = 'N/A';
							if(isset($decision['updated_user_id']) && !empty($decision['updated_user_id'])) {
								$userDetail = $this->ViewModel->get_user( $decision['updated_user_id'], null, 1 );
								if(isset($userDetail) && !empty($userDetail)) {
									$updated_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
								}
							}
						?>


						<a class="btn btn-xs btn-default trigger" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p>Created By: <?php echo $creator_name; ?></p> <p>Created On: <?php echo _displayDate($decision['created']); ?></p> <p>Last Updated: <?php echo _displayDate($decision['modified']); ?></p> <p>Updated By: <?php echo $updated_user_name; ?></p> </div>"  href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $decision['element_id'], $decision['id'], 'admin' => FALSE ), TRUE ); ?>#decisions"><i class="fa fa-folder-open"></i></a>

						<a title="<?php echo $tipD; ?>" class="btn btn-xs btn-default trigger tipText"  href="#"><i class="<?php echo $iconD;?>"></i></a>
					</span>
					<?php //if( isset($data['decision_detail']) && !empty($data['decision_detail']) ) { ?>
					<?php //if( isset($data['ElementDecisionDetail']) && !empty($data['ElementDecisionDetail']) ) { ?>
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
					<?php //} ?>
				</div>
			<?php } ?>

			<?php }
			else {
			?>
			<div class="no-data">No Decisions</div>
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
 
	.ps-flag{ margin-top:0; margin-right: 0;}
</style>