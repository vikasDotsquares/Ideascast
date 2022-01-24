<?php
echo $this->Html->css('projects/wiki');
?>

<?php
if (isset($data) && !empty($data)) {
    $user_id = $this->Session->read('Auth.User.id');
    $project_id = $data['Project']['id'];
    $owner = $this->Common->ProjectOwner($project_id, $this->Session->read('Auth.User.id'));
    $participants = participants($project_id, $owner['UserProject']['user_id']);
    $participants_owners = participants_owners($project_id, $owner['UserProject']['user_id']);
    $participantsGpOwner = participants_group_owner($project_id);
    $participantsGpSharer = participants_group_sharer($project_id);

    $participants = (isset($participants) && !empty($participants)) ? array_filter($participants) : array();
    $participants_owners = (isset($participants_owners) && !empty($participants_owners)) ? array_filter($participants_owners) : array();
    $participantsGpOwner = (isset($participantsGpOwner) && !empty($participantsGpOwner)) ? array_filter($participantsGpOwner) : array();
    $participantsGpSharer = (isset($participantsGpSharer) && !empty($participantsGpSharer)) ? array_filter($participantsGpSharer) : array();

    $total = 0;
	
	$participants_tot = ( isset($participants) && !empty($participants) ) ? count($participants) : 0;
	$participants_owners_tot = ( isset($participants_owners) && !empty($participants_owners) ) ? count($participants_owners) : 0;
	$participantsGpOwner_tot = ( isset($participantsGpOwner) && !empty($participantsGpOwner) ) ? count($participantsGpOwner) : 0;
	$participantsGpSharer_tot = ( isset($participantsGpSharer) && !empty($participantsGpSharer) ) ? count($participantsGpSharer) : 0;
	
    $total = $participants_tot + $participants_owners_tot + $participantsGpOwner_tot + $participantsGpSharer_tot;

    $project_wiki = $this->Wiki->getProjectWiki($data['Project']['id'], $this->Session->read('Auth.User.id'));
    $wiki_id = (isset($project_wiki['Wiki']['id']) && !empty($project_wiki['Wiki']['id'])) ? $project_wiki['Wiki']['id'] : null;
    if (isset($project_id) && !empty($project_id)) {

        $latestBlog = $this->TeamTalk->getLatestBlog($data['Project']['id'], $this->Session->read('Auth.User.id'));
        $bloglist = $this->TeamTalk->getWikiBlogList($data['Project']['id'], $this->Session->read('Auth.User.id'));
        
        $latestWikiPage = $this->Wiki->getLatestWikiPage($data['Project']['id'], $this->Session->read('Auth.User.id'),$wiki_id);
        $wikiPageLists = $this->Wiki->getWikiPageLists($data['Project']['id'], $this->Session->read('Auth.User.id'),$wiki_id);
    }
    ?>	
    <?php 
        $p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));
        $user_project = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));
        $gp_exists = $this->Group->GroupIDbyUserID($project_id, $this->Session->read('Auth.User.id'));
        if (isset($gp_exists) && !empty($gp_exists)) {
            $p_permission = $this->Group->group_permission_details($project_id, $gp_exists);
        }
	  $is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id") ,1);
	  
    ?>
    <div class="panel ideacast-panel <?php echo str_replace('bg-', '', $data['Project']['color_code']); ?>" style="clear: both" data-id="panels-<?php echo $data['Project']['id']; ?>">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="trim-text wiki-panel-headingone">
                    <i class="fa fa-briefcase text-white"></i> <?php echo strip_tags($data['Project']['title']); ?>
                </span>
                <span class="pull-right tipText wiki-panel-headingtwo" style="margin-right:0">
                    <?php 
					//echo "<strong>Start:</strong> " . date("d M, Y", strtotime($data['Project']['start_date'])) . "  " . "<strong>End:</strong> " . date("d M, Y", strtotime($data['Project']['end_date']));
					
					echo "<strong>Start:</strong> " . $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['Project']['start_date'])),$format = 'd M, Y') . "  " . "<strong>End:</strong> " . $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($data['Project']['end_date'])),$format = 'd M, Y');

					?>
                    <div class="ideacast-projecticon">
                        <i class="fa fa-minus btn btn-sm btn-default open_panel" style="margin: 0 0 0 10px; padding: 3px 6px 3px 5px;" data-target="#open_by<?php echo $data['Project']['id']; ?>" data-parent="#project_accordion" data-toggle="collapse" aria-expanded="false" ></i>&nbsp;
                        <a href="<?php echo SITEURL . 'projects/index/' . $data['Project']['id'] . '/'; ?>"><i class="fa fa-folder-open btn btn-sm btn-default tipText" style="margin:0; padding: 3px 0 3px 5px;" title="Open Project" >&nbsp;</i></a>
                    </div>
                </span>
            </h4>
        </div>

        <div class="panel-body panel-collapse collapse close_panel in" id="open_by<?php echo $data['Project']['id']; ?>" >

            <div class="box-content wiki-header">							
               	<?php echo $this->element('../Wikies/buttons', array(
                                "user_project"=>$user_project,
                                "p_permission"=>$p_permission,
                                "total"=>$total,
                                "bloglist"=>$bloglist,
                                'project_wiki' => $project_wiki,
                                "wiki_id"=>$wiki_id,
                                "project_id"=>$project_id
                            )
                        ); 
                ?>					
            </div>

            <div class="box-content wiki-inner">
                    <?php $flag =  true;
                    if ((isset($is_full_permission_to_current_login) && !empty($is_full_permission_to_current_login)) ) {
				 
				 	
							
						if(isset($project_wiki) && !empty($project_wiki)){
							if(((isset($project_wiki) && !empty($project_wiki) && ($project_wiki['Wiki']['status']==1))) || ($project_wiki['Wiki']['user_id']==$this->Session->read('Auth.User.id')) ||  (( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) ){
						
							
							 $is_requested_user = $this->Wiki->is_requested_user_is_approved($user_id, $project_wiki['Wiki']['id']);
							 if(( (isset($user_project)) && (!empty($user_project)) ) || ( isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1 )) {
                                                             
                                                         }else if((isset($is_requested_user) && $is_requested_user == 1 ) &&  ($project_wiki['Wiki']['wtype'] !=1)){
							
							 $flag =  false;
							
							}
                                                        //echo $project_wiki['Wiki']['id'].'--';
                                                        //echo $this->Session->read('Auth.User.id');
                                                        //echo $p_permission['ProjectPermission']['project_level'];
                                                        //pr($is_requested_user);
                                                        
							if( $flag == true){
							
					?>
                
                            <ul class="tabtype nav nav-tabs">
                                <li class="active" style="cursor: pointer;">
                                    <a id="1" data-remote="<?php echo SITEURL; ?>wikies/wiki_read/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>" style="cursor: pointer;" ><i class="fa fa-eye"></i>&nbsp;Read</a>
                                </li>										
                                <li>
                                    <a  data-remote="<?php echo SITEURL; ?>wikies/wiki_comment/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>" id="2" style="cursor: pointer;"><i class="fa fa-commenting-o"></i>&nbsp;Comments</a>
                                </li>
<!--                                <li>
                                    <a data-remote="<?php echo SITEURL; ?>wikies/wiki_document/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>" id="3" style="cursor: pointer;"><i class="fa fa-folder-o"></i>&nbsp;Documents&nbsp;(<span id="doccount"><?php echo $this->Wiki->get_wiki_document_count($wiki_id);?></span>)</a>
                                </li>-->
                                <?php
                                $is_full_permission_to_current_login = $this->Wiki->check_permission($project_id,$this->Session->read("Auth.User.id"));
                                if (isset($is_full_permission_to_current_login) && !empty($is_full_permission_to_current_login)) {
                                ?>
                                
                                <li>
                                    <a data-remote="<?php echo SITEURL; ?>wikies/wiki_history/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>" id="4" style="cursor: pointer;"><i class="fa fa-history"></i>&nbsp;History</a>
                                </li>
                                <li>
                                    <a data-remote="<?php echo SITEURL; ?>wikies/wiki_dashboard/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>" id="5" style="cursor: pointer;"><i class="fa fa-dashboard"></i>&nbsp;Dashboard</a>
                                </li>
                                <li>
                                    <a data-remote="<?php echo SITEURL; ?>wikies/wiki_admin/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>" id="6" style="cursor: pointer;"><i class="fa fa-gear"></i>&nbsp;Admin</a>
                                </li>
                                <?php 
                                
                                }
                                ?>
                                
                                <li class="pull-right">
                                    <input type="text" name="keyword" id="searchinput" class="read" data-remote="<?php echo SITEURL;?>wikies/get_wiki_page_by_user/<?php echo $project_id . '/' . $user_id . '/' . $wiki_id; ?>" data-type="read" data-tab="page" value="" placeholder="Search" >
                                </li>
                            </ul>									
                            <ul class="updateDetail">  
                                <?php
                                //pr($project_wiki);
                                ?>
                                    <li>Created: 
                                        <?php 
                                    echo (isset($project_wiki['Wiki']['created']) && !empty($project_wiki['Wiki']['created'])) ? $this->Wiki->_displayDate(date("Y-m-d h:i:s",$project_wiki['Wiki']['created'])) : 'N/A'; 
                                        ?>
                                    </li>
                                    <li>Created by: 
                                        <?php 
                                        echo (isset($project_wiki['Wiki']['user_id']) && !empty($project_wiki['Wiki']['user_id'])) ? $this->Common->userFullname($project_wiki['Wiki']['user_id']) : 'N/A'; 
                                        ?>
                                    </li>
                                    <li id="wikiupdateeddate">Updated: 
                                        <?php 
                                        
                                        $updated = (isset($project_wiki['Wiki']['updated']) && isset($project_wiki['Wiki']['updated_user_id']) && $project_wiki['Wiki']['updated_user_id'] != null) ? $project_wiki['Wiki']['updated'] : '';
                                        echo (isset($updated) && !empty($updated)) ? $this->Wiki->_displayDate(date("Y-m-d h:i:s",$updated)) : 'N/A'; 
                                        ?>
                                    </li>
                                    <li id="wikiupdatedbyuser">Updated by: 
                                    <?php 
                                    $wikiupdatedusername = (isset($project_wiki['Wiki']['updated_user_id']) && $project_wiki['Wiki']['updated_user_id'] != null) ? $this->Common->userFullname($project_wiki['Wiki']['updated_user_id']) : '';
                                    echo (isset($wikiupdatedusername) && $wikiupdatedusername != '') ? $wikiupdatedusername : 'N/A'; 
                                    ?>
                                    </li>
                            </ul>
                            <div class="row wikiesclass">
                                    <div class="tabContent" id="tabContent1">
                                        <?php echo $this->element('../Wikies/partials/wiki_read/wiki_read', array("project_id"=>$project_id,"wiki_id"=>$wiki_id) );  ?>
                                    </div>

                                    <div class="tabContent" id="tabContent2">
                                        <?php //echo $this->element('../Wikies/partials/wiki_comment/wiki_comment', array("project_id"=>$project_id,"wiki_id"=>$wiki_id ));?>
                                    </div>
                                    <div class="tabContent" id="tabContent3">
                                        <?php //echo $this->element('../Wikies/partials/wiki_document/wiki_document', array("project_id"=>$project_id,"wiki_id"=>$wiki_id )); ?>
                                    </div>
                                    <div class="tabContent" id="tabContent4">
                                        <?php //echo $this->element('../Wikies/partials/wiki_history/wiki_history', array( "project_id"=>$project_id,"wiki_id"=>$wiki_id) ); ?>
                                    </div>
                                    <div class="tabContent" id="tabContent5">
                                        <?php  //echo $this->element('../Wikies/partials/wiki_dashboard/wiki_dashboard', array("project_id"=>$project_id,"wiki_id"=>$wiki_id ) );?>
                                    </div>
                                    <div class="tabContent" id="tabContent6">
                                        <?php //echo $this->element('../Wikies/partials/wiki_admin/wiki_admin', array("project_id"=>$project_id,"wiki_id"=>$wiki_id ));  ?>
                                    </div>
                            </div>
                    <?php
                                                }else{
                                                    ?>
                <div class="col-sm-12 text-center padding">
                        Access not allowed.
                    </div>
                
                <?php
                                                }        }
                    }else {
                    ?>
                    <div class="col-sm-12 text-center padding">
                        No Wikis.
                    </div>
                    <?php
                    }
					}else{
					?>
					 <div class="col-sm-12 text-center padding">
                        Access not allowed.
                    </div>
					<?php
					}
                    ?>
            </div>
        </div>					
    </div>

    <script type="text/javascript">
        var selectIds1 = $('#open_by<?php echo $data['Project']['id']; ?>');
        
        $(function ($) {
                var icons = {
                    header: "ui-icon-circle-arrow-e",
                    activeHeader: "ui-icon-circle-arrow-s"
                };
                $( "#accordion-wiki" ).accordion({
                    icons: icons
                });
            
            
            $('.tabContent').hide();
            $('#tabContent1').show();

            $('.open_panel.fa.fa-minus').click(function () {
                if ($(this).length > 0)
                    $(this).toggleClass(' fa-minus fa-plus');
            })
            $('.tabtype a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
                var tabContent = '#tabContent' + this.id;
                $('.tabContent').hide();
                var $current = $(this),id = $current.attr("id"), project_id = '<?php echo $project_id; ?>',wiki_id = '<?php echo $wiki_id; ?>',user_id = '<?php echo $this->Session->read('Auth.User.id'); ?>', actionURL = $current.data("remote");
                if(id == 1 || id == 2){
                    $("#searchinput").show();
                    
                    if(id == 1){
                        $("#searchinput").attr("data-type","read");
                        $("#searchinput").removeClass("comment");
                        $("#searchinput").addClass("read");
                    }else if(id == 2){
                        $("#searchinput").removeClass("read");
                        $("#searchinput").addClass("comment");
                        $("#searchinput").attr("data-type","comment");
                    }
                }else{
                   $("#searchinput").hide(); 
                }
                $.ajax({
                    url: actionURL,
                    type: "POST",
                    global: true,
                    data: {project_id: project_id, wiki_id: wiki_id, user_id: user_id},
                    success: function (response) {
                        $('.tooltip').hide()
                        $(".tabContent").html("");
                        $(tabContent).html(response).show( "fade", { direction: "Down"  }, 500 );
                        $("#searchinput").val("");
                    }
                });
                
            })
        });
    </script>
<?php }else{?>
     <div class="panel ideacast-panel bg-red">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="trim-text">
                    <i class="fa fa-briefcase text-white"></i>  No Project Found!
                </span>
                
            </h4>
        </div>
     </div>
    
    
<?php } ?>
<div class="modal modal-success fade " id="modal_create_wiki" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal modal-success fade " id="modal_create_wiki_page" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
<div id="confirm_box_img_del" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Delete confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Do you want to delete To-do attachment you sure to before click delete?</p>
                <p class="text-warning"><small>If you click on delete, your To-do attachment will be lost.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete-yes" class="btn btn-success">Delete</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-success fade " id="modal_add_blogComments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>


<!-- END MODAL BOX -->


