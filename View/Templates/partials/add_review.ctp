
<?php //echo $this->Html->css('star-rating'); ?>
<?php //echo $this->Html->script('star-ratings', array('inline' => true));?>

<style>

	.profile-view-header.modal-lg{
		    width: 600px;
	}
.profile-view-header.modal-lg .modal-header .modal-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 5px;
}
	#annotate-list {
		margin-left: -10px;
		margin-right: -10px;
		max-height: 300px;
		overflow-x: hidden;
		overflow-y: auto;
	}
	.annotate-item {
		clear: both;
		display: block;
		min-height: 80px;
		padding: 10px;
		border-bottom: 1px solid #ccc;
	}
	.annotate-item:last-child {
		border-bottom: medium none;
	}
	.annotate-text {
		display: block;
		font-size: 13px;
		min-height: 40px;
	}
	.date-options {
		display: block;
		font-size: 13px;
		margin: 10px 0 0;
		width: 100%;
	}
	.annotate-text-image {
		display: block;
		text-align: justify;
	}
	/*.annotate-user-image {
		border: 2px solid #ccc;
		height: 40px;
		margin: 0 10px 0 0;
		width: 40px;
		border-radius: 50%;
	}*/


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

	.rating-xs {
		display: inline-block;
		margin-left: 20px;
	}


	/**************************************************/
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
	}
	.form-body {
		border: 1px solid #ccc;
		margin: 0 0 10px;
		padding: 10px 10px 0;
	}
	.icon_used_unused {
		border-radius: 3px;
		cursor: pointer;
		padding: 2px;
		color: #fff;
		width: 20px;
		margin-top: 1px;
   	    vertical-align: top;
	}
	/*.icon_used_unused.red {
		background-color: #dd4b39;
		border: 1px solid #d73925;
	}
	.icon_used_unused.green {
		background-color: #4cae4c;
		border: 1px solid #5cb85c;
	}*/
	
	
	.rating-container  {
		line-height: 24px;
	}
	#modelFormTemplateReview .checkbox label::before {
		    margin-left: -18px;
	}
	
	.review-item .btn:active {
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
}
	.review-item .btn{
   padding: 1px 0;
}
	
	@media(min-width: 768px){
	.used-project{
		text-align: right;
	}
	}
</style>
<?php
	$current_user_id = $this->Session->read('Auth.User.id');
	$current_org = $this->Permission->current_org();
	$inputDisabled = "";
	if( $dataCreator['TemplateRelation']['user_id'] == $current_user_id ) {
		$inputDisabled = ' disabled="disabled" ';
	}

	echo $this->Form->create('TemplateReview', array('url' => array('controller' => 'projects', 'action' => 'save_review'), 'class' => 'form-bordered', 'id' => 'modelFormTemplateReview')); ?>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
	<?php $template_relation_data = getByDbId('TemplateRelation', $template_relation_id, ['title']); ?>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="myModalLabel">Reviews: <?php echo htmlentities($template_relation_data['TemplateRelation']['title'],ENT_QUOTES, "UTF-8"); ?></h3>

	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<div class="form-body addreviewpopup">

			<input type="hidden" name="data[TemplateReview][id]" id="TemplateReviewId" value="" />
			<input type="hidden" name="data[TemplateReview][template_relation_id]" id="TemplateReviewTemplateRelationId" value="<?php echo $template_relation_id; ?>" />
			<input type="hidden" name="data[TemplateReview][user_id]" id="TemplateReviewUserId" value="<?php echo $current_user_id; ?>" />

		<div class="form-group" style="">
			<label class="">Comment:</label>
			<label class="pull-right  tipText" title="Clear Review" id="clear_annotate" style="display: none;">
				<i class="deleteblack"></i>
			</label>
			<textarea rows="3" class="form-control" <?php echo $inputDisabled; ?> name="data[TemplateReview][comments]" id="TemplateReviewComments" style="resize: none;" placeholder="max 250 chars"></textarea>
			<span class="error-message text-danger" ></span>
		</div>
		<div class="row form-group">
		<div class="col-sm-6" style=" ">
			<label class="">Rate:</label>
			<input data-id="0" name="data[TemplateReview][rating]" <?php echo $inputDisabled ?> id="input-rating" value="0" type="number" class="rating">
		</div>

		<div class="col-sm-6 used-project" >
			<label for="used_unused">Used in Project:</label>
			<div class="checkbox checkbox-success" style="margin-left: 20px;">
				<input id="used_unused" <?php echo $inputDisabled ?> name="data[TemplateReview][used_unused]" class="fancy_input" value="1" type="checkbox">
				<label class="fancy_labels" for="used_unused"> </label>
			</div>
		</div>
		</div>
		</div>

		<div class="" id="annotate-list">
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
						<?php

						if(( $row['TemplateReview']['user_id'] == $current_user_id ) &&  ( $dataCreator['TemplateRelation']['user_id'] != $current_user_id ))  { ?>
							<a type="button" id="" class="btn  btn-xs edit_review tipText" title="Edit">
								<i class="edit-icon"></i>
							</a>
							<a type="button" data-temprid="<?php echo $templater_id;?>" id="" class="btn  btn-xs delete_review tipText" title="Delete">
								<i class="deleteblack"></i>
							</a>
						<?php } ?>
						</span>


						<?php $rate_val = $row['TemplateReview']['rating']; ?>
						<?php $item_id = $row['TemplateReview']['id'];

						?>
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
							<i class="activegreen green icon_used_unused tipText" title="Used in project"></i>
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

		</div>
	</div>

	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
	<?php

	if( $dataCreator['TemplateRelation']['user_id'] != $current_user_id ) { ?>
		<button type="button" id="submit_review" class="btn btn-success">Save</button>
	<?php }else{ ?>
		<!--<button type="button" id="" class="btn btn-success">Save</button>-->
	<?php } ?>
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>

		<?php echo $this->Form->end(); ?>
