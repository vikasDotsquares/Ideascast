<?php
$current_user_id = $this->Session->read('Auth.User.id');
if (isset($history_lists) && !empty($history_lists)) {
    foreach ($history_lists as $key => $history_list) {
        
       // pr($history_list);
        ?>

        <div class="row ">
            <div class="col-sm-1 history_gap col-activity1">
                <?php
                if (isset($history_list['UserDetail']['profile_pic']) && !empty($history_list['UserDetail']['profile_pic'])) {
                    $urlImg = SITEURL . 'uploads/user_images/' . $history_list['UserDetail']['profile_pic'];
                } else {
                    $urlImg = SITEURL . 'img/image_placeholders/logo_placeholder.gif';
                }

                $job_title = $history_list['UserDetail']['job_title'];
                $html = '';
                if( $history_list['WorkspaceActivity']['updated_user_id'] != $current_user_id ) {
                        $html = CHATHTML($history_list['WorkspaceActivity']['updated_user_id']);
                }
                //echo SITEURL.'img/image_placeholders/logo_placeholder.gif';
                ?>
                <img data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $history_list['WorkspaceActivity']['updated_user_id'] ?>"  data-target="#popup_modal"  data-toggle="modal" class="thumbnail-history pophover" align="left" data-content="<div><p><?php echo htmlentities($history_list['UserDetail']['first_name'],ENT_QUOTES) . ' ' .htmlentities($history_list['UserDetail']['last_name'],ENT_QUOTES); ?></p><p><?php echo htmlentities($job_title,ENT_QUOTES); ?></p><?php echo $html; ?></div>"  src="<?php echo $urlImg; ?>"  src="<?php echo $urlImg; ?>" style="width:50px;"> 
            </div>
            <div class="col-sm-1 text-left history_gap col-activity2">
                <?php
                $user_type_per = $this->Common->project_permission_details($history_list['WorkspaceActivity']['project_id'], $history_list['WorkspaceActivity']['updated_user_id']);
                $user_type = $this->Common->userproject($history_list['WorkspaceActivity']['project_id'], $history_list['WorkspaceActivity']['updated_user_id']);

                if (isset($user_type_per['ProjectPermission']) && (!empty($user_type_per['ProjectPermission']['project_level']) && $user_type_per['ProjectPermission']['project_level'] > 0)) {
                     echo '<span class="profile-type ">Owner</span>';
                } else if (isset($user_type['UserProject']['owner_user']) && !empty($user_type['UserProject']['owner_user']) && $user_type['UserProject']['owner_user'] == 1) {
                    echo '<span class="profile-type ">Owner</span>';
                } else {

                    echo "<span class='profile-type bg-orange'>Sharer</span>";
                }
                // pr($user_type_per['ProjectPermission']['project_level'],1);
                ?>
            </div>
            <div class="col-sm-3 text-left history_gap col-activity3">
                <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $history_list['WorkspaceActivity']['updated_user_id'] ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                    <i class="fa fa-user"></i>
                </a> 
                <?php echo htmlentities($history_list['UserDetail']['first_name'],ENT_QUOTES) . '&nbsp;' . htmlentities($history_list['UserDetail']['last_name'],ENT_QUOTES); ?>
            </div>
            <div class="col-sm-4 col-activity4">
               <span> <?php echo $history_list['WorkspaceActivity']['message']; ?>, </span>   
              <span>  <?php 
				// pr($history_list['Activity']);
				//echo date('d M, Y g:iA', strtotime($history_list['WorkspaceActivity']['updated'])); 
				echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($history_list['WorkspaceActivity']['updated'])),$format = 'd M, Y g:iA');
				?></span> 
            </div>
            <div class="col-sm-3 text-left history_gap col-activity5">
                <?php
                App::uses('CakeTime', 'Utility');
                $date = strtotime($history_list['WorkspaceActivity']['updated']);
                echo '<i class="fa fa-clock-o"></i> ';
                // echo CakeTime::timeAgoInWords_custom($date, array('format' => 'F jS, Y', 'end' => '+10 year'));
				echo time_elapsed($history_list['WorkspaceActivity']['updated'], true);
                ?>
            </div>
<!--            <div class="col-sm-1 history_gap history_gap_del">
                <a data-remote="<?php echo SITEURL; ?>entities/delete_history/<?php echo $history_list['WorkspaceActivity']['id']; ?>" data-auth-remote="" data-header="Authentication" data-toggle="confirmation" data-msg="Are you sure you want to delete this history?" title="" data-action="remove" data-id="<?php echo $history_list['WorkspaceActivity']['id']; ?>" class="btn btn-sm btn-danger remove_history tipText" href="#" data-original-title="Remove">
                    <i class="fa fa-trash"></i>
                </a>

            </div>-->

        </div>
    <?php
    }
} else {
    ?>
    <div class="row">
        <div class="col-sm-12 text-center">
            <p>No Records found!</p>
        </div>


    </div>
    <?php
}
?>

<script type="text/javascript" >
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