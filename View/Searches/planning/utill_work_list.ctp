<?php $current_org = $this->Permission->current_org(); ?>

<?php if(isset($ef_data) && !empty($ef_data)){ ?>
	<?php foreach ($ef_data as $key => $value) {
		$detail = $value;
		$el_eff = $detail['ef'];
		$user_id = $el_eff['user_id'];
		$profile_pic = $detail['ud']['profile_pic'];
		if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
			$profilesPic = SITEURL . USER_PIC_PATH . $profile_pic;
		} else {
			$profilesPic = SITEURL . 'images/placeholders/user/user_1.png';
		}
		$profile_url = Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_id, 'admin' => FALSE ), true );
	?>
	<div class="pln-data-row">
		<div class="pln-col ut-col-1">
			<div class="style-people-com">
				<span class="style-popple-icon-out">
					<a class="style-popple-icon ud" href="#" data-toggle="modal" data-remote="<?php echo $profile_url; ?>" data-target="#popup_modal">
						<img alt="User Profile Pic" src="<?php echo $profilesPic; ?>" data-content="<div class='wpop'><p><?php echo htmlspecialchars($detail[0]['username']); ?></p><p><?php echo htmlspecialchars($detail['ud']['job_title']); ?></p></div>" class="user-image ud" align="left" data-original-title="" title="">
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
		<div class="pln-col ut-col-2">
			<a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "index", $el_eff['project_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['prj']['pname'], ENT_QUOTES, "UTF-8"); ?></div></a>
			<a href="<?php echo Router::Url( array( "controller" => "projects", "action" => "manage_elements", $el_eff['project_id'], $el_eff['workspace_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['wsp']['wname'], ENT_QUOTES, "UTF-8"); ?></div></a>
			<a href="<?php echo Router::Url( array( "controller" => "entities", "action" => "update_element", $el_eff['element_id'], 'admin' => FALSE ), true ); ?>"><div class="text-ellipsis proworktask"><?php echo htmlentities($detail['task']['ename'], ENT_QUOTES, "UTF-8"); ?></div></a>
		</div>
		<div class="pln-col ut-col-3">
			<div class="pln-date"> <?php echo date('d M, Y', strtotime($detail['task']['start_date'])); ?> </div>
			<div class="pln-date"> <?php echo date('d M, Y', strtotime($detail['task']['end_date'])); ?> </div>
		</div>
		<div class="pln-col ut-col-4">
			<span class="tipText" title="<?php echo htmlspecialchars($el_eff['comment']); ?>"><?php echo ($el_eff['completed_hours'] == 1) ? $el_eff['completed_hours'].' Hr' : $el_eff['completed_hours'].' Hrs'; ?></span>
		</div>
		<div class="pln-col ut-col-5">
			<span class="tipText" title="<?php echo htmlspecialchars($el_eff['comment']); ?>"><?php echo ($el_eff['remaining_hours'] == 1) ? $el_eff['remaining_hours'].' Hr' : $el_eff['remaining_hours'].' Hrs'; ?></span>
		</div>
		<div class="pln-col ut-col-6 plnactions">
			<a class="tipText edit edit-works" title="Add Adjustment" href="#" data-user="<?php echo $el_eff['user_id'] ?>" data-project="<?php echo $el_eff['project_id'] ?>" data-workspace="<?php echo $el_eff['workspace_id'] ?>" data-task="<?php echo $el_eff['element_id'] ?>"> <i class="workspace-icon"></i></a>
		</div>
	</div>
	<?php } ?>
<?php }else{ ?>
 	<div class="no-add-adu">No Tasks</div>
<?php } ?>

<script type="text/javascript">
	$(() => {
		$('.edit-works').off('click').on('click', function(event) {
			event.preventDefault();
			$('#modal_util').modal('hide');
			var user_id = $(this).data('user');
			var project_id = $(this).data('project');
			var workspace_id = $(this).data('workspace');
			var task_id = $(this).data('task');
			$('#modal_add_adj').modal({
				remote: $js_config.base_url + 'searches/add_adjustment/0/' + user_id + '/' + project_id + '/' + workspace_id + '/' + task_id
			})
			.modal('show');
		});
	})
</script>