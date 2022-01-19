<style>
	.select_msg {
	    color: #bbbbbb;
	    font-size: 30px;
	    left: 3px;
	    position: absolute;
	    text-align: center;
	    text-transform: uppercase;
	    margin-top: 13%;
	    width: 98%;
	}
	.wsp_template{
		background:rgba(103, 160, 40, 0.2);
	}

	/* .popover p:first-child {
		font-weight: 600 !important;
		width: 170px !important;
	}

	.popover p:nth-child(2) {
		font-size: 11px;
	} */

	.popover p {
		margin-bottom: 2px !important;
	}

	.tooltip.right {
		margin-top:8px;
	}

	.tooltip.right .tooltip-arrow{
		top: 7%;
		margin-top:0;
	}

	.tooltip.top .tooltip-arrow{
		left: 6%;
		bottom:1px;
	}

</style>
<?php

$current_org = $this->Permission->current_org();
$showFullWidth = false;
	if( $this->Session->read('Auth.User.role_id') == 1 ) {
		$showFullWidth = true;
		$hide = 1;
	}
	if($showFullWidth == true ){
		$columnWidth = 4;
		$ulWidht = 12;

	} else {
		$columnWidth = 3;
		$ulWidht = 12;
		$hide = 0;
	}
?>
	<div class="col-sm-12 col-md-3 pull-right tparty" style="<?php  echo ($hide == 1 )? 'display:none;' :'';?>" >
		<div class="full-col-width">
			<div class="panel panel-default  edit_sketch_panel">
				<div class="panel-heading bg-green">
					<h5 class="temp-panel-title nowrap-title">
						Third Party
					</h5>
				</div>
				<div class="panel-body third-party-template" style="background : rgb(239, 239, 239) none repeat scroll 0 0; height:300px; overflow:auto;">
					<div class="panel-group saveasdiv_ajax" id="accordion" role="tablist" aria-multiselectable="true">
					<?php
					$thirdpartyUsers = $thirdpartyuser;
					echo $this->element('../Templates/partials/thirdpartytemplate',  array('thirdpartyUsers'=> $thirdpartyUsers, 'template_category_id'=>$template_category_id) );
					?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
