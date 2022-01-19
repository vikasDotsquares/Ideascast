<?php
$project_id = $project_id;
$user_id = $user_id;
$data['Project']['id'] = $project_id;
	$lu = 1;
	foreach($bloglist as $listView){

	$blogconter = $this->TeamTalk->getBlogCounter($project_id,$listView['Blog']['id']);

	$userBlog = $this->TeamTalk->userBlogLike($data['Project']['id'],$listView['Blog']['id'],$this->Session->read('Auth.User.id'));
?>
                                                <div class="accordion-group" data-bcounter="<?php echo $lu; ?>">
                                                    <div class="accordion-heading">
                                                    <a  class="blog_data" data-project="<?php echo $data['Project']['id']; ?>" data-rel="<?php echo $listView['Blog']['id'];?>"  data-id="#collapse<?php echo $listView['Blog']['id'];?>">
                                                        <em class="icon-fixed-width fa fa-plus-square-o"></em>
                                                        <img src="<?php echo SITEURL.'img/blog-icon-black-300.png' ?>" width="22">
                                                        <?php echo $listView['Blog']['title'];?>
                                                     </a>
													<?php /* if( $listView['Blog']['user_id'] == $this->Session->read('Auth.User.id') ) { ?>
                                                    <a data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'edit_blog', $listView['Blog']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $listView['Blog']['id'];?>" data-area="<?php echo $listView['Blog']['id'];?>" id="create_wiki" data-target="#modal_edit_blogpost" data-toggle="modal" style="cursor: pointer;" >&nbsp;Edit</a>
													<?php } */ ?>
                                                    </div>
													<div class="blog-full-details" style="margin: 0 0 0 48px;">
														<div class="blog_details"><?php echo $this->Common->userFullname($listView['Blog']['user_id']); ?>, <?php //echo date('d M Y H:i A',strtotime($listView['Blog']['created']) );

														$startTimeStamp = strtotime($listView['Blog']['created']);
														$endTimeStamp = strtotime(date('Y-m-d H:i:s'));
														$timeDiff = abs($endTimeStamp - $startTimeStamp);
														$numberDays = $timeDiff/86400;
														$numberDays = intval($numberDays);

														if( $numberDays <= 0 ){
															echo "Today";
														} else if( $numberDays == 1 ){
															echo $numberDays.' Day ago';
														} else {
															echo $numberDays.' Days ago';
														}
														/* if( $userBlog <= 0 && $listView['Blog']['user_id'] != $this->Session->read('Auth.User.id') ){
														?>&nbsp;&nbsp;
															<a id="blog_like" data-value="<?php echo $listView['Blog']['id'];?>" style="cursor:pointer;"><i class="fa fa-thumbs-up" aria-hidden="true">&nbsp;</i></a><span id="blogcounter<?php echo $listView['Blog']['id'];?>"><?php echo $blogconter; ?></span>
														<?php } else { ?>
															<a style="cursor:pointer;"><i class="fa fa-thumbs-up" aria-hidden="true">&nbsp;</i></a><span><?php echo $blogconter; ?></span>
														<?php }  */

														if( $userBlog <= 0 && $listView['Blog']['user_id'] != $this->Session->read('Auth.User.id') ){
															?>&nbsp;&nbsp;
																<a id="blog_like" data-value="<?php echo $listView['Blog']['id'];?>" style="cursor:pointer;" class="btn btn-xs btn-default ">
																	<i class="fa fa-thumbs-o-up"></i>
																	<span class="label bg-purple" id="blogcounter<?php echo $listView['Blog']['id'];?>"><?php echo $blogconter; ?></span>
																</a>
															<?php } else { ?>
																<a style="cursor:pointer;" class="btn btn-xs btn-default disabled">
																	<i class="fa fa-thumbs-o-up"></i>
																	<span class="label bg-purple" ><?php echo $blogconter; ?></span>
																</a>

																<?php

																$lu++;

																 $project_id = $listView['Blog']['project_id'];
																 $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
																 $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
																 $gp_exists = $this->Group->GroupIDbyUserID($project_id,$this->Session->read('Auth.User.id'));

																 if( isset($gp_exists) && !empty($gp_exists) ){
																  $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
																 }

																if ( ( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) {

																}

																if( $listView['Blog']['user_id'] == $this->Session->read('Auth.User.id') ) { ?>
																<a data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'edit_blog', $listView['Blog']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $listView['Blog']['id'];?>" data-area="<?php echo $listView['Blog']['id'];?>" id="create_wiki" data-target="#popup_modal" data-toggle="modal" style="cursor: pointer;" class="btn btn-xs btn-default tipText" >&nbsp;<i class="fa fa-pencil"></i></a>&nbsp;<a id="confirm_blog_delete" class="btn btn-xs btn-danger tipText delete_blog" data-original-title="Delete" data-value="<?php echo $listView['Blog']['id'];?>"><i class="fa fa-trash"></i></a>
																<?php } ?>

														<?php } ?>


														</div>

														<div id="collapse<?php echo $listView['Blog']['id'];?>" class="accordion-body  ">
														  <div class="accordion-inner heightmin vik">
															<?php echo $listView['Blog']['description'];?>
														  </div>
														</div>
													</div>

                                                </div>
                                            <?php }
echo $this->Js->writeBuffer(); 						?>