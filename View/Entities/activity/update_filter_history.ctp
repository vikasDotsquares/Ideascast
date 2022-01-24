<style>
.table-rows:not(.table-catcher) [class*=" col-sm-"], .table-rows:not(.table-catcher) [class^=col-sm-] {
    display: block;
    font-size: 13px;
    margin: 4px 0;
}

.user_profile {
    white-space: nowrap;
    text-overflow: ellipsis;    
    overflow: hidden;
}
	
</style>
<?php
$current_user_id = $this->Session->read('Auth.User.id');
if (isset($history_lists) && !empty($history_lists)) {
    foreach ($history_lists as $key => $history_list) {
	   if(isset($history_list['Activity']['updated_user_id']) && !empty($history_list['Activity']['updated_user_id']))
	   {
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
                if( $history_list['Activity']['updated_user_id'] != $current_user_id ) {
                        $html = CHATHTML($history_list['Activity']['updated_user_id']);
                }
                //echo SITEURL.'img/image_placeholders/logo_placeholder.gif';
                ?>
                <img data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $history_list['Activity']['updated_user_id'] ?>"  data-target="#popup_modal"  data-toggle="modal" class="thumbnail-history pophover" align="left" data-content="<div><p><?php echo htmlentities($history_list['UserDetail']['first_name'],ENT_QUOTES) . ' ' .htmlentities($history_list['UserDetail']['last_name'],ENT_QUOTES); ?></p><p><?php echo htmlentities($job_title,ENT_QUOTES); ?></p><?php echo $html; ?></div>"  src="<?php echo $urlImg; ?>" style="width:50px;"> 
            </div>
            <div class="col-sm-1 text-left history_gap col-activity2">
                <?php
				//echo $history_list['Activity']['updated_user_id'];
                $user_type_per = $this->Common->project_permission_details($history_list['Activity']['project_id'], $history_list['Activity']['updated_user_id']);
				
				

                $user_type = $this->Common->userproject($history_list['Activity']['project_id'], $history_list['Activity']['updated_user_id']);

                $owner = $this->Common->ProjectOwner($history_list['Activity']['project_id']);
                $participants_group_owner = participants_group_owner($history_list['Activity']['project_id']);
                // pr($participants_group_owner);
				
				
				$grp_id = $this->Group->GroupIDbyUserID($history_list['Activity']['project_id'], $history_list['Activity']['updated_user_id']);
				$project_level = array();
				if (isset($grp_id) && !empty($grp_id)) {

					$group_permission = $this->Group->group_permission_details($history_list['Activity']['project_id'], $grp_id);
					if (isset($group_permission['ProjectPermission']['project_level']) && $group_permission['ProjectPermission']['project_level'] == 1) {
						$project_level = $group_permission['ProjectPermission']['project_level'];
					}
				}
                

                if (isset($user_type_per['ProjectPermission']) && (!empty($user_type_per['ProjectPermission']['project_level']) && $user_type_per['ProjectPermission']['project_level'] > 0)) {
                     echo '<span class="profile-type ">Owner</span>';
                } else if (isset($user_type['UserProject']['owner_user']) && !empty($user_type['UserProject']['owner_user']) && $user_type['UserProject']['owner_user'] == 1) {
                    echo '<span class="profile-type ">Owner</span>';
                }  else if ( isset($project_level) && !empty($project_level) ) {
                    echo '<span class="profile-type ">Owner</span>';
                } else {

                    echo "<span class='profile-type bg-orange'>Sharer</span>";
                }
				
				//pr($user_type_per['ProjectPermission']); 
				//pr($user_type); 
				//pr($participants_group_owner); 
				//pr($participants_group_owner); 
				
				
				//die;
                // pr($user_type_per['ProjectPermission']['project_level'],1);
                ?>
            </div>
            <div class="col-sm-3 user_profile text-left history_gap col-activity3">
                <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $history_list['Activity']['updated_user_id'] ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                    <i class="fa fa-user"></i>
                </a> 
                <?php echo htmlentities($history_list['UserDetail']['first_name'],ENT_QUOTES) . '&nbsp;' . htmlentities($history_list['UserDetail']['last_name'],ENT_QUOTES); ?>
            </div>
            <div class="col-sm-4 col-activity4">
                <?php echo $history_list['Activity']['message']; ?>,    
                <?php 
					//echo date('d M, Y h:iA', strtotime($history_list['Activity']['updated'])); 
					echo (isset($history_list['Activity']['updated']) && !empty($history_list['Activity']['updated']) )? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($history_list['Activity']['updated'])),$format = 'd M, Y h:iA') : 'N/A';
				
                ?>
            </div>
            <div class="col-sm-3 text-left history_gap col-activity5">
                <?php
                App::uses('CakeTime', 'Utility');
                $date = strtotime($history_list['Activity']['updated']);
                echo '<i class="fa fa-clock-o"></i> ';
               //  echo CakeTime::timeAgoInWords_custom($date, array('format' => 'F jS, Y', 'end' => '+10 year'));
			  // echo $history_list['Activity']['updated']."<br>";
			   //echo date('Y-m-d h:i:s')."<br>";
			   
                echo time_elapsed($history_list['Activity']['updated'], true);
                ?>
            </div>
<!--            <div class="col-sm-1 history_gap history_gap_del">
                <a data-remote="<?php echo SITEURL; ?>entities/delete_history/<?php echo $history_list['Activity']['id']; ?>" data-auth-remote="" data-header="Authentication" data-toggle="confirmation" data-msg="Are you sure you want to delete this history?" title="" data-action="remove" data-id="<?php echo $history_list['Activity']['id']; ?>" class="btn btn-sm btn-danger remove_history tipText" href="#" data-original-title="Remove">
                    <i class="fa fa-trash"></i>
                </a>

            </div>-->

        </div>
    <?php
    } }
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