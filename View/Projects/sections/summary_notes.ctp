
<?php
$current_user_id = $this->Session->read("Auth.User.id");
$current_org = $this->Permission->current_org();
$list = $this->Permission->project_notes($project_id);
// pr($list );
if(isset($list) && !empty($list)){  ?>
	<ul class="proj-summary-not">
		<?php foreach ($list as $key => $value) {
		$data = $value['pn'];
		$user_data = $value['ud'];

		$user_name = htmlentities($value[0]['notes_user'], ENT_QUOTES);
		$profile_pic = $user_data['profile_pic'];
		$job_title = htmlentities($user_data['job_title'], ENT_QUOTES);

		$html = '';
		if( $data['user_id'] != $current_user_id ) {
			$html = CHATHTML($data['user_id'], $data['project_id']);
		}

		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
		if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
			$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
		}

		?>
	    <div class="style-people-com" data-id="<?php echo $data['id']; ?>">
			<span class="style-popple-icon-out">
				<a class="style-popple-icon" data-remote="<?php echo Router::url(['controller' => 'shares', 'action' => 'show_profile', $data['user_id'], 'admin' => FALSE], TRUE) ?>" data-target="#popup_modal" data-toggle="modal">
					<img src="<?php echo $user_image; ?>" class="user-image" align="left" width="36" height="36" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
				</a>
				<?php   if($current_org['organization_id'] != $user_data['organization_id']){ ?>
					<i class="communitygray18 tipText community-g" style="cursor: pointer;" data-remote="<?php echo Router::url(['controller' => 'shares', 'action' => 'show_profile', $data['user_id'], 'admin' => FALSE], TRUE) ?>" data-target="#popup_modal" data-toggle="modal" title="Not In Your Organization"></i>
				<?php } ?>
			</span>

			<div class="style-people-info">
					<span class="style-people-name" style="cursor: default"><?php echo nl2br(htmlentities($data['note'], ENT_QUOTES, "UTF-8")); ?></span>
					<span class="style-people-title">
						<?php echo $this->Wiki->_displayDate( date('Y-m-d H:i',strtotime($data['modified'])), 'd M, Y h:i A'); ?>
					</span>
					<span style="display: none;"><?php echo $data['modified']; ?></span>
			</div>

		</div>
		<?php } ?>
	</ul>
<?php }else{ ?>
	<div class="no-sec-data-found">No Notes</div>
<?php } ?>


<script type="text/javascript">
	$(function(){

		$('.notes-section').find('.ts-count').html($('.proj-summary-not .style-people-com').length);
		/* $('.user-image').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400},
            template: '<div class="popover abcd" role="tooltip"><div class="arrow"></div><div class="popover-content user-menus-popoverss"></div></div>'
        }) */

        setTimeout(() => {

        }, 1)
	})
</script>
<style>.style-people-name{ white-space: inherit;}</style>