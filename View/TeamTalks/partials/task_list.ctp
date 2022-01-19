<style>
.blogleftsection {
	border-right: 1px solid #4a7ebb;
}
.blogrightsection {
	border-left: 1px solid #4a7ebb;
	margin-left: -1px;
}
</style>
<?php
if(isset($data) && !empty($data)) {

$project_id = $data['Project']['id'];
$owner = $this->Common->ProjectOwner($project_id,$this->Session->read('Auth.User.id'));
$participants = participants($project_id,$owner['UserProject']['user_id']);
$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
$participantsGpOwner = participants_group_owner($project_id );
$participantsGpSharer = participants_group_sharer($project_id );

$participants = isset($participants) ? array_filter($participants) : $participants;


	$participants = (isset($participants) && !empty($participants)) ? array_filter($participants) : array();
	$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : array();
	$participantsGpOwner = (isset($participantsGpOwner) && !empty($participantsGpOwner)) ? array_filter($participantsGpOwner) : array();
	$participantsGpSharer = (isset($participantsGpSharer) && !empty($participantsGpSharer)) ? array_filter($participantsGpSharer) : array();

$total = 0;
$participants_tot = ( isset($participants) && !empty($participants) ) ? count($participants) : 0;
$participants_owners_tot = ( isset($participants_owners) && !empty($participants_owners) ) ? count($participants_owners) : 0;
$participantsGpOwner_tot = ( isset($participantsGpOwner) && !empty($participantsGpOwner) ) ? count($participantsGpOwner) : 0;
$participantsGpSharer_tot = ( isset($participantsGpSharer) && !empty($participantsGpSharer) ) ? count($participantsGpSharer) : 0;

$total = $participants_tot + $participants_owners_tot + $participantsGpOwner_tot + $participantsGpSharer_tot;

$project_wiki = $this->TeamTalk->getProjectWiki($data['Project']['id'], $this->Session->read('Auth.User.id'));

if( isset($data['Project']['id']) ) {

	// below function is not using... so these are comented
	//$latestBlog = $this->TeamTalk->getLatestBlog($data['Project']['id'],$this->Session->read('Auth.User.id'));
	$bloglist = $this->TeamTalk->getWikiBlogList($data['Project']['id'],$this->Session->read('Auth.User.id'));


}
	$project_id = $data['Project']['id'];
    $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
	$user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
	$gp_exists = $this->Group->GroupIDbyUserID($project_id,$this->Session->read('Auth.User.id'));
	if( isset($gp_exists) && !empty($gp_exists) ){
		$p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
	}

?>
			<div class="panel ideacast-panel <?php echo str_replace('bg-', '', $data['Project']['color_code']) ; ?>" style="clear: both" data-id="panels-<?php echo $data['Project']['id']; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<span class="trim-text wiki-panel-headingone">
								<i class="fa fa-briefcase text-white"></i> <?php echo htmlentities($data['Project']['title']); ?>
							</span>
							<span class="pull-right tipText wiki-panel-headingtwo" style="margin-right:0">
								<?php
								//echo "<strong>Start:</strong> ".date("d M, Y",strtotime($data['Project']['start_date']))."  "."<strong>End:</strong> ".date("d M, Y",strtotime($data['Project']['end_date']));
								echo "<strong>Start:</strong> ".$this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['Project']['start_date'])),$format = 'd M, Y')."  "."<strong>End:</strong> ".$this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['Project']['end_date'])),$format = 'd M, Y');


								?>
								<div class="ideacast-projecticon">
								<!--<i class="fa fa-plus btn btn-sm btn-default open_panel " style="margin: 0 0 0 10px; padding: 3px 6px 3px 5px;" data-target="#open_by<?php echo $data['Project']['id']; ?>" data-parent="#project_accordion" data-toggle="collapse" aria-expanded="false" id="show-main-panel" ></i>&nbsp;-->
								<a href="<?php echo SITEURL.'projects/index/'.$data['Project']['id'].'/'; ?>"><i class="fa fa-folder-open btn btn-sm btn-default tipText" style="margin:0; padding: 3px 0 3px 5px;" title="Open Project" >&nbsp;</i></a>
                                </div>
							</span>
						</h4>
					</div>

					<div class="panel-body panel-collapse collapse close_panel " id="open_by<?php echo $data['Project']['id']; ?>" >

						<div class="box-content wiki-header">
								<div class="pull-left">
									<a class="bg-blue sb_blog" data-target="#modal_people" data-toggle="modal" href="<?php echo SITEURL ?>projects/project_people/<?php echo $data['Project']['id']; ?>"><button class="btn btn-sm btn-primary bg-blue">Project Team: <?php echo $total; ?></button></a>

									<?php
								/*	if(isset($project_wiki['Wiki']['id']) && !empty($project_wiki['Wiki']['id']) )
									if ( ((count($project_wiki) > 0) && (isset($project_wiki['Wiki']['status']) && $project_wiki['Wiki']['status']==1)) || ($project_wiki['Wiki']['user_id'] == $this->Session->read("Auth.User.id"))  ) { ?>
										<a href="<?php echo Router::Url(array('controller' => 'wikies', 'action' => 'index','project_id'=>$data['Project']['id'], 'admin' => FALSE), TRUE); ?>"><button class="btn btn-sm btn-success tipText" title="Open Wiki">Wiki &nbsp;<i class="fa fa-folder-open  "></i></button></a>
									<?php }  */
										/*if( count($bloglist) > 0 ) { ?>
										<!--<a href="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'index','project'=>$data['Project']['id'], 'admin' => FALSE), TRUE); ?>"><button class="btn btn-sm btn-success">Blog Post</button></a>-->
									<?php } */ ?>
								</div>
								<div class="pull-right">
								<?php
								if ( ( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) {

								$project_wiki_tot = ( isset($project_wiki) && !empty($project_wiki) ) ? count($project_wiki) : 0;

									 /*	if( $project_wiki_tot <= 0){
								?>
										<button data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'create_wiki', $data['Project']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['Project']['id'];?>" data-area="<?php echo $data['Project']['id'];?>" id="create_wiki" data-target="#modal_create_wiki" data-toggle="modal" data-original-title="Create Wiki" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Create Wiki</button>
									<?php }  */

									?>
									<button data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'create_blog', $data['Project']['id'], $this->Session->read('Auth.User.id'), 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['Project']['id'];?>" data-area="<?php //echo $project_wiki['Wiki']['id'];?>" id="create_wiki" data-target="#modal_create_blogpost" data-toggle="modal" data-original-title="Create Blog" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Create Blog Post</button>
								<?php
								} else if ( (empty($p_permission['ProjectPermission']['project_level']) || $p_permission['ProjectPermission']['project_level'] != 1 ) ) {

								/* if( count($project_wiki) > 0){
								?>
								<button data-original-title="Request Wiki" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Request Wiki</button>
								<?php } */
								}?>
								</div>
						</div>

						<div class="box-content wiki-inner">

							<?php if( isset($bloglist) && !empty($bloglist) ){ ?>
							<div>
									<ul class="nav nav-tabs">
										<li class="active" style="cursor: pointer;">
										<a id="blog_read" data-value="<?php echo $this->Session->read('Auth.User.id'); ?>" data-project="<?php echo $data['Project']['id'];?>" ><i class="fa fa-eye"></i>&nbsp;Read</a></li>
										<li><a id="blog_comments" style="cursor: pointer;" data-value="<?php echo $this->Session->read('Auth.User.id'); ?>" data-project="<?php echo $data['Project']['id'];?>" ><i class="fa fa-commenting-o"></i>&nbsp;Comments</a></li>
										<li><a id="blog_documents" style="cursor: pointer;" data-value="<?php echo $this->Session->read('Auth.User.id'); ?>" data-project="<?php echo $data['Project']['id'];?>" ><i class="fa fa-folder-o"></i>&nbsp;Documents&nbsp;<?php //echo $this->TeamTalk->documentCounter($data['Project']['id'], $this->Session->read('Auth.User.id'));?></a></li>
										<?php if ( ( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) { ?>
										<li><a id="blog_dashboard" style="cursor: pointer;" data-value="<?php echo $this->Session->read('Auth.User.id'); ?>" data-project="<?php echo $data['Project']['id'];?>" ><i class="fa fa-dashboard"></i>&nbsp;Dashboard</a></li>
										<li><a id="blog_admin" style="cursor: pointer;" data-value="<?php echo $this->Session->read('Auth.User.id'); ?>" data-project="<?php echo $data['Project']['id'];?>"><i class="fa fa-gear"></i>&nbsp;Admin</a></li>
										<?php } ?>
										<li class="pull-right ttsearch" ><input type="text" value="" placeholder="Search" ></li>
									</ul>
									<?php /* <ul class="updateDetail">
										<?php if(isset($latestBlog['Blog']['created'])){ ?>
										<li>Created: <?php echo date('d M Y H:i:s',strtotime($latestBlog['Blog']['created']) ); ?></li>
										<?php } if(isset($latestBlog['Blog']['user_id'])){ ?>
										<li>Created by: <?php echo $this->Common->userFullname($latestBlog['Blog']['user_id']); ?></li>
										<?php } if(isset($latestBlog['Blog']['updated'])){ ?>
										<li>Updated: <?php echo date('d M Y H:i:s',strtotime($latestBlog['Blog']['updated']) );  ?></li><?php } ?>
										<?php if(isset($latestBlog['Blog']['updated_id'])){ ?>
										<li>Updated by: <?php echo $this->Common->userFullname($latestBlog['Blog']['updated_id']);  ?></li><?php } ?>
									</ul> */ ?>
                                    <div class="row" style="overflow:hidden">

                                    <div class="col-lg-9 col-md-8 padding-bottom blogleftsection">

										<div class="tabContent task-list-left-wrap" id="tabContent1" style="">
											<div id="accordion-first" class="clearfix" style="padding-top:26px; max-height:1314px; overflow-y:auto;overflow-x:hidden;">
												<div class="accordion" id="accordion2">
												<?php $lu = 1;
													foreach($bloglist as $listView){

													$blogconter = $this->TeamTalk->getBlogCounter($data['Project']['id'],$listView['Blog']['id']);

													$userBlog = $this->TeamTalk->userBlogLike($data['Project']['id'],$listView['Blog']['id'],$this->Session->read('Auth.User.id'));
												?>
													<div class="accordion-group" id="bloglistview<?php echo $listView['Blog']['id'];?>" data-bcounter="<?php echo $lu; ?>" >
													<a href="#bloglistview<?php echo $listView['Blog']['id'];?>"></a>
														<div class="accordion-heading">
														<a id="target<?php echo $listView['Blog']['id'];?>"   class="blog_data" data-project="<?php echo $data['Project']['id']; ?>" data-rel="<?php echo $listView['Blog']['id'];?>"  data-id="#collapse<?php echo $listView['Blog']['id'];?>">
															<em class="icon-fixed-width fa fa-plus-square-o"></em>
															<img src="<?php echo SITEURL.'img/blog-icon-black-300.png' ?>" width="22">
															<span style="font-weight:600;"><?php echo htmlentities($listView['Blog']['title']);?></span>
														 </a>
														<?php /* if( $listView['Blog']['user_id'] == $this->Session->read('Auth.User.id') ) { ?>
														<a data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'edit_blog', $listView['Blog']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $listView['Blog']['id'];?>" data-area="<?php echo $listView['Blog']['id'];?>" id="create_wiki" data-target="#modal_edit_blogpost" data-toggle="modal" style="cursor: pointer;" >&nbsp;Edit</a>
														<?php }  */?>

														</div>
														<div class="blog-full-details" style="margin: 0 0 0 52px;">
															<div class="blog_details">
																<?php echo $this->Common->userFullname($listView['Blog']['user_id']); ?>, <?php

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

																	<?php if( $listView['Blog']['user_id'] == $this->Session->read('Auth.User.id') ) {  ?>
																		<a data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'edit_blog', $listView['Blog']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $listView['Blog']['id'];?>" data-area="<?php echo $listView['Blog']['id'];?>" id="create_wiki" data-target="#popup_modal" data-toggle="modal" style="cursor: pointer;" class="btn btn-xs btn-default tipText" >&nbsp;<i class="fa fa-pencil"></i></a>&nbsp;<a id="confirm_blog_delete" data-user="<?php echo $this->Session->read('Auth.User.id');?>" class="btn btn-xs btn-danger tipText delete_blog" data-original-title="Delete" data-bcounter="<?php echo $lu;?>" data-value="<?php echo $listView['Blog']['id'];?>"><i class="fa fa-trash"></i></a>
																	<?php
																	$lu++;
																	} ?>

																<?php } ?>
															</div>
															<div id="collapse<?php echo $listView['Blog']['id'];?>" class="accordion-body  ">
															  <div class="accordion-inner heightmin vik">
																<?php echo $listView['Blog']['description'];?>
															  </div>
															</div>
														</div>
													</div>
												<?php } ?>
												</div><!-- end accordion -->
											</div>
										</div>

										<div class="tabContent task-list-left-wrap" id="tabContent3">
											<ul class="list-unstyled idea-team-talks-commentlist" id="accordion"  >
												<?php
													$blog_comments = $this->TeamTalk->user_blog_lists($listView['Blog']['id'], $project_id);
													echo $this->element('../TeamTalks/partials/blog_comment_list', array('blog_list' => $blog_comments));
												?>
											</ul>
										</div>
										<div class="tabContent task-list-left-wrap" id="tabContent4">
											<div class="pull-right">
												<a data-area="<?php echo $listView['Blog']['id'];?>" id="upload_user_blog_document" style="cursor: pointer; text-transform:none !important;" class="btn btn-sm btn-default tipText disabled_comment_list" data-original-title="Add Document" >Add a file</a>
											</div>
											<div class="idea-doc-list">
												<div class="row" style="max-height:480px;">
													<ul class="list-unstyled idea-team-talks-document-list" id="accordion"  ></ul>
												</div>
											</div>
										</div>

										<div class="tabContent task-list-left-wrap dashboard_blog_list" id="tabContent6"></div>
										<div class="tabContent task-list-left-wrap admin_right_blog_list" id="tabContent7"></div>
									</div>
                                    <?php $blogusers =  $this->TeamTalk->getBlogUsers($project_id);?>
                                    <div class="col-lg-3 col-md-4 blogrightsection" >
                                    	<div class="idea-blog-list" id="blog_user_lists" >
                                        	<?php echo $this->element('../TeamTalks/partials/blog_sidebar', array('listusers'=>$blogusers) ); ?>
                                        </div>
										<div class="idea-blog-list" id="blog_comment_lists" ></div>
										<div class="idea-blog-list" id="blog_document_lists" ></div>
										<div class="idea-blog-list" id="commentblog_lists"></div>
										<div class="idea-admin-blog-list" id="adminblog_lists"></div>
										<div class="idea-blog-list" id="dashboardblog_lists"></div>
                                    </div>
                                    </div>
								</div>
						<?php  } else {

									if( empty($project_wiki) && count($project_wiki) <= 0 ){
										echo '<div class="text-center noblog">NO BLOG POSTS</div>';
									} else {
										echo '<div class="text-center noblog">NO BLOG POSTS</div>';
									}

							}  ?>
						</div>
					</div>
			</div>

<?php } else { ?>
<div class="panel">
	<div class="col-sm-12 partial_data box-borders">
		<div class="overview-box" id="projects">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 project_data">
				<div class="no-data"> Select Project </div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<style>
.heightmin{ height:74px; overflow:hidden; text-overflow:ellipsis;}
.heightmax{ height:auto !important; overflow:hidden;}
.disable{ pointer-events:none !important;}

.partial_data {
    padding: 0;
    width: 100%;
    border-image: none;
    border-style: solid solid solid;
    border-width: 1px 1px 1px;
    border-color: #fff;
	background-color:#fff;
	min-height:500px;
}
.overview-box {
    float: left;
    width: 100%;
}
.project_data {
    padding: 0;
    min-height: 177px;
}
.no-data {
    color: #bbbbbb;
    font-size: 32px;
    left: 4px;
    position: absolute;
    text-align: center;
    text-transform: uppercase;
    top: 25%;
    width: 98%;
}


</style>
 <script type="text/javascript">
	var selectIds1 = $('#open_by<?php echo $data['Project']['id']; ?>');
			$('#tabContent2').hide();
			$('#tabContent3').hide();
			$('#tabContent4').hide();
			$('#tabContent5').hide();
			$('#tabContent6').hide();
			$('#tabContent7').hide();
	$(function ($) {
		$('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
		$("#blog_comment_lists").hide();
		$("#blog_document_lists").hide();
		$("#commentblog_lists").hide();


		$('.open_panel.fa.fa-plus').click(function(){
			if($(this).length > 0)
			$(this).toggleClass('fa-plus fa-minus');
		})


		$('.wiki-inner .nav a').click(function (e) {

			$(this).tab('show');
			var tabContent = '#tabContent' + this.id;

			$('#tabContent1').hide();
			$('#tabContent2').hide();
			$('#tabContent3').hide();
			$('#tabContent4').hide();
			$('#tabContent5').hide();
			$('#tabContent6').hide();
			$('#tabContent7').hide();
			$(tabContent).show();

		})

		// Accordion Toggle Items
			/* var iconOpen = 'fa fa-plus-square-o',
			iconClose = 'fa fa-minus-square-o'; */

		console.log($("#ProjectId option:selected").val());

		if( $("#ProjectId option:selected").val() && $("#ProjectId option:selected").val().length > 0  ){

			$("#show-main-panel").removeClass('fa-plus').addClass('fa-minus');
			$("#open_by"+$("#ProjectId option:selected").val()).addClass('in');

		}

	});
</script>
