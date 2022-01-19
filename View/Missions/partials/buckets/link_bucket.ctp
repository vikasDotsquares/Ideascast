<?php
$links = $data;
?>
<div class="idea-bucket-inner link_bucket" data-order="<?php echo $sort_order; ?>" data-slug="links">
	<div class="panel panel-default links" id="links">
		<div class="panel-heading">
			<i class="asset-all-icon linkwhite"></i> Links: <?php echo ( isset($links) && !empty($links) ) ? count($links) : 0; ?>
			<span data-original-title="Add Link" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'select_element', $workspace_id, 'links', 'admin' => FALSE), TRUE); ?>" data-toggle="modal" data-target="#modal_box" data-hash="links" class="btn btn-xs pull-right tipText add_asset <?php if(empty($el_count)){ ?>disabled<?php } ?>" style=""><i class="addwhite" style=""></i></span>
		</div>
		<div class="panel-body" style="position: relative">

			<?php if(isset($links) && !empty($links)) { ?>
				<?php foreach($links as $key => $val) {
					$linkData = $val['ElementLink'];
					// pr($linkData);
				?>
					<div class="bucket-data notes_wrapper">
						<span class="ellipsis_text"><?php echo $linkData['title']; ?></span>
						<span class="options notes_options">
						<?php if(isset($linkData['creater_id']) && !empty($linkData['creater_id'])) { ?>
							<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $linkData['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
						<?php } ?>


							<?php
								$creator_name = 'N/A';
								if(isset($linkData['creater_id']) && !empty($linkData['creater_id'])) {
									$userDetail = $this->ViewModel->get_user( $linkData['creater_id'], null, 1 );
									if(isset($userDetail) && !empty($userDetail)) {
										$creator_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									}
								}
							?>

							<?php
								$updated_user_name = 'N/A';
								if(isset($linkData['updated_user_id']) && !empty($linkData['updated_user_id'])) {
									$userDetail = $this->ViewModel->get_user( $linkData['updated_user_id'], null, 1 );
									if(isset($userDetail) && !empty($userDetail)) {
										$updated_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
									}
								}
							?>


							<a class="btn btn-xs btn-default trigger open_link" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p>Created By: <?php echo $creator_name; ?></p> <p>Created On: <?php echo _displayDate($linkData['created']); ?></p> <p>Last Updated: <?php echo _displayDate($linkData['modified']); ?></p> <p>Updated By: <?php echo $updated_user_name; ?></p> </div>"  data-id="<?php echo $linkData['id']; ?>" data-type="<?php echo $linkData['link_type']; ?>" data-link="<?php echo ($linkData['link_type'] == 1) ? $linkData['references'] : ''; ?>" href="#"><i class="fa fa-folder-open"></i></a>
						</span>
					</div>
				<?php } ?>
			<?php }
			else {
			?>
			<div class="no-data">No Links</div>
			<?php
			} ?>
		</div>
	</div>
</div>

<script type="text/javascript" >
$(function(){

	$('[data-toggle="popover"]').popover({
        placement : 'bottom',
        trigger : 'hover',
		container: 'body',
    });

})
</script>