<script type="text/javascript" >
$(function() {
	// console.clear();

		$("#input-rating").rating({
			clearButton: '<i class="clearblackicon text-white btn-xs tipText" title="Clear Rating" style="padding: 1px 3px 2px;margin-top: 5px;"></i>',
			filledStar: '<i class="fa fa-star"></i>',
			emptyStar: '<i class="fa fa-star-o"></i>',
			min:0,
			max:5,
			step:0.5,
			size:'xs'
		});

	$('body').delegate('.delete_review', 'click', function(event) {
        var $this = $(this),
			$parent = $this.parents('.review-item:first'),
			data = $parent.data(),
			id = data.id;
			templater_id = $this.data('temprid');

			console.log("tesssssssss");
			console.log(templater_id);

			//console.log(templater_id);
			//return false;
		// return;

		if( id != '' && id !== undefined && templater_id != "" && templater_id != undefined ) {
			$.ajax({
				type:'POST',
				dataType:'JSON',
				data: {},
				url: $js_config.base_url + 'templates/delete_review/' + id+'/'+templater_id,
				global: true,
				success: function( response, status, jxhr ) {
					$this.parents('.review-item:first').fadeOut(1000, function(){
						$(this).remove();
					})
				}
			})
		}
    });

	$('#model_bx').on('hidden.bs.modal', function () {
		$(this).removeData('bs.modal');
	});


	$('body').delegate('.edit_review', 'click', function(event){
		event.preventDefault();
		var $parent = $(this).parents('.review-item:first'),
			id = $parent.data('id'),
			rating = $parent.data('rating'),
			used = $parent.data('used'),
			text = $parent.find('.review-text').text();

		$('#TemplateReviewId').val(id);
		$('#TemplateReviewComments').val(text).trigger('keyup');
		$('#input-rating').rating('update', rating);

		if( used > 0 )
			$('#used_unused').prop('checked', true);
		else
			$('#used_unused').prop('checked', false);

		$('#clear_annotate').show();
	})

	$('body').delegate('#clear_annotate', 'click', function(event){
		event.preventDefault();
		$("textarea#TemplateReviewComments").val('');
		$('#TemplateReviewId').val('');
		$(this).hide();
		$("textarea#TemplateReviewComments").next().html('');
		$('#input-rating').rating('reset');
		$('#used_unused').prop('checked', false);
	})


        $('body').delegate('#TemplateReviewComments', 'keyup focus', function(event){
            var characters = 250;

            event.preventDefault();
            var $error_el = $(this).parent().find('.error-message');
            if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
                $.input_char_count(this, characters, $error_el);
            }
        })



	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    })
})
</script>