if(isset($user_templates) && !empty($user_templates) ){

?>

<ul id="new_templates" class="clearfix templates_list col-sm-<?php echo $ulWidht;?>  col-md-9">
<?php
foreach( $user_templates as $key => $val ) {
	//pr($val);
	$item = $val['TemplateRelation'];
	//$columnwidht
?>
<?php
	/*$t_title = str_replace("'", "", $item['title']);
	$t_title = str_replace('"', "", $t_title);*/
	$t_title = $item['title'];

 ?>
	<li class="col-lg-<?php echo $columnwidht; ?> col-md-6 col-sm-12 utemp_list" data-id="<?php echo $item['id']; ?>"  >

			<div class="box box-success">
				<div class="box-body clearfix<?php if( isset($item['wsp_imported']) && $item['wsp_imported'] == 1 ){?> wsp_template<?php } ?>">

				<div class="box-header"> <h3 class="box-title truncate" >
					<span style="text-transform: none !important;" title="<?php echo htmlentities($t_title, ENT_QUOTES); ?>" class="temp-title tipText"><?php echo htmlentities($t_title, ENT_QUOTES); ?> </span>
					</h3> </div>
					<div class="top-section">
						<div class="tamplate-thumb-left text-center">
							<?php
								if( $item['type'] == 3) {
									$userDetail = $this->Template->get_thirdparty_user( $item['thirdparty_id']);
								} else {
									$userDetail = $this->ViewModel->get_user( $item['user_id'], null, 1 );
								}

								$profilesPic = SITEURL.'images/placeholders/user/user_1.png';

								$html = '';
								if( $item['type'] == 2 ) {
									$html = '';
								}else{
									$html = CHATHTML($item['user_id']);
									if( !empty($project_id) ){
										$html = CHATHTML($item['user_id'], $project_id);
									}
								}

								$style = '';

									$user_image = SITEURL . 'images/placeholders/user/user_1.png';
									$user_name = 'N/A';
									$job_title = 'N/A';

								if(isset($userDetail) && !empty($userDetail) && ( $item['type'] == 1 || $item['type'] == 2 )) {
										$user_name = $userDetail['UserDetail']['first_name'] . ' ' . $userDetail['UserDetail']['last_name'];
										$profile_pic = $userDetail['UserDetail']['profile_pic'];

										$job_title = htmlentities($userDetail['UserDetail']['job_title'],ENT_QUOTES);
										$job_title = htmlentities($job_title,ENT_QUOTES);

										if(!empty($profile_pic) && file_exists(USER_PIC_PATH . $profile_pic)) {
											$user_image = SITEURL . USER_PIC_PATH . $profile_pic;

										}

										if( $item['type'] == 2) {
											$user_image = SITEURL . 'images/logo_idea_white.png';
											$user_name = '<span class=thrdauthor>Author:</span> IdeasCast Limited';
											$html = 'Contact us for more information.';
											$job_title = '';
										}

										/* if($userDetail['User']['role_id'] == 1){
											//$dataRemoteUrl = Router::Url(array('controller' => 'templates', 'action' => 'show_admin_profile', $item['thirdparty_id'] ), TRUE);
										} else {  */

										$dataRemoteUrl = Router::Url(array('controller' => 'shares', 'action' => 'show_profile', $item['user_id'] ), TRUE);

										//}

									if($userDetail['User']['role_id'] == 1){
										$user_image = SITEURL . 'images/logo_idea_white.png';
									?>
							<div class="knowledge-temp-org">
									<a href="javascript:void(0);"  class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
										<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
									</a>
								<?php if( ($userDetail['UserDetail']['organization_id'] != $current_org['organization_id']) &&  ($userDetail['UserDetail']['user_id'] != $this->Session->read('Auth.User.id')) ){ ?>	
											<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization"></i>
																		
								<?php } ?>
								
								
								
								
																		</div>
									<?php } else { ?>
							<div class="knowledge-temp-org">
									<a href="#"  data-remote="<?php echo $dataRemoteUrl; ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><p><?php echo $user_name; ?></p><p><?php echo $job_title; ?></p><p><?php echo $html; ?></p></div>"  >
										<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
									</a>
							<?php if( ($userDetail['UserDetail']['organization_id'] != $current_org['organization_id']) &&  ($userDetail['UserDetail']['user_id'] != $this->Session->read('Auth.User.id')) ){ ?>			
								<i class="communitygray18 tipText community-g" style="cursor: pointer;" title="" data-original-title="Not In Your Organization"></i>
							<?php } ?>											
																		
																		</div>
							<?php  }
							} else {

										//pr($userDetail['ThirdParty']);

										$style = "width:auto;height:73px;";

										$user_name = '';
									$profile_pic = '';
									$address = '';
									$phone = '';
									$email = '';
									$contact1 = '';
									$contact2 = '';
									$website = '';
									$summary =  '';
									$job_title ='';
									if( isset($userDetail['ThirdParty']) && !empty($userDetail['ThirdParty']) ){
										$user_name = $userDetail['ThirdParty']['username'];
										$profile_pic = ( isset($userDetail['ThirdParty']['profile_img']) && !empty($userDetail['ThirdParty']['profile_img']) )? $userDetail['ThirdParty']['profile_img'] : '';
										$address = $userDetail['ThirdParty']['address'];
										$phone = $userDetail['ThirdParty']['phone'];
										$email = $userDetail['ThirdParty']['email'];
										$contact1 = $userDetail['ThirdParty']['contact1'];
										$contact2 = $userDetail['ThirdParty']['contact2'];
										$website = $userDetail['ThirdParty']['website'];
										$summary = $userDetail['ThirdParty']['summary'];
										$job_title = $userDetail['ThirdParty']['contact2'];
									}



										$project = 0;

										$html ='';
										$html .='<p class=thrdauthor>Author: '.$user_name.'</p>';
										if(!empty($website)){
											$html .='<p class=useremal><a target=_blank href=http://'.$website.'>'.$website.'</a></p>'; }
										if( !empty($summary) ){
										$html .='<p>Summary: '.$summary.'</p>';}
										$html .='<p class=more_infor>For more information</p>';

										if( !empty($contact1) ){
										$html .='<p>Contact 1: '.$contact1.'</p>';}

										if( !empty($contact2) ){
										$html .='<p>Contact 2: '.$contact2.'</p>';}
										if( !empty($phone) ){
										$html .='<p>Telephone: '.$phone.'</p>';}
										if(!empty($email)){
										$html .='<p class=useremal>Email: <a href=mailto:'.$email.'>'.$email.'</a></p>';}


										//$html = "<p><a data-id='".$userDetail['ThirdParty']['id']."' href='mailto:".$email."'  class='btn btn-default chat_start_email btn-xs disabled'>Send Email</a> </p>";

										$user_image = SITEURL . 'images/placeholders/user/user_1.png';
										if(!empty($profile_pic) && file_exists(THIRD_PARTY_USER_PATH . $profile_pic)) {
											$user_image = SITEURL . 'uploads/thirdy_party_user/'.$profile_pic;
										}

										//$dataRemoteUrl = Router::Url(array('controller' => 'templates', 'action' => 'show_admin_profile', $item['thirdparty_id'] ), TRUE);
										$dataRemoteUrl = '';


									/* <a href="#" data-remote="<?php echo $dataRemoteUrl; ?>"  data-target="#popup_modal" data-toggle="modal" class="pophover" data-content="<div><?php echo $html; ?></div>"  >
										<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
									</a>  */?>

									<a href="javascript:void(0);" class="pophover" data-content="<div><?php echo $html; ?></div>"  >
										<img src="<?php echo $user_image; ?>" class="user-image" style="<?php echo $style; ?>" >
									</a>
							<?php


							} ?>
							<div class="template_creator-image">
							</div>
							<?php if ( !isset($project_id) || empty($project_id)) { ?>

							<a style="margin-top:4px; cursor: default;" class="btn btn-jeera btn-sm select-btn   disable"  id="btn_select_user_templates" > <i class="fa fa-check"></i> Select </a>
							<?php }else { ?>
							<a class="btn btn-jeera btn-sm select-btn btn_select_user_template " id="btn_select_user_template" > <i class="fa fa-check"></i> Select </a>
							<?php }  ?>
						</div>

						<div class="tamplate-thumb-right block-thumb">

							<?php //pr($item);


						   //	$workspacetip = str_replace("'", "", $item['description']);
							//$workspacetip = str_replace('"', "", $workspacetip);
							$workspacetip = htmlentities( $item['description']);



							?>
							<div class="sec-bar-tamlate" >
								<?php echo $this->element('../Templates/partials/area_template', ['template_id' => $item['template_id'],'allow'=>0,'selection' => null ] ); ?>
								<?php //echo $this->Html->image('layouts/'.$val['Template']['layout_preview'], ['class' => 'thumb']); ?>
							</div>

							<div class="bost-block" >

								<a id="" class="btn btn-xs template_pophover rv-margin-rd" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?php echo nl2br($workspacetip); ?>" href="#"><i class="fa fa-info template_info"></i></a>

								<?php $review_count = template_reviews($item['id'], 1); ?>
							<?php $sum_template_reviews = sum_template_reviews($item['id']);

							$average = 0;
								if( (isset($sum_template_reviews[0][0]['total']) && !empty($sum_template_reviews[0][0]['total'])) && (isset($review_count) && !empty($review_count)) ) {
									$average = $sum_template_reviews[0][0]['total'] / $review_count;

									$whole = floor($average);      // 1
									$fraction = $average - $whole; // .25

									if($fraction > 0.5 || $fraction < 0.5){
									$average = round($average);

									}else{
									$average = $average;
									}

								}



							?>


							<?php /* ?><span class="rv-span rv-margin" ><i title="<?php if( isset($review_count) && !empty($review_count) ){ ?> Annotations<?php }else {?>Annotate<?php } ?>" class="review-icon tipText pull-right <?php if( isset($review_count) && !empty($review_count) ){ ?>review-black<?php }else{ ?>review-gray<?php } ?>" data-toggle="modals" data-target="#popup_modals" data-remotes="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'add_review', $item['id'] ), TRUE); ?>"></i>
							</span><?php */ ?>







							<?php

							$verify_doc = template_element_documents($item['id']);
							$classG = '';
							 if($verify_doc > 0){
								$doc_tt = "Documents in Knowledge Template: ".$verify_doc;
								$classG = 'text-green';
							 }else{
								$doc_tt = 'No Documents in Knowledge Template';
							 }
							?>
								<a class="btn-kt folder-doc  tipText" title="<?php echo $doc_tt; ?>"><i class="noteblack <?php echo $classG; ?>"></i></a>



								<?php if( $item['user_id'] == $this->Session->read('Auth.User.id')) { ?>
									<a id="" class="btn-kt rv-margin tipText" data-title="Edit Knowledge Template" href="<?php echo Router::Url(array( "controller" => "templates", "action" => "update_template", $item['id'], $item['template_category_id'], $project_id, 'admin' => FALSE ), true); ?>"><i class="edit-icon"></i></a>

								<?php } ?>


							</div>




						<div class=" rv-count">
						<div class="rv-cont-inside"><a data-original-title="Likes" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'like_comment', $item['id'] ), TRUE); ?>" class="btn btn-xs btn-like-task tipText <?php if( $this->Session->read('Auth.User.id') != $item['user_id'] && !template_commented($item['id'], $this->Session->read('Auth.User.id'))) { ?>like_comment<?php } ?>">
									<i class="thumbsupblack"></i>
									<span class="label bg-purple"><?php echo (isset($val['TemplateLike']) && !empty($val['TemplateLike'])) ? count($val['TemplateLike']) : 0; ?> </span>
								</a>

						<?php /* <a id="" class="btn btn-default rv-like btn-xs tipText" title="Elements in Template"   ><i class="icon_elm"></i> <?php echo template_elements($item['id']); ?></a>*/
							$verify = template_elements($item['id']);

						?>



						<a id="" class="btn btn-like-task rv-like btn-xs tipText" title="Knowledge Template Tasks"  <?php if( !empty($verify) ) { ?> data-toggle="modal" data-target="#popup_modal_element" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'view_elements', $item['id'] ), TRUE); ?>" <?php } ?>><i class="tasksblack18"></i> <?php echo template_elements($item['id']); ?></a>



						</div>
							<?php if( $item['user_id'] == $this->Session->read('Auth.User.id')) { ?>

									<!-- <a id="" class="btn btn-xs btn-danger trash_template rv-margin" ><i class="fa fa-trash"></i></a> -->
									<a id="" class="btn-kt trash_template rv-margin  delete-an-item  tipText" data-title="Delete" data-toggle="modal" data-target="#modal_delete" data-remote="<?php echo Router::Url( array( "controller" => "templates", "action" => "delete_an_item", $item['id'], 'admin' => FALSE ), true ); ?>"><i class="deleteblack"></i></a>
								<?php } ?>
						</div>



						</div>




					</div>
					<div class="middle-section">
						<div class="btn-bar">
							<div class="pull-left">







							</div>

							<div class="pull-right">
								<?php /* deploy  ?><a id="" class="btn btn-default btn-xs tipText" title="Elements in Template"  <?php if( !empty(template_elements($item['id'])) ) { ?> data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'view_elements', $item['id'] ), TRUE); ?>" <?php } ?>><i class="icon_elm"></i> <?php echo template_elements($item['id']); ?></a><?php  */ ?>




							</div>

						</div>


						<?php  ?>
						<div class="review-bar">
							<span class="rev-text">Reviews (<b class="review-count"><?php echo $review_count; ?></b>)</span>


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


							<span class="rv-span  rv-absolute" ><i title="<?php if( isset($review_count) && !empty($review_count) ){ ?> Reviews<?php }else {?>Reviews<?php } ?>" class="review-icon tipText pull-right <?php if( isset($review_count) && !empty($review_count) ){ ?>review-black<?php }else{ ?>review-gray<?php } ?>" data-toggle="modal" data-target="#popup_modal" data-remote="<?php echo Router::Url(array('controller' => 'templates', 'action' => 'add_review', $item['id'] ), TRUE); ?>"></i>
							</span>


						</div>



						<?php  ?>
						<div class="arrow-down-click"><a href="#" class="closed"><i class="fa " aria-hidden="true"></i></a></div>

						<div class="bottom-section">
						<div class="dates-bar">
							<span class="col-sm-6">Created:<br />
							<?php echo $this->Wiki->_displayDate( date('Y-m-d h:i:s', strtotime($item['created'])), 'd M Y'); ?>
							</span>
							<span class="col-sm-6">Updated:<br />
							<?php echo $this->Wiki->_displayDate( date('Y-m-d h:i:s', strtotime($item['modified'])), 'd M Y'); ?>
							</span>
						</div>
							<?php
							if( isset($val['AreaRelation']) && !empty($val['AreaRelation'])) {


							usort($val['AreaRelation'], function($a, $b) {
								return $a['id'] > $b['id'];
							});


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

			</div>

		</li>
<?php } echo "</ul>"; ?>

<?php   }  else   { ?>

	<div class="col-sm-<?php echo $ulWidht;?> select_msg_main">
		<div class="select_msg col-sm-<?php echo $ulWidht;?>"  > NO KNOWLEDGE TEMPLATES AVAILABLE  </div>
	</div>

<?php } ?>


	<div class="paginate_links"></div>
	<?php /* if(isset($templateCount) && $templateCount > $pageLimit){ ?>
	<div class="pagination_temp_filter_template">
		<?php echo $this->element('template_paging',array('templatecounter'=>$templateCount));  ?>
	</div>
	<?php } */ ?>

<script type="text/javascript">
$(function(){
	$('a[href="#"],a[href=""]').attr('href', 'javascript:;');
	$('[data-toggle="tooltip"]').tooltip();

	$('.popover').remove();
	$('.template_pophover').popover({
        placement : 'top',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    });

/* 	$('.pophover').popover({
        placement : 'bottom',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    }); */

	$('.saveasdiv_ajax .third_party_template .panel-collapse .edited-template.text-center').each(function(){

		//  $(this).parents('.third_party_template').hide();
		 $(this).parents('.panel-collapse').hide();

	})

});
	</script>
	<div class="modal modal-success fade " id="popup_modal_element" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>