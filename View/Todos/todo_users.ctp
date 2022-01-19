dfsdf<?php
$current_user_id = $this->Session->read('Auth.User.id');
$list_rec_do_users = $this->Group->do_list_users($do_list_id);

    $project_id = null;
    if (isset($data['DoList']) && !empty($data['DoList'])) {
        $project_id = (isset($data['DoList']) && !empty($data['DoList'])) ? $data['DoList']['project_id'] : null;
    }

    if (isset($list_rec_do_users) && !empty($list_rec_do_users)) {

        if(!in_array($data['DoList']['user_id'], $list_rec_do_users)){
                $udetail = $this->Common->UserDetail($data['DoList']['user_id']);
                $user_data = $this->ViewModel->get_user_data($data['DoList']['user_id']);
                $ud = $this->ViewModel->get_user($data['DoList']['user_id']);
                $job_title = htmlentities($user_data['UserDetail']['job_title'],ENT_QUOTES);

                $pic = $user_data['UserDetail']['profile_pic'];
                $profiles = SITEURL . USER_PIC_PATH . $pic;


                if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                    $profiles = SITEURL . USER_PIC_PATH . $pic;
                } else {
                    $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                }

                $html = '';
                if( $data['DoList']['user_id'] != $current_user_id ) {
                        $html = CHATHTML($data['DoList']['user_id'],  $project_id);
                }
                ?>
                    <a class="pophover" align="left" data-content="<div><p><?php echo htmlentities($user_data['UserDetail']['first_name'],ENT_QUOTES) . ' ' .htmlentities($user_data['UserDetail']['last_name'],ENT_QUOTES); ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>"  data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo $data['DoList']['user_id'];?>" data-toggle="modal" data-target="#popup_modal" href="#">
                        <?php
                            echo $this->Html->image($profiles, array("", "class" => "user-image comment-people-pic border-darks"));
                        ?>
                    </a>
                <?php

            }
			//pr($list_rec_do_users);
        foreach ($list_rec_do_users as $user) {


            $udetail = $this->Common->UserDetail($user);
            $user_data = $this->ViewModel->get_user_data($user);
            $ud = $this->ViewModel->get_user($user);
			$job_title = '';
			if( !empty($user_data['UserDetail']['job_title']) ){
				$job_title = htmlentities($user_data['UserDetail']['job_title'],ENT_QUOTES);
			}

			//$pic = $user_data['UserDetail']['profile_pic'];
			$pic = '';
			if( !empty($user_data['UserDetail']['profile_pic']) ){
				$pic = $user_data['UserDetail']['profile_pic'];
			}
            $profiles = SITEURL . USER_PIC_PATH . $pic;


            if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
                $profiles = SITEURL . USER_PIC_PATH . $pic;
            } else {
                $profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
            }


            $html = '';
			if( $user != $current_user_id ) {
					$html = CHATHTML($user, $project_id);
			}

			$fullname = 'N/A';
			if( !empty($user_data['UserDetail']['first_name']) && !empty($user_data['UserDetail']['last_name']) ){
				$fullname = $user_data['UserDetail']['first_name'] . ' ' .$user_data['UserDetail']['last_name'];
			} else if( !empty($user_data['UserDetail']['first_name']) && empty($user_data['UserDetail']['last_name']) ){

				$fullname = $user_data['UserDetail']['first_name'];

			} else if( empty($user_data['UserDetail']['first_name']) && !empty($user_data['UserDetail']['last_name']) ){

				$fullname = $user_data['UserDetail']['last_name'];
			}
?>
        <a class="pophover" align="left" data-content="<div><p><?php echo htmlentities($fullname,ENT_QUOTES); ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>"  data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo $user;?>" data-toggle="modal" data-target="#popup_modal" href="#">
            <?php
                echo $this->Html->image($profiles, array("", "class" => "user-image comment-people-pic"));
            ?>
        </a>
<?php
        }
    } else {

		$udetail = $this->Common->UserDetail($data['DoList']['user_id']);
		$user_data = $this->ViewModel->get_user_data($data['DoList']['user_id']);
		$ud = $this->ViewModel->get_user($data['DoList']['user_id']);
		$job_title = '';
		if( isset($user_data['UserDetail']['job_title']) && !empty($user_data['UserDetail']['job_title']) ){
			$job_title = htmlentities($user_data['UserDetail']['job_title'],ENT_QUOTES);
		}
		$pic = '';
		if( isset($user_data['UserDetail']['profile_pic']) && !empty($user_data['UserDetail']['profile_pic'])  ){
			$pic = $user_data['UserDetail']['profile_pic'];
		}
		$profiles = SITEURL . USER_PIC_PATH . $pic;


		if (!empty($pic) && file_exists(USER_PIC_PATH . $pic)) {
			$profiles = SITEURL . USER_PIC_PATH . $pic;
		} else {
			$profiles = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
		}

		$html = '';
		if( $data['DoList']['user_id'] != $current_user_id ) {
				$html = CHATHTML($data['DoList']['user_id'],  $project_id);
		}
		?>
			<a class="pophover" align="left" data-content="<div><p><?php echo htmlentities($user_data['UserDetail']['first_name']) . ' ' .htmlentities($user_data['UserDetail']['last_name']); ?></p><p><?php echo htmlentities($job_title); ?></p><?php echo htmlentities($html); ?></div>"  data-remote="<?php echo SITEURL;?>shares/show_profile/<?php echo htmlentities($data['DoList']['user_id']);?>" data-toggle="modal" data-target="#popup_modal" href="#">
				<?php
					echo $this->Html->image($profiles, array("", "class" => "user-image comment-people-pic border-darks"));
				?>
			</a>
		<?php
	}
?>


<script type="text/javascript" >
    $('a[href="#"],a[href=""]').attr('href', 'javascript:;');
    $('#modal_small').on('hidden.bs.modal', function (event) {
        $(this).find('modal-content').html("")
        $(this).removeData('bs.modal')
    })
	$(function(){

		$('.pophover').popover({
			placement : 'bottom',
			trigger : 'hover',
			html : true,
			container: 'body',
			delay: {show: 50, hide: 400}
		})
		$('body').on('click', function (e) {
			$('.pophover').each(function () {
				//the 'is' for buttons that trigger popups
				//the 'has' for icons within a button that triggers a popup
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
					var $that = $(this);
					$that.popover('hide');
				}
			});
		});

	})
</script>
<style>.border-darks{ border:solid 1px #000 !important;}</style>