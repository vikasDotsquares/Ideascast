<?php
$current_user_id = $this->Session->read('Auth.User.id');

$project_detail = getByDbId('Project', $project_id);
$project_detail = $project_detail['Project'];
$project_title = strip_tags($project_detail['title']);

$project_charity = project_charity($project_id);
$project_charity = $project_charity['RewardCharity'];

$charity_redeem_data = charity_redeem_data($project_charity['id']);
$charity_amount = [];
$total_charity_amount = $total_charity_value = 0;

if($charity_redeem_data) {
	foreach ($charity_redeem_data as $key => $value) {
		$gb = $value['RewardRedeem']['given_by'];
		$amount = $value['RewardRedeem']['redeem_amount'];
		$redeemed_value = $value['RewardRedeem']['redeemed_value'];
		$total_charity_amount += $amount;
		$total_charity_value += $redeemed_value;
		if(isset($charity_amount[$gb]) && !empty($charity_amount[$gb])) {
			if(isset($charity_amount[$gb]['amount']) && !empty($charity_amount[$gb]['amount'])) {
				$charity_amount[$gb]['amount'] = $charity_amount[$gb]['amount'] + $amount;
			}
			else{
				$charity_amount[$gb]['amount'] = $amount;
			}
		}
		else{
			$charity_amount[$gb]['amount'] = $amount;
		}
	}
}
$creatorUser = $this->ViewModel->get_user_data($project_charity['given_by']);
$creator_user = $creatorUser['UserDetail']['full_name'];

$currency_symbol = project_currency_symbols($project_id);
 ?>
<style type="text/css">
	.lbl-text {
		font-weight: normal;
	}

</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Project - Charity OV</h3>
</div>
<div class="modal-body clearfix">
    <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
    <input type="hidden" name="project_id" id="project_id" value="<?php echo $project_id; ?>" />
    <div class="ov-project">
		<div class="charity-info"><label class="col-sm-3">Charity: </label><label class="col-sm-9 lbl-text"><?php echo htmlentities($project_charity['title'], ENT_QUOTES, "UTF-8"); ?></label></div>
		<div class="charity-info"><label class="col-sm-3">Nominated by: </label><label class="col-sm-9 lbl-text"><?php echo $creator_user; ?></label></div>
		<div class="charity-info"><label class="col-sm-3">Total OV: </label><label class="col-sm-9 lbl-text"><?php echo $total_charity_amount; ?></label></div>
		<div class="charity-info"><label class="col-sm-3">Total Value: </label><label class="col-sm-9 lbl-text charity-label"><?php echo $currency_symbol.$total_charity_value; ?></label></div>

		<?php if($charity_redeem_data) { ?>
			<div class="ov-project-list">
				<div class="panel panel-default">
				  	<div class="panel-heading">
						<h4 class="panel-title">
					  		<span class="project-name"><i class="fa fa-briefcase"></i> <?php echo htmlentities($project_title, ENT_QUOTES, "UTF-8"); ?></span>
						</h4>
				  	</div>
				  	<div class="panel-collapse collapse in">
						<div class="panel-body">
							<div class="ov-list-member">
							  	<ul>
							  		<?php foreach ($charity_amount as $user => $amount) {

										$html = '';
										if( $user != $current_user_id ) {
										    $html = CHATHTML($user, $project_id);
										}
										$userDetail = $this->ViewModel->get_user_data($user);
										$user_image = SITEURL . 'images/placeholders/user/user_1.png';
										$user_name = 'Not Available';
										$job_title = 'Not Available';
										if(isset($userDetail) && !empty($userDetail)) {
										    $user_name = htmlentities($userDetail['UserDetail']['first_name'],ENT_QUOTES) . ' ' . htmlentities($userDetail['UserDetail']['last_name'],ENT_QUOTES);
											$profile_pic = $userDetail['UserDetail']['profile_pic'];
											$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);

										    if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
										        $user_image = SITEURL . USER_PIC_PATH . $profile_pic;
										    }
										}
							  			?>
								  	<li>
										<span class="ov-list-img tipText" title="<?php echo $user_name; ?>"><img src="<?php echo $user_image; ?>" alt=""></span>
										<span class="count"><?php echo $amount['amount']; ?></span>
								  	</li>
								  	<?php } ?>
								</ul>
							</div>
					  	</div>
				  	</div>
			  	</div>
			</div>
		<?php } ?>
	</div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<script type="text/javascript">
    $(function(){
    	/*$('.ov-list-img').popover({
            placement : 'bottom',
            trigger : 'hover',
            html : true,
            container: 'body',
            delay: {show: 50, hide: 400}
        });*/
    })// END document ready
</script>


<style>
.charity-info {
	display: inline-block;
	width: 100%;
	padding-bottom: 5px;
}
.ov-project-list {
	display: inline-block;
	width: 100%;
	max-height: 465px;
overflow: auto;
}
.ov-project-list .panel{
	margin-top: 10px;
}
.ov-project-list .panel .panel-heading {
    padding: 10px;
	}
.ov-project-list .panel	.panel-body{
		padding: 10px;
	}
	.ov-list-member {
		display: inline-block;
	width: 100%;
				white-space: nowrap;
overflow: auto;
	}
	.ov-list-member ul{
		padding: 0px;margin: 0px;
	}
	.ov-list-member ul li{
	display: inline-block;
	width: 50px;
		margin-right: 3px;
	}

.ov-list-member ul li .ov-list-img {
    border: 2px solid #ccc;
    display: inline-block;
    border-radius: 50%;
    overflow: hidden;
	width: 40px;
	height: 40px;
}

	.ov-list-member ul li .count{
		color: #95c043;
		font-weight: bold;
		font-size: 13px;
		display:block;

	}

	.charity-label i {
		font-weight: inherit !important;
	}
</style>