
<?php if( isset($user_templates) && !empty($user_templates) ) { pr($user_templates, 1); ?>

	<!-- LIST AND GRID VIEW START	-->
	<ul id="new_templates" class="clearfix templates_list">
		<?php foreach( $user_templates as $key => $val ) {
			// pr($val);
			$item = $val['TemplateRelation'];
		?>

			<li class="col-lg-3 col-md-4 col-sm-6 utemp_list" data-id="<?php echo $item['id']; ?>">

				<div class="box box-success">

					<div class="box-header"> <h3 class="box-title "><?php echo strip_tags($item['title']) ?> </h3> </div>
					<div class="box-body clearfix <?php if( isset($item['wsp_imported']) && $item['wsp_imported'] == 1 ){?> wsp_template<?php } ?>">
						<div class="top-section">
<?php if ( !isset($project_id) || empty($project_id)) { ?>
																	
																	<a style="margin-top:6px;" class="btn btn-jeera btn-sm select-btn   disable pophover  " data-content="Select from Project" id="btn_select_user_templates" > <i class="fa fa-check"></i> Select </a>																	
																	<?php }else { ?>
																	<a class="btn btn-jeera btn-sm select-btn btn_select_user_template " id="btn_select_user_template" > <i class="fa fa-check"></i> Select </a>
																	<?php }  ?>
								<div class="pull-right"> <?php echo $this->Html->image('layouts/'.$val['Template']['layout_preview'], ['class' => 'thumb']); ?></div>
						</div>
						<div class="middle-section">
							<div class="btn-bar">
								<div class="pull-left">
									<?php
									$userDetail = $this->ViewModel->get_user( $item['user_id'], null, 1 );
									$profilesPic = SITEURL.'images/placeholders/user/user_1.png';
									if(isset($userDetail) && !empty($userDetail)) {
										$profile_pic = $userDetail['UserDetail']['profile_pic'];

										if(!empty($profile_pic) && file_exists(USER_PIC_PATH.$profile_pic)){
											$profilesPic = SITEURL.USER_PIC_PATH.$profile_pic;
										}
									}
									?>
									<img class="template_creator" alt="Logo Image" style=""  src="<?php echo $profilesPic ?>" alt="Profile Image" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'show_profile', $item['user_id'] ), TRUE); ?>" />
									<!--
									<i class="fa fa-user text-maroon btn btn-xs" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'shares', 'action' => 'show_profile', $item['user_id'] ), TRUE); ?>" ></i>
									-->

									<a data-original-title="Likes" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'like_comment', $item['id'] ), TRUE); ?>" class="btn btn-xs btn-default tipText <?php if( $this->Session->read('Auth.User.id') != $item['user_id'] && !template_commented($item['id'], $this->Session->read('Auth.User.id'))) { ?>like_comment<?php } ?>">
										<i class="fa fa-thumbs-o-up"></i>
										<span class="label bg-purple"><?php echo (isset($val['TemplateLike']) && !empty($val['TemplateLike'])) ? count($val['TemplateLike']) : 0; ?> </span>
									</a>

									<?php if( $item['user_id'] == $this->Session->read('Auth.User.id')) { ?>
										<a id="" class="btn btn-xs btn-default" href="<?php echo Router::Url(array( "controller" => "templates", "action" => "update_template", $item['id'], $item['template_category_id'], $project_id, 'admin' => FALSE ), true); ?>"><i class="fa fa-pencil"></i></a>
										<!-- <a id="" class="btn btn-xs btn-danger trash_template" ><i class="fa fa-trash"></i></a> -->
										<a id="" class="btn btn-xs btn-danger trash_template delete-an-item" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "templates", "action" => "delete_an_item", $item['id'], 'admin' => FALSE ), true ); ?>" ><i class="fa fa-trash"></i></a>
									<?php } ?>

								</div>

								<div class="pull-right">
									<a id="" class="btn btn-default btn-xs tipText" title="Elements in this Template"  <?php if( !empty(template_elements($item['id'])) ) { ?> data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'view_elements', $item['id'] ), TRUE); ?>" <?php } ?>><i class="icon_elm"></i> <?php echo template_elements($item['id']); ?></a>
									<a id="" class="btn btn-xs template_pophover" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?php echo nl2br($item['description']); ?>" href="#"><i class="fa fa-info template_info"></i></a>
								</div>

							</div>
							<div class="dates-bar">
								<span>Created:
								<?php echo $this->Wiki->_displayDate( date('Y-m-d h:i:s', strtotime($item['created'])), 'd M Y'); ?>
								</span>
								<span>Updated:
								<?php echo $this->Wiki->_displayDate( date('Y-m-d h:i:s', strtotime($item['modified'])), 'd M Y'); ?>
								</span>
							</div>

							<div class="review-bar">
								<?php $review_count = template_reviews($item['id'], 1); ?>
								<?php $sum_template_reviews = sum_template_reviews($item['id']);
								$average = 0;
								if( (isset($sum_template_reviews[0][0]['total']) && !empty($sum_template_reviews[0][0]['total'])) && (isset($review_count) && !empty($review_count)) ) {
									$average = $sum_template_reviews[0][0]['total'] / $review_count;
									$average = round($average);
								}

								?>
								<span>Reviews(<b class="review-count"><?php echo $review_count; ?></b>)
									<i class="review-icon <?php if( isset($review_count) && !empty($review_count) ){ ?>review-black<?php }else{ ?>review-gray<?php } ?>" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'add_review', $item['id'] ), TRUE); ?>"></i>
								</span>
								
								<?php $item_id = $item['id']; ?>
								<span class="star-rating">
									<input id="star5" name="rating_<?php echo $item_id ?>" value="5" type="radio" <?php if($average == 5){ ?>checked="checked" <?php } ?>>
									<label class="full lbl" for="star5" title="Awesome - 5 stars"></label>

									<input id="star4half" name="rating_<?php echo $item_id ?>" value="4 and a half" type="radio" <?php if($average == 4.5){ ?>checked="checked" <?php } ?>>
									<label class="half lbl" for="star4half" title="Pretty good - 4.5 stars"></label>

									<input id="star4" name="rating_<?php echo $item_id ?>" value="4" type="radio" <?php if($average == 4){ ?>checked="checked" <?php } ?>>
									<label class="full lbl" for="star4" title="Pretty good - 4 stars"></label>

									<input id="star3half" name="rating_<?php echo $item_id ?>" value="3 and a half" type="radio" <?php if($average == 3.5){ ?>checked="checked" <?php } ?>>
									<label class="half lbl" for="star3half" title="Meh - 3.5 stars"></label>

									<input id="star3" name="rating_<?php echo $item_id ?>" value="3" type="radio" <?php if($average == 3){ ?>checked="checked" <?php } ?>>
									<label class="full lbl" for="star3" title="Meh - 3 stars"></label>

									<input id="star2half" name="rating_<?php echo $item_id ?>" value="2 and a half" type="radio" <?php if($average == 2.5){ ?>checked="checked" <?php } ?>>
									<label class="half lbl" for="star2half" title="Kinda bad - 2.5 stars"></label>

									<input id="star2" name="rating_<?php echo $item_id ?>" value="2" type="radio" <?php if($average == 2){ ?>checked="checked" <?php } ?>>
									<label class="full lbl" for="star2" title="Kinda bad - 2 stars"></label>

									<input id="star1half" name="rating_<?php echo $item_id ?>" value="1 and a half" type="radio" <?php if($average == 1.5){ ?>checked="checked" <?php } ?>>
									<label class="half lbl" for="star1half" title="Meh - 1.5 stars"></label>

									<input id="star1" name="rating_<?php echo $item_id ?>" value="1" type="radio"  <?php if($average == 1){ ?>checked="checked" <?php } ?> >
									<label class="full lbl" for="star1" title="Sucks big time - 1 star"></label>

									<input id="starhalf" name="rating_<?php echo $item_id ?>" value="half"type="radio" <?php if($average == 0.5){ ?>checked="checked" <?php } ?>>
									<label class="half lbl" for="starhalf" title="Sucks big time - 0.5 stars"></label>
								</span>
							</div>
						</div>
						<div class="bottom-section">
						<?php
						if( isset($val['AreaRelation']) && !empty($val['AreaRelation'])) {
							foreach( $val['AreaRelation'] as $arkey => $arval ) {
						?>
							<span class="area_wrap">
								<h5 class="area_title"><?php echo htmlentities($arval['title'],ENT_QUOTES, "UTF-8"); ?></h5>
								<span class="area_desc">
									<?php echo nl2br(htmlentities($arval['description'],ENT_QUOTES, "UTF-8")); ?>
								</span>
							</span>
						<?php }
						} ?>
						</div>
					</div>

				</div>

			</li>
		<?php } ?>
	</ul>
	<?php if (isset($trPageCount) && !empty($trPageCount)) { ?>
		<div class="ajax-page clearfix">
			<?php 
			pr($this->params['action']);
			echo $this->element('pagination', array('model' => 'TemplateRelation', 'limit' => 1, 'pageCount' => $trPageCount )); ?>
		</div>
		
	<?php } ?> 
<?php } else if( (isset($template_category_id) && !empty($template_category_id)) ) { ?>
	<div class="select_msg" > There are no user created templates </div>
<?php } ?>