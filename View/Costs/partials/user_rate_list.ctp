<style type="text/css">
    .prj_com_dnone {
        display: none !important;
    }
</style>
<?php

    /******************************************************************************************************************************/
    $users_list = [];
	$project_status = $this->Permission->project_status($project_id);
	if(isset($project_status) && !empty($project_status)){
		$project_status = $project_status[0][0]['prj_status'];
	}

	$all_users = $this->Permission->project_user_list($project_id);
	if(isset($all_users) && !empty($all_users)){
		foreach ($all_users as $key => $value) {
			 $users_list[$value['user_details']['user_id']] = $value[0]['fullname'];
		}
	}
	function asrt($a, $b) {
		$t1 = iconv('UTF-8', 'ASCII//TRANSLIT', $a);
		$t2 = iconv('UTF-8', 'ASCII//TRANSLIT', $b);
	    return strcasecmp($t1, $t2);
	}
	uasort($users_list, 'asrt');
	// pr($users_list);
	$project_user_rates = $this->Permission->project_user_rates($project_id);
	$current_org = $this->Permission->current_org();

 ?>

<div class="cost-col-header">
	<div class="cost-col cost-col-1">
		People <span class="total-people">(<?php echo count($project_user_rates); ?>)</span>
	</div>
	<div class="cost-col cost-col-2">
		Day Rate
	</div>
	<div class="cost-col cost-col-3">
		Hour Rate
	</div>
	<div class="cost-col cost-col-4">
		Actions
	</div>
</div>
<div class="cost-data-list">
	<?php if(isset($project_user_rates) && !empty($project_user_rates)){ ?>
	<?php foreach ($project_user_rates as $key => $value) {
		$full_name = $value[0]['fullname'];
		$user_detail = $value['user_details'];
		$user_rates = $value['upc'];
		$user_role = $value['user_permissions']['role'];

		$profile_pic = $user_detail['profile_pic'];
		if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
			$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
		} else {
			$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
		}
	?>
	<div class="cost-data-row" data-user="<?php echo $user_detail['user_id']; ?>" data-day="<?php echo (!empty($user_rates['day_rate'])) ? number_format($user_rates['day_rate'], 2, '.', ',') : ''; ?>" data-hour="<?php echo (!empty($user_rates['hour_rate'])) ? number_format($user_rates['hour_rate'], 2, '.', ',') : ''; ?>">
		<div class="cost-col cost-col-1">
			<div class="style-people-com">
				<span class="style-popple-icon-out">
					<a class="style-popple-icon" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_detail['user_id'], 'admin' => FALSE ), true ); ?>">
						<img src="<?php echo $profilesPic; ?>" class="user-image" align="left" width="40" height="40">
					</a>
					<?php if($current_org['organization_id'] != $user_detail['organization_id']){ ?>
						<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="Not In Your Organization"></i>
					<?php } ?>
				</span>
				<div class="style-people-info">
					<span class="style-people-name u-name" data-target="#popup_modal" data-toggle="modal" data-remote="<?php echo Router::Url( array( "controller" => "shares", "action" => "show_profile", $user_detail['user_id'], 'admin' => FALSE ), true ); ?>"><?php echo $full_name; ?></span>
					<span class="style-people-title" style="cursor: default !important;"><?php echo $user_detail['job_title']; ?></span>
				</div>
			</div>
		</div>
		<div class="cost-col cost-col-2">
			<?php echo (!empty($user_rates['day_rate'])) ? number_format($user_rates['day_rate'], 2, '.', ',') : ''; ?>
		</div>
		<div class="cost-col cost-col-3">
			<?php echo (!empty($user_rates['hour_rate'])) ? number_format($user_rates['hour_rate'], 2, '.', ',') : ''; ?>
		</div>
		<div class="cost-col cost-col-4 cost-actions <?php if($project_status == 'completed'){ ?>prj_com_dnone<?php } ?>">
			<a class="tipText edit-rates " href="#" title="Edit"> <i class="edit-icon"></i></a>
			<a class="tipText clear-rates " href="#" title="Clear"> <i class="clearblackicon"></i></a>
		</div>
	</div>
	<?php } ?>
	<?php } ?>
</div>
<script type="text/javascript">
    $(()=>{
        var project_id = '<?php echo $project_id; ?>';
        $('.names-link').off('click').on('click', function(event) {
            event.preventDefault();
        })

        $('.edit-rates').off('click').on('click', function(event) {
            event.preventDefault();
            var $parent = $(this).parents('.cost-data-row:first'),
                data = $parent.data();

            $('#user_day_rate').val(data.day);
            $('#user_hour_rate').val(data.hour);
            $('#users_list').val(data.user).multiselect('refresh');
        })
        $('.clear-rates').off('click').on('click', function(event) {
            event.preventDefault();
            var $parent = $(this).parents('.cost-data-row:first'),
                data = $parent.data(),
                post = {user_id: data.user, project_id: project_id}

            $.ajax({
                global: false,
                url: $js_config.base_url + 'costs/clear_user_rates',
                type: 'POST',
                dataType: 'JSON',
                data: post,
                success: function(response) {
                    if(response.success){
                        $.user_rate_list();
                    }
                }
            })
        })
        $('.style-popple-icon,.u-name').off('click').on('click', function(event) {
            event.preventDefault();
            $("#model_bx").modal('hide');
        })
    })
</script>