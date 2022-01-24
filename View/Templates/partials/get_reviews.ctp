<style>
/* 
.rating-stars {
	border: none;
	float: right;
	margin: -4px 0 0 0;
	position: relative;
}

.rating-stars > input { display: none;  }
.rating-stars > label:before {
	margin: 5px;
	font-size: 14px;
	font-family: FontAwesome;
	display: inline-block;
	content: "\f005";
}

.rating-stars > .half:before {
	content: "\f089";
	position: absolute;
}

.rating-stars > label {
	color: #ddd;
	float: right;
	pointer-events: none;
}
.rating-stars > input:checked ~ label { color: #FFD700;  }
 
.review-item {
	border-bottom: 1px solid #ccc;
	clear: both;
	display: block;
	float: left;
	min-height: 80px;
	padding: 10px 10px 0;
	width: 100%;
}
.review-image {
	display: block;
	text-align: justify;
	float: left;
	width: 8%;
}
.img-wrap {
	text-align: center;
	display: inline-block;
	vertical-align: top;
	float: left;
}
.review-text {
	display: inline-block;
	float: left;
	width: 89%;
	overflow-wrap: break-word;
}
.used_unused_flag {
	display: block;
	text-align: center;
	padding: 0 0 10px;
}
#no-review-list {
	font-size: 20px;
	text-align: center;
}  */
</style>

<?php
	$current_user_id = $this->Session->read('Auth.User.id');
    $current_org = $this->Permission->current_org();	
?>

<?php if( isset($data) && !empty($data) ) { ?>
				<?php foreach($data as $key => $row) { ?>
					<?php

						$userDetail = $this->ViewModel->get_user( $row['TemplateReview']['user_id'], null, 1 );
						$user_image = SITEURL . 'images/placeholders/user/user_1.png';
						$user_name = 'Not Available';
						$job_title = 'Not Available';
						if(isset($userDetail) && !empty($userDetail)) {
							$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
							$profile_pic = $userDetail['UserDetail']['profile_pic'];
							$job_title = $userDetail['UserDetail']['job_title'];

							$html = '';
							if( $row['TemplateReview']['user_id'] != $current_user_id ) {
								$html = CHATHTML($row['TemplateReview']['user_id']);
							}

							if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)) {
								$user_image = SITEURL . USER_PIC_PATH . $profile_pic;
							}
						}
						$templater_id = $row['TemplateReview']['template_relation_id'];
					?>
				<div class="review-item" data-id="<?php echo $row['TemplateReview']['id']; ?>" data-rating="<?php echo $row['TemplateReview']['rating']; ?>" data-used="<?php echo $row['TemplateReview']['used_unused']; ?>">
					
					<div class="review-image" style="float: left;  width: 10%;">
						<div class="img-wrap">
							<span class="style-popple-icon-out">
							<span class="style-popple-icon">
							<img src="<?php echo $user_image; ?>" class="annotate-user-image pophover" align="left" width="40" height="40" data-content="<div class='user-popover'><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><?php echo $html; ?></div>">
								</span>
							
							<?php if($userDetail['UserDetail']['organization_id'] != $current_org['organization_id']){ ?>
								<i class="communitygray18 tipText community-g" title="" data-original-title="Not In Your Organization"></i>
							<?php } ?>
							
								</span>
						</div>
						
						
					</div>
					<div class="review-text" style=""><?php echo nl2br(htmlentities($row['TemplateReview']['comments'],ENT_QUOTES, "UTF-8")); ?></div>

					<div class="date-options pull-left">
						<span class="date-text"><?php echo _displayDate($row['TemplateReview']['created']); ?></span>
						<span class="controls pull-right">
						<?php if( $row['TemplateReview']['user_id'] == $current_user_id ) { ?>
							<a type="button" id="" class="btn   btn-xs edit_review tipText" title="Edit">
								<i class="edit-icon"></i>
							</a>
							<a type="button" data-temprid="<?php echo $templater_id;?>" class="btn  btn-xs delete_review tipText" title="Delete">
								<i class="deleteblack"></i>
							</a>
						<?php } ?>
						</span>


						<?php $rate_val = $row['TemplateReview']['rating']; ?>
						<?php $item_id = $row['TemplateReview']['id']; ?>
						<span class="rating-stars">
							<?php $item_id = $row['TemplateReview']['id']; ?>
							<input id="star5" name="rating_<?php echo $item_id ?>" value="5" type="radio" <?php if($rate_val == 5){ ?>checked="checked" <?php } ?>>
							<label class="full lbl" for="star5" title="Awesome - 5 stars"></label>

							<input id="star4half" name="rating_<?php echo $item_id ?>" value="4 and a half" type="radio" <?php if($rate_val == 4.5){ ?>checked="checked" <?php } ?>>
							<label class="half lbl" for="star4half" title="Pretty good - 4.5 stars"></label>

							<input id="star4" name="rating_<?php echo $item_id ?>" value="4" type="radio" <?php if($rate_val == 4){ ?>checked="checked" <?php } ?>>
							<label class="full lbl" for="star4" title="Pretty good - 4 stars"></label>

							<input id="star3half" name="rating_<?php echo $item_id ?>" value="3 and a half" type="radio" <?php if($rate_val == 3.5){ ?>checked="checked" <?php } ?>>
							<label class="half lbl" for="star3half" title="Meh - 3.5 stars"></label>

							<input id="star3" name="rating_<?php echo $item_id ?>" value="3" type="radio" <?php if($rate_val == 3){ ?>checked="checked" <?php } ?>>
							<label class="full lbl" for="star3" title="Meh - 3 stars"></label>

							<input id="star2half" name="rating_<?php echo $item_id ?>" value="2 and a half" type="radio" <?php if($rate_val == 2.5){ ?>checked="checked" <?php } ?>>
							<label class="half lbl" for="star2half" title="Kinda bad - 2.5 stars"></label>

							<input id="star2" name="rating_<?php echo $item_id ?>" value="2" type="radio" <?php if($rate_val == 2){ ?>checked="checked" <?php } ?>>
							<label class="full lbl" for="star2" title="Kinda bad - 2 stars"></label>

							<input id="star1half" name="rating_<?php echo $item_id ?>" value="1 and a half" type="radio" <?php if($rate_val == 1.5){ ?>checked="checked" <?php } ?>>
							<label class="half lbl" for="star1half" title="Meh - 1.5 stars"></label>

							<input id="star1" name="rating_<?php echo $item_id ?>" value="1" type="radio"  <?php if($rate_val == 1){ ?>checked="checked" <?php } ?> >
							<label class="full lbl" for="star1" title="Sucks big time - 1 star"></label>

							<input id="starhalf" name="rating_<?php echo $item_id ?>" value="half"type="radio" <?php if($rate_val == 0.5){ ?>checked="checked" <?php } ?>>
							<label class="half lbl" for="starhalf" title="Sucks big time - 0.5 stars"></label>
						</span>
					</div>
					
					<div class="used_unused_flag pull-left">Knowledge Template Used in Project 
						<?php if( isset($row['TemplateReview']['used_unused']) && !empty($row['TemplateReview']['used_unused']) ) { ?>
							<i class="activegreen green  icon_used_unused tipText" title="Used in project"></i>
							<?php }else{ ?>
							<i class="inactivered red icon_used_unused tipText" title="Not used in project"></i>
						<?php } ?>
					</div>

				</div>
				<?php } ?>
			<?php }
			else { ?>
			<div id="no-review-list" >No Reviews</div>
			<?php } ?>
<script type="text/javascript" >
	
/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }) */ 
</script>