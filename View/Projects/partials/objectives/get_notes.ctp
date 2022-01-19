<?php
	$current_user_id = $this->Session->read('Auth.User.id');

	$current_org = $this->Permission->current_org();
if( isset($data) && !empty($data) ) { ?>
	<?php foreach($data as $key => $row) { ?>
		<?php

			$userDetail = $this->ViewModel->get_user( $row['ProjectNote']['user_id'], null, 1 );
			$user_image = SITEURL . 'images/placeholders/user/user_1.png';
			$user_name = 'Not Available';
			$job_title = 'Not Available';
			if(isset($userDetail) && !empty($userDetail)) {
				$user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
				$profile_pic = $userDetail['UserDetail']['profile_pic'];
				$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

				$html = '';
				if( $row['ProjectNote']['user_id'] != $current_user_id ) {
					$html = CHATHTML($row['ProjectNote']['user_id'],$row['ProjectNote']['project_id']);
				}

				if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
					$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
				}
			}

			$current_org_other = $this->Permission->current_org($row['ProjectNote']['user_id']);
			// pr($current_org_other);
		?>

				<div class="annotate-item" data-id="<?php echo $row['ProjectNote']['id']; ?>">
					<div class="style-people-com">

					<a class="style-popple-icons " data-remote="<?php echo SITEURL; ?>/shares/show_profile/<?php echo $row['ProjectNote']['user_id']; ?>" id="trigger_edit_profile" data-target="#popup_modal" data-toggle="modal">
						<span class="style-popple-icon-out">
							<span class="style-popple-icon" style="cursor: default;">
							<img src="<?php echo $user_image; ?>" class="tipText pophover" style="cursor:pointer;" title="<?php echo $user_name; ?>" align="left" width="40" height="40" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
							<?php   if($current_org['organization_id'] != $current_org_other['organization_id']){ ?>
								<i class="communitygray18 tipText community-g" data-original-title="Not In Your Organization" ></i>
							<?php }  ?>

							</span>


						</span>
					</a>

						<div class="style-people-info">
						<span class="style-people-name" style="cursor: default;"><?php echo nl2br(htmlentities($row['ProjectNote']['note'])); ?></span>
						<span class="date-text"><?php echo _displayDate($row['ProjectNote']['modified']); ?></span>
						<div class="date-options">
						<span class="controls">
						<?php if( $row['ProjectNote']['user_id'] == $current_user_id ) { ?>
							<a type="button" id="" class="edit_note tipText" title="Edit Note">
								<i class="edit-icon"></i>
							</a>
							<a type="button" id="" class="delete_note tipText" title="Delete Note">
								<i class="deleteblack"></i>
							</a>
						<?php } ?>
						</span>
					    </div>
					  </div>
					</div>


				</div>


	<?php } ?>
<?php }
else { ?>
<div class="no-sec-data-found" >No Notes</div>
<?php } ?>



<style>
	.popover p {
    margin-bottom: 2px !important;
	}
	.popover p:nth-child(2) {
		font-size: 11px;
	}
	.style-people-name { white-space : inherit;}
</style>
<script type="text/javascript" >
$(function() {

	$('.style-popple-icons').off('click').on('click', function(event) {

		$('#model_bx').modal('hide');
	})
})
</script>

 <script>
$(function(){

 $('.pophover').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400},
            template: '<div class="popover abcd" role="tooltip"><div class="arrow"></div><div class="popover-content user-menus-popoverss"></div></div>'
        })

})
</script>
