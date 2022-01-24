<?php
$notes = $data;
?>
<div class="idea-bucket-inner note_bucket" data-order="<?php echo $sort_order; ?>" data-slug="notes">
	<div class="panel panel-default notes" id="notes">
		<div class="panel-heading">
			<i class="asset-all-icon notewhite"></i> Notes: <?php echo ( isset($notes) && !empty($notes) ) ? count($notes) : 0; ?>
			<span data-original-title="Add Note" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'select_element', $workspace_id, 'notes', 'admin' => FALSE), TRUE); ?>" data-toggle="modal" data-target="#modal_box" data-hash="notes" class="btn btn-xs pull-right tipText add_asset <?php if(empty($el_count)){ ?>disabled<?php } ?>" style=""><i class="addwhite" style=""></i></span>

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
								<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $noteData['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
							<?php } ?>
							<!-- <a class="btn btn-xs btn-default trigger" href="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $noteData['element_id'], $noteData['id'], 'mission_room', 'admin' => FALSE ), TRUE ); ?>#notes"><i class="fa fa-folder-open"></i></a> -->


							<?php
								$creator_name = 'N/A';
								if(isset($noteData['creater_id']) && !empty($noteData['creater_id'])) {
									$userDetail = $this->ViewModel->get_user( $noteData['creater_id'], null, 1 );
									if(isset($userDetail) && !empty($userDetail)) {
										$creator_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									}
								}
							?>

							<?php
								$updated_user_name = 'N/A';
								if(isset($noteData['updated_user_id']) && !empty($noteData['updated_user_id'])) {
									$userDetail = $this->ViewModel->get_user( $noteData['updated_user_id'], null, 1 );
									if(isset($userDetail) && !empty($userDetail)) {
										$updated_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									}
								}
							?>


							<a class="btn btn-xs btn-default trigger open_asset" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p>Created By: <?php echo $creator_name; ?></p> <p>Created On: <?php echo _displayDate($noteData['created']); ?></p> <p>Last Updated: <?php echo _displayDate($noteData['modified']); ?></p> <p>Updated By: <?php echo $updated_user_name; ?></p> </div>"  href="#" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $noteData['element_id'], $noteData['id'], 'mission', 'admin' => FALSE ), TRUE ); ?>" data-hash="notes"><i class="fa fa-folder-open"></i></a>
						</span>
					</div>
				<?php } ?>
			<?php }
			else {
			?>
			<div class="no-data">No Notes</div>
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