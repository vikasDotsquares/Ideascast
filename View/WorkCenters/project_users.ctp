<style>
.popover .workinfo {
    font-size: 13px;
}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h3 class="modal-title" id="myModalLabel" style="display: inline"> Members With No Tasks </h3>
</div>
<div class="modal-body">
    <div class="project-users-lists">
		<?php if( isset($projects) && !empty($projects) && isset($start_date) && !empty($start_date) && isset($end_date) && !empty($end_date ) ){ ?>
        <div class="rangetitle">
		<strong>Selected Date Range:</strong>
		<?php			
			$stdate = date_create($start_date);
			$endate = date_create($end_date);
			$diff= date_diff($stdate,$endate);
			$datediff = $diff->format("%a days");
			if( $datediff == 0 && $start_date == date('Y-m-d') ){
				echo " Today";
			} else {
				echo " ".date('d M Y',strtotime($start_date)).' - '.date('d M Y',strtotime($end_date));
			}
		?>
		</div>

		<div class="form-group form-horizontal">
		    <label class="control-label col-sm-1 nopadding-right nopadding-left" style="text-align: left;">Skills: </label>
		    <div class="input-group">
		        <input type="text" class="form-control search-by-skills" name="" style="">
		        <span class="input-group-btn">
		            <button class="btn bg-gray clear-skill" type="button" style="display: inline-block;">
		                <i class="fa fa-search"></i>
		            </button>
		        </span>
		    </div>
		</div>

        <ul class="showuserlist">
            <?php
			
				$projectcnt = false;
				$current_user_id = $this->Session->read("Auth.User.id");
				$projectlists = $this->Common->get_projectbyid($projects);
				 
				$loggedInuserProjectCnt = $this->Common->getUserProjects($current_user_id,1,$start_date,$end_date);
				
				if( isset($projectlists) && !empty($projectlists) ){
				$i=0;
				// free task users by projects
				$usersLists = $this->ViewModel->noTaskWorkingUsers($projectlists,$start_date,$end_date);

				if( isset($usersLists['project_id']) && !empty($usersLists['project_id']) ){
					$projectid = '';

					foreach($usersLists['project_id'] as $pid => $freeuserslist){
					$projectid = $pid;

					if( isset($freeuserslist) && !empty($freeuserslist) ){

						$usersids = Set::extract($freeuserslist, '{n}');
						if( isset($usersids) && !empty($usersids) ){
						$userlists = array_unique($usersids);
						//pr($userlists);

				?>
                <li class="project-list">
                    <label class="projectname">
                        <i class="fa fa-briefcase" style="cursor: default !important;"></i> <?php
						if( isset($pid) && !empty($pid) ){
						echo $this->Common->get_projectnamebyid($pid); }  ?>
                    </label>
                    <ul>
                        <div class="users">
                            <?php foreach($userlists as $listuserss){ ?>
                                <?php
								$listUserProjectCnt = $this->Common->getUserProjects($listuserss,1,$start_date,$end_date);
								
								if( !empty($listUserProjectCnt) && !empty($loggedInuserProjectCnt) ){
									$projectDiffCnt_diff = array_diff($listUserProjectCnt,$loggedInuserProjectCnt);
									
									$projectDiffCnt = ( isset($projectDiffCnt_diff) && !empty($projectDiffCnt_diff) ) ? count($projectDiffCnt_diff) : 0;
									
									
								} else if( !empty($listUserProjectCnt) && empty($loggedInuserProjectCnt) ){
									$projectDiffCnt = ( isset($listUserProjectCnt) && !empty($listUserProjectCnt) ) ? count($listUserProjectCnt) : 0;
								} else if( empty($listUserProjectCnt) && !empty($loggedInuserProjectCnt) ){
									$projectDiffCnt = ( isset($loggedInuserProjectCnt) && !empty($loggedInuserProjectCnt) ) ? count($loggedInuserProjectCnt) : 0;
								} else {
									$projectDiffCnt = 0;
								}		
								
								if( !empty($projectDiffCnt) && count($projectDiffCnt) > 0 ){
									$projectDiff = $projectDiffCnt;
								} else {
									$projectDiff = 'no';
								}								
								
								$userDetail = $this->Common->userDetail($listuserss);

								$user_id = $userDetail['UserDetail']['user_id'];
								$userFullName = $userDetail['UserDetail']['full_name'];
								$owner = $this->Common->ProjectOwner($pid);
								$html = '';

								if( $user_id != $current_user_id ) {
									$html = CHATHTML($user_id, $pid);
								}

								$style = '';
								if( $owner['UserProject']['user_id'] == $user_id ) {
									$style = 'border: 2px solid #333';
								}

								$userDetail = $this->ViewModel->get_user( $user_id, null, 1 );
								$user_image = SITEURL . 'images/placeholders/user/user_1.png';
								$user_name = 'N/A';
								$job_title = 'N/A';
									 if(isset($userDetail) && !empty($userDetail)) {
										$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
										$profile_pic = $userDetail['UserDetail']['profile_pic'];
										$job_title = $userDetail['UserDetail']['job_title'];

										if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
											$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

									} ?>


								<?php

								if( $datediff == 0 && $start_date == date('Y-m-d') ){

									$currentavail =  $this->ViewModel->currentAvaiability($userDetail['UserDetail']['user_id']);
									$start_date = $end_date = date('Y-m-d');
									if( isset($currentavail) && !empty($currentavail) ){

										$endd = date('Y-m-d',strtotime($currentavail[0]['availabilities']['avail_end_date']));
										$start_date = date('Y-m-d');
										$end_date = $endd;
									}
								}

								$tooltiphtml = '<div class="workinfo-wrap not_available_dates_wrap">';

								$noAvailDates = $this->ViewModel->not_available_dates_range($user_id, $start_date, $end_date);
								$showIcon = false;
								if( isset($noAvailDates) && !empty($noAvailDates) ){

									$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$user_id);
									if( isset($datelists) && !empty($datelists) ){
										$tooltiphtml .= '<div style="font-weight: 600; font-size: 13px;">Unavailable:</div>';
										$tooltiphtml .=$datelists;
										$showIcon = true;
									} else {
										$tooltiphtml .= '<span class="workinfo">No Time Away</span>';
									}
								} else {
									$tooltiphtml .= '<span class="workinfo">No Time Away</span>';
								}
								$tooltiphtml .= '</div>';

								$skillsList = '';
								$userSkills = get_userSkills($user_id);
								if(isset($userSkills) && !empty($userSkills)) {
									$userSkills = Set::extract($userSkills, '/UserSkill/skill_id');
									$skillsList = getByDbIds('Skill', $userSkills);
									$skillsList = Set::extract($skillsList, '/Skill/title');
									$skillsList = implode(",", $skillsList);
								}
								?>
                            <li data-skills="<?php echo $skillsList; ?>" class="users-list">

                                <a href="javascript:void(0);" data-username="<?php echo $user_name; ?>" class="user-pophover" data-click-content='<?php echo $tooltiphtml;?>' data-hover-content="<div><p style='word-wrap: break-word;'><?php echo $user_name; ?></p><p style='word-wrap: break-word;'><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>" >
										<?php if( $showIcon ){ ?><i class="fa fa-calendar-times-o noavaildatausertip"></i><?php } ?>
										<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
									</a>
									<i class="fa fa-briefcase tipText" title="Involved In <?php echo $projectDiff;?> Other OpusView Projects" style="cursor: default !important;color:#f00;position: absolute;bottom: 5px;left: 4px;"></i>
                                <?php } ?>
                            </li>
                            <?php } ?>
                        	<li class="no-users" style="display: none; width: 100%; text-align: center;">No Result</li>
                        </div>
                    </ul>
                </li>
                <?php 		}
						}
				?>
				
				<?php
					}
				} else { $projectcnt = false; ?>
					<?php
					if( $projectcnt == false ){
						foreach($projectlists as $projectpid){ ?>
						<li>
							<label class="projectname">
								<?php echo $projectpid['Project']['title']; ?>
							</label>
							<ul>
								<div class="users">
									<li>No Users</li>
								</div>
							</ul>
						</li>
						<?php }
					} ?>
				<?php }
			} else {
				$projectcnt = false;
				echo "No Users";
			}
		?>
        </ul>
	<?php } else {
		$projectcnt = false;
	} ?>

	</div>
