<?php
$documents = $data;
?>
<div class="idea-bucket-inner document_bucket" data-order="<?php echo $sort_order; ?>" data-slug="documents">
	<div class="panel panel-default documents" id="documents">
	  <div class="panel-heading">
		<i class="asset-all-icon documentwhite"></i> Documents: <?php echo ( isset($documents) && !empty($documents) ) ? count($documents) : 0; ?>
	  <span data-original-title="Add Document" data-remote="<?php echo Router::Url(array('controller' => 'missions', 'action' => 'select_element', $workspace_id, 'documents', 'admin' => FALSE), TRUE); ?>" data-toggle="modal" data-target="#modal_box" data-hash="documents" class="btn btn-xs pull-right tipText add_asset <?php if(empty($el_count)){ ?>disabled<?php } ?>" style=""><i class="addwhite" style=""></i></span>

	  </div>
	  <div class="panel-body">
			<?php if(isset($documents) && !empty($documents)) { ?>
				<?php foreach($documents as $key => $val) {
					$docData = $val['ElementDocument'];
					// pr($docData);
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
								$cntBaseName = ( isset($base_name) && !empty($base_name) ) ? count($base_name)-1 : 0;
								unset($base_name[$cntBaseName]);
								$basename1 = implode('', $base_name);
							}
						}
					?>

					<div class="bucket-data doc_wrapper">
						<span class="ellipsis_text"><?php echo $docData['title']; ?></span>
						<span class="options docs_options">
							<?php if(isset($docData['creater_id']) && !empty($docData['creater_id'])) { ?>
								<a class="btn btn-xs btn-default trigger" href="#" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(['controller' => 'shares', 'action' => 'show_profile', $docData['creater_id'], 'admin' => false], true) ?>"><i class="fa fa-user text-maroon"></i></a>
							<?php } ?>


							<?php
							$creator_name = 'N/A';
							if(isset($docData['creater_id']) && !empty($docData['creater_id'])) {
								$userDetail = $this->ViewModel->get_user( $docData['creater_id'], null, 1 );
								if(isset($userDetail) && !empty($userDetail)) {
									$creator_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
								}
							}
							?>

							<?php
							$updated_user_name = 'N/A';
							if(isset($docData['updated_user_id']) && !empty($docData['updated_user_id'])) {
								$userDetail = $this->ViewModel->get_user( $docData['updated_user_id'], null, 1 );
								if(isset($userDetail) && !empty($userDetail)) {
									$updated_user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
								}
							}
							?>

							<a class="btn btn-xs btn-default trigger" data-html="true" data-toggle="popover" data-content="<div class='pop-content'> <p>Created By: <?php echo $creator_name; ?></p> <p>Created On: <?php echo _displayDate($docData['created']); ?></p> <p>Last Updated: <?php echo _displayDate($docData['modified']); ?></p> <p>Updated By: <?php echo $updated_user_name; ?></p> </div>" href="<?php echo Router::Url(array('controller' => 'entities', 'action' => 'download_asset', $docData['id'], 'admin' => FALSE), TRUE); ?>"><i class="fa fa-folder-open"></i></a>
						</span>
					</div>
				<?php } ?>
			<?php }
			else {
			?>
			<div class="no-data">No Documents</div>
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

