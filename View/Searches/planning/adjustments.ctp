<style>
</style>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h3 class="modal-title annotationeleTitle" id="myModalLabel">Adjustments</h3>
</div>
<div class="modal-body popup-select-icon">
	<?php $current_org = $this->Permission->current_org(); //pr($data); ?>
	<div class="unit-filt-adj-warp">
		<div class="planning-unit-header adj-list-header">
			<div class="pln-col p-col-1">
				<span class="ps-h-two sort_order active al-list" data-type="pe" data-by="first_name" data-order="desc" data-original-title="" title="">
					Name
					<i class="fa fa-sort" aria-hidden="true"></i>
					<i class="fa fa-sort-asc" aria-hidden="true"></i>
					<i class="fa fa-sort-desc" aria-hidden="true"></i>
				</span>
				<span class="ps-h-one sort_order al-list" data-type="pe" data-by="last_name" data-order="" data-original-title="" title="">
					<i class="fa fa-sort" aria-hidden="true"></i>
					<i class="fa fa-sort-asc" aria-hidden="true"></i>
					<i class="fa fa-sort-desc" aria-hidden="true"></i>
				</span>
			</div>
			<div class="pln-col p-col-2">
				<span class="ps-h-two sort_order al-list" data-type="pe" data-by="creator" data-order="" data-original-title="" title="">
					Added By
					<i class="fa fa-sort" aria-hidden="true"></i>
					<i class="fa fa-sort-asc" aria-hidden="true"></i>
					<i class="fa fa-sort-desc" aria-hidden="true"></i>
				</span>
				<span class="ps-h-one sort_order al-list" data-type="pe" data-by="created" data-order="" data-original-title="" title="">
					On
					<i class="fa fa-sort" aria-hidden="true"></i>
					<i class="fa fa-sort-asc" aria-hidden="true"></i>
					<i class="fa fa-sort-desc" aria-hidden="true"></i>
				</span>
			</div>
			<div class="pln-col p-col-3">
				<span class="ps-h-two sort_order al-list" data-type="pe" data-by="pname" data-order="" data-original-title="" title="">
					Project
					<i class="fa fa-sort" aria-hidden="true"></i>
					<i class="fa fa-sort-asc" aria-hidden="true"></i>
					<i class="fa fa-sort-desc" aria-hidden="true"></i>
				</span>
				<span class="ps-h-one sort_order al-list" data-type="pe" data-by="wname" data-order="" data-original-title="" title="">
					Workspace
					<i class="fa fa-sort" aria-hidden="true"></i>
					<i class="fa fa-sort-asc" aria-hidden="true"></i>
					<i class="fa fa-sort-desc" aria-hidden="true"></i>
				</span>
				<span class="ps-h-one sort_order al-list" data-type="pe" data-by="ename" data-order="" data-original-title="" title="">
					Task
					<i class="fa fa-sort" aria-hidden="true"></i>
					<i class="fa fa-sort-asc" aria-hidden="true"></i>
					<i class="fa fa-sort-desc" aria-hidden="true"></i>
				</span>
			</div>
			<div class="pln-col p-col-4">
				<span class="ps-h-two sort_order al-list" data-type="pe" data-by="remaining_hours" data-order="" data-original-title="" title="">
					Remaining (Hrs)
					<i class="fa fa-sort" aria-hidden="true"></i>
					<i class="fa fa-sort-asc" aria-hidden="true"></i>
					<i class="fa fa-sort-desc" aria-hidden="true"></i>
				</span>
			</div>
			<div class="pln-col p-col-5 plnactions">
				Actions
			</div>
		</div>
		<div class="unit-filt-adj-cont">
			<?php if(isset($data) && !empty($data)){ ?>
			<?php foreach ($data as $key => $value) {
				$detail = $value;
				$plan_eff = $detail['pe'];

				$user_id = $plan_eff['user_id'];
	            $profile_pic = $detail['ud']['profile_pic'];
	            if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)){
	                $profilesPic = SITEURL . USER_PIC_PATH . $profile_pic;
	            } else {
	                $profilesPic = SITEURL . 'images/placeholders/user/user_1.png';
	            }

	            $profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true );
	            $html = CHATHTML($user_id);
			?>
			<div class="pln-data-row" data-id="<?php echo $plan_eff['id']; ?>">
				<div class="pln-col p-col-1">
					<div class="style-people-com">
						<span class="style-popple-icon-out">
							<a class="style-popple-icon ud" href="#" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal">
								<img alt="User Profile Pic" src="<?php echo $profilesPic; ?>" data-content="<div class='wpop'><p><?php echo htmlspecialchars($detail[0]['username']); ?></p><p><?php echo htmlspecialchars($detail['ud']['job_title']); ?></p><?php echo $html; ?></div>" class="user-image ud" align="left" data-original-title="" title="">
							</a>
	                        <?php if($current_org['organization_id'] != $detail['ud']['organization_id']){ ?>
	                            <i class="communitygray18 tipText community-g ud" style="cursor: pointer;" title="" data-original-title="Not In Your Organization" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo $profile_url; ?>"></i>
	                        <?php } ?>
						</span>
						<div class="style-people-info">
							<span class="style-people-name ud" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal"><?php echo ($detail[0]['username']); ?></span>
							<span class="style-people-title"><?php echo ($detail['ud']['job_title']); ?></span>
						</div>
					</div>
				</div>
				<div class="pln-col p-col-2">
					<div class="text-ellipsis lineheight17"><a class="ud"  data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $plan_eff['created_by'], 'admin' => FALSE ), true ); ?>" data-target="#popup_modal" href="#"><?php echo ($detail[0]['creator']); ?></a></div>
					<div class="pln-date"><?php echo date('d M, Y', strtotime($plan_eff['created'])); ?></div>
				</div>
				<div class="pln-col p-col-3">
					<a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "index", $plan_eff['project_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['prj']['pname'], ENT_QUOTES, "UTF-8"); ?></div></a>
					<a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "manage_elements", $plan_eff['project_id'], $plan_eff['workspace_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['wsp']['wname'], ENT_QUOTES, "UTF-8"); ?></div></a>
					<a href="<?php echo Router::Url( array( "controller" => "entities", "action" => "update_element", $plan_eff['element_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['task']['ename'], ENT_QUOTES, "UTF-8"); ?></div></a>
				</div>
				<div class="pln-col p-col-4">
					<span class="tipText" title="<?php echo htmlspecialchars($plan_eff['comment']); ?>"><?php echo $plan_eff['remaining_hours']; ?></span>
				</div>
				<div class="pln-col p-col-5 plnactions">
					<a class="tipText edit-pe" title="Edit" href="#"><i class="edit-icon"></i></a>
					<a class="tipText delete-pe" title="Delete" href="#"><i class="deleteblack"></i></a>
				</div>
			</div>
		<?php } ?>
		<?php }else{ ?>
			<div class="no-add-adu">No Adjustments</div>
		<?php } ?>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
	$(()=>{
		$('.ud').off('click').on('click', function(event) {
			event.preventDefault();
			$('#modal_adjustments').modal('hide');
		});

		$('.edit-pe').off('click').on('click', function(event) {
			event.preventDefault();
			$('#modal_adjustments').modal('hide');
			var id = $(this).parents('.pln-data-row:first').data('id');
			$('#modal_add_adj').modal({
				remote: $js_config.base_url + 'searches/add_adjustment/' + id
			})
			.modal('show');
		});
	})
</script>