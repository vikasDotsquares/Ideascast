<?php if(isset($data) && !empty($data)) { 

$project_id = $data['Project']['id'];
$owner = $this->Common->ProjectOwner($project_id,$this->Session->read('Auth.User.id')); 
$participants = participants($project_id,$owner['UserProject']['user_id']);
$participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
$participantsGpOwner = participants_group_owner($project_id );
$participantsGpSharer = participants_group_sharer($project_id );

$participants = isset($participants) ? array_filter($participants) : $participants;
$participants_owners = isset($participants_owners) ? array_filter($participants_owners) : $participants_owners;
$participantsGpOwner = isset($participantsGpOwner) ? array_filter($participantsGpOwner) : $participantsGpOwner;
$participantsGpSharer = isset($participantsGpSharer) ? array_filter($participantsGpSharer) : $participantsGpSharer;

$total = 0; 

$participants_tot = ( isset($participants) && !empty($participants) ) ? count($participants) : 0;
$participants_owners_tot = ( isset($participants_owners) && !empty($participants_owners) ) ? count($participants_owners) : 0;
$participantsGpOwner_tot = ( isset($participantsGpOwner) && !empty($participantsGpOwner) ) ? count($participantsGpOwner) : 0;
$participantsGpSharer_tot = ( isset($participantsGpSharer) && !empty($participantsGpSharer) ) ? count($participantsGpSharer) : 0;

$total = $participants_tot + $participants_owners_tot + $participantsGpOwner_tot + $participantsGpSharer_tot;
$project_wiki = $this->TeamTalk->getProjectWiki($data['Project']['id'], $this->Session->read('Auth.User.id'));

if( isset($data['Project']['id']) ) {

	$latestwiki = $this->TeamTalk->getLatestWiki($data['Project']['id'],$this->Session->read('Auth.User.id'));
	$wikilist = $this->TeamTalk->getWikiList($data['Project']['id'],$this->Session->read('Auth.User.id'));
	$wikiCount = $this->TeamTalk->getWiki($data['Project']['id'],$this->Session->read('Auth.User.id'));
	
	$latestBlog = $this->TeamTalk->getLatestBlog($data['Project']['id'],$this->Session->read('Auth.User.id'));
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
			<div class="panel <?php echo str_replace('bg-', '', $data['Project']['color_code']) ; ?>" style="clear: both" data-id="panels-<?php echo $data['Project']['id']; ?>">
					<div class="panel-heading" >
						<h4 class="panel-title">
							<span class="trim-text">
								<i class="fa fa-briefcase text-white"></i> <?php echo strip_tags($data['Project']['title']); ?>
							</span>
							<span class="pull-right tipText" style="margin-right:0">
								<?php 
								//echo "<strong>Start:</strong> ".date("d M, Y",strtotime($data['Project']['start_date']))."  "."<strong>End:</strong> ".date("d M, Y",strtotime($data['Project']['end_date'])); 
								echo "<strong>Start:</strong> ".$this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['Project']['start_date'])),$format = 'd M, Y')."  "."<strong>End:</strong> ".$this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['Project']['end_date'])),$format = 'd M, Y'); 
								
								?>
								
								<i class="fa fa-plus btn btn-sm btn-default open_panel collapsed" style="margin: 0 0 0 10px; padding: 3px 6px 3px 5px;" data-target="#open_by<?php echo $data['Project']['id']; ?>" data-parent="#project_accordion" data-toggle="collapse" aria-expanded="false" ></i>&nbsp;
								<a href="<?php echo SITEURL.'projects/index/'.$data['Project']['id'].'/'; ?>"><i class="fa fa-folder-open btn btn-sm btn-default tipText" style="margin:0; padding: 3px 0 3px 5px;" title="Open Project" >&nbsp;</i></a>
							</span>
						</h4>
					</div>
					
					<div class="panel-body panel-collapse collapse close_panel " id="open_by<?php echo $data['Project']['id']; ?>" >
						
						<div class="box-content wiki-header">							
								<div class="pull-left">
									<a class="bg-blue sb_blog" data-target="#modal_medium" data-toggle="modal" href="<?php echo SITEURL ?>projects/project_people/<?php echo $data['Project']['id']; ?>"><button class="btn  btn-sm btn-primary">People on this Project <?php echo $total; ?></button></a>
									<?php  /* if( isset($project_wiki) && !empty($project_wiki) && count($project_wiki) > 0 ){ ?>
										<a href="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'wiki','project'=>$data['Project']['id'],'admin' => FALSE), TRUE); ?>"><button class="btn btn-sm btn-success">Wiki &nbsp;<i class="fa fa-folder-open tipText" title="Open Wiki"></i></button></a>
									<?php } */  
										if( isset($bloglist) && !empty($bloglist) && count($bloglist) > 0 ) { ?>	
										<a href="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'index', 'project'=>$data['Project']['id'], 'admin' => FALSE), TRUE); ?>"><button class="btn btn-sm btn-success">Blog Post</button></a>
									<?php } ?>	
								</div>
								<div class="pull-right">
								<?php
								if (((isset($user_project)) && (!empty($user_project))) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 ) ) {
								/*	if( $wikiCount <= 0 ){
								?>
									<button data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'create_wiki', $data['Project']['id'], 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['Project']['id'];?>" data-area="<?php echo $data['Project']['id'];?>" id="create_wiki" data-target="#modal_create_wiki" data-toggle="modal" data-original-title="Create Wiki" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Create Wiki</button>
									
									<?php } */?>
									
									<button data-remote="<?php echo Router::Url(array('controller' => 'team_talks', 'action' => 'create_blog', $data['Project']['id'], $this->Session->read('Auth.User.id'), 'admin' => FALSE), TRUE); ?>" data-id="<?php echo $data['Project']['id'];?>" data-area="<?php //echo $project_wiki['Wiki']['id'];?>" id="create_wiki" data-target="#modal_create_blogpost" data-toggle="modal" data-original-title="Create Blog" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Create Blog Post</button>
								<?php 
								} else if ( (empty($p_permission['ProjectPermission']['project_level']) || $p_permission['ProjectPermission']['project_level'] != 1 ) ) {
									if( isset($project_wiki) && !empty($project_wiki) && count($project_wiki) > 0 ){	
								?>
								<button data-original-title="Request Wiki" class="btn btn-sm btn-warning tipText"><i class="fa fa-plus"></i>&nbsp;Request Wiki</button>
								<?php } 
								} ?>
								</div>							
						</div>
						
						<div class="box-content wiki-inner">
							<div>
							<?php if( isset($wikilist) && !empty($wikilist) ){ ?>
								<table data-id="" class="table table-bordered">
									<ul class="nav nav-tabs">
										<li class="active"><a id="1" href="#"><i class="fa fa-eye"></i>&nbsp;Read</a></li>										
										<li><a id="3" style="cursor: pointer;"><i class="fa fa-commenting-o"></i>&nbsp;Comments</a></li>
										<li><a id="4" style="cursor: pointer;"><i class="fa fa-folder-o"></i>&nbsp;Documents&nbsp;(0)</a></li>
										<li><a id="5" style="cursor: pointer;"><i class="fa fa-history"></i>&nbsp;History</a></li>
										<li><a id="6" style="cursor: pointer;"><i class="fa fa-dashboard"></i>&nbsp;Dashboard</a></li>
										<li><a id="7" style="cursor: pointer;"><i class="fa fa-gear"></i>&nbsp;Admin</a></li>
										<li class="pull-right"><input type="text" value="" placeholder="Search" ></li>
									</ul>									
									<ul class="updateDetail">  
										<?php if(isset($latestwiki['Wiki']['created'])){ ?>
										<li>Created: <?php 
										//echo date('d M Y g:i:s',strtotime($latestwiki['Wiki']['created']) ); 
										echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($latestwiki['Wiki']['created'])),$format = 'd M Y g:i:s');
										?></li>
										<?php } if(isset($latestwiki['Wiki']['user_id'])){ ?>
										<li>Created by: <?php echo $this->Common->userFullname($latestwiki['Wiki']['user_id']); ?></li>
										<?php } if(isset($latestwiki['Wiki']['updated'])){ ?>
										<li>Updated: <?php 
										//echo date('d M Y g:i:s',strtotime($latestwiki['Wiki']['updated']) );
										echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($latestwiki['Wiki']['updated'])),$format = 'd M Y g:i:s');		
										?></li><?php } ?>
										<?php if(isset($latestwiki['Wiki']['user_id'])){ ?>
										<li>Updated by: <?php echo $this->Common->userFullname($latestwiki['Wiki']['user_id']);  ?></li><?php } ?>
									</ul>
                                    <div class="row">
                                    <div class="col-sm-8 col-md-9">
                                    <div class="tabContent" id="tabContent1">
                                        <div id="accordion-first" class="clearfix">
                                            <div class="accordion" id="accordion2">
                                            <?php
                                                foreach($wikilist as $listView){								
                                            ?>
                                                <div class="accordion-group">
                                                    <div class="accordion-heading">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $listView['Wiki']['id'];?>">
                                                        <em class="icon-fixed-width fa fa-plus-square-o"></em>
                                                        <img src="<?php echo SITEURL.'img/blog-icon-black-300.png' ?>" width="22">
                                                        <?php echo $listView['Wiki']['title'];?>: By <?php echo $this->Common->userFullname($listView['Wiki']['user_id']); ?>, <?php 
														//echo date('d M Y g:i A',strtotime($listView['Wiki']['created']) );
														echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($listView['Wiki']['created'])),$format = 'd M Y g:i A');	
														?>
                                                    </a>														
                                                    </div>
													<div style="overflow: hidden; height: 40px;font-size:13px; color:#666; margin-top: -4px;" class="short_desc">
														<div class="accordion-inner"><br>
															<?php echo $listView['Wiki']['description'];?>
														</div>	
													</div>
                                                    <div style="height: 0px;" id="collapse<?php echo $listView['Wiki']['id'];?>" class="accordion-body collapse">
                                                      <div class="accordion-inner">
                                                        <?php echo $listView['Wiki']['description'];?>
                                                      </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            </div><!-- end accordion -->
                                        </div>								
                                       
                                    </div>									
									<div class="tabContent" id="tabContent3">
									  Wiki Comments
									</div>
									<div class="tabContent" id="tabContent4">
									   Wiki Documents
									</div>
									<div class="tabContent" id="tabContent5">
									  Wiki History
									</div>
									<div class="tabContent" id="tabContent6">
									  Wiki Dashboard
									</div>
									<div class="tabContent" id="tabContent7">
									  Wiki Admin
									</div>
                                    </div>
                                    <div class="col-sm-4 col-md-3">
                                    	<div class="idea-blog-list">
                                        	<ul class="list-inline">
                                            	<li>
                                                	<a href="#"><img src="http://192.168.4.29/ideascomposer/img/blog-icon-black-300.png" alt=""/></a>
                                                    <h5>Bal mattu</h5>
                                                    <p>
                                                    	<span><img src="http://192.168.4.29/ideascomposer/img/blog-icon-black-300.png" alt="" /></span>
                                                        <a href="#">2</a>
                                                    </p>
                                                </li>
                                                <li>
                                                	<a href="#"><img src="http://192.168.4.29/ideascomposer/img/blog-icon-black-300.png" alt=""/></a>
                                                    <h5>Bal mattu</h5>
                                                    <p>
                                                    	<span><img src="http://192.168.4.29/ideascomposer/img/blog-icon-black-300.png" alt="" /></span>
                                                        <a href="#">2</a>
                                                    </p>
                                                </li>
                                                <li>
                                                	<a href="#"><img src="http://192.168.4.29/ideascomposer/img/blog-icon-black-300.png" alt=""/></a>
                                                    <h5>Bal mattu</h5>
                                                    <p>
                                                    	<span><img src="http://192.168.4.29/ideascomposer/img/blog-icon-black-300.png" alt="" /></span>
                                                        <a href="#">2</a>
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    </div>
								</table>							
						<?php  } else { ?>	
							No Wiki Found
						<?php }  ?>	
							</div>
						</div>
					</div>					
			</div>
				
				<script type="text/javascript">
					var selectIds1 = $('#open_by<?php echo $data['Project']['id']; ?>');
							$('#tabContent2').hide();
							$('#tabContent3').hide();
							$('#tabContent4').hide();
							$('#tabContent5').hide();
							$('#tabContent6').hide();
							$('#tabContent7').hide();
					$(function ($) { 
						 
						$('.open_panel.fa.fa-plus').click(function(){
						    if($(this).length > 0)
							$(this).toggleClass('fa-plus fa-minus'); 
						})
						 
					
						$('.nav a').click(function (e) {
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
						var iconOpen = 'fa fa-plus-square-o',
							iconClose = 'fa fa-minus-square-o';

						$(document).on('show.bs.collapse hide.bs.collapse', '.accordion', function (e) {
							
							$(this).find(".short_desc").hide();
							
							var $target = $(e.target)
							  $target.siblings('.accordion-heading')
							  .find('em').toggleClass(iconOpen + ' ' + iconClose);
							  if(e.type == 'show')
								  $target.prev('.accordion-heading').find('.accordion-toggle').addClass('active');
							  if(e.type == 'hide')
								  $(this).find('.accordion-toggle').not($target).removeClass('active');
						});
    
						
					});
				</script>
<?php } ?>
  