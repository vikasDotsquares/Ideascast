
<?php
    $project_detail = getByDbId("Project", $project_id, ['id', 'title', 'currency_id', 'budget']);
    $project_detail = $project_detail['Project'];
    $currency_symbol = 'GBP';
    if(isset($project_detail['currency_id']) && !empty($project_detail['currency_id'])) {
        $currency_detail = getByDbId("Currency", $project_detail['currency_id'], ['id', 'country', 'name', 'symbol', 'sign']);
        $currency_detail = $currency_detail['Currency'];
        $currency_symbol = $currency_detail['sign'];
    }
    if($currency_symbol == 'USD') {
        $currency_symbol = '<span class="" style="font-size: 12px;">&#x24;</span>';
    }
    else if($currency_symbol == 'GBP') {
        $currency_symbol = '<span class="" style="font-size: 12px;">&#xa3;</span>';
    }
    else if($currency_symbol == 'EUR') {
        $currency_symbol = '<span class="" style="font-size: 12px;">&#x20AC;</span>';
    }
    else if($currency_symbol == 'DKK' || $currency_symbol == 'ISK') {
        $currency_symbol = '<span  style="font-size: 12px;">Kr</span>';
    }

	$owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));

	$participants = participants($project_id, $owner['UserProject']['user_id']);

	$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);

	$participantsGpOwner = participants_group_owner($project_id);

	$participantsGpSharer = participants_group_sharer($project_id);

	$participants = isset($participants) ? array_filter($participants) : $participants;
	$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
	$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
	$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

	$project_users = [];
	if (is_array($participants)) {
			$project_users = array_merge($project_users, $participants);
	}

	if (is_array($participants_owners)) {
		$project_users = array_merge($project_users, $participants_owners);
	}

	if (is_array($participantsGpOwner)) {
		$project_users = array_merge($project_users, $participantsGpOwner);
	}
	if (is_array($participantsGpSharer)) {
		$project_users = array_merge($project_users, $participantsGpSharer);
	}

	if(isset($project_users) && !empty($project_users)) {
		$project_users = array_unique($project_users);
	}
 //    pr($owner);
	// pr($project_users);
 ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title annotationeleTitle" id="myModalLabel">Rate Card Manager</h4>
</div>
<div class="modal-body">
    <div class="rate-card-manager">
        <div class="col-rate">
            <label>Project:</label>
            <div class="rate-card-text"><?php echo htmlentities($project_detail['title']); ?></div>
        </div>
        <div class="col-rate-project">
            <label>People on Project:</label>
            <?php if(isset($project_users) && !empty($project_users)) { ?>
            <div class="people-project">
                <ul>
                	<?php foreach ($project_users as $key => $user_id) {
                        $isOwner = $this->viewModel->sharingPermitType($project_detail['id'], $user_id);
                        $border = 'border: 2px solid #ccc !important;';
                        if($isOwner){
                            $border = 'border: 2px solid #666 !important;';
                        }
                        $rates_added = false;
                        $rates_popover = '<div class="project-rates">';
                		$user_name = '';
                		$user_image = SITEURL . 'images/placeholders/user/user_1.png';
                		$user_detail = $this->ViewModel->get_user_data($user_id);
                		if(isset($user_detail) && !empty($user_detail)) {
	                		$user_name = $user_detail['UserDetail']['first_name'] . ' ' . $user_detail['UserDetail']['last_name'];
	                		$profile_pic = $user_detail['UserDetail']['profile_pic'];
	                		if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
	                			$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
	                		}
                            $rates_popover .= '<div class="user-name">'.$user_name.'</div>';

                            $data_day_rates = '';
                            $data_hour_rates = '';
                            $pop_day_rates = 'N/A';
                            $pop_hour_rates = 'N/A';
                            $user_project_cost = $this->ViewModel->user_project_cost($user_id, $project_id);
                            if(isset($user_project_cost) && !empty($user_project_cost)) {
                                $data_day = $user_project_cost['UserProjectCost']['day_rate'];
                                $data_hour = $user_project_cost['UserProjectCost']['hour_rate'];
                                if(!empty($data_day)) {
                                    $data_day_rates = number_format($data_day, 2, '.', ',');
                                    $rates_added = true;
                                    $pop_day_rates = $currency_symbol.$data_day_rates;
                                }
                                if(!empty($data_hour)) {
                                    $data_hour_rates = number_format($data_hour, 2, '.', ',');
                                    $rates_added = true;
                                    $pop_hour_rates = $currency_symbol.$data_hour_rates;
                                }
                            }
                            $rates_popover .= '<div class="rates" style="margin-top: 10px;">Day Rate: '.$pop_day_rates.'</div>';
                            $rates_popover .= '<div class="rates">Hour Rate: '.$pop_hour_rates.'</div>';
	                	}
                        $rates_popover .= '</div>';
                	?>
                    <li style="<?php echo $border; ?>">
                    	<a href="#" class="user-rates" data-name="<?php echo $user_name; ?>" data-userid="<?php echo $user_id; ?>" data-day="<?php echo $data_day_rates; ?>" data-hour="<?php echo $data_hour_rates; ?>" data-content='<?php echo $rates_popover; ?>' title="Project Rates">
                    		<img src="<?php echo $user_image; ?>" />
                    		<?php if($rates_added){ ?><i class="fa fa-check text-green"></i><?php } ?>
                    		<i class="fa fa-check text-red"></i>
                    	</a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>
        </div>
        <div class="col-rate rate-data-entry">
            <label>Name:</label>
            <div class="rate-card-text" id="rate_card_name">&nbsp;</div>
        </div>
        <div class="col-rate rate-data-entry">
            <input class="" type="hidden" name="userid" id="userid">
            <div class="rateday">
                <label>Day Rate:</label>
                <input class="form-control numeric urates" type="text" name="dayrate" id="dayrate" >
                <i class="btn btn-danger btn-xs fa fa-times clear-num clear-num-day"></i>
            </div>
            <div class="rateday">
                <label>Hour Rate:</label>
                <input class="form-control numeric urates" type="text" name="hourrate" id="hourrate" >
                <i class="btn btn-danger btn-xs fa fa-times clear-num clear-num-hour"></i>
            </div>
        </div>
        <span class="error-message error text-danger"></span>
    </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button" id="submit_rates" class="btn btn-success disabled">Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>
<style type="text/css">
    .rate-card-manager .rate-data-entry {
        display: block;
    }
</style>
<script type="text/javascript">
    $(function(){
        $('.user-rates').popover({
            placement: "bottom",
            container: 'body',
            trigger: 'hover',
            html: true,
        })
        .on('show.bs.popover', function(){
            var data = $(this).data('bs.popover'),
                $tip = data.$tip,
                $content = $tip.find('.popover-content');

            $tip.css('min-width','180px');
            $content.css('padding','0');
        })
    })
</script>