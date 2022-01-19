<?php
$mindmaps = $data;
?>
<div class="idea-bucket-inner mindmap_bucket" data-order="<?php echo $sort_order; ?>" data-slug="mindmaps">
	<div class="panel panel-default mindmaps" id="mindmaps">
		<div class="panel-heading"><i class="asset-all-icon mindmapwhite"></i> Mind Maps: <?php echo ( isset($mindmaps) && !empty($mindmaps) ) ? count($mindmaps) : 0; ?>
			<span data-original-title="Add Mind map" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'select_element', $workspace_id, 'mind_maps', 'admin' => FALSE), TRUE); ?>" data-toggle="modal" data-target="#modal_box" data-hash="mind_maps" class="btn btn-xs pull-right tipText add_asset <?php if(empty($el_count)){ ?>disabled<?php } ?>" style=""><i class="addwhite" style=""></i></span>
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
								<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $mmData['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
							<?php } ?>


							<?php
								$creator_name = 'N/A';
								if(isset($mmData['creater_id']) && !empty($mmData['creater_id'])) {
									$userDetail = $this->ViewModel->get_user( $mmData['creater_id'], null, 1 );
									if(isset($userDetail) && !empty($userDetail)) {
										$creator_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									}
								}
							?>

							<?php
								$updated_user_name = 'N/A';
								if(isset($mmData['updated_user_id']) && !empty($mmData['updated_user_id'])) {
									$userDetail = $this->ViewModel->get_user( $mmData['updated_user_id'], null, 1 );
									if(isset($userDetail) && !empty($userDetail)) {
										$updated_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									}
								}
							?>


							<a class="btn btn-xs btn-default trigger open_asset" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p>Created By: <?php echo $creator_name; ?></p> <p>Created On: <?php echo _displayDate($mmData['created']); ?></p> <p>Last Updated: <?php echo _displayDate($mmData['modified']); ?></p> <p>Updated By: <?php echo $updated_user_name; ?></p> </div>"  href="#" data-hash="mind_maps" data-remote="<?php echo Router::Url( array( 'controller' => 'entities', 'action' => 'update_element', $mmData['element_id'], $mmData['id'], 'mission', 'admin' => FALSE ), TRUE ); ?>"><i class="fa fa-folder-open"></i></a>

						</span>
					</div>
				<?php } ?>
			<?php }
			else {
			?>
			<div class="no-data">No Mind Maps</div>
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