</div>


<div class="modal-footer">
    <button class="btn btn-danger" id="close_modal" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
	$(function(){
    var showClickPopover = function () {
    	var username = $(this).data('username');
        $(this).data('bs.popover').options.content = $(this).data('click-content');
        $(this).data('bs.popover').options.title = username;
        $(this).popover("show");
        $('.popover-title').show();
    };

    $('.user-pophover').popover({
        placement: "bottom",
        container: 'body',
        trigger: 'manual',
        html: true,
        delay: {show: "50", hide: "400"},
        template: '<div class="popover fade bottom in" role="tooltip" style="min-width:180px;"><div class="arrow" style="left: 50%;"></div><h3 class="popover-title" style=""></h3><div class="popover-content"></div></div>'
    })
    .click(showClickPopover)
    .on("mouseenter", function () {
        var _this = this;

        $(this).data('bs.popover').options.content = $(this).data('hover-content');
        $(this).data('bs.popover').options.title = '';
        $(this).data('original-title', '');
        $(this).attr('data-original-title', '');
        $(this).popover('show');
        setTimeout(function(){
	        $(".popover").on("mouseleave", function () {
	            $(_this).popover('hide');
	        });
        }, 300)
    })
    .on("mouseleave", function () {
        var _this = this;
        setTimeout(function () {
            if (!$(".popover:hover").length) {
                $(_this).popover("hide");
            }
        }, 300);
    });
	
	
	$('body').delegate('.clear-skill', 'click', function(event){
		event.preventDefault();
		
		$('.search-by-skills').val('').trigger('keyup');
		$(this).find('i').removeClass('fa-times').addClass('fa-search');
		$(this).removeClass('bg-red').addClass('bg-gray');
		
	})
	
    $('.search-by-skills').on('keyup', function(event){
		
		$('.clear-skill').find('i').removeClass('fa-search').addClass('fa-times');
		$('.clear-skill').removeClass('bg-gray').addClass('bg-red');
		
		
    	var searchTerm = $(this).val(),
    		searchTerm = (searchTerm) ? searchTerm.toLowerCase() : searchTerm;

		$('.no-users').hide();
		if(searchTerm == '' || searchTerm === undefined) {
			$('.showuserlist').find('li.users-list').show();
			// $('.showuserlist').find('li.project-list').show();
			
			$('.clear-skill').find('i').removeClass('fa-times').addClass('fa-search');
			$('.clear-skill').removeClass('bg-red').addClass('bg-gray');
			
			return;
		}

		var filter = $('li.users-list').filter(function() {
		    return $(this).attr('data-skills').toLowerCase().indexOf(searchTerm) > -1;
		});
		console.log(filter)
		$('.showuserlist').find('li.users-list').hide();
		filter.show();


		// $('.showuserlist').find('li.users-list[data-skills*="' + searchTerm + '"]').show();

		$('.project-list').each(function(index, el) {
			if($(this).find('li.users-list:visible').length <= 0) {
				$(this).find('.no-users').show();
				// console.log('length', $(this).find('li.users-list:visible').length)
			}
		});
    })

	})
</